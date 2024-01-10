<?php

namespace Acquia\BltBehat\Blt\Wizards;

use Acquia\Blt\Robo\Config\BltConfig;
use Acquia\Blt\Robo\Config\YamlConfigProcessor;
use Consolidation\Config\Loader\YamlConfigLoader;
use function file_exists;
use Acquia\Blt\Robo\Wizards\Wizard;

/**
 * Class TestsWizard.
 *
 * @package Acquia\Blt\Robo\Wizards
 */
class TestsWizard extends Wizard {

  /**
   * Prompts user to generate valid Behat configuration file.
   */
  public function wizardConfigureBehat() {
    $behat_local_config_file = $this->getConfigValue('repo.root') . '/tests/behat/local.yml';
    if (!file_exists($behat_local_config_file) || !$this->isBehatConfigured()) {
      $this->logger->warning('Behat is not configured properly.');
      $this->say("BLT can (re)generate tests/behat/local.yml using tests/behat/example.local.yml.");
      $confirm = $this->confirm("Do you want (re)generate local Behat config in <comment>tests/behat/local.yml</comment>?", TRUE);
      if ($confirm) {
        $this->getConfigValue('composer.bin');
        $behat_local_config_file = $this->getConfigValue('repo.root') . "/tests/behat/local.yml";
        if (file_exists($behat_local_config_file)) {
          $this->fs->remove($behat_local_config_file);
        }
        $this->invokeCommand('tests:behat:init');
      }
    }
  }

  /**
   * Determines if Behat is properly configured on the local machine.
   *
   * This will ensure that required Behat file exists, and that require
   * configuration values are properly defined.
   *
   * @return bool
   *   TRUE is Behat is properly configured on the local machine.
   */
  public function isBehatConfigured() {
    // Verify that URIs required for Drupal and Behat are configured correctly.
    $local_behat_config = $this->getLocalBehatConfig();
    if ($this->getConfigValue('project.local.uri') != $local_behat_config->get('local.extensions.Behat\MinkExtension.base_url')) {
      $this->logger->warning('project.local.uri in blt.yml does not match local.extensions.Behat\MinkExtension.base_url in local.yml.');
      $this->logger->warning('project.local.uri = ' . $this->getConfigValue('project.local.uri'));
      $this->logger->warning('local.extensions.Behat\MinkExtension.base_url = ' . $local_behat_config->get('local.extensions.Behat\MinkExtension.base_url'));
      return FALSE;
    }

    // Verify that URIs required for an ad-hoc PHP internal server are
    // configured correctly.
    if ($this->getConfigValue('tests.run-server')) {
      if ($this->getConfigValue('tests.server.url') != $this->getConfigValue('project.local.uri')) {
        $this->logger->warning("tests.run-server is enabled, but the server URL does not match Drupal's base URL.");
        $this->logger->warning('project.local.uri = ' . $this->getConfigValue('project.local.uri'));
        $this->logger->warning('tests.server.url = ' . $this->getConfigValue('tests.server.url'));
        $this->logger->warning('local.extensions.Behat\MinkExtension.base_url = ' . $local_behat_config->get('local.extensions.Behat\MinkExtension.base_url'));

        return FALSE;
      }
    }

    // Verify that required Behat files are present.
    if (!$this->areBehatConfigFilesPresent()) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Gets the local behat configuration defined in local.yml.
   *
   * @return \Acquia\Blt\Robo\Config\BltConfig
   *   The local Behat configuration.
   */
  public function getLocalBehatConfig() {
    $behat_local_config_file = $this->getConfigValue('repo.root') . '/tests/behat/local.yml';

    $behat_local_config = new BltConfig();
    $loader = new YamlConfigLoader();
    $processor = new YamlConfigProcessor();
    $processor->extend($loader->load($behat_local_config_file));
    $processor->extend($loader->load($this->getConfigValue('repo.root') . '/tests/behat/behat.yml'));
    $behat_local_config->replace($processor->export());

    return $behat_local_config;
  }

  /**
   * Determines if required Behat files exist.
   *
   * @return bool
   *   TRUE if all required Behat files exist.
   */
  public function areBehatConfigFilesPresent() {
    return $this->getInspector()->filesExist($this->getBehatConfigFiles());
  }

  /**
   * Returns an array of required Behat files, as defined by Behat config.
   *
   * For instance, this will return the Drupal root dir, Behat features dir,
   * and bootstrap dir on the local file system. All of these files are
   * required for behat to function properly.
   *
   * @return array
   *   An array of required Behat configuration files.
   */
  public function getBehatConfigFiles() {
    $behat_local_config = $this->getLocalBehatConfig();

    return [
      $behat_local_config->get('local.extensions.Drupal\DrupalExtension.drupal.drupal_root'),
      $behat_local_config->get('local.suites.default.paths.features'),
    ];
  }

}
