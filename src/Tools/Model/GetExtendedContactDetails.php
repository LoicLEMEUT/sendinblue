<?php

namespace Drupal\sendinblue\Tools\Model;

/**
 *
 */
class GetExtendedContactDetails {

  /**
   * @var string*/
  public $email;
  /**
   * @var int*/
  public $id = 0;
  /**
   * @var bool*/
  public $emailBlacklisted;
  /**
   * @var bool*/
  public $smsBlacklisted;
  /**
   * @var \DateTime*/
  public $createdAt;
  /**
   * @var \DateTime*/
  public $modifiedAt;
  /**
   * @var array*/
  public $listIds;
  public $attributes;

  /**
   * GetExtendedContactDetails constructor.
   */
  public function __construct(array $data = []) {
    $this->setId($data['id']);
    $this->setEmail($data['email']);
    $this->setSmsBlacklisted($data['smsBlacklisted']);
    $this->setEmailBlacklisted($data['emailBlacklisted']);
    $this->setCreatedAt(new \DateTime($data['createdAt']));
    $this->setModifiedAt(new \DateTime($data['modifiedAt']));
    $this->setListIds($data['listIds']);
    $this->setAttributes($data['attributes']);
  }

  /**
   * @return string
   */
  public function getEmail(): string {
    return $this->email;
  }

  /**
   * @param string $email
   */
  public function setEmail(string $email) {
    $this->email = $email;
  }

  /**
   * @return int
   */
  public function getId(): int {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @return bool
   */
  public function isEmailBlacklisted(): bool {
    return $this->emailBlacklisted;
  }

  /**
   * @param bool $emailBlacklisted
   */
  public function setEmailBlacklisted(bool $emailBlacklisted) {
    $this->emailBlacklisted = $emailBlacklisted;
  }

  /**
   * @return bool
   */
  public function isSmsBlacklisted(): bool {
    return $this->smsBlacklisted;
  }

  /**
   * @param bool $smsBlacklisted
   */
  public function setSmsBlacklisted(bool $smsBlacklisted) {
    $this->smsBlacklisted = $smsBlacklisted;
  }

  /**
   * @return \DateTime
   */
  public function getCreatedAt(): \DateTime {
    return $this->createdAt;
  }

  /**
   * @param \DateTime $createdAt
   */
  public function setCreatedAt(\DateTime $createdAt) {
    $this->createdAt = $createdAt;
  }

  /**
   * @return \DateTime
   */
  public function getModifiedAt(): \DateTime {
    return $this->modifiedAt;
  }

  /**
   * @param \DateTime $modifiedAt
   */
  public function setModifiedAt(\DateTime $modifiedAt) {
    $this->modifiedAt = $modifiedAt;
  }

  /**
   * @return array
   */
  public function getListIds(): array {
    return $this->listIds;
  }

  /**
   * @param array $listIds
   */
  public function setListIds(array $listIds) {
    $this->listIds = $listIds;
  }

  /**
   * @return mixed
   */
  public function getAttributes() {
    return $this->attributes;
  }

  /**
   * @param mixed $attributes
   */
  public function setAttributes($attributes) {
    $this->attributes = $attributes;
  }

}
