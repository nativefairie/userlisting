<?php

namespace Drupal\userlisting\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\userlisting\Event\DummyUserEvent;
use Drupal\userlisting\Repository\DummyUserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'User List' block.
 *
 * @Block(
 *   id = "userlist_block",
 *   admin_label = @Translation("User List"),
 *   category = @Translation("Custom")
 * )
 */
class UserListingBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The pager manager service.
   *
   * @var \Drupal\Core\Pager\PagerManagerInterface
   */
  protected $pagerManager;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The dummy user repository.
   *
   * @var \Drupal\userlisting\Repository\DummyUserRepository
   */
  protected $dummyUserRepository;

  /**
   * Constructs a new UserListBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface | NULL $configFactory
   *   The configuration factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface | NULL $eventDispatcher
   *   The event dispatcher.
   * @param \Drupal\Core\Pager\PagerManagerInterface | NULL $pagerManager
   *   The pager manager service.
   * @param \Psr\Log\LoggerInterface | NULL $logger
   *   The logger.
   * @param \Drupal\userlisting\Repository\DummyUserRepository | NULL $dummyUserRepository
   *   The dummy user repository.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $configFactory, EntityTypeManagerInterface $entityTypeManager, EventDispatcherInterface $eventDispatcher, PagerManagerInterface $pagerManager, LoggerInterface $logger, DummyUserRepository $dummyUserRepository) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $configFactory;
    $this->entityTypeManager = $entityTypeManager;
    $this->eventDispatcher = $eventDispatcher;
    $this->pagerManager = $pagerManager;
    $this->logger = $logger;
    $this->dummyUserRepository = $dummyUserRepository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('event_dispatcher'),
      $container->get('pager.manager'),
      $container->get('logger.channel.userlisting'),
      $container->get('userlisting.dummy_user_repository')
    );
  }

  /**

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'labels' => [
        'email' => 'Email Address',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
      ],
      'per_page' => 3,
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array{
    return ['userlist.settings'];
  }

  /**
   * {@inheritdoc}
   *
   * @throws GuzzleException
   * @throws EntityStorageException
   */
  public function build() {
    // Prepare fields from config.
    // Todo: Refactor this part.
    $configName = $this->getEditableConfigNames();
    $configFactory = $this->configFactory->get(reset($configName));
    // Get custom configuration values.
    $defaultValues = $this->defaultConfiguration();
    $config = $configFactory->get('labels');
    $emailLabel = $config['email'] ?? $defaultValues['labels']['email'];
    $firstNameLabel = $config['first_name'] ?? $defaultValues['labels']['first_name'];
    $lastNameLabel = $config['last_name'] ?? $defaultValues['labels']['last_name'];
    // Prepare field labels.
    $fields = [
      'email' => $emailLabel,
      'first_name' => $firstNameLabel,
      'last_name' => $lastNameLabel,
    ];
    $perPage = $configFactory->get('per_page') ?? '3';

    // Check if data exists in the storage.
    $count = $this->dummyUserRepository->getTotalUsersCount();

    if (isset($count) && $count !== 0) {
      // Perform the query, using the requested offset from
      // PagerManagerInterface::findPage(). This comes from a URL parameter, so
      // here we are assuming that the URL parameter corresponds to an actual
      // page of results that will exist within the set.
      $page = $this->pagerManager->findPage();

      // Load users and paginator.
      $users = $this->dummyUserRepository->getPaginatedUsers($page, $perPage);
      $this->pagerManager->createPager($count, $perPage);

      // Check if the users array is not empty.
      if (!empty($users)) {
        // Build the render array.
        $build = [
          '#theme' => 'userlist',
          '#results' => $users,
          '#labels' => $fields,
          '#attached' => [
            'library' => [
              'userlisting/userlist'
            ],
          ],
          '#pager' => [
            '#type' => 'pager',
          ],
        ];

        return $build;
      } else {
        // Users array is empty.
        return [
          '#markup' => $this->t('No users found.'),
        ];
      }
    } else {
      // No data found in the storage.
      return [
        '#markup' => $this->t('No data found.'),
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $configName = $this->getEditableConfigNames();
    $config = $this->configFactory->get(reset($configName));

    $labels = $config->get('labels');
    $perPage = $config->get('per_page');

    $form['labels'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Labels for user fields'),
    ];

    $form['labels']['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email Label'),
      '#default_value' => $labels['email'],
    ];

    $form['labels']['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name Label'),
      '#default_value' => $labels['first_name'],
    ];

    $form['labels']['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name Label'),
      '#default_value' => $labels['last_name'],
    ];

    $form['per_page'] = [
      '#type' => 'number',
      '#title' => $this->t('Items Per Page'),
      '#default_value' => $perPage,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(&$form, FormStateInterface $form_state) {
    // Validate the 'per_page' field to ensure it is a positive integer.
    // Todo: Add more validations.
    $perPage = $form_state->getValue('per_page');
    if (!is_numeric($perPage) || $perPage < 1 || intval($perPage) != $perPage) {
      $form_state->setErrorByName('per_page', $this->t('Items per page must be a positive integer.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    try {
      $count = $this->dummyUserRepository->getTotalUsersCount();

      // Check if data exists in the storage.
      if (!isset($count) || $count == 0) {
        $event = new DummyUserEvent();
        $this->eventDispatcher->dispatch($event, DummyUserEvent::POPULATE);
      }

      // Save form values to configuration.
      $configName = $this->getEditableConfigNames();
      $config = $this->configFactory->getEditable(reset($configName));
      $config
        ->set('labels', $values['labels'])
        ->set('per_page', $values['per_page'])
        ->save();
    } catch (\Exception $e) {
      // Log an error if configuration saving fails.
      \Drupal::logger('userlisting')->error('Failed to save configuration: @error', ['@error' => $e->getMessage()]);
    }
    // Invalidate block cache.
    Cache::invalidateTags(['block:userlist_block']);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(): array{
    return Cache::mergeTags(parent::getCacheTags(), ['block:userlist_block']);
  }

}
