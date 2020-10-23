<?php

namespace Drupal\sendinblue\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\sendinblue\SendinblueManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Form "Register user in Sib list during registration" on login SiB page.
 */
class RegisteringUserForm extends ConfigFormBase {

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
   * @param \Drupal\sendinblue\SendinblueManager $sendinblueManager
   *   SendinblueManager.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    SendinblueManager $sendinblueManager
  ) {
    parent::__construct($config_factory);
    $this->sendinblueManager = $sendinblueManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
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
    return SendinblueManager::CONFIG_SETTINGS_REGISTERING_USER;
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
    $sendinblue_lists = $this->sendinblueManager->getLists();
    $options = [];
    foreach ($sendinblue_lists as $mc_list) {
      $options[$mc_list['id']] = $mc_list['name'];
    }

    $form['sendinblue_put_registered_user'] = [
      '#tree' => TRUE,
    ];

    $form['sendinblue_put_registered_user']['active'] = [
      '#type' => 'radios',
      '#title' => $this->t('Save SendInBlue User ?'),
      '#default_value' => $this->configFactory()
        ->get(SendinblueManager::CONFIG_SETTINGS_REGISTERING_USER)
        ->get('sendinblue_put_registered_user')['active'],
      '#description' => $this->t('Register the user in SendInBlue list during registration'),
      '#options' => [1 => $this->t('Yes'), 0 => $this->t('No')],
    ];

    $form['sendinblue_put_registered_user']['list'] = [
      '#type' => 'select',
      '#title' => $this->t('List where subscribers are saved'),
      '#options' => $options,
      '#default_value' => $this->configFactory()
        ->get(SendinblueManager::CONFIG_SETTINGS_REGISTERING_USER)
        ->get('sendinblue_put_registered_user')['list'],
      '#description' => $this->t('Select the list where you want to add your new subscribers'),
      '#states' => [
        // Hide unless needed.
        'visible' => [
          ':input[name="sendinblue_put_registered_user[active]"]' => ['value' => 1],
        ],
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Settings'),
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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $sendinblue_put_registered_user = $form_state->getValue('sendinblue_put_registered_user');

    $config = $this->configFactory()->getEditable(SendinblueManager::CONFIG_SETTINGS_REGISTERING_USER);
    $config->set('sendinblue_put_registered_user', $sendinblue_put_registered_user)->save();

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
    return [SendinblueManager::CONFIG_SETTINGS_REGISTERING_USER];
  }

}
