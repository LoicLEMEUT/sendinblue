<?php

namespace Drupal\sendinblue\Tools\Model;

/**
 * Class GetExtendedList.
 *
 * @package Drupal\sendinblue\Tools\Model
 *
 *         'id' => 'int',
 * 'name' => 'string',
 * 'totalBlacklisted' => 'int',
 * 'totalSubscribers' => 'int',
 * 'folderId' => 'int',
 * 'createdAt' => '\DateTime',
 * 'campaignStats' => '\SendinBlue\Client\Model\GetExtendedListCampaignStats[]',
 * 'dynamicList' => 'bool'
 */
class GetExtendedList {
  /**
   * @var int*/
  public $id;
  /**
   * @var string*/
  public $name;
  /**
   * @var int*/
  public $totalBlacklisted = 0;
  /**
   * @var int*/
  public $totalSubscribers = 0;
  /**
   * @var int*/
  public $folderId;
  /**
   * @var \DateTime*/
  public $createdAt;
  /**
   * @var int*/
  public $dynamicList;

  /**
   * GetExtendedList constructor.
   */
  public function __construct(array $data = []) {

    $this->setId($data['id']);
    $this->setName($data['name']);
    $this->setTotalBlacklisted($data['totalBlacklisted']);
    $this->setTotalSubscribers($data['totalSubscribers']);
    $this->setFolderId($data['folderId']);
    $this->setDynamicList($data['dynamicList']);

    if (!empty($data['createdAt'])) {
      $this->setCreatedAt(new \DateTime($data['createdAt']));
    }
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
  public function setId(int $id) {
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
   * @return int
   */
  public function getTotalBlacklisted(): int {
    return $this->totalBlacklisted;
  }

  /**
   * @param int $totalBlacklisted
   */
  public function setTotalBlacklisted($totalBlacklisted) {
    $this->totalBlacklisted = $totalBlacklisted;
  }

  /**
   * @return int
   */
  public function getTotalSubscribers(): int {
    return $this->totalSubscribers;
  }

  /**
   * @param int $totalSubscribers
   */
  public function setTotalSubscribers($totalSubscribers) {
    $this->totalSubscribers = $totalSubscribers;
  }

  /**
   * @return int
   */
  public function getFolderId(): int {
    return $this->folderId;
  }

  /**
   * @param int $folderId
   */
  public function setFolderId(int $folderId) {
    $this->folderId = $folderId;
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
   * @return int
   */
  public function getDynamicList(): int {
    return $this->dynamicList;
  }

  /**
   * @param int $dynamicList
   */
  public function setDynamicList(int $dynamicList) {
    $this->dynamicList = $dynamicList;
  }

}
