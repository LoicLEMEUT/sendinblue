<?php

namespace Drupal\sendinblue\Tools\Model;

/**
 *
 */
class GetSmtpDetails {

  /**
   * @var string*/
  public $userName;
  /**
   * @var string*/
  public $relay;
  /**
   * @var int*/
  public $port;
  /**
   * @var bool*/
  private $enabled;

  /**
   * GetSmtpDetails constructor.
   *
   * @param string $userName
   * @param string $relay
   * @param int $port
   * @param bool $enabled
   */
  public function __construct(string $userName, string $relay, int $port, bool $enabled) {
    $this->userName = $userName;
    $this->relay = $relay;
    $this->port = $port;
    $this->enabled = $enabled;
  }

  /**
   * @return string
   */
  public function getUserName(): string {
    return $this->userName;
  }

  /**
   * @param string $userName
   */
  public function setUserName(string $userName) {
    $this->userName = $userName;
  }

  /**
   * @return string
   */
  public function getRelay(): string {
    return $this->relay;
  }

  /**
   * @param string $relay
   */
  public function setRelay(string $relay) {
    $this->relay = $relay;
  }

  /**
   * @return int
   */
  public function getPort(): int {
    return $this->port;
  }

  /**
   * @param int $port
   */
  public function setPort(int $port) {
    $this->port = $port;
  }

  /**
   * @return bool
   */
  public function isEnabled(): bool {
    return $this->enabled;
  }

  /**
   * @param bool $enabled
   */
  public function setEnabled(bool $enabled) {
    $this->enabled = $enabled;
  }

}
