<?php

namespace Drupal\sendinblue\Tools\Model;

/**
 *
 */
class GetSmtpTemplates {

  /**
   * @var string*/
  public $count;
  /**
   * @var GetSmtpTemplateOverview[]*/
  public $templates;

  /**
   * GetSmtpTemplates constructor.
   *
   * @param array $data
   */
  public function __construct(array $data = []) {
    $this->count = $data['count'];

    if (!empty($data['templates'])) {
      foreach ($data['templates'] as $template) {
        $this->templates[] = new GetSmtpTemplateOverview($template);
      }
    }
  }

  /**
   * @return string
   */
  public function getCount(): string {
    return $this->count;
  }

  /**
   * @param string $count
   */
  public function setCount(string $count) {
    $this->count = $count;
  }

  /**
   * @return GetSmtpTemplateOverview[]
   */
  public function getTemplates(): array {
    return $this->templates;
  }

  /**
   * @param GetSmtpTemplateOverview[] $templates
   */
  public function setTemplates(array $templates) {
    $this->templates = $templates;
  }

}
