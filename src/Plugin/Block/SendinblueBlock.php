<?php

namespace Drupal\sendinblue\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\sendinblue\Form\SubscribeForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Display all instances for 'YourBlock' block plugin.
 *
 * @Block(
 *   id = "sendinblue_block",
 *   admin_label = @Translation("Sendinblue block"),
 *   deriver = "Drupal\sendinblue\Plugin\Derivative\SendinblueBlock"
 * )
 */
class SendinblueBlock extends BlockBase implements ContainerFactoryPluginInterface {
  /**
   * FormBuilderInterface.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  private $formBuilder;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, FormBuilderInterface $formBuilder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->formBuilder = $formBuilder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder')
    );
  }

  /**
   * Build the content for mymodule block.
   */
  public function build() {
    $getPluginDefinition = $this->getPluginDefinition();

    return $this->formBuilder->getForm(SubscribeForm::class, $getPluginDefinition['mcsId']);
  }

}
