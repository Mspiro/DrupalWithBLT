<?php

namespace Acquia\Blt\Robo\Commands\Tests;

use Acquia\Blt\Robo\BltTasks;

/**
 * Defines commands in the "tests" namespace.
 */
class SecurityUpdatesCommand extends BltTasks {

  /**
   * Check local Drupal installation for security updates.
   *
   * This used to only check Drupal projects via the drush pm:security command,
   * but since that command is deprecated we now use `composer audit` to check
   * all Composer packages, even non-Drupal ones.
   *
   * @command tests:security-drupal
   */
  public function testsSecurityUpdates() {
    $result = $this->taskExecStack()
      ->dir($this->getConfigValue('repo.root'))
      ->exec('composer audit')
      ->run();

    if ($result->getExitCode()) {
      $this->logger->notice('To disable security checks, set disable-targets.tests.security-drupal to true in blt.yml.');
      return 1;
    }
    else {
      $this->writeln("<info>There are no outstanding security updates for Composer projects.</info>");
      return 0;
    }
  }

  /**
   * Check composer.lock for security updates.
   *
   * @command tests:security-composer
   */
  public function testsSecurityComposer() {
    $bin = $this->getConfigValue('composer.bin');
    $result = $this->taskExecStack()
      ->dir($this->getConfigValue('repo.root'))
      ->exec("$bin/security-checker security:check composer.lock")
      ->run();

    if ($result->getExitCode()) {
      $this->logger->notice('To disable security checks, set disable-targets.tests.security-composer to true in blt.yml.');
      return 1;
    }
    else {
      $this->writeln("<info>There are no outstanding security updates for your composer packages.</info>");
      return 0;
    }
  }

}
