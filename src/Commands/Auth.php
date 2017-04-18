<?php namespace Danj\Spotify\Commands;

use Danj\Spotify\AuthHelper;
use Danj\Spotify\AuthenticationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Auth extends Command
{
    /**
     * Creates a new instance of the Auth command
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->auth = new AuthHelper;
    }

    /**
     * Sets the name, description and help message for this command
     * @return void
     */
    protected function configure()
    {
        $this->setName('login')
             ->setDescription('Allows the user to link their Spotify account')
             ->setHelp('This command allows you to authenticate the Spotify CLI Remote with your Spotify account');
    }

    /**
     * Executes the console command
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input = $input;

        if (!$this->auth->isAuthorised()) {
            $this->getAuthorisation();
        }else{
			$this->output->writeln('');
			$this->output->writeln('--------------------------------------------------------------');
			$this->output->writeln('| <info>You\'re already authenticated!</info>');
			$this->output->writeln('| Use the <comment>list</comment> command to see available commands');
			$this->output->writeln('--------------------------------------------------------------');
			$this->output->writeln('');
		}
    }

    /**
     * Refreshes the access token if they have one, otherwise authorisation
     * from the user will be requested
     * @return void
     */
    private function getAuthorisation()
    {
        if($this->auth->attemptOrRefreshAuthentication()){
            return true;
        }else{
            $this->requestAuthorisationFromUser();
        }
    }

    /**
     * Instructs the user how to get an authorisation code, and then initiates
     * the prompt for that code.
     * @return void
     */
    public function requestAuthorisationFromUser()
    {
        $this->output->writeln('');
        $this->output->writeln('--------------------------------------------------------------');
        $this->output->writeln('| Please visit the following URL to link your Spotify account,');
        $this->output->writeln("| <info>".$this->auth->getBaseUrl()."</info>");
        $this->output->writeln('| <comment>Then paste the authorisation code below</comment>');
        $this->output->writeln('--------------------------------------------------------------');
        $this->output->writeln('');

        $this->askForAuthorisationCode();
    }

    /**
     * Prompts the user for the authorisation code to their account
     * @return void
     */
    private function askForAuthorisationCode()
    {
        $helper = $this->getHelper('question');
        $question = new Question('Authorization Code: ', false);

        if (!$authKey = $helper->ask($this->input, $this->output, $question)) {
            throw new AuthenticationException('Authorization code cannot be blank');
        }

        if ($tokens = $this->auth->requestRefreshAndAccessToken($authKey)) {
            $this->auth->storeNewTokens($tokens);
            $this->output->writeln('<info>Authentication complete</info>');
            $this->output->writeln('Use the <comment>list</comment> command to see available commands');
        }
    }

}
