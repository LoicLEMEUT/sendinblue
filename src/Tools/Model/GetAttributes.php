<?php

namespace Drupal\sendinblue\Tools\Model;

/**
 *
 */
class GetAttributes {
  /**
   * @var GetAttributesAttributes[]*/
  public $attributes = [];

  /**
   * GetAttributes constructor.
   *
   * @param GetAttributesAttributes[] $attributes
   */
  public function __construct(array $attributes) {
    if (!empty($attributes['attributes'])) {
      foreach ($attributes['attributes'] as $attribute) {
        $this->attributes[] = new GetAttributesAttributes($attribute);
      }
    }
  }

  /**
   * @return GetAttributesAttributes[]
   */
  public function getAttributes(): array {
    return $this->attributes;
  }

  /**
   * @param GetAttributesAttributes[] $attributes
   */
  public function setAttributes(array $attributes) {
    $this->attributes = $attributes;
  }

}
