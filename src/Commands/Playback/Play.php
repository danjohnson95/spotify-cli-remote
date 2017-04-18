<?php namespace Danj\Spotify\Commands\Playback;

use Danj\Spotify\Library;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Play extends Command{

    protected function configure()
    {
        $this->setName('play')
             ->setDescription('Starts or resumes playback');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $Library = new Library;
        $Library->play();
    }
}
