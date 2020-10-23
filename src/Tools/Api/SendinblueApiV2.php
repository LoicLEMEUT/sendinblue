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
use Drupal\sendinblue\Tools\Model\GetSmtpTemplates;

/**
 * Sendinblue REST client.
 */
class SendinblueApiV2 implements SendInBlueApiInterface {

  const API_URL = 'https://api.sendinblue.com/v2.0';

  /**
   * Sib ApiKey.
   *
   * @var string
   */
  public $apiKey;

  /**
   * Sib BaseURL.
   *
   * @var string
   */
  public $baseUrl;

  /**
   * GuzzleClient to comm with Sib.
   *
   * @var \Drupal\sendinblue\Tools\Http\SendinblueHttpClient
   */
  public $sIBHttpClient;

  /**
   * Logger Service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   */
  protected $loggerFactory;

  /**
   * SendinblueApiV2 constructor.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   LoggerChannelFactory.
   * @param \Drupal\sendinblue\Tools\Http\SendinblueHttpClient $sIBHttpClient
   *   ClientInterface.
   */
  public function __construct(
    LoggerChannelFactoryInterface $loggerFactory,
    SendinblueHttpClient $sIBHttpClient
  ) {
    $this->loggerFactory = $loggerFactory;

    $this->sIBHttpClient = $sIBHttpClient;
    $this->sIBHttpClient->setApiKey($this->apiKey);
    $this->sIBHttpClient->setBaseUrl(self::API_URL);
  }

  /**
   * {@inheritdoc}
   */
  public function setApiKey($apiKey) {
    $this->apiKey = $apiKey;
    $this->sIBHttpClient->setApiKey($this->apiKey);
  }

  /**
   * {@inheritdoc}
   */
  public function getAccount() {
    $account = $this->sIBHttpClient->get("account", "");

    $accountData = $account['data'];

    return new GetAccount(
      [
        'firstName' => $accountData[2]['first_name'],
        'lastName' => $accountData[2]['last_name'],
        'email' => $accountData[2]['email'],
        'companyName' => $accountData[2]['company'],
        'address' => [
          'city' => $accountData[2]['city'],
          'zipCode' => $accountData[2]['zip_code'],
          'country' => $accountData[2]['country'],
          'street' => $accountData[2]['address'],
        ],
        'plan' => [
          [
            'type' => $accountData[0]['plan_type'],
            'credits' => $accountData[0]['credits'],
            'creditsType' => $accountData[0]['credit_type'],
          ],
          [
            'type' => $accountData[1]['plan_type'],
            'credits' => $accountData[1]['credits'],
            'creditsType' => $accountData[1]['credit_type'],
          ],
        ],
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getTemplates() {
    $response = $this->sIBHttpClient->get("campaign/detailsv2", Json::encode(["type" => 'template']));

    $templates = [];
    if (($response['code'] === 'success') && (is_array($response['data']))) {
      foreach ($response['data']['campaign_records'] as $template) {
        $templates[] = [
          'id' => $template['id'],
          'name' => $template['campaign_name'],
          'subject' => $template['subject'],
          'htmlContent' => $template['html_content'],
          'sender' => [
            'email' => $template['from_email'],
            'name' => $template['from_name'],
          ],
        ];
      }
    }

    return new GetSmtpTemplates(['templates' => $templates]);
  }

  /**
   * {@inheritdoc}
   */
  public function getTemplate($id) {
    $templates = $this->getTemplates();

    foreach ($templates->getTemplates() as $template) {
      if ($template->getId() === $id) {
        return $template;
      }
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getLists() {
    $lists = $this->sIBHttpClient->get("list", "");

    return new GetLists($lists['data']);
  }

  /**
   * {@inheritdoc}
   */
  public function getList($id) {
    $list = $this->sIBHttpClient->get("list/" . $id, "");

    return new GetExtendedList([
      'id' => $list['data']['id'],
      'name' => $list['data']['name'],
      'totalSubscribers' => $list['data']['total_subscribers'],
      'totalBlacklisted' => $list['data']['total_blacklisted'],
      'createdAt' => $list['data']['entered'],
      'folderId' => $list['data']['list_parent'],
      'dynamicList' => $list['data']['dynamic_list'],
    ]);
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
    $replyto += ['email' => NULL, 'name' => NULL];
    $to += ['email' => NULL, 'name' => NULL];
    $from += ['email' => NULL, 'name' => NULL];

    $emailData = [
      "text" => $text,
      "replyto" => [$replyto['email'] => $replyto['name']],
      "html" => $html,
      "to" => [$to['email'] => $to['name']],
      "attachment" => $attachment,
      "from" => [$from['email'], $from['name']],
      "subject" => $subject,
      "headers" => $headers,
    ];

    if (!empty($cc)) {
      $cc += ['email' => NULL, 'name' => NULL];
      $emailData['cc'] = [$cc['email'] => $cc['name']];
    }

    if (!empty($bcc)) {
      $bcc += ['email' => NULL, 'name' => NULL];
      $emailData['bcc'] = [$bcc['email'] => $bcc['name']];
    }

    $message = $this->sIBHttpClient->post("email", Json::encode($emailData));

    if ($message['code'] === 'success') {
      return new CreateSmtpEmail($message['data']['message-id']);
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getUser($email) {
    $contactInfo = $this->sIBHttpClient->get("user/" . $email, "");

    if (empty($contactInfo['data'])) {
      return NULL;
    }

    return new GetExtendedContactDetails([
      'email' => $contactInfo['data']['email'],
      'emailBlacklisted' => $contactInfo['data']['blacklisted'],
      'smsBlacklisted' => $contactInfo['data']['blacklisted_sms'],
      'createdAt' => $contactInfo['data']['entered'],
      'modifiedAt' => $contactInfo['data']['entered'],
      'listIds' => $contactInfo['data']['listid'],
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function createUpdateUser($email, array $attributes = [], array $blacklisted = [], $listid = '', $listid_unlink = '') {
    $this->sIBHttpClient->post("user/createdituser", Json::encode(
      [
        "email" => $email,
        "attributes" => $attributes,
        "blacklisted" => $blacklisted,
        "listid" => $listid,
        "listid_unlink" => $listid_unlink,
      ]));
  }

  /**
   * {@inheritdoc}
   */
  public function getAttributes() {
    $sibAttributes = $this->sIBHttpClient->get("attribute/", "");
    $attributes['attributes'] = [];

    foreach ($sibAttributes['data']['normal_attributes'] as $attribute) {
      $attributes['attributes'][] = [
        'name' => $attribute['name'],
        'type' => $attribute['type'],
        'category' => 'normal',
      ];
    }

    return new GetAttributes($attributes);
  }

  /**
   * {@inheritdoc}
   */
  public function getAccessTokens() {
    return $this->sIBHttpClient->get("account/token", "")['data']['access_token'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSmtpDetails() {
    $smtpDetails = $this->sIBHttpClient->get("account/smtpdetail", "");

    $sibSmtpDetails = $smtpDetails['data']['relay_data']['data'];
    $enabled = $smtpDetails['data']['relay_data']['status'] === 'enabled';

    return new GetSmtpDetails($sibSmtpDetails['username'], $sibSmtpDetails['relay'], $sibSmtpDetails['port'], $enabled);
  }

  /**
   * {@inheritdoc}
   */
  public function countUserlists(array $listids = []) {
    $userLists = $this->sIBHttpClient->post("list/display", Json::encode(
      [
        'listids' => $listids,
      ]
    ));

    return $userLists['data']['total_list_records'];
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
