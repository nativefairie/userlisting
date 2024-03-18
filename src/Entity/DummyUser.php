<?php

/**
 * @file
 * Contains \Drupal\userlisting\Entity\DummyUser.
 */

namespace Drupal\userlisting\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the dummy entity class.
 *
 * @ContentEntityType(
 *   id = "dummyuser",
 *   label = @Translation("Dummy"),
 *   label_collection = @Translation("Dummies"),
 *   label_singular = @Translation("dummy"),
 *   label_plural = @Translation("dummies"),
 *   label_count = @PluralTranslation(
 *     singular = "@count dummies",
 *     plural = "@count dummies",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\userlisting\DummyUserListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "edit" = "Drupal\userlisting\Form\UpdateDummyUserStatusForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "dummyuser",
 *   admin_permission = "administer dummyuser",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/dummyuser",
 *     "edit-form" = "/admin/content/dummyuser/{dummyuser}/edit",
 *     "canonical" = "/admin/content/dummyuser/{dummyuser}"
 *   },
 * )
 */

class DummyUser extends ContentEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    // Define base fields.
    $fields = [];

    // ID field.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Dummy User entity.'))
      ->setReadOnly(TRUE);

    // UUID field.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Dummy User entity.'))
      ->setReadOnly(TRUE);

    // Email field.
    $fields['email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Email'))
      ->setDescription(t('The email address of the user.'));

    // First name field.
    $fields['first_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('First Name'))
      ->setDescription(t('The first name of the user.'));

    // Last name field.
    $fields['last_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Last Name'))
      ->setDescription(t('The last name of the user.'));

    // Avatar field.
    $fields['avatar'] = BaseFieldDefinition::create('uri')
      ->setLabel(t('Avatar'))
      ->setDescription(t('The avatar of the user.'));

    // Status field.
    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('User Status'))
      ->setDescription(t('Further filtering will be added - excluded user based on status.'));

    return $fields;
  }

  /**
   * Gets the status of the Dummy User entity.
   *
   * This method returns the boolean value of the 'status' field, indicating
   * whether the user is active or inactive.
   *
   * @return bool
   *   TRUE if the user is active, FALSE if the user is inactive.
   */
  public function status():bool {
    return $this->get('status')->value;
  }

}
