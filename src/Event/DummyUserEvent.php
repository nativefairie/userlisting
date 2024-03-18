<?php

/**
 * @file
 * Contains \Drupal\userlisting\Event\DummyUserEvent.
 */

namespace Drupal\userlisting\Event;

/**
 * Defines events for the DummyUser entity.
 */
final class DummyUserEvent {

  /**
   * Event triggered when dummy users should be populated.
   *
   * This event allows subscribers to populate dummy users.
   *
   * @Event
   *
   * @var string
   */
  const POPULATE = 'userlisting.dummy_user.populate';

}
