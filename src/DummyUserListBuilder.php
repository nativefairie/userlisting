<?php

/**
 * @file
 * Contains \Drupal\userlisting\DummyUserListBuilder.
 */

declare(strict_types=1);

namespace Drupal\userlisting;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityMalformedException;

/**
 * Provides a list controller for the dummy entity type.
 */
final class DummyUserListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['id'] = $this->t('Machine name');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   *
   * @throws EntityMalformedException
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $row['id'] = $entity->toLink();
    $row['status'] = $entity->status() ? $this->t('Active') : $this->t('Inactive');
    return $row + parent::buildRow($entity);
  }

}
