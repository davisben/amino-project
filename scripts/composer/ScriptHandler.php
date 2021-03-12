<?php

namespace AminoProject\composer;

use Composer\Script\Event;
use Drupal\Core\Site\Settings;
use DrupalFinder\DrupalFinder;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

/**
 * Class containing compoer scripts.
 */
class ScriptHandler {

  /**
   * Create files required by Drupal.
   *
   * @param \Composer\Script\Event $event
   *   The composer event object.
   */
  public static function createRequiredFiles(Event $event) {
    $fs = new Filesystem();
    $drupalFinder = new DrupalFinder();
    $drupalFinder->locateRoot(getcwd());
    $drupalRoot = $drupalFinder->getDrupalRoot();

    $dirs = [
      'modules',
      'profiles',
      'themes',
    ];

    // Required for unit testing.
    foreach ($dirs as $dir) {
      if (!$fs->exists($drupalRoot . '/' . $dir)) {
        $fs->mkdir($drupalRoot . '/' . $dir);
        $fs->touch($drupalRoot . '/' . $dir . '/.gitkeep');
      }
    }

    // Prepare the settings file for installation.
    if (!$fs->exists($drupalRoot . '/sites/default/settings.php') && $fs->exists($drupalRoot . '/sites/default/default.settings.php')) {
      $fs->copy($drupalRoot . '/sites/default/default.settings.php', $drupalRoot . '/sites/default/settings.php');

      require_once $drupalRoot . '/core/includes/bootstrap.inc';
      require_once $drupalRoot . '/core/includes/install.inc';

      new Settings([]);
      $settings['settings']['config_sync_directory'] = (object) [
        'value' => Path::makeRelative($drupalFinder->getComposerRoot() . '/config/sync', $drupalRoot),
        'required' => TRUE,
      ];

      drupal_rewrite_settings($settings, $drupalRoot . '/sites/default/settings.php');

      $fs->chmod($drupalRoot . '/sites/default/settings.php', 0666);
      $event->getIO()->write('Created the sites/default/settings.php file');
    }

    // Create the public files directory.
    if (!$fs->exists($drupalRoot . '/sites/default/files')) {
      $oldmask = umask();
      $fs->mkdir($drupalRoot . '/sites/default/files', 0777);
      umask($oldmask);
      $event->getIO()->write('Created the sites/default/files directory');
    }
  }

  /**
   * Create the public files directory.
   *
   * @param \Composer\Script\Event $event
   *   The composer event object.
   */
  public static function checkPublicFilesDirectory(Event $event) {
    $fs = new Filesystem();
    $drupalFinder = new DrupalFinder();
    $drupalFinder->locateRoot(getcwd());
    $drupalRoot = $drupalFinder->getDrupalRoot();

    // Create the public files directory if it does not exist.
    if (!$fs->exists($drupalRoot . '/sites/default/files')) {
      $oldmask = umask();
      $fs->mkdir($drupalRoot . '/sites/default/files', 0777);
      umask($oldmask);
      $event->getIO()->write('Created the sites/default/files directory');
    }
  }

}
