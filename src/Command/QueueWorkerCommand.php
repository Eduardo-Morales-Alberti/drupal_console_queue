<?php

namespace Drupal\test\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Drupal\Console\Core\Command\ContainerAwareCommand;
use Drupal\Console\Annotations\DrupalCommand;
use Drupal\Console\Core\Generator\GeneratorInterface;
use Drupal\Console\Command\Shared\ModuleTrait;
use Drupal\Console\Command\Shared\ConfirmationTrait;

/**
 * Class QueueWorkerCommand.
 *
 * @DrupalCommand (
 *     extension="test",
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
   * Constructs a new QueueWorkerCommand object.
   *
   * @param \Drupal\Console\Generator\ModuleFileGenerator $queue_generator
   *   Queue Generator.
   *
   */
  public function __construct(
    GeneratorInterface $queue_generator
  ) {
    $this->generator = $queue_generator;
    parent::__construct();
  }
  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('generate:queue')
      ->setDescription($this->trans('commands.generate.plugin.queue.description'))
      ->setHelp($this->trans('commands.generate.plugin.queue.help'))
      ->addOption(
          'module',
          null,
          InputOption::VALUE_REQUIRED,
          'option'
      )->setAliases(['gqueue']);
  }

 /**
  * {@inheritdoc}
  */
  protected function interact(InputInterface $input, OutputInterface $output) {
    // --module option
    $this->getModuleOption();

    // --queue-file-class option
    $queue_file = $input->getOption('queue-file');
    if (!$queue_file) {
        $queue_file = $this->getIo()->ask(
            $this->trans('commands.generate.plugin.queue.questions.type-class'),
            'ExampleFieldType',
            function ($typeClass) {
                return $this->validator->validateClassName($typeClass);
            }
        );
        $input->setOption('type-class', $typeClass);
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
    $this->generator->generate([
      'module' => $module,
      'queue_file' => $queue_file,
      'queue_id' => $queue_id,
    ]);
    $this->chainQueue->addCommand('cache:rebuild', ['cache' => 'discovery']);

    return 0;
  }
}
