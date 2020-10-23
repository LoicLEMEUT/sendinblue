<?php

namespace Drupal\sendinblue\Routing;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\sendinblue\SendinblueManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Defines dynamic routes.
 */
class SubscribeRoutes implements ContainerInjectionInterface {
  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * EntityModerationForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function routes() {
    $routes = [];

    $signups = $this->entityTypeManager->getStorage(SendinblueManager::SENDINBLUE_SIGNUP_ENTITY)
      ->loadMultiple();

    foreach ($signups as $signup) {
      if ((int) $signup->mode->value == SendinblueManager::SENDINBLUE_SIGNUP_PAGE || (int) $signup->mode->value == SendinblueManager::SENDINBLUE_SIGNUP_BOTH) {
        $settings = (!$signup->settings->first()) ? [] : $signup->settings->first()
          ->getValue();

        $routes['sendinblue.subscribe.' . $signup->name->value] = new Route('/' . $settings['path'],
          [
            '_form' => '\Drupal\sendinblue\Form\SubscribeForm',
            '_title' => $signup->title->value,
            'mcsId' => $signup->mcsId->value,
          ],
          [
            '_permission' => 'access content',
          ]
        );
      }
    }

    return $routes;
  }

}
