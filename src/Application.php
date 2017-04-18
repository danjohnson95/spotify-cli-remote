<?php namespace Danj\Spotify;

use Danj\Spotify\AuthHelper;
use Danj\Spotify\AuthenticationException;
use Symfony\Component\Console\Application as BaseApp;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApp
{
    protected $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new AuthHelper;
    }

    public function start(array $classes)
    {
        foreach ($classes as $class) {
            $this->add(new $class);
        }
        return $this->run();
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if($this->getCommandName($input) != "login" && !$this->auth->attemptOrRefreshAuthentication()){
            return $this->promptForAuthentication();
        }

        parent::doRun($input, $output);

    }

    private function promptForAuthentication()
    {
        throw new AuthenticationException("Run the login command first");
    }
}
