<?php namespace Danj\Spotify\Commands\Playback;

use Danj\Spotify\Library;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class Volume extends Command{

    protected function configure()
    {
        $this->setName('vol')
             ->addArgument('volume', InputArgument::REQUIRED, 'Volume between 0 to 100')
             ->setDescription('Controls the volume of the playback');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $Library = new Library;
        $Library->setVolume($input->getArgument("volume"));
    }
}
