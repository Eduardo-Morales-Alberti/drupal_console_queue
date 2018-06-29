<?php

namespace Drupal\drupal_console_queue\Generator;

use Drupal\Console\Core\Generator\Generator;
use Drupal\Console\Core\Generator\GeneratorInterface;
use Drupal\Console\Extension\Manager;

/**
 * Class QueueWorkerGenerator.
 *
 * @package Drupal\Console\Generator
 */
class QueueWorkerGenerator extends Generator implements GeneratorInterface {

  /**
   * Extension Manager.
   *
   * @var \Drupal\Console\Extension\Manager
   */
  protected $extensionManager;

  /**
   * PluginQueueWorker constructor.
   *
   * @param \Drupal\Console\Extension\Manager $extensionManager
   *   Extension manager.
   */
  public function __construct(
       Manager $extensionManager
   ) {
    $this->extensionManager = $extensionManager;
  }

  /**
   * {@inheritdoc}
   */
  public function generate(array $parameters) {
    $module = $parameters['module'];
    $queue_file_name = $parameters['queue_file_name'];
    $this->renderFile(
      'module/src/Plugin/QueueWorker/queue_worker.php.twig',
      $this->extensionManager->getPluginPath($module, 'QueueWorker') . '/' . $queue_file_name . '.php',
      $parameters
    );
  }

}
