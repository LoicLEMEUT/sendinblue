<?php

namespace Drupal\sendinblue\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Menu\MenuLinkManagerInterface;
use Drupal\sendinblue\SendinblueManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Parameter Form to login SendinBlue.
 */
class ConfigurationSendinblueForm extends ConfigFormBase {
  /**
   * SendinblueManager.
   *
   * @var \Drupal\sendinblue\SendinblueManager
   */
  private $sendinblueManager;
  /**
   * CacheBackendInterface.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  private $cacheBackend;
  /**
   * MenuLinkManagerInterface.
   *
   * @var \Drupal\Core\Menu\MenuLinkManagerInterface
   */
  private $menuLinkManager;

  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\sendinblue\SendinblueManager $sendinblueManager
   *   SendinblueManager.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   CacheBackendInterface.
   * @param \Drupal\Core\Menu\MenuLinkManagerInterface $menuLinkManager
   *   MenuLinkManagerInterface.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    SendinblueManager $sendinblueManager,
    CacheBackendInterface $cacheBackend,
    MenuLinkManagerInterface $menuLinkManager
  ) {
    parent::__construct($config_factory);
    $this->sendinblueManager = $sendinblueManager;
    $this->cacheBackend = $cacheBackend;
    $this->menuLinkManager = $menuLinkManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('sendinblue.manager'),
      $container->get('cache.menu'),
      $container->get('plugin.manager.menu.link')
    );
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'sendinblue_form_login';
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
    $form['access_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => $this->t('API Key'),
      ],
      '#size' => 50,
      '#maxlenght' => 100,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Login'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $accessKey = $form_state->getValue('access_key');

    if (empty($accessKey)) {
      $form_state->setErrorByName('access_key', $this->t('API key is invalid'));
    }

    $sendinblueMailin = $this->sendinblueManager->updateSendinblueMailin($accessKey);
    $sibAccount = $sendinblueMailin->getAccount();

    if ($sibAccount->getEmail() === NULL) {
      $form_state->setErrorByName('sib_account', $this->t('Unable to get account info on Sib'));
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
    $accessKey = $form_state->getValue('access_key');
    $config = $this->configFactory->getEditable(SendinblueManager::CONFIG_SETTINGS);
    $configRegistering = $this->configFactory()->getEditable(SendinblueManager::CONFIG_SETTINGS_REGISTERING_USER);

    $sendinblueMailin = $this->sendinblueManager->updateSendinblueMailin($accessKey);
    $sibAccount = $sendinblueMailin->getAccount();

    if ($sibAccount->getEmail() !== NULL) {
      $account_user_name = $sibAccount->getFirstName() . ' ' . $sibAccount->getLastName();
      $config->set(SendinblueManager::ACCESS_KEY, $accessKey)->save();
      $config->set(SendinblueManager::ACCOUNT_EMAIL, $sibAccount->getEmail())->save();
      $config->set(SendinblueManager::ACCOUNT_USERNAME, $account_user_name)->save();
      $config->set(SendinblueManager::ACCOUNT_DATA, Json::encode($sibAccount))->save();
      $configRegistering->set('sendinblue_put_registered_user', '0')->save();

      $this->sendinblueManager->updateSmtpDetails();
      $sendinblueMailin->partnerDrupal();
    }

    // Clear cache for menu tasks.
    $this->cacheBackend->invalidateAll();
    $this->menuLinkManager->rebuild();

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
    return ['sendinblue_form_login.settings'];
  }

}
