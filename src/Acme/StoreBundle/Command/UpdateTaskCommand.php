<?php

namespace Acme\StoreBundle\Command;

use Acme\StoreBundle\Service\Morphology;
use Acme\StoreBundle\Service\UpdateTask;
use Acme\StoreBundle\Service\UrlsWorker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateTaskCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('task:update')
            ->setDescription('update task document');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var UpdateTask $updateTask */
        $updateTask = $this->getApplication()->getKernel()->getContainer()->get('update.task');
        $updateTask->update();
    }
}
