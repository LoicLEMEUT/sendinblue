<?php

namespace Drupal\sendinblue\Tools\Model;

/**
 *
 */
class GetAttributesEnumeration {
  /**
   * @var int*/
  public $value;
  /**
   * @var string*/
  public $label;

  /**
   * GetAttributesEnumeration constructor.
   *
   * @param int $value
   */
  public function __construct(array $data = []) {
    $this->value = $data['value'];
    $this->label = $data['label'];
  }

  /**
   * @return int
   */
  public function getValue(): int {
    return $this->value;
  }

  /**
   * @param int $value
   */
  public function setValue(int $value) {
    $this->value = $value;
  }

  /**
   * @return string
   */
  public function getLabel(): string {
    return $this->label;
  }

  /**
   * @param string $label
   */
  public function setLabel(string $label) {
    $this->label = $label;
  }

}
