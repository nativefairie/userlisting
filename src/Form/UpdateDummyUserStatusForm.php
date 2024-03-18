<?php

/**
 * @file
 * Contains \Drupal\userlisting\Form\UpdateDummyUserStatusForm.
 */

namespace Drupal\userlisting\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the dummy user edit form.
 */
class UpdateDummyUserStatusForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    // Get the entity.
    $entity = $this->getEntity();

    // Add/edit form elements as needed.
    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Status'),
      '#default_value' => $entity->get('status')->value,
      '#description' => $this->t('Check to enable, uncheck to disable.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->getEntity();

    // Set the status field value based on the checkbox value.
    $entity->set('status', $form_state->getValue('status'));

    // Invalidate cache
    Cache::invalidateTags(['block:userlist_block']);

    // Call the parent save method.
    return parent::save($form, $form_state);
  }

}
