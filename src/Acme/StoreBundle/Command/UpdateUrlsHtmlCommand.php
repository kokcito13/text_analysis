<?php

namespace Acme\StoreBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUrlsHtmlCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('urls:get:html')
            ->setDescription('get html from url');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $urlsWorker = $this->getApplication()->getKernel()->getContainer()->get('urls.worker');
        $urlsWorker->getHtml();
    }
}
