<?php

namespace Drupal\sendinblue\Tools\Model;

/**
 *
 */
class GetLists {

  /**
   * @var array*/
  public $lists = [];
  /**
   * @var int*/
  public $count = 0;

  /**
   * GetLists constructor.
   *
   * @param array $lists
   * @param int $count
   */
  public function __construct(array $lists) {
    foreach ($lists as $list) {
      $this->addList($list);
    }

    $this->setCount(count($this->getLists()));
  }

  /**
   * @return array
   */
  public function getLists(): array {
    return $this->lists;
  }

  /**
   * @param array $lists
   */
  public function setLists(array $lists) {
    $this->lists = $lists;
  }

  /**
   * @param array $list
   */
  public function addList(array $list) {
    $this->lists[] = $list;
  }

  /**
   * @return int
   */
  public function getCount(): int {
    return $this->count;
  }

  /**
   * @param int $count
   */
  public function setCount(int $count) {
    $this->count = $count;
  }

}
