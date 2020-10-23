<?php

namespace Drupal\sendinblue\Tools\Model;

/**
 * Class GetAttributesAttributes.
 *
 * @package Drupal\sendinblue\Tools\Model
 */
class GetAttributesAttributes {

  /**
   * @var string*/
  public $name;
  /**
   * @var string*/
  public $category;
  /**
   * @var string*/
  public $type;
  /**
   * @var string*/
  public $calculatedValue;
  /**
   * @var GetAttributesEnumeration[]*/
  public $enumeration;

  /**
   * GetAttributesAttributes constructor.
   *
   * @param array $data
   */
  public function __construct(array $data = []) {
    $this->name = $data['name'];
    $this->type = $data['type'];
    $this->category = $data['category'];

    if (!empty($data['calculatedValue'])) {
      $this->calculatedValue = $data['calculatedValue'];
    }

    if (!empty($data['enumeration'])) {
      foreach ($data['enumeration'] as $enumeration) {
        $this->enumeration[] = new GetAttributesEnumeration($enumeration);
      }
    }
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
  public function getCategory(): string {
    return $this->category;
  }

  /**
   * @param string $category
   */
  public function setCategory(string $category) {
    $this->category = $category;
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
  public function getCalculatedValue() {
    return $this->calculatedValue;
  }

  /**
   * @param string $calculatedValue
   */
  public function setCalculatedValue(string $calculatedValue) {
    $this->calculatedValue = $calculatedValue;
  }

  /**
   * @return GetAttributesEnumeration[]
   */
  public function getEnumeration(): array {
    return $this->enumeration;
  }

  /**
   * @param GetAttributesEnumeration[] $enumeration
   */
  public function setEnumeration(array $enumeration) {
    $this->enumeration = $enumeration;
  }

}
