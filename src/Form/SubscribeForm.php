<?php

namespace Drupal\sendinblue\Form;

use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Drupal\sendinblue\SendinblueManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Subscribe form to signup SendinBlue newsletter.
 */
class SubscribeForm extends FormBase {
  /**
   * The signUp form Id.
   *
   * @var string
   */
  public $signupIp;

  /**
   * EntityTypeManagerInterface.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;
  /**
   * EmailValidatorInterface.
   *
   * @var \Drupal\Component\Utility\EmailValidatorInterface
   */
  private $emailValidator;
  /**
   * SendinblueManager.
   *
   * @var \Drupal\sendinblue\SendinblueManager
   */
  private $sendinblueManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    MessengerInterface $messenger,
    EmailValidatorInterface $emailValidator,
    SendinblueManager $sendinblueManager
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->messenger = $messenger;
    $this->emailValidator = $emailValidator;
    $this->sendinblueManager = $sendinblueManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('messenger'),
      $container->get('email.validator'),
      $container->get('sendinblue.manager')
    );
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'sendinblue_form_subscribe';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param int $mcsId
   *   The ID of signupForm.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $mcsId = NULL) {
    if ($mcsId) {
      $this->signupIp = $mcsId;
    }

    $signup = $this->entityTypeManager
      ->getStorage(SendinblueManager::SENDINBLUE_SIGNUP_ENTITY)
      ->load($this->signupIp);
    $settings = (!$signup->settings->first()) ? [] : $signup->settings->first()
      ->getValue();

    $form['#attributes'] = ['class' => ['sendinblue-signup-subscribe-form']];
    $form['description'] = [
      '#markup' => $settings['description']['value'],
    ];

    $form['fields'] = [
      '#prefix' => '<div id="sendinblue-newsletter-' . ($settings['subscription']['settings']['list']) . '-mergefields" class="sendinblue-newsletter-mergefields">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
    ];

    if (isset($settings['fields']['mergefields'])) {
      $merge_fields = $settings['fields']['mergefields'];
      $attributes = $this->sendinblueManager->getAttributeLists();

      if (is_array($merge_fields)) {
        foreach ($merge_fields as $key => $value) {
          if ($key === 'EMAIL') {
            $form['fields'][$key] = [
              '#type' => 'textfield',
              '#title' => ($value['label']),
              '#attributes' => ['style' => 'width:100%;box-sizing:border-box;'],
              '#required' => TRUE,
            ];
          }
          else {
            if (isset($value['check']) && $value['required']) {
              $enumerations = [];
              $type = '';

              foreach ($attributes as $attribute) {
                if ($attribute->getName() === $key) {
                  $type = $attribute->getType();
                  if ($type === 'category') {
                    $enumerations = $attribute->getEnumeration();
                  }
                  break;
                }
              }

              if ($type !== 'category') {
                $form['fields'][$key] = [
                  '#type' => 'textfield',
                  '#title' => ($value['label']),
                  '#attributes' => ['style' => 'width:100%;box-sizing:border-box;'],
                  '#required' => isset($value['required']) && $value['required'] ? TRUE : FALSE,
                ];
              }
              else {
                $options = [];
                foreach ($enumerations as $enumeration) {
                  $options[$enumeration->getValue()] = $enumeration->getLabel();
                }
                $form['fields'][$key] = [
                  '#type' => 'select',
                  '#title' => ($value['label']),
                  '#options' => $options,
                  '#attributes' => ['style' => 'width:100%;box-sizing:border-box;'],
                  '#required' => isset($value['required']) ? TRUE : FALSE,
                ];
              }
            }
          }
        }
      }
    }

    $form['submit'] = [
      '#type' => 'submit',
      '#weight' => 10,
      '#value' => ($settings['fields']['submit_button']),
    ];

    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $signup = $this->entityTypeManager->getStorage(SendinblueManager::SENDINBLUE_SIGNUP_ENTITY)
      ->load($this->signupIp);
    $settings = (!$signup->settings->first()) ? [] : $signup->settings->first()
      ->getValue();

    $email = $form_state->getValue(['fields', 'EMAIL']);
    $list_id = $settings['subscription']['settings']['list'];

    if (!$this->emailValidator->isValid($email)) {
      $form_state->setErrorByName('email', $settings['subscription']['messages']['invalid']);
      return;
    }

    $response = $this->sendinblueManager->validationEmail($email, $list_id);
    if ($response['code'] === 'invalid') {
      $form_state->setErrorByName('email', $settings['subscription']['messages']['invalid']);
      return;
    }
    if ($response['code'] === 'already_exist') {
      $form_state->setErrorByName('email', $settings['subscription']['messages']['existing']);
      return;
    }

    $list_ids = $response['listid'];
    $list_ids[] = $list_id;

    $info = [];
    $attributes = $this->sendinblueManager->getAttributeLists();

    foreach ($attributes as $attribute) {
      $field_attribute_name = $form_state->getValue([
        'fields',
        $attribute->getName(),
      ]);
      if (isset($field_attribute_name)) {
        $info[$attribute->getName()] = $form_state->getValue([
          'fields',
          $attribute->getName(),
        ]);
      }
    }
    $this->sendinblueManager->subscribeUser($email, $info, $list_ids);

    // Store db.
    $data = $this->sendinblueManager->getSubscriberByEmail($email);
    if ($data == FALSE) {
      $data = [
        'email' => $email,
        'info' => serialize($info),
        'code' => uniqid('', TRUE),
        'is_active' => 1,
      ];
      $this->sendinblueManager->addSubscriberTable($data);
    }
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $signup = $this->entityTypeManager->getStorage(SendinblueManager::SENDINBLUE_SIGNUP_ENTITY)
      ->load($this->signupIp);
    $settings = (!$signup->settings->first()) ? [] : $signup->settings->first()
      ->getValue();

    // Send confirm email.
    $email = $form_state->getValue(['fields', 'EMAIL']);
    $email_confirmation = $settings['subscription']['settings']['email_confirmation'];

    if ($email_confirmation) {
      $template_id = $settings['subscription']['settings']['template'];
      $this->sendinblueManager->sendEmail('confirm', $email, $template_id);
    }

    $this->messenger->addMessage($settings['subscription']['messages']['success']);

    if ($settings['subscription']['settings']['redirect_url'] != '') {
      $form_state->setRedirectUrl(Url::fromUri('internal:/' . $settings['subscription']['settings']['redirect_url']));
    }
  }

}
