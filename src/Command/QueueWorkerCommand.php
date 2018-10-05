<?php

namespace Drupal\drupal_console_queue\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Drupal\Console\Core\Command\ContainerAwareCommand;
use Drupal\Console\Core\Generator\GeneratorInterface;
use Drupal\Console\Command\Shared\ModuleTrait;
use Drupal\Console\Command\Shared\ConfirmationTrait;
use Symfony\Component\Console\Input\InputOption;
use Drupal\Console\Extension\Manager;
use Drupal\Console\Annotations\DrupalCommand;
use Drupal\Console\Utils\Validator;
use Drupal\Console\Core\Utils\StringConverter;

/**
 * Class QueueWorkerCommand.
 *
 * @DrupalCommand (
 *     extension="drupal_console_queue",
 *     extensionType="module"
 * )
 */
class QueueWorkerCommand extends ContainerAwareCommand {

  use ModuleTrait;
  use ConfirmationTrait;

  /**
   * Drupal\Console\Core\Generator\GeneratorInterface definition.
   *
   * @var \Drupal\Console\Core\Generator\GeneratorInterface
   */
  protected $generator;

  /**
   * Extension Manager.
   *
   * @var \Drupal\Console\Extension\Manager
   */
  protected $extensionManager;

  /**
   * Validator.
   *
   * @var \Drupal\Console\Utils\Validator
   */
  protected $validator;

  /**
   * String converter.
   *
   * @var \Drupal\Console\Core\Utils\StringConverter
   */
  protected $stringConverter;

  /**
   * Constructs a new QueueWorkerCommand object.
   *
   * @param \Drupal\Console\Core\Generator\GeneratorInterface $queue_generator
   *   Queue Generator.
   * @param \Drupal\Console\Extension\Manager $extensionManager
   *   Extension manager.
   * @param \Drupal\Console\Utils\Validator $validator
   *   Validator.
   * @param \Drupal\Console\Core\Utils\StringConverter $stringConverter
   *   String Converter.
   */
  public function __construct(
    GeneratorInterface $queue_generator,
    Manager $extensionManager,
    Validator $validator,
    StringConverter $stringConverter
  ) {
    $this->generator = $queue_generator;
    $this->extensionManager = $extensionManager;
    $this->validator = $validator;
    $this->stringConverter = $stringConverter;
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('generate:plugin:queue')
      ->setDescription($this->trans('commands.generate.plugin.queue.description'))
      ->setHelp($this->trans('commands.generate.plugin.queue.help'))
      ->addOption(
          'module',
          NULL,
          InputOption::VALUE_REQUIRED,
          $this->trans('commands.generate.plugin.queue.options.module')
      )
      ->addOption(
          'queue-file',
          NULL,
          InputOption::VALUE_REQUIRED,
          $this->trans('commands.generate.plugin.queue.options.queue-file')
      )
      ->addOption(
          'queue-id',
          NULL,
          InputOption::VALUE_REQUIRED,
          $this->trans('commands.generate.plugin.queue.options.queue-id')
      )
      ->addOption(
          'cron-time',
          NULL,
          InputOption::VALUE_REQUIRED,
          $this->trans('commands.generate.plugin.queue.options.cron-time')
      )
      ->addOption(
          'label',
          NULL,
          InputOption::VALUE_REQUIRED,
          $this->trans('commands.generate.plugin.queue.options.label')
      )
      ->setAliases(['gpqueue']);
  }

  /**
   * {@inheritdoc}
   */
  protected function interact(InputInterface $input, OutputInterface $output) {
    // --module option.
    $this->getModuleOption();

    // --queue-file-class option.
    $queue_file = $input->getOption('queue-file');
    if (!$queue_file) {
      $queue_file = $this->getIo()->ask(
            $this->trans('commands.generate.plugin.queue.questions.queue-file'),
            'ExampleQueue',
            function ($queue_file) {
              return $this->validator->validateClassName($queue_file);
            }
        );
      $input->setOption('queue-file', $queue_file);
    }

    // --queue-id option.
    $queue_id = $input->getOption('queue-id');
    if (!$queue_id) {
      $queue_id = $this->getIo()->ask(
          $this->trans('commands.generate.plugin.queue.questions.queue-id'),
          'example_queue_id',
          function ($queue_id) {
            return $this->stringConverter->camelCaseToUnderscore($queue_id);
          }
      );
      $input->setOption('queue-id', $queue_id);
    }

    // --cron-time option.
    $cron_time = $input->getOption('cron-time');
    if (!$cron_time) {
      $cron_time = $this->getIo()->ask(
          $this->trans('commands.generate.plugin.queue.questions.cron-time'),
          30
      );
      $input->setOption('cron-time', $cron_time);
    }

    // --label option.
    $label = $input->getOption('label');
    if (!$label) {
      $label = $this->getIo()->ask(
          $this->trans('commands.generate.plugin.queue.questions.label'),
          'Queue description.'
      );
      $input->setOption('label', $label);
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    // @see use Drupal\Console\Command\Shared\ConfirmationTrait::confirmOperation
    if (!$this->confirmOperation()) {
      return 1;
    }
    $module = $input->getOption('module');
    $queue_file = $input->getOption('queue-file');
    $queue_id = $input->getOption('queue-id');
    $cron_time = $input->getOption('cron-time');
    $label = $input->getOption('label');
    $this->generator->generate([
      'module' => $module,
      'queue_file_name' => $queue_file,
      'queue_id' => $queue_id,
      'cron_time' => $cron_time,
      'label' => $label,
    ]);
  }

}
