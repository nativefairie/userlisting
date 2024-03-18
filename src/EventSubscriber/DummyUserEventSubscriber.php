<?php

/**
 * @file
 * Contains \Drupal\userlisting\EventSubscriber\DummyUserEventSubscriber.
 */

namespace Drupal\userlisting\EventSubscriber;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\userlisting\Event\DummyUserEvent;
use Drupal\userlisting\Repository\DummyUserRepository;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DummyUserEventSubscriber.
 */
class DummyUserEventSubscriber implements EventSubscriberInterface {

  /**
   * The dummy user repository.
   *
   * @var \Drupal\userlisting\Repository\DummyUserRepository
   */
  protected $dummyUserRepository;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a new DummyUserEventSubscriber object.
   *
   * @param \Drupal\userlisting\Repository\DummyUserRepository $dummyUserRepository
   *   The dummy user repository.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger.
   */
  public function __construct(DummyUserRepository $dummyUserRepository, LoggerInterface $logger) {
    $this->dummyUserRepository = $dummyUserRepository;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      DummyUserEvent::POPULATE => 'onPopulate',
    ];
  }

  /**
   * Populates dummy users.
   */
  public function onPopulate() {
    // Call the repository method to populate dummy users.
    try {
      $this->dummyUserRepository->populateDummyUsers();
    } catch (EntityStorageException | GuzzleException $e) {
      // Log any exceptions that occur during user population.
      $this->logger->error('Failed to populate dummy users: @error', ['@error' => $e->getMessage()]);
    }
  }

}
