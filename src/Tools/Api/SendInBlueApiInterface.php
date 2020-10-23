<?php

namespace Drupal\sendinblue\Tools\Api;

/**
 * Interface for SendInBlue API.
 */
interface SendInBlueApiInterface {

  /**
   * Set the SendinBlue APIKEY.
   *
   * @param string|null $apiKey
   *   SendInBlue Apikey.
   */
  public function setApiKey($apiKey);

  /**
   * Get the details of an account.
   *
   * @return \Drupal\sendinblue\Tools\Model\GetAccount
   *   An array of account detail.
   */
  public function getAccount();

  /**
   * Get templates.
   *
   * @return \Drupal\sendinblue\Tools\Model\GetSmtpTemplates
   *   An array of campaigns.
   */
  public function getTemplates();

  /**
   * Get template by id.
   *
   * @param string $id
   *   A template identification.
   *
   * @return \Drupal\sendinblue\Tools\Model\GetSmtpTemplateOverview
   *   A template.
   */
  public function getTemplate($id);

  /**
   * Get lists of an account.
   *
   * @return \Drupal\sendinblue\Tools\Model\GetLists
   *   An array of all lists.
   */
  public function getLists();

  /**
   * Get list by id.
   *
   * @param string $id
   *   A list identification.
   *
   * @return \Drupal\sendinblue\Tools\Model\GetExtendedList
   *   An array of lists.
   */
  public function getList($id);

  /**
   * Send email via sendinblue.
   *
   * @param array $to
   *   A recipe email address.
   * @param string $subject
   *   A subject of email.
   * @param string $html
   *   A html body of email content.
   * @param string $text
   *   A text body of email content.
   * @param array $from
   *   A sender email address.
   * @param array $replyto
   *   A reply address.
   * @param array $cc
   *   A cc address.
   * @param array $bcc
   *   A bcc address.
   * @param array $attachment
   *   A attachment information.
   * @param array $headers
   *   A header of email.
   *
   * @return \Drupal\sendinblue\Tools\Model\CreateSmtpEmail
   *   An array of response code.
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
  );

  /**
   * Get user by email.
   *
   * @param string $email
   *   An email address.
   *
   * @return \Drupal\sendinblue\Tools\Model\GetExtendedContactDetails
   *   An array of response code.
   */
  public function getUser($email);

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
   */
  public function createUpdateUser($email, array $attributes = [], array $blacklisted = [], $listid = '', $listid_unlink = '');

  /**
   * Get attribute by type.
   *
   * @return \Drupal\sendinblue\Tools\Model\GetAttributes
   *   An array of attributes.
   */
  public function getAttributes();

  /**
   * Get the access token.
   *
   * @return string
   *   An access token information.
   */
  public function getAccessTokens();

  /**
   * Get the details of smtp.
   *
   * @return \Drupal\sendinblue\Tools\Model\GetSmtpDetails
   *   A smtp details.
   */
  public function getSmtpDetails();

  /**
   * Count all users of lists.
   *
   * @param array $listids
   *   An array of lists.
   *
   * @return int
   *   Number of users.
   */
  public function countUserlists(array $listids = []);

  /**
   * Add the Partner's name in sendinblue.
   */
  public function partnerDrupal();

}
