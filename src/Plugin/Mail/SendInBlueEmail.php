<?php

namespace Drupal\sendinblue\Plugin\Mail;

use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Mail\MailInterface;
use Drupal\Core\Mail\MailFormatHelper;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\sendinblue\SendinblueManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the default Drupal mail backend, using PHP's native mail() function.
 *
 * @Mail(
 *   id = "sendinblue_mail",
 *   label = @Translation("Sendinblue mailer"),
 *   description = @Translation("Send emails with Sendinblue mailer")
 * )
 */
class SendInBlueEmail implements MailInterface, ContainerFactoryPluginInterface {

  /**
   * SendinblueManager.
   *
   * @var \Drupal\sendinblue\SendinblueManager
   *   SendinblueManager
   */
  private $sendinblueManager;

  /**
   * Logger Service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   *   LoggerChannelFactory
   */
  protected $loggerFactory;

  /**
   * SendInBlueEmailConstructor.
   *
   * Allows to send SMTP emails with SendInBue API.
   */
  public function __construct(SendinblueManager $sendinblueManager, LoggerChannelFactory $logger_factory) {
    $this->sendinblueManager = $sendinblueManager;
    $this->loggerFactory = $logger_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('sendinblue.manager'),
      $container->get('logger.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function format(array $message) {
    // Join the body array into one string.
    $message['body'] = implode("\n\n", $message['body']);

    // Convert any HTML to plain-text.
    $message['body'] = MailFormatHelper::htmlToText($message['body']);
    // Wrap the mail body for sending.
    $message['body'] = MailFormatHelper::wrapMail($message['body']);

    return $message;
  }

  /**
   * {@inheritdoc}
   */
  public function mail(array $message) {
    try {
      $to = [
        'email' => $message['to'],
      ];

      $from = [
        'email' => $message['from'],
      ];

      $message['reply-to'] = !empty($message['reply-to']) ? $message['reply-to'] : $message['from'];
      $replyTo = [
        'email' => $message['reply-to'],
      ];

      unset($message['headers']['Content-Type']);

      $result = $this->sendinblueManager->getSendinblueMailin()->sendEmail(
        $to,
        $message['subject'],
        nl2br($message['body']),
        $message['body'],
        $from,
        $replyTo,
        [],
        [],
        [],
        $message['headers']
      );

      if (empty($result->getMessageId())) {
        $this->loggerFactory->get('mail')
          ->error('[SENDINBLUE] - Error sending email (from %from to %to with reply-to %reply).', [
            '%from' => $message['from'],
            '%to' => $message['to'],
            '%reply' => $message['reply-to'] ? $message['reply-to'] : 'not set',
          ]);

        return FALSE;
      }

      $this->loggerFactory->get('mail')
        ->info('[SENDINBLUE] - Sending email %messageId (from %from to %to).', [
          '%from' => $message['from'],
          '%to' => $message['to'],
          '%messageId' => $result->getMessageId(),
        ]);
      return TRUE;
    }
    catch (\Exception $e) {
      $this->loggerFactory->get('mail')
        ->error('[SENDINBLUE] - Error sending email (from %from to %to with reply-to %reply) [%error].', [
          '%from' => $message['from'],
          '%to' => $message['to'],
          '%reply' => $message['reply-to'] ? $message['reply-to'] : 'not set',
          '%error' => $e->getMessage(),
        ]);
      return FALSE;
    }
  }

}
