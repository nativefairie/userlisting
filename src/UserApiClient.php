<?php

/**
 * @file
 * Contains \Drupal\userlisting\UserApiClient.
 */

namespace Drupal\userlisting;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

/**
 * Service to fetch users from a third-party API.
 */
class UserApiClient {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  private $httpClient;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  private $logger;

  /**
   * Constructs a new UserApiClient object.
   *
   * @param \GuzzleHttp\ClientInterface $httpClient
   *   The HTTP client.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger.
   */
  public function __construct(ClientInterface $httpClient, LoggerInterface $logger) {
    $this->httpClient = $httpClient;
    $this->logger = $logger;
  }

  /**
   * Fetches users from the third-party API.
   *
   * @param int $perPage
   *   The number of users per page - 12 by default as we currently
   *   use our DummyUser entity to wrap them.
   * @param int $page
   *    The page number - 1 by default.
   *
   * @return array
   *   An array of users.
   *
   * @throws GuzzleException
   */
  public function fetchUsers(int $perPage = 12, int $page = 1): array {
    try {
      // Todo: Refactor to have the API endpoint url and settings in config.
      // Send a GET request to the API endpoint with per_page parameter.
      $response = $this->httpClient->request('GET', 'https://reqres.in/api/users', [
        'query' => [
          'per_page' => $perPage,
          'page' => $page
        ],
      ]);

      // Decode the response body into an associative array.
      $data = json_decode($response->getBody()->getContents(), TRUE);

      // Return the array from the response or an empty array if not set.
      return $data ?? [];
    } catch (\Exception $e) {
      // Log an error message if fetching users fails.
      $this->logger->error('Failed to fetch users from the API: @error', ['@error' => $e->getMessage()]);
      return [];
    }
  }
}
