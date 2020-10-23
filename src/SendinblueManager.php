<?php

namespace Drupal\sendinblue;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\sendinblue\Form\ConfigurationSendinblueForm;
use Drupal\sendinblue\Form\LogoutForm;
use Drupal\sendinblue\Form\RegisteringUserForm;
use Drupal\sendinblue\Form\TransactionnalEmailForm;
use Drupal\sendinblue\Tools\Api\SendInBlueApiInterface;
use Drupal\sendinblue\Tools\Api\SendinblueApiV2;
use Drupal\sendinblue\Tools\Api\SendinblueApiV3;

/**
 * Basic manager of module.
 */
class SendinblueManager {

  use StringTranslationTrait;

  const SENDINBLUE_SIGNUP_ENTITY = 'sendinblue_signup_form';
  const SENDINBLUE_SIGNUP_BLOCK = 1;
  const SENDINBLUE_SIGNUP_PAGE = 2;
  const SENDINBLUE_SIGNUP_BOTH = 3;

  const SENDINBLUE_API_VERSION_V2 = 'v2';
  const SENDINBLUE_API_VERSION_V3 = 'v3';

  /**
   * Variable name of Sendinblue URL.
   */
  const SIB_URL = 'https://my.sendinblue.com';

  /**
   * Variable name of Sendinblue access key.
   */
  const CONFIG_SETTINGS = 'sendinblue.config_global.settings';

  /**
   * Variable name of Sendinblue access key.
   */
  const CONFIG_SETTINGS_REGISTERING_USER = 'sendinblue.config_registering_user.settings';

  /**
   * Variable name of Sendinblue access key.
   */
  const CONFIG_SETTINGS_SEND_EMAIL = 'sendinblue.config_send_email.settings';

  /**
   * Variable name of Sendinblue access key.
   */
  const ACCESS_KEY = 'sendinblue_access_key';

  /**
   * Variable name of Sendinblue account email.
   */
  const ACCOUNT_EMAIL = 'sendinblue_account_email';

  /**
   * Variable name of Sendinblue account user name.
   */
  const ACCOUNT_USERNAME = 'sendinblue_account_username';

  /**
   * Variable name of Sendinblue account data.
   */
  const ACCOUNT_DATA = 'sendinblue_account_data';

  /**
   * Variable name of access_token.
   */
  const ACCESS_TOKEN = 'sendinblue_access_token';

  /**
   * Variable name of smtp details.
   */
  const SMTP_DETAILS = 'sendinblue_smtp_details';

  /**
   * ConfigFactoryInterface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $configFactory;
  /**
   * FormBuilderInterface.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  private $formBuilder;
  /**
   * Renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  private $renderer;
  /**
   * SendinblueMailin.
   *
   * @var \Drupal\sendinblue\Tools\Api\SendInBlueApiInterface
   */
  private $sendinblueMailin;
  /**
   * Connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;
  /**
   * MailManagerInterface.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  private $mailManager;
  /**
   * AccountProxyInterface.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  private $accountProxy;
  /**
   * SendinblueApi V2.
   *
   * @var \Drupal\sendinblue\Tools\Api\SendinblueApiV2
   */
  private $sendinblueApiV2;
  /**
   * SendinblueApi V3.
   *
   * @var \Drupal\sendinblue\Tools\Api\SendinblueApiV3
   */
  private $sendinblueApiV3;

  /**
   * EntityModerationForm constructor.
   *
   * @param \Drupal\sendinblue\Tools\Api\SendinblueApiV2 $sendinblueApiV2
   *   SendinblueMailin.
   * @param \Drupal\sendinblue\Tools\Api\SendinblueApiV3 $sendinblueApiV3
   *   SendinblueMailin.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   ConfigFactoryInterface.
   * @param \Drupal\Core\Database\Connection $connection
   *   Connection.
   * @param \Drupal\Core\Form\FormBuilderInterface $formBuilder
   *   FormBuilderInterface.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   Renderer.
   * @param \Drupal\Core\Mail\MailManagerInterface $mailManager
   *   MailManagerInterface.
   * @param \Drupal\Core\Session\AccountProxyInterface $accountProxy
   *   AccountProxyInterface.
   */
  public function __construct(
    SendinblueApiV2 $sendinblueApiV2,
    SendinblueApiV3 $sendinblueApiV3,
    ConfigFactoryInterface $configFactory,
    Connection $connection,
    FormBuilderInterface $formBuilder,
    Renderer $renderer,
    MailManagerInterface $mailManager,
    AccountProxyInterface $accountProxy
  ) {
    $this->configFactory = $configFactory;
    $this->connection = $connection;
    $this->formBuilder = $formBuilder;
    $this->renderer = $renderer;
    $this->mailManager = $mailManager;
    $this->accountProxy = $accountProxy;
    $this->sendinblueApiV2 = $sendinblueApiV2;
    $this->sendinblueApiV3 = $sendinblueApiV3;

    $this->updateSendinblueMailin($this->getAccessKey());
  }

  /**
   * Get the access key store in configuration.
   *
   * @return string
   *   The SiB access key
   */
  public function getAccessKey() {
    return $this->configFactory->get(self::CONFIG_SETTINGS)->get(self::ACCESS_KEY);
  }

  /**
   * Get the access key store in configuration.
   *
   * @param string $accessKey
   *   The SiB access key.
   *
   * @return string
   *   The SiB API version (V2 or V3)
   */
  public function getApiVersion($accessKey) {
    if (strlen($accessKey) > 20 && strpos($accessKey, 'xkeysib') !== FALSE) {
      return self::SENDINBLUE_API_VERSION_V3;
    }
    return self::SENDINBLUE_API_VERSION_V2;
  }

  /**
   * Get the correct Class in function of API version.
   *
   * @return \Drupal\sendinblue\Tools\Api\SendInBlueApiInterface
   *   SendInBlueApiInterface (V2 or V3)
   */
  public function getSendinblueMailin(): SendInBlueApiInterface {
    return $this->sendinblueMailin;
  }

  /**
   * Change Class Class in function of API version.
   *
   * @param string $accessKey
   *   The SiB access key.
   *
   * @return \Drupal\sendinblue\Tools\Api\SendInBlueApiInterface
   *   SendInBlueApiInterface (V2 or V3)
   */
  public function updateSendinblueMailin($accessKey) {
    if ($this->getApiVersion($accessKey) === self::SENDINBLUE_API_VERSION_V3) {
      $this->sendinblueMailin = $this->sendinblueApiV3;
    }
    else {
      $this->sendinblueMailin = $this->sendinblueApiV2;
    }

    $this->sendinblueMailin->setApiKey($accessKey);

    return $this->sendinblueMailin;
  }

  /**
   * Get the account email store in configuration.
   *
   * @return string
   *   The SiB account email
   */
  public function getAccountEmail() {
    return $this->configFactory->get(self::CONFIG_SETTINGS)->get(self::ACCOUNT_EMAIL);
  }

  /**
   * Get the account username store in configuration.
   *
   * @return string
   *   The SiB account username
   */
  public function getAccountUsername() {
    return $this->configFactory->get(self::CONFIG_SETTINGS)->get(self::ACCOUNT_USERNAME);
  }

  /**
   * Get the data account store in configuration.
   *
   * @return string
   *   The SiB account store
   */
  public function getAccountData() {
    return $this->configFactory->get(self::CONFIG_SETTINGS)->get(self::ACCOUNT_DATA);
  }

  /**
   * Get the data account store in configuration.
   *
   * @return string
   *   The SiB account store
   */
  public function getSmtpDetails() {
    return $this->configFactory->get(self::CONFIG_SETTINGS_SEND_EMAIL)->get(self::SMTP_DETAILS);
  }

  /**
   * Get the access token store in configuration.
   *
   * @return string
   *   The SiB access token
   */
  public function getAccessKeyToken() {
    return $this->configFactory->get(self::CONFIG_SETTINGS)->get(self::ACCESS_TOKEN);
  }

  /**
   * Generate Home layout of Log out.
   *
   * @return array
   *   A html of home page when log out.
   */
  public function generateHomeLogout() {
    $form = $this->formBuilder->getForm(ConfigurationSendinblueForm::class);

    return ['#formulaire_api_key' => $this->renderer->render($form)];
  }

  /**
   * Generate Home layout of Log out.
   *
   * @return array
   *   A html of home page when login.
   */
  public function generateHomeLogin() {

    // Calculate total count of subscribers.
    $lists = $this->sendinblueMailin->getLists();
    $totalSubscribers = 0;
    $listIds = [];

    if ($lists->getCount() > 0) {
      $listData = $lists->getLists();
      foreach ($listData as $list) {
        $listIds[] = $list['id'];
      }

      $totalSubscribers = $this->sendinblueMailin->countUserlists($listIds);
    }

    // Get account details.
    $accountEmail = $this->getAccountEmail();
    $accountUsername = $this->getAccountUsername();
    $account_data = Json::decode($this->getAccountData());

    $sendinblue_logout_form = $this->formBuilder->getForm(LogoutForm::class);
    $sendinblue_send_email_form = $this->formBuilder->getForm(TransactionnalEmailForm::class);
    $sendinblue_user_register_form = $this->formBuilder->getForm(RegisteringUserForm::class);

    return [
      '#account_username' => [
        '#plain_text' => $accountUsername,
      ],
      '#account_email' => [
        '#plain_text' => $accountEmail,
      ],
      '#total_subscribers' => [
        '#plain_text' => $totalSubscribers,
      ],
      '#account_data' => $account_data,
      '#api_version' => $this->getApiVersion($this->getAccessKey()),
      '#sendinblue_logout_form' => $this->renderer->render($sendinblue_logout_form),
      '#sendinblue_send_email_form' => $this->renderer->render($sendinblue_send_email_form),
      '#sendinblue_user_register_form' => $this->renderer->render($sendinblue_user_register_form),
    ];

  }

  /**
   * Generate List page when log in.
   *
   * @return string
   *   A html of list page.
   */
  public function generateListLogin() {
    $access_token = $this->updateAccessToken();
    return sprintf(self::SIB_URL . '/lists/index/access_token/%s', $access_token);
  }

  /**
   * Generate Campaign page when log in.
   *
   * @return string
   *   A html of campaign.
   */
  public function generateCampaignLogin() {
    $access_token = $this->updateAccessToken();
    return sprintf(self::SIB_URL . '/camp/listing/access_token/%s', $access_token);

  }

  /**
   * Generate Statistic page when log in.
   *
   * @return string
   *   A html of statistic page.
   */
  public function generateStatisticLogin() {
    $access_token = $this->updateAccessToken();
    return sprintf(self::SIB_URL . '/camp/message/access_token/%s', $access_token);
  }

  /**
   * Check if current state is logged in.
   *
   * @return bool
   *   A status of login of user.
   */
  public function isLoggedInState() {
    $access_key = $this->getAccessKey();
    if (!empty($access_key)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Update access token.
   *
   * @return string
   *   An access token information.
   */
  public function updateAccessToken() {
    $config = $this->configFactory->getEditable('sendinblue.settings');

    // Get new access_token.
    $access_token = $this->sendinblueMailin->getAccessTokens();

    $config->set(self::ACCESS_TOKEN, $access_token);
    return $access_token;
  }

  /**
   * Get email template by type.
   *
   * @param string $type
   *   A type of email.
   *
   * @return array
   *   An array of email content.
   */
  public function getEmailTemplate($type = 'test') {
    $file = 'temp';
    $file_path = drupal_get_path('module', 'sendinblue') . '/asset/email-templates/' . $type . '/';
    // Get html content.
    $html_content = file_get_contents($file_path . $file . '.html');
    // Get text content.
    $text_content = file_get_contents($file_path . $file . '.txt');
    $templates = [
      'html_content' => $html_content,
      'text_content' => $text_content,
    ];
    return $templates;
  }

  /**
   * Send mail.
   *
   * @param string $type
   *   A type of email.
   * @param string $to_email
   *   A recipe address.
   * @param string $template_id
   *   A template identification.
   */
  public function sendEmail($type, $to_email, $template_id = '-1') {
    $subjects = [
      'confirm' => $this->t('Subscription confirmed'),
      'test' => $this->t('[SendinBlue SMTP] test email'),
    ];
    $account_email = $this->getAccountEmail();
    $account_username = $this->getAccountUsername();

    // Set subject info.
    $subject = $subjects[$type] ?? '[SendinBlue]';
    $sender_email = !empty($account_email) ? $account_email : $this->t('no-reply@sendinblue.com');
    $sender_name = !empty($account_username) ? $account_username : $this->t('SendinBlue');

    // Get template html and text.
    $template_contents = $this->getEmailTemplate($type);
    $html_content = $template_contents['html_content'];
    $text_content = $template_contents['text_content'];

    if ($type === "confirm" && $template_id !== '-1') {
      $template = $this->sendinblueMailin->getTemplate($template_id);

      if ($template !== NULL) {
        $html_content = $template->getHtmlContent();
        $subject = $template->getSubject();

        if (($template->getFromName() !== '[DEFAULT_FROM_NAME]') && ($template->getFromEmail() !== '[DEFAULT_FROM_EMAIL]')) {
          $sender_name = $template->getFromName();
          $sender_email = $template->getFromEmail();
        }
      }
    }

    // Send mail.
    $replyTo = ['email' => $sender_email, 'name' => $sender_name];
    $from = ['email' => $sender_email, 'name' => $sender_name];
    $to = ['email' => $to_email];

    $base_url = $this->getBaseUrl();
    $site_domain = str_replace(['https://', 'http://'], '', $base_url);
    $html_content = str_replace(['{title}', '{site_domain}'], [$subject, $site_domain], $html_content);
    $text_content = str_replace('{site_domain}', $base_url, $text_content);

    $this->sendinblueMailin->sendEmail($to, $subject, $html_content, $text_content, $from, $replyTo);
  }

  /**
   * Get Base URL.
   *
   * @return string
   *   A base url of the site.
   */
  public function getBaseUrl() {
    global $base_url;
    return $base_url;
  }

  /**
   * Get Attribute lists.
   *
   * @return \Drupal\sendinblue\Tools\Model\GetAttributesAttributes[]
   *   An array of attributes.
   */
  public function getAttributeLists() {
    $sibAttributes = $this->sendinblueMailin->getAttributes();

    if (!empty($sibAttributes->getAttributes())) {
      $attributes = [];

      foreach ($sibAttributes->getAttributes() as $attribute) {
        if ($attribute->getCategory() === 'normal') {
          $attributes[] = $attribute;
        }
      }

      return $attributes;
    }
    return [];
  }

  /**
   * Get template list.
   *
   * @return \Drupal\sendinblue\Tools\Model\GetSmtpTemplates
   *   An array of template.
   */
  public function getTemplateList() {
    return $this->sendinblueMailin->getTemplates();
  }

  /**
   * Get lists.
   *
   * @return array
   *   An array of lists.
   */
  public function getLists() {
    $lists = $this->sendinblueMailin->getLists();

    if ($lists !== NULL) {
      return $lists->getLists();
    }

    return [];
  }

  /**
   * Get list name form id.
   *
   * @param int $list_id
   *   A list id.
   *
   * @return string
   *   A list name.
   */
  public function getListNameById($list_id) {
    $list = $this->sendinblueMailin->getList($list_id);

    return $list !== NULL ? $list->getName() : NULL;
  }

  /**
   * Check the email address of subscriber.
   *
   * @param string $email
   *   An email address.
   * @param string $list_id
   *   A list id.
   *
   * @return array
   *   A response information.
   */
  public function validationEmail($email, $list_id) {
    $contactInfo = $this->sendinblueMailin->getUser($email);
    if ($contactInfo === NULL) {
      $ret = [
        'code' => 'success',
        'listid' => [],
      ];
      return $ret;
    }

    $listId = $contactInfo->getListIds();

    if ($contactInfo->isEmailBlacklisted()) {
      $ret = [
        'code' => 'update',
        'listid' => $listId,
      ];
    }
    else {
      if (!in_array($list_id, $listId)) {
        $ret = [
          'code' => 'success',
          'listid' => $listId,
        ];
      }
      else {
        $ret = [
          'code' => 'already_exist',
          'listid' => $listId,
        ];
      }
    }

    return $ret;
  }

  /**
   * Subscriber user.
   *
   * @param string $email
   *   An email address of subscriber.
   * @param array $info
   *   A data of subscriber.
   * @param array $listids
   *   An array of list id.
   */
  public function subscribeUser($email, array $info = [], array $listids = []) {
    $this->sendinblueMailin->createUpdateUser($email, $info, [], $listids, NULL);
  }

  /**
   * Get subscriber data by email on drupal table.
   *
   * @param string $email
   *   An email address.
   *
   * @return string
   *   A details of subscriber.
   */
  public function getSubscriberByEmail($email) {
    $record = $this->connection->select('sendinblue_contact', 'c')
      ->fields('c', ['email'])
      ->condition('c.email', $email)
      ->execute()->fetchAssoc();

    return $record;
  }

  /**
   * Add subscriber on drupal table.
   *
   * @param array $data
   *   A data to add in table.
   */
  public function addSubscriberTable(array $data = []) {
    $this->connection->insert('sendinblue_contact')->fields(
      [
        'email' => $data['email'],
        'info' => $data['info'],
        'code' => $data['code'],
        'is_active' => $data['is_active'],
      ]
    )->execute();
  }

  /**
   * Update smtp details.
   *
   * @return string|bool
   *   A access token if exist, else 0.
   */
  public function updateSmtpDetails() {
    $smtpDetails = $this->sendinblueMailin->getSmtpDetails();

    $config = $this->configFactory->getEditable(self::CONFIG_SETTINGS_SEND_EMAIL);
    $drupalEmailconfig = $this->configFactory->getEditable('system.mail');

    if ($smtpDetails->isEnabled()) {
      // Set SendinBlue SMTP on ON.
      $config->set('sendinblue_on', 1)->save();
      $config->set(self::SMTP_DETAILS, Json::encode($smtpDetails))->save();
      // Set DRUPAL SMTP on ON with SiB.
      $drupalEmailconfig->set('interface.default', 'sendinblue_mail')->save();

      return $smtpDetails;
    }

    // Set SendinBlue SMTP on OFF.
    $config->set('sendinblue_on', 0)->save();
    $config->set(self::SMTP_DETAILS, NULL)->save();
    // Set DRUPAL SMTP on OFF with SiB, reset with php_mail.
    $drupalEmailconfig->set('interface.default', 'php_mail')->save();

    return NULL;
  }

}
