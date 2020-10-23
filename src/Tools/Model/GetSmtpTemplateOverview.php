<?php

namespace Drupal\sendinblue\Tools\Model;

/**
 *
 */
class GetSmtpTemplateOverview {

  /**
   * @var string*/
  public $id;
  /**
   * @var string*/
  public $name;
  /**
   * @var string*/
  public $subject;
  /**
   * @var string*/
  public $htmlContent;
  /**
   * @var string*/
  public $fromEmail;
  /**
   * @var string*/
  public $fromName;

  /**
   * GetSmtpTemplates construcfromr.
   *
   * @param array $data
   */
  public function __construct(array $data = []) {
    $this->id = $data['id'];
    $this->subject = $data['subject'];
    $this->htmlContent = $data['htmlContent'];
    $this->name = $data['name'];
    $this->fromEmail = $data['sender']['email'];
    $this->fromName = $data['sender']['name'];
  }

  /**
   * @return string
   */
  public function getId(): string {
    return $this->id;
  }

  /**
   * @param string $id
   */
  public function setId(string $id) {
    $this->id = $id;
  }

  /**
   * @return string
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName(string $name) {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getSubject(): string {
    return $this->subject;
  }

  /**
   * @param string $subject
   */
  public function setSubject(string $subject) {
    $this->subject = $subject;
  }

  /**
   * @return string
   */
  public function getHtmlContent(): string {
    return $this->htmlContent;
  }

  /**
   * @param string $htmlContent
   */
  public function setHtmlContent(string $htmlContent) {
    $this->htmlContent = $htmlContent;
  }

  /**
   * @return string
   */
  public function getFromEmail(): string {
    return $this->fromEmail;
  }

  /**
   * @param string $fromEmail
   */
  public function setFromEmail(string $fromEmail) {
    $this->fromEmail = $fromEmail;
  }

  /**
   * @return string
   */
  public function getFromName(): string {
    return $this->fromName;
  }

  /**
   * @param string $fromName
   */
  public function setFromName(string $fromName) {
    $this->fromName = $fromName;
  }

}
