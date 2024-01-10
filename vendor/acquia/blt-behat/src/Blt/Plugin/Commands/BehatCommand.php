<?php

namespace Acquia\BltBehat\Blt\Plugin\Commands;

use Acquia\Blt\Robo\BltTasks;
use Acquia\Blt\Robo\Exceptions\BltException;
use Robo\Contract\VerbosityThresholdInterface;

/**
 * Defines commands in the "recipes:behat:*" namespace.
 */
class BehatCommand extends BltTasks {

  /**
   * Generates example files for writing custom Behat tests.
   *
   * @command recipes:behat:init
   *
   * @throws \Acquia\Blt\Robo\Exceptions\BltException
   */
  public function init() {
    $source = $this->getConfigValue('blt.root') . '/../blt-behat/scripts';
    $dest = $this->getConfigValue('repo.root') . '/tests/behat';
    $result = $this->taskCopyDir([$source => $dest])
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERBOSE)
      ->run();

    if (!$result->wasSuccessful()) {
      throw new BltException("Could not copy example files into the repository root.");
    }

    $this->say("<info>Example Behat tests were copied into your application.</info>");
  }

}
