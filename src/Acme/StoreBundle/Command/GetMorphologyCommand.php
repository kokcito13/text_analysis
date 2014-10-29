<?php

namespace Acme\StoreBundle\Command;

use Acme\StoreBundle\Service\Morphology;
use Acme\StoreBundle\Service\UrlsWorker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetMorphologyCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mor:get:word')
            ->setDescription('get html from url');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Morphology $urlsWorker */
        $urlsWorker = $this->getApplication()->getKernel()->getContainer()->get('morphology');
        $urlsWorker->getWords('бег');
    }
}
