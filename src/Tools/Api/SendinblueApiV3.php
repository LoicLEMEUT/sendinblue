<?php

namespace Drupal\sendinblue\Tools\Api;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\sendinblue\Tools\Http\SendinblueHttpClient;
use Drupal\sendinblue\Tools\Model\CreateSmtpEmail;
use Drupal\sendinblue\Tools\Model\GetAccount;
use Drupal\sendinblue\Tools\Model\GetAttributes;
use Drupal\sendinblue\Tools\Model\GetExtendedContactDetails;
use Drupal\sendinblue\Tools\Model\GetExtendedList;
use Drupal\sendinblue\Tools\Model\GetLists;
use Drupal\sendinblue\Tools\Model\GetSmtpDetails;
use Drupal\sendinblue\Tools\Model\GetSmtpTemplateOverview;
use Drupal\sendinblue\Tools\Model\GetSmtpTemplates;
use SendinBlue\Client\Api\AccountApi;
use SendinBlue\Client\Api\AttributesApi;
use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\Api\EmailCampaignsApi;
use SendinBlue\Client\Api\ListsApi;
use SendinBlue\Client\Api\ResellerApi;
use SendinBlue\Client\Api\SendersApi;
use SendinBlue\Client\Api\SMTPApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\CreateContact;
use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Model\SendSmtpEmailAttachment;
use SendinBlue\Client\Model\SendSmtpEmailBcc;
use SendinBlue\Client\Model\SendSmtpEmailCc;
use SendinBlue\Client\Model\SendSmtpEmailReplyTo;
use SendinBlue\Client\Model\SendSmtpEmailSender;
use SendinBlue\Client\Model\SendSmtpEmailTo;
use SendinBlue\Client\Model\UpdateContact;

/**
 * Sendinblue REST client.
 */
class SendinblueApiV3 implements SendInBlueApiInterface {

  /**
   * Sib ApiKey.
   *
   * @var string
   */
  public $apiKey;

  /**
   * Logger Service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   *   LoggerChannelFactory.
   */
  protected $loggerFactory;

  /**
   * Get the AccountApi from SendInBlue.
   *
   * @var \SendinBlue\Client\Api\AccountApi
   *   AccountApi.
   */
  private $sibAccountApi;
  /**
   * Get the EmailCampaignsApi from SendInBlue.
   *
   * @var \SendinBlue\Client\Api\EmailCampaignsApi
   *   EmailCampaignsApi.
   */
  private $sibEmailCampaignsApi;
  /**
   * Get the ListsApi from SendInBlue.
   *
   * @var \SendinBlue\Client\Api\ListsApi
   *   ListsApi.
   */
  private $sibListsApi;
  /**
   * Get the ContactsApi from SendInBlue.
   *
   * @var \SendinBlue\Client\Api\ContactsApi
   *   ContactsApi.
   */
  private $sibContactsApi;
  /**
   * Get the AttributesApi from SendInBlue.
   *
   * @var \SendinBlue\Client\Api\AttributesApi
   *   AttributesApi.
   */
  private $sibAttributesApi;
  /**
   * Get the SendersApi from SendInBlue.
   *
   * @var \SendinBlue\Client\Api\SendersApi
   *   SendersApi.
   */
  private $sibSendersApi;
  /**
   * Get the SMTPApi from SendInBlue.
   *
   * @var \SendinBlue\Client\Api\SMTPApi
   *   SMTPApi
   */
  private $sibSMTPApi;
  /**
   * Get the Configuration from SendInBlue.
   *
   * @var \SendinBlue\Client\Configuration
   *   Configuration.
   */
  private $sibClientConfiguration;
  /**
   * Get the SendinblueHttpClient for HTTP cURL.
   *
   * @var \Drupal\sendinblue\Tools\Http\SendinblueHttpClient
   *   SendinblueHttpClient.
   */
  private $sIBHttpClient;
  /**
   * Get the ResellerApi from SendInBlue.
   *
   * @var \SendinBlue\Client\Api\ResellerApi
   *   ResellerApi.
   */
  private $sibResellerApi;

  /**
   * SendinblueMailin constructor.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   LoggerChannelFactory.
   * @param \Drupal\sendinblue\Tools\Http\SendinblueHttpClient $sIBHttpClient
   *   SendinblueHttpClient.
   */
  public function __construct(
    LoggerChannelFactoryInterface $logger_factory,
    SendinblueHttpClient $sIBHttpClient
  ) {
    $sibClientConfiguration = Configuration::getDefaultConfiguration();
    $this->sibClientConfiguration = $sibClientConfiguration->setApiKey("api-key", $this->apiKey);

    $this->sibAccountApi = new AccountApi(NULL, $this->sibClientConfiguration);
    $this->sibEmailCampaignsApi = new EmailCampaignsApi(NULL, $this->sibClientConfiguration);
    $this->sibListsApi = new ListsApi(NULL, $this->sibClientConfiguration);
    $this->sibContactsApi = new ContactsApi(NULL, $this->sibClientConfiguration);
    $this->sibAttributesApi = new AttributesApi(NULL, $this->sibClientConfiguration);
    $this->sibSendersApi = new SendersApi(NULL, $this->sibClientConfiguration);
    $this->sibSMTPApi = new SMTPApi(NULL, $this->sibClientConfiguration);
    $this->sibResellerApi = new ResellerApi(NULL, $this->sibClientConfiguration);

    $this->loggerFactory = $logger_factory;
    $this->sIBHttpClient = $sIBHttpClient;
  }

  /**
   * {@inheritdoc}
   */
  public function setApiKey($apiKey) {
    $this->apiKey = $apiKey;
    $this->sibClientConfiguration->setApiKey("api-key", $this->apiKey);
  }

  /**
   * Get SendInBlueV3 Configuration.
   *
   * @return \SendinBlue\Client\Configuration
   *   SendInBlueV3 Configuration
   */
  public function getSibClientConfiguration() {
    return $this->sibClientConfiguration;
  }

  /**
   * {@inheritdoc}
   */
  public function getAccount() {
    $account = $this->sibAccountApi->getAccount();
    return new GetAccount(Json::decode($account));
  }

  /**
   * {@inheritdoc}
   */
  public function getTemplates() {
    return new GetSmtpTemplates(Json::decode($this->sibSMTPApi->getSmtpTemplates()));;
  }

  /**
   * {@inheritdoc}
   */
  public function getTemplate($id) {
    return new GetSmtpTemplateOverview(Json::decode($this->sibSMTPApi->getSmtpTemplate($id)));
  }

  /**
   * {@inheritdoc}
   */
  public function getLists() {
    return new GetLists($this->sibListsApi->getLists()->getLists());
  }

  /**
   * {@inheritdoc}
   */
  public function getList($id) {
    $list = $this->sibListsApi->getList($id);

    return new GetExtendedList(Json::decode($list));
  }

  /**
   * {@inheritdoc}
   */
  public function sendEmail(
    array $to,
    string $subject,
    string $html,
    string $text,
    array $from = [],
    array $replyto = [],
    array $cc = [],
    array $bcc = [],
    array $attachment = [],
    array $headers = []
  ) {
    $to += ['email' => NULL, 'name' => NULL];
    $sibTo = new SendSmtpEmailTo(['email' => $to['email'], 'name' => $to['name']]);

    $emailData = [
      'to' => [$sibTo],
      'htmlContent' => $html,
      'textContent' => $text,
      'subject' => $subject,
    ];

    if (!empty($from)) {
      $from += ['email' => NULL, 'name' => NULL];
      $emailData['sender'] = new SendSmtpEmailSender(['email' => $from['email'], 'name' => $from['name']]);
    }

    if (!empty($bcc)) {
      $bcc += ['email' => NULL, 'name' => NULL];
      $emailData['bcc'] = [new SendSmtpEmailBcc(['email' => $bcc['email'], 'name' => $bcc['name']])];
    }

    if (!empty($cc)) {
      $cc += ['email' => NULL, 'name' => NULL];
      $emailData['cc'] = [new SendSmtpEmailCc(['email' => $cc['email'], 'name' => $cc['name']])];
    }

    if (!empty($replyto)) {
      $replyto += ['email' => NULL, 'name' => NULL];
      $emailData['replyTo'] = new SendSmtpEmailReplyTo(['email' => $replyto['email'], 'name' => $replyto['name']]);
    }

    if (!empty($headers)) {
      $emailData['headers'] = $headers;
    }

    if (!empty($attachment)) {
      $attachments = [];
      foreach ($attachment as $item) {
        $attachments[] = new SendSmtpEmailAttachment($item);
      }

      $emailData['attachment'] = $attachments;
    }

    $message = $this->sibSMTPApi->sendTransacEmail(new SendSmtpEmail($emailData));

    if ($message->valid()) {
      return new CreateSmtpEmail($message->getMessageId());
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getUser($email) {
    try {
      $contactInfo = $this->sibContactsApi->getContactInfo($email);

      return new GetExtendedContactDetails(Json::decode($contactInfo));
    }
    catch (\Throwable $e) {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function createUpdateUser($email, array $attributes = [], array $blacklisted = [], $listid = '', $listid_unlink = '') {
    $isContactExist = $this->getUser($email) !== NULL;

    if ($isContactExist) {
      $updateContact = new UpdateContact(
        [
          'attributes' => (object) $attributes,
          'emailBlacklisted' => (bool) $blacklisted,
          'listIds' => [(int) $listid[0]],
          'unlinkListIds' => $listid_unlink,
        ]
      );

      $this->sibContactsApi->updateContact($email, $updateContact);
    }
    else {
      $updateContact = new CreateContact(
        [
          'email' => $email,
          'attributes' => (object) $attributes,
          'emailBlacklisted' => (bool) $blacklisted,
          'listIds' => [(int) $listid[0]],
          'unlinkListIds' => $listid_unlink,
        ]
          );

      $this->sibContactsApi->createContact($updateContact);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getAttributes() {
    return new GetAttributes(Json::decode($this->sibAttributesApi->getAttributes()));
  }

  /**
   * {@inheritdoc}
   */
  public function getAccessTokens() {
    return $this->sibAccountApi->getConfig()->getAccessToken();
  }

  /**
   * {@inheritdoc}
   */
  public function getSmtpDetails() {
    $smtpDetails = $this->sibAccountApi->getAccount()->getRelay();

    return new GetSmtpDetails(
      $smtpDetails->getData()->getUserName(),
      $smtpDetails->getData()->getRelay(),
      $smtpDetails->getData()->getPort(),
      (bool) $smtpDetails->getEnabled()
    );
  }

  /**
   * {@inheritdoc}
   */
  public function countUserlists(array $listids = []) {
    $total = 0;

    foreach ($listids as $listid) {
      $userList = $this->sibListsApi->getContactsFromList($listid);
      $total += $userList->getCount();
    }

    return $total;
  }

  /**
   * {@inheritdoc}
   */
  public function partnerDrupal() {
    $data = [];
    $data['key'] = $this->apiKey;
    $data['webaction'] = 'MAILIN-PARTNER';
    $data['partner'] = 'DRUPAL';
    $data['source'] = 'Drupal';

    $options = [
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      'form_params' => $data,
      'verify' => FALSE,
    ];

    return $this->sIBHttpClient->doRequestDirect(SendinblueHttpClient::WEBHOOK_WS_SIB_URL, 'POST', $data, $options);
  }

}
