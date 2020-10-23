<?php

namespace Drupal\sendinblue\Tools\Model;

/**
 *
 */
class GetAccount {

  /**
   * @var string*/
  public $email;
  /**
   * @var string*/
  public $firstName;
  /**
   * @var string*/
  public $lastName;
  /**
   * @var string*/
  public $companyName;

  /**
   * @var GetExtendedClientAddress*/
  public $address;

  /**
   * @var GetAccountPlan[]*/
  public $plan = [];

  /**
   * GetAccount constructor.
   */
  public function __construct(array $data = []) {
    $this->setEmail($data['email']);
    $this->setFirstName($data['firstName']);
    $this->setLastName($data['lastName']);
    $this->setCompanyName($data['companyName']);

    if (!empty($data['address'])) {
      $address = new GetExtendedClientAddress($data['address']);
      $this->setAddress($address);
    }

    if (!empty($data['plan'])) {
      foreach ($data['plan'] as $plan) {
        $accountPlan = new GetAccountPlan($plan);
        $this->addPlan($accountPlan);
      }
    }
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
   * @return string
   */
  public function getFirstName(): string {
    return $this->firstName;
  }

  /**
   * @param string $firstName
   */
  public function setFirstName(string $firstName) {
    $this->firstName = $firstName;
  }

  /**
   * @return string
   */
  public function getLastName(): string {
    return $this->lastName;
  }

  /**
   * @param string $lastName
   */
  public function setLastName(string $lastName) {
    $this->lastName = $lastName;
  }

  /**
   * @return string
   */
  public function getCompanyName(): string {
    return $this->companyName;
  }

  /**
   * @param string $companyName
   */
  public function setCompanyName(string $companyName) {
    $this->companyName = $companyName;
  }

  /**
   * @return GetExtendedClientAddress
   */
  public function getAddress(): GetExtendedClientAddress {
    return $this->address;
  }

  /**
   * @param GetExtendedClientAddress $address
   */
  public function setAddress(GetExtendedClientAddress $address) {
    $this->address = $address;
  }

  /**
   * @return GetAccountPlan[]
   */
  public function getPlan(): array {
    return $this->plan;
  }

  /**
   * @param GetAccountPlan $plan
   */
  public function addPlan(GetAccountPlan $plan) {
    $this->plan[] = $plan;
  }

}
