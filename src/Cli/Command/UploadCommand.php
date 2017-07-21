<?php
namespace Helhum\TerClient\Cli\Command;

use Helhum\TerClient\Connection;
use Helhum\TerClient\ExtensionUploadPacker;
use Helhum\TerClient\Security\UsernamePasswordCredentials;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class UploadCommand extends Command
{
    /**
     * Configure
     */
    protected function configure()
    {
        $this->setName('upload')
            ->addArgument(
                'extensionKey',
                InputArgument::REQUIRED,
                'Extension key'
            )
            ->addArgument(
                'directory',
                InputArgument::REQUIRED,
                'The directory the extension resides in'
            )
            ->addOption(
                'username',
                '-u',
                InputOption::VALUE_OPTIONAL,
                'Username of account which is owner of the extension'
            )
            ->addOption(
                'password',
                '-p',
                InputOption::VALUE_OPTIONAL,
                'Password of account which is owner of the extension'
            )
            ->addOption(
                'upload-comment',
                '-m',
                InputOption::VALUE_OPTIONAL,
                'Upload comment',
                'Uploaded with ter-client'
            )
            ->addOption(
                'wsd-url',
                null,
                InputOption::VALUE_OPTIONAL,
                'Alternative WSD URL / SOAP Endpoint',
                'https://typo3.org/wsdl/tx_ter_wsdl.php'
            );
        $this->setDescription('Uploads the given directory to TER');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getOption('username');
        $password = $input->getOption('password');
        while (empty($username)) {
            $username = $this->ask($input, $output, '<comment>Please specify a username:</comment> ');
        }
        while (empty($password)) {
            $password = $this->ask($input, $output, '<comment>Please specify a password:</comment> ', true);
        }
        $input->setOption('username', $username);
        $input->setOption('password', $password);
    }


    /**
     * Execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     * @throws \SoapFault
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $extensionKey = $input->getArgument('extensionKey');
        $directory = realpath($input->getArgument('directory'));
        $username = $input->getOption('username');
        $password = $input->getOption('password');
        $comment = $input->getOption('upload-comment');
        $wsdUrl = $input->getOption('wsd-url');

        $uploadPacker = new ExtensionUploadPacker();
        $connection = Connection::create($wsdUrl);
        $result = $connection->upload(
            new UsernamePasswordCredentials($username, $password),
            $uploadPacker->pack($extensionKey, $directory, $comment)
        );

        if (isset($result[Connection::SOAP_RETURN_VERSION])) {
            $output->writeln('<info>Successfully uploaded new version: ' . $result[Connection::SOAP_RETURN_VERSION] . '</info>');
        }
        if (isset($result[Connection::SOAP_RETURN_MESSAGES])) {
            foreach ($result[Connection::SOAP_RETURN_MESSAGES] as $index => $message) {
                $output->writeln('<comment>Message #' . ($index + 1) . ':</comment> ' . $message);
            }
        }

        return 0;
    }

    /**
     * Asks a question to the user
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string|array $question The question to ask. If an array each array item is turned into one line of a multi-line question
     * @param bool $hidden
     * @return string The user answer
     */
    private function ask(InputInterface $input, OutputInterface $output, $question, $hidden = false)
    {
        $questionHelper = new QuestionHelper();
        $helperSet = new HelperSet(array(new FormatterHelper()));
        $questionHelper->setHelperSet($helperSet);

        $question = new Question($question);
        $question
            ->setHidden($hidden)
            ->setHiddenFallback(true);

        return $questionHelper->ask($input, $output, $question);
    }
}
