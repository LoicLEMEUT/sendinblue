<?php

namespace Drupal\sendinblue\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\sendinblue\SendinblueManager;

/**
 * Returns responses for entity browser routes.
 */
class SendinblueController extends ControllerBase {

  /**
   * SendinblueManager.
   *
   * @var \Drupal\sendinblue\SendinblueManager
   */
  private $sendinblueManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    SendinblueManager $sendinblueManager
  ) {
    $this->sendinblueManager = $sendinblueManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('sendinblue.manager')
    );
  }

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   */
  public function access(AccountInterface $account) {
    return AccessResult::allowedIf($this->sendinblueManager->isLoggedInState());
  }

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   */
  public function accessToSsoSib(AccountInterface $account) {
    $apiVersion = $this->sendinblueManager->getApiVersion($this->sendinblueManager->getAccessKey());

    return AccessResult::allowedIf($this->sendinblueManager->isLoggedInState())
      ->andIf(AccessResult::allowedIf($apiVersion === SendinblueManager::SENDINBLUE_API_VERSION_V2));
  }

  /**
   * Return cusotm page if user ligin or logout.
   */
  public function home() {
    if ($this->sendinblueManager->isLoggedInState()) {
      $home_controller = $this->sendinblueManager->generateHomeLogin();
      $home_controller['#theme'] = 'generateHomeLogin';
    }
    else {
      $home_controller = $this->sendinblueManager->generateHomeLogout();
      $home_controller['#theme'] = 'generateHomeLogout';
    }

    return $home_controller;
  }

  /**
   * Return page for list page (Iframe Sib)
   */
  public function listPage() {
    $listPage_controller['#theme'] = 'iframe_page';
    $listPage_controller['#url_iframe'] = [
      '#plain_text' => $this->sendinblueManager->generateListLogin(),
    ];

    return $listPage_controller;
  }

  /**
   * Return page for compaigns page (Iframe Sib)
   */
  public function listCampaigns() {
    $listPage_controller['#theme'] = 'iframe_page';
    $listPage_controller['#url_iframe'] = [
      '#plain_text' => $this->sendinblueManager->generateCampaignLogin(),
    ];

    return $listPage_controller;
  }

  /**
   * Return page for statistics page (Iframe Sib)
   */
  public function statisticsPage() {
    $listPage_controller['#theme'] = 'iframe_page';
    $listPage_controller['#url_iframe'] = [
      '#plain_text' => $this->sendinblueManager->generateStatisticLogin(),
    ];

    return $listPage_controller;
  }

}
