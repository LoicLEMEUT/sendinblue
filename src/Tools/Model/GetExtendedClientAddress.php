<?php

namespace Drupal\sendinblue\Tools\Model;

/**
 *
 */
class GetExtendedClientAddress {

  /**
   * @var string*/
  public $street;
  /**
   * @var string*/
  public $city;
  /**
   * @var string*/
  public $zipCode;
  /**
   * @var string*/
  public $country;

  /**
   * GetAccount constructor.
   */
  public function __construct(array $data = []) {
    $this->setStreet($data['street']);
    $this->setCity($data['city']);
    $this->setZipCode($data['zipCode']);
    $this->setCountry($data['country']);
  }

  /**
   * @return string
   */
  public function getStreet(): string {
    return $this->street;
  }

  /**
   * @param string $street
   */
  public function setStreet(string $street) {
    $this->street = $street;
  }

  /**
   * @return string
   */
  public function getCity(): string {
    return $this->city;
  }

  /**
   * @param string $city
   */
  public function setCity(string $city) {
    $this->city = $city;
  }

  /**
   * @return string
   */
  public function getZipCode(): string {
    return $this->zipCode;
  }

  /**
   * @param string $zipCode
   */
  public function setZipCode(string $zipCode) {
    $this->zipCode = $zipCode;
  }

  /**
   * @return string
   */
  public function getCountry(): string {
    return $this->country;
  }

  /**
   * @param string $country
   */
  public function setCountry(string $country) {
    $this->country = $country;
  }

}
