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
   */
  public function __construct(GeneratorInterface $queue_generator) {
    $this->generator = $queue_generator;
    parent::__construct();
  }
  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('generate:queue')
      ->setDescription($this->trans('commands.generate.queue.description'))
      ->setHelp($this->trans('commands.generate.queue.help'))
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

  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    // @see use Drupal\Console\Command\Shared\ConfirmationTrait::confirmOperation
    if (!$this->confirmOperation()) {
        return 1;
    }
    $this->getIo()->info($this->trans('commands.generate.queue.messages.success'));
    $this->generator->generate([]);
  }
}
