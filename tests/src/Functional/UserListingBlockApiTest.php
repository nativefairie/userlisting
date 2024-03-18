<?php

namespace Drupal\Tests\userlisting\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the functionality of the User Listing block.
 *
 * @group userlisting
 */
class UserListingBlockTest extends BrowserTestBase {

  /**
   * An administrative user to configure the test environment.
   */
  protected $adminUser;

  /**
   * Modules to install.
   *
   * @var array
   */
  protected static $modules = ['userlisting'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'olivero';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create an administrative user.
    $this->adminUser = $this->drupalCreateUser([
      'administer blocks',
      'administer dummyuser',
      'administer site configuration',
    ]);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Tests the functionality of the User Listing block.
   */
  public function testUserListingBlockFunctionality() {
    // Place the User Listing block on the page.
    $this->drupalPlaceBlock('userlist_block', [
      'region' => 'content',
    ]);

    // Visit the home page to ensure the block is displayed.
    $this->drupalGet('');

    // Assert that the block content is present.
    $this->assertSession()->pageTextContains('User List'); // Adjust as needed.

    // We could add more assertions as needed to test the functionality of the block.
  }

}
