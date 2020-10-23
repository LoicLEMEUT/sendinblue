<?php

namespace Drupal\sendinblue\Tools\Model;

/**
 *
 */
class CreateSmtpEmail {
  /**
   * @var string*/
  public $messageId;

  /**
   * CreateSmtpEmail constructor.
   *
   * @param string $messageId
   */
  public function __construct(string $messageId) {
    $this->messageId = $messageId;
  }

  /**
   * @return string
   */
  public function getMessageId(): string {
    return $this->messageId;
  }

  /**
   * @param string $messageId
   */
  public function setMessageId(string $messageId) {
    $this->messageId = $messageId;
  }

}
