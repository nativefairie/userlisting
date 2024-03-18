<?php

namespace Drupal\Tests\userlisting\Unit;

use Drupal\userlisting\Plugin\Block\UserListingBlock;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\userlisting\Repository\DummyUserRepository;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality of the User Listing block.
 *
 * @coversDefaultClass \Drupal\userlisting\Plugin\Block\UserListingBlock
 *
 * @group userlisting
 */
class UserListingBlockTest extends TestCase {

  /**
   * A mock configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $configFactory;

  /**
   * A mock entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $entityTypeManager;

  /**
   * A mock pager manager.
   *
   * @var \Drupal\Core\Pager\PagerManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $pagerManager;

  /**
   * A mock logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $loggerFactory;

  /**
   * A mock dummy user repository.
   *
   * @var \Drupal\userlisting\Repository\DummyUserRepository|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $dummyUserRepository;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->configFactory = $this->createMock(ConfigFactoryInterface::class);
    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->pagerManager = $this->createMock(PagerManagerInterface::class);
    $this->loggerFactory = $this->createMock(LoggerChannelFactoryInterface::class);
    $this->dummyUserRepository = $this->createMock(DummyUserRepository::class);
  }

  /**
   * Tests the build method of UserListingBlock.
   *
   * @covers ::build
   */
  public function testBuildMethod(): void {
    // Create an instance of UserListingBlock.
    $block = new UserListingBlock([], 'userlist_block', [], $this->configFactory, NULL, $this->pagerManager, NULL, $this->loggerFactory, $this->dummyUserRepository);

    // Mock the behavior of getEditableConfigNames to return a config name.
    $block->expects($this->any())
      ->method('getEditableConfigNames')
      ->willReturn(['userlist.settings']);

    // Mock the behavior of defaultConfiguration to return default config values.
    $block->expects($this->any())
      ->method('defaultConfiguration')
      ->willReturn([
        'labels' => [
          'email' => 'Email Address',
          'first_name' => 'First Name',
          'last_name' => 'Last Name',
        ],
        'per_page' => 3,
      ]);

    // Mock the behavior of getPagerManager to return a pager manager.
    $this->pagerManager->expects($this->once())
      ->method('findPage')
      ->willReturn(0);

    // Mock the behavior of getTotalUsersCount to return a count.
    $this->dummyUserRepository->expects($this->once())
      ->method('getTotalUsersCount')
      ->willReturn(5);

    // Call the build method.
    $build = $block->build();

    // Assert that the build method returns an array.
    $this->assertIsArray($build);

    // We can add more specific assertions based on the expected output of the build method.
  }

}
