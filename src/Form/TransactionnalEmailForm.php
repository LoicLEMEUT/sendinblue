<?php

namespace Drupal\sendinblue\Form;

use Drupal\Component\Utility\EmailValidator;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\sendinblue\SendinblueManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Form Transactionnal emails SMTP.
 */
class TransactionnalEmailForm extends ConfigFormBase {
  /**
   * EmailValidatorInterface.
   *
   * @var \Drupal\Component\Utility\EmailValidator
   */
  private $emailValidator;
  /**
   * SendinblueManager.
   *
   * @var \Drupal\sendinblue\SendinblueManager
   */
  private $sendinblueManager;

  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Component\Utility\EmailValidator $emailValidator
   *   EmailValidatorInterface.
   * @param \Drupal\sendinblue\SendinblueManager $sendinblueManager
   *   SendinblueManager.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    EmailValidator $emailValidator,
    SendinblueManager $sendinblueManager
  ) {
    parent::__construct($config_factory);
    $this->emailValidator = $emailValidator;
    $this->sendinblueManager = $sendinblueManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
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
    return SendinblueManager::CONFIG_SETTINGS_SEND_EMAIL;
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $smtpDetails = $this->configFactory()
      ->get(SendinblueManager::CONFIG_SETTINGS_SEND_EMAIL)
      ->get(SendinblueManager::SMTP_DETAILS);

    $smtpAvailable = ($smtpDetails !== NULL);

    if ($smtpAvailable === FALSE) {
      $form['sendinblue_alert'] = [
        '#type' => 'markup',
        '#prefix' => '<div id="sendinblue_alert_area" style="padding: 10px;background-color: #fef5f1;color: #8c2e0b;border-color: #ed541d;border-width: 1px;border-style: solid;">',
        '#markup' => $this->t('Current you can not use SendinBlue SMTP. Please confirm at <a href="@smtp-sendinblue" target="_blank">Here</a>', ['@smtp-sendinblue' => 'https://mysmtp.sendinblue.com/?utm_source=drupal_plugin&utm_medium=plugin&utm_campaign=module_link']),
        '#suffix' => '</div>',
        '#tree' => TRUE,
      ];
    }

    $form['sendinblue_on'] = [
      '#type' => 'radios',
      '#title' => $this->t('Send emails through SendinBlue SMTP'),
      '#default_value' => $this->configFactory()
        ->get(SendinblueManager::CONFIG_SETTINGS_SEND_EMAIL)
        ->get('sendinblue_on'),
      '#description' => $this->t('Choose "Yes" if you want to use SendinBlue SMTP to send transactional emails.'),
      '#options' => [1 => $this->t('Yes'), 0 => $this->t('No')],
      '#disabled' => ($smtpAvailable === TRUE) ? FALSE : TRUE,
    ];

    $form['sendinblue_to_email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter email to send a test'),
      '#description' => $this->t('Select here the email address you want to send a test email to.'),
      '#disabled' => ($smtpAvailable === TRUE) ? FALSE : TRUE,
      '#states' => [
        // Hide unless needed.
        'visible' => [
          ':input[name="sendinblue_on"]' => ['value' => 1],
        ],
        'required' => [
          ':input[name="sendinblue_on"]' => ['value' => 1],
        ],
      ],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Settings'),
      '#disabled' => ($smtpAvailable === TRUE) ? FALSE : TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $sendEmail = $form_state->getValue('sendinblue_to_email');

    if (!empty($sendEmail)) {
      if (!$this->emailValidator->isValid($sendEmail)) {
        $form_state->setErrorByName('sendinblue_to_email', $this->t('The email address is invalid.'));
        return;
      }
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
    $sendEmail = $form_state->getValue('sendinblue_to_email');
    $sendinblueOn = $form_state->getValue('sendinblue_on');

    $config = $this->configFactory()->getEditable(SendinblueManager::CONFIG_SETTINGS_SEND_EMAIL);
    $config->set('sendinblue_on', $sendinblueOn)->save();

    if ((bool) $sendinblueOn) {
      $this->sendinblueManager->sendEmail('test', $sendEmail);

      $this->sendinblueManager->updateSmtpDetails();
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return [SendinblueManager::CONFIG_SETTINGS_SEND_EMAIL];
  }

}
