<?php

/**
 * @file
 * Contains \Drupal\userlisting\Repository\DummyUserRepository.
 */

namespace Drupal\userlisting\Repository;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\userlisting\Entity\DummyUser;
use Drupal\userlisting\UserApiClient;
use Psr\Log\LoggerInterface;

/**
 * Class DummyUserRepository.
 */
class DummyUserRepository {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The user list service.
   *
   * @var \Drupal\userlisting\UserApiClient
   */
  protected $userApiClient;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a new DummyUserRepository object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\userlisting\UserApiClient $userApiClient
   *   The user list service.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, UserApiClient $userApiClient, LoggerInterface $logger) {
    $this->entityTypeManager = $entityTypeManager;
    $this->userApiClient = $userApiClient;
    $this->logger = $logger;
  }

  /**
   * Populate dummy users from the API.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *   Thrown when the entity cannot be saved.
   * @throws \GuzzleHttp\Exception\GuzzleException
   *   Thrown when an error occurs during the API request.
   */
  public function populateDummyUsers() {
    $count = $this->getTotalUsersCount();

    // Check if data exists in the storage.
    if (isset($count) && $count !== 0) {
      $this->logger->warning('Already saved dummy users.');
    }
    else {
      // Fetch users from the API using the UserApiClient service.
      $users = $this->userApiClient->fetchUsers();

      // Iterate through the fetched users and create DummyUser entities.
      foreach ($users['data'] as $userData) {
        $dummyUser = DummyUser::create([
          'first_name' => $userData['first_name'],
          'last_name' => $userData['last_name'],
          'email' => $userData['email'],
          'avatar' => $userData['avatar'],
          'status' => TRUE,
        ]);
        $dummyUser->save();
      }
      $this->getTotalUsersCount() ?? $this->logger->info('Just saved dummy users.');
    }
  }

  /**
   * Get paginated users.
   *
   * @param int $page
   *    The page number.
   * @param int $perPage
   *   The number of users per page.
   *
   * @return \Drupal\userlisting\Entity\DummyUser[]
   *   The paginated list of users.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function getPaginatedUsers(int $page, int $perPage): array {
    // Get the storage for the dummy_user entity.
    $storage = $this->entityTypeManager->getStorage('dummyuser');

    // Check if data exists in the storage.
    $count = $this->getTotalUsersCount();

    // Check if data exists in the storage.
    if ($count !== 0) {
      $offset = $page * $perPage;

      $query = $storage->getQuery()
        ->accessCheck(TRUE)
        ->condition('status', TRUE)
        ->sort('id', 'ASC')
        ->range($offset, $perPage);
      $userIds = $query->execute();

      // Load users based on the IDs.
      $users = $storage->loadMultiple($userIds);
    }
    else {
      $users = [];
    }

    return $users;
  }

  /**
   * Get total count of users.
   *
   * @return int
   *   The total count of users.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function getTotalUsersCount(): int {
    return $this->entityTypeManager->getStorage('dummyuser')
      ->getQuery()
      ->accessCheck(TRUE)
      ->count()
      ->execute();
  }

}
