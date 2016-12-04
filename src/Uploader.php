<?php
namespace NamelessCoder\TYPO3RepositoryClient;

use NamelessCoder\TYPO3RepositoryClient\Security\UsernamePasswordCredentials;

/**
 * Class Uploader
 */
class Uploader
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ExtensionUploadPacker
     */
    private $packer;

    /**
     * Uploader constructor.
     *
     * @param Connection $connection
     * @param ExtensionUploadPacker $packer
     */
    public function __construct(Connection $connection, ExtensionUploadPacker $packer = null)
    {
        $this->connection = $connection;
        $this->packer = $packer ?: new ExtensionUploadPacker();
    }

    /**
     * @param string $directory
     * @param string $username
     * @param string $password
     * @param string $comment
     * @param string $extensionKey
     * @return array
     * @throws \RuntimeException
     * @throws \SoapFault
     */
    public function upload($directory, $username, $password, $comment = '', $extensionKey = null)
    {
        return $this->connection->call(
            new UsernamePasswordCredentials($username, $password),
            Connection::FUNCTION_UPLOAD,
            $this->packer->pack($directory, $comment, $extensionKey)
        );
    }
}
