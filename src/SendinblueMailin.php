<?php

namespace Drupal\sendinblue;

use Drupal\Component\Serialization\Json;
use GuzzleHttp\Exception\RequestException;

/**
 * Sendinblue REST client.
 */
class SendinblueMailin {
  public $apiKey;
  public $baseUrl;
  public $client;
  public $curlOpts = [];

  /**
   * Constructor.
   *
   * @param string $base_url
   *   A request url of api.
   * @param string $api_key
   *   A access key of api.
   */
  public function __construct($base_url, $api_key) {
    if (!function_exists('curl_init')) {
      $msg = 'SendinBlue requires CURL module';
      \Drupal::logger('sendinblue')->error($msg);
      return;
    }
    $this->client = \Drupal::httpClient();
    $this->baseUrl = $base_url;
    $this->apiKey = $api_key;
  }

  /**
   * Do CURL request with authorization.
   *
   * @param string $resource
   *   A request action of api.
   * @param string $method
   *   A method of curl request.
   * @param string $input
   *   A data of curl request.
   *
   * @return array
   *   An associate array with respond data.
   */
  private function doRequest($resource, $method, $input) {
    if (!function_exists('curl_init')) {
      $msg = 'SendinBlue requires CURL module';
      \Drupal::logger('sendinblue')->error($msg);
      return NULL;
    }
    $called_url = $this->baseUrl . "/" . $resource;

    $options = [
      'headers' => [
        'api-key' => $this->apiKey,
        'Content-Type' => 'application/json'
      ],
      'verify' => FALSE,
    ];

    if (!empty($input)) {
      $options['body'] = $input;
    }

    try {
      $clientRequest = $this->client->request($method, $called_url, $options);
      $body = $clientRequest->getBody();
    } catch (RequestException $e) {
      \Drupal::logger('sendinblue')->error('Curl error: @error', ['@error' => $e->getMessage()]);
    }

    return Json::decode($body);
  }

  /**
   * Do CURL request directly into sendinblue.
   *
   * @param array $data
   *   A data of curl request.
   *
   * @return array
   *   An associate array with respond data.
   */
  private function doRequestDirect($data) {
    if (!function_exists('curl_init')) {
      $msg = 'SendinBlue requires CURL module';
      \Drupal::logger('sendinblue')->error($msg);
      return NULL;
    }
    $called_url = 'http://ws.mailin.fr/';
    $data['source'] = 'Drupal';

    $options = [
      'headers' => [
        'Content-Type' => 'application/json'
      ],
      'form_params' => $data,
      'verify' => FALSE
    ];

    try {
      $clientRequest = $this->client->request('POST', $called_url, $options);
      $body = $clientRequest->getBody();
    } catch (RequestException $e) {
      \Drupal::logger('sendinblue')->error('Curl error: @error', ['@error' => $e->getMessage()]);
    }

    return Json::decode($body);
  }

  /**
   * Get Request of API.
   *
   * @param string $resource
   *   A request action.
   * @param string $input
   *   A data of curl request.
   *
   * @return array
   *   A respond data.
   */
  public function get($resource, $input) {
    return $this->doRequest($resource, "GET", $input);
  }

  /**
   * Put Request of API.
   *
   * @param string $resource
   *   A request action.
   * @param string $input
   *   A data of curl request.
   *
   * @return array
   *   A respond data.
   */
  public function put($resource, $input) {
    return $this->doRequest($resource, "PUT", $input);
  }

  /**
   * Post Request of API.
   *
   * @param string $resource
   *   A request action.
   * @param string $input
   *   A data of curl request.
   *
   * @return array
   *   A respond data.
   */
  public function post($resource, $input) {
    return $this->doRequest($resource, "POST", $input);
  }

  /**
   * Delete Request of API.
   *
   * @param string $resource
   *   A request action.
   * @param string $input
   *   A data of curl request.
   *
   * @return array
   *   A respond data.
   */
  public function delete($resource, $input) {
    return $this->doRequest($resource, "DELETE", $input);
  }

  /**
   * Get the details of an account.
   *
   * @return array
   *   An array of account detail.
   */
  public function getAccount() {
    return $this->get("account", "");
  }

  /**
   * Get campaigns by type.
   *
   * @param string $type
   *   A campaign type.
   *
   * @return array
   *   An array of campaigns.
   */
  public function getCampaigns($type) {
    return $this->get("campaign/detailsv2", Json::encode(["type" => $type]));
  }

  /**
   * Get campaign by id.
   *
   * @param string $id
   *   A campaign identification.
   *
   * @return array
   *   An array of campaigns.
   */
  public function getCampaign($id) {
    return $this->get("campaign/" . $id . "/detailsv2", "");
  }

  /**
   * Get lists of an account.
   *
   * @return array
   *   An array of all lists.
   */
  public function getLists() {
    return $this->get("list", "");
  }

  /**
   * Get list by id.
   *
   * @param string $id
   *   A list identification.
   *
   * @return array
   *   An array of lists.
   */
  public function getList($id) {
    return $this->get("list/" . $id, "");
  }

  /**
   * Send email via sendinblue.
   *
   * @param string $to
   *   A recipe email address.
   * @param string $subject
   *   A subject of email.
   * @param string $from
   *   A sender email address.
   * @param string $html
   *   A html body of email content.
   * @param string $text
   *   A text body of email content.
   * @param array $cc
   *   A cc address.
   * @param array $bcc
   *   A bcc address.
   * @param string $replyto
   *   A reply address.
   * @param array $attachment
   *   A attachment information.
   * @param array $headers
   *   A header of email.
   *
   * @return array
   *   An array of response code.
   */
  public function sendEmail($to, $subject, $from, $html, $text, $cc = [], $bcc = [], $replyto = '', $attachment = [], $headers = []) {
    return $this->post("email", Json::encode(
      [
        "cc" => $cc,
        "text" => $text,
        "bcc" => $bcc,
        "replyto" => $replyto,
        "html" => $html,
        "to" => $to,
        "attachment" => $attachment,
        "from" => $from,
        "subject" => $subject,
        "headers" => $headers,
      ]));
  }

  /**
   * Get user by email.
   *
   * @param string $email
   *   An email address.
   *
   * @return array
   *   An array of response code.
   */
  public function getUser($email) {
    return $this->get("user/" . $email, "");
  }

  /**
   * Create and update user.
   *
   * @param string $email
   *   An email address of user.
   * @param array $attributes
   *   An attributes to update.
   * @param array $blacklisted
   *   An array of black user.
   * @param string $listid
   *   A new listid.
   * @param string $listid_unlink
   *   A link unlink.
   *
   * @return array
   *   A response code.
   */
  public function createUpdateUser($email, $attributes = [], $blacklisted = [], $listid = '', $listid_unlink = '') {
    return $this->post("user/createdituser", Json::encode(
      [
        "email" => $email,
        "attributes" => $attributes,
        "blacklisted" => $blacklisted,
        "listid" => $listid,
        "listid_unlink" => $listid_unlink,
      ]));
  }

  /**
   * Get attribute by type.
   *
   * @param string $type
   *   A type.
   *
   * @return array
   *   An array of attributes.
   */
  public function getAttribute($type) {
    return $this->get("attribute/" . $type, "");
  }

  /**
   * Get attribute by type.
   *
   * @return array
   *   An array of attributes.
   */
  public function getAttributes() {
    return $this->get("attribute/", "");
  }

  /**
   * Get senders.
   *
   * @param array $option
   *   A option information.
   *
   * @return array
   *   A sender details.
   */
  public function getSenders($option = []) {
    return $this->get("advanced", Json::encode(["option" => $option]));
  }

  /**
   * Get the access token.
   *
   * @return array
   *   An access token information.
   */
  public function getAccessTokens() {
    return $this->get("account/token", "");
  }

  /**
   * Delete access token.
   *
   * @param string $key
   *   An access token.
   *
   * @return array
   *   A response code.
   */
  public function deleteToken($key) {
    return $this->post("account/deletetoken", Json::encode(["token" => $key]));
  }

  /**
   * Get the details of smtp.
   *
   * @return array
   *   A smtp details.
   */
  public function getSmtpDetails() {
    return $this->get("account/smtpdetail", "");
  }

  /**
   * Display all users of list.
   *
   * @param array $listids
   *   An array of lists.
   * @param int $page
   *   A page number.
   * @param int $page_limit
   *   A page limit per one page.
   *
   * @return array
   *   An array of users.
   */
  public function displayListUsers($listids = [], $page = 0, $page_limit = 50) {
    return $this->post("list/display", Json::encode(
      [
        "listids" => $listids,
        "page" => $page,
        "page_limit" => $page_limit,
      ]));
  }

  /**
   * Add the Partner's name in sendinblue.
   */
  public function partnerDrupal() {
    $data = [];
    $data['key'] = $this->apiKey;
    $data['webaction'] = 'MAILIN-PARTNER';
    $data['partner'] = 'DRUPAL';
    $this->doRequestDirect($data);
  }

}
