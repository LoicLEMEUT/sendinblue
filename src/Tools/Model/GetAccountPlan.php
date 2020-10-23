<?php

namespace Drupal\sendinblue\Tools\Model;

/**
 *
 */
class GetAccountPlan {

  /**
   * @var string*/
  public $type;
  /**
   * @var string*/
  public $creditsType;
  /**
   * @var int*/
  public $credits;
  /**
   * @var \DateTime|null*/
  public $startDate;
  /**
   * @var \DateTime|null*/
  public $endDate;
  /**
   * @var int|null*/
  public $userLimit;

  /**
   * GetAccount constructor.
   */
  public function __construct(array $data = []) {
    $this->setType($data['type']);
    $this->setCreditsType($data['creditsType']);
    $this->setCredits($data['credits']);

    if (!empty($data['startDate'])) {
      $this->setStartDate(new DateTime($data['startDate']));
    }

    if (!empty($data['endDate'])) {
      $this->setEndDate(new DateTime($data['endDate']));
    }

    if (!empty($data['userLimit'])) {
      $this->setUserLimit($data['userLimit']);
    }
  }

  /**
   * @return string
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * @param string $type
   */
  public function setType(string $type) {
    $this->type = $type;
  }

  /**
   * @return string
   */
  public function getCreditsType(): string {
    return $this->creditsType;
  }

  /**
   * @param string $creditsType
   */
  public function setCreditsType(string $creditsType) {
    $this->creditsType = $creditsType;
  }

  /**
   * @return int
   */
  public function getCredits(): int {
    return $this->credits;
  }

  /**
   * @param int $credits
   */
  public function setCredits(int $credits) {
    $this->credits = $credits;
  }

  /**
   * @return DateTime|null
   */
  public function getStartDate(): DateTime {
    return $this->startDate;
  }

  /**
   * @param DateTime|null $startDate
   */
  public function setStartDate(DateTime $startDate) {
    $this->startDate = $startDate;
  }

  /**
   * @return DateTime|null
   */
  public function getEndDate(): DateTime {
    return $this->endDate;
  }

  /**
   * @param DateTime|null $endDate
   */
  public function setEndDate(DateTime $endDate) {
    $this->endDate = $endDate;
  }

  /**
   * @return int|null
   */
  public function getUserLimit(): int {
    return $this->userLimit;
  }

  /**
   * @param int|null $userLimit
   */
  public function setUserLimit(int $userLimit) {
    $this->userLimit = $userLimit;
  }

}
