<?php

namespace Drupal\sendinblue\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\sendinblue\SendinblueManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides block plugin definitions for sendinblue blocks.
 *
 * @see \Drupal\sendinblue\Plugin\Block\SendinblueBlock
 */
class SendinblueBlock extends DeriverBase implements ContainerDeriverInterface {
  use StringTranslationTrait;

  /**
   * EntityTypeManagerInterface.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * Block name.
   *
   * @var string
   */
  private $blockName;

  /**
   * {@inheritdoc}
   */
  public function __construct($blockName, EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->blockName = $blockName;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $blockName) {
    return new static(
      $blockName,
      $container->get('entity_type.manager')
    );
  }

  /**
   * Provide multiple blocks for sendinblue signup forms.
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $signups = $this->entityTypeManager->getStorage(SendinblueManager::SENDINBLUE_SIGNUP_ENTITY)->loadMultiple();

    foreach ($signups as $signup) {
      if ((int) $signup->mode->value == SendinblueManager::SENDINBLUE_SIGNUP_BLOCK || (int) $signup->mode->value == SendinblueManager::SENDINBLUE_SIGNUP_BOTH) {
        $this->derivatives[$signup->mcsId->value] = $base_plugin_definition;
        $this->derivatives[$signup->mcsId->value]['admin_label'] = $this->t('SendinBlue Subscription Form: @name', ['@name' => $signup->name->value]);
        $this->derivatives[$signup->mcsId->value]['mcsId'] = $signup->mcsId->value;
      }
    }

    return $this->derivatives;
  }

}
