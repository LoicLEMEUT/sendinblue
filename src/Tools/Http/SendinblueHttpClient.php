<?php

namespace Drupal\sendinblue\Tools\Http;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\ClientInterface;

/**
 * Sendinblue REST client.
 */
class SendinblueHttpClient {
  const WEBHOOK_WS_SIB_URL = 'http://ws.mailin.fr/';

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
   * @var \GuzzleHttp\ClientInterface
   */
  public $client;

  /**
   * Logger Service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   */
  protected $loggerFactory;

  /**
   * SendinblueHttpClient constructor.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   LoggerChannelFactory.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   ClientInterface.
   */
  public function __construct(
    LoggerChannelFactoryInterface $logger_factory,
    ClientInterface $http_client
  ) {
    if (!function_exists('curl_init')) {
      $msg = 'SendinBlue requires CURL module';
      $logger_factory->get('sendinblue')->error($msg);
      return;
    }

    $this->loggerFactory = $logger_factory;
    $this->client = $http_client;
  }

  /**
   * Set the APIKEy for use HTTP cURLs.
   *
   * @param string $apikey
   *   Sendinblue APIKEY.
   */
  public function setApiKey($apikey) {
    $this->apiKey = $apikey;
  }

  /**
   * Set the URL for use HTTP cURLs.
   *
   * @param string $baseUrl
   *   SendInBlue URL.
   */
  public function setBaseUrl($baseUrl) {
    $this->baseUrl = $baseUrl;
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
      $this->loggerFactory->get('sendinblue')->error($msg);
      return NULL;
    }
    $called_url = $this->baseUrl . "/" . $resource;

    $options = [
      'headers' => [
        'api-key' => $this->apiKey,
        'Content-Type' => 'application/json',
      ],
      'verify' => FALSE,
    ];

    if (!empty($input)) {
      $options['body'] = $input;
    }

    try {
      $clientRequest = $this->client->request($method, $called_url, $options);
      $body = $clientRequest->getBody();
    }
    catch (RequestException $e) {
      $this->loggerFactory->get('sendinblue')->error('Curl error: @error', ['@error' => $e->getMessage()]);
    }

    return Json::decode($body);
  }

  /**
   * Do CURL request directly into sendinblue.
   *
   * @param string $called_url
   *   URL.
   * @param string $method
   *   cURL method.
   * @param array $data
   *   A data of curl request.
   * @param array $options
   *   A data of curl options.
   *
   * @return array
   *   An associate array with respond data.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function doRequestDirect(string $called_url, string $method, array $data, array $options) {
    if (!function_exists('curl_init')) {
      $msg = 'SendinBlue requires CURL module';
      $this->loggerFactory->get('sendinblue')->error($msg);
      return NULL;
    }

    try {
      $clientRequest = $this->client->request($method, $called_url, $options);
      $body = $clientRequest->getBody();
    }
    catch (RequestException $e) {
      $this->loggerFactory->get('sendinblue')->error('Curl error: @error', ['@error' => $e->getMessage()]);
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
    return $this->doRequest($resource, 'GET', $input);
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
    return $this->doRequest($resource, 'PUT', $input);
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
    return $this->doRequest($resource, 'POST', $input);
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
    return $this->doRequest($resource, 'DELETE', $input);
  }

}
