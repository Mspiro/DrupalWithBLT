<?php

namespace Example\Blt\Plugin\Commands;

use Acquia\Blt\Robo\BltTasks;

/**
 * Defines commands in the "custom" namespace.
 */
class CustomClearCache extends BltTasks {

    /**
   * Clears caches and rebuilds the Drupal cache.
   *
   * @command custom:clear-cache
   */
  public function clearCache() {
    $this->say('Clearing caches...');

    // Clear all caches using Drush.
    $this->taskDrush()
      ->drush('cache:rebuild')
      ->run();

    $this->say('Cache cleared and rebuilt successfully.');
  }
}
