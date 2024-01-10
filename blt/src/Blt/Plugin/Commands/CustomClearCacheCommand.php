<?php

namespace Example\Blt\Plugin\Commands;


use Acquia\Blt\Robo\BltTasks;

/**
 * Defines commands in the "custom" namespace.
 */
class CustomClearCacheCommand extends BltTasks
{

  /**
   * Executes the custom command.
   *
   * @command custom:clearcache
   * 
   */
  public function customCommand()
  {
    $this->say('Clearing caches...');

    // Clear all caches using Drush.
    $this->taskDrush()
      ->drush('cache:rebuild')
      ->run();

    $this->say('Cache cleared and rebuilt successfully.');
  
  }
}