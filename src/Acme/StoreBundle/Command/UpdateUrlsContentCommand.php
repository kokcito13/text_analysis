<?php

namespace Acme\StoreBundle\Command;

use Acme\StoreBundle\Service\UrlsWorker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUrlsContentCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('urls:get:content')
            ->setDescription('get content from url');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var UrlsWorker $urlsWorker */
        $urlsWorker = $this->getApplication()->getKernel()->getContainer()->get('urls.worker');
        $urlsWorker->getContent();
    }
}
