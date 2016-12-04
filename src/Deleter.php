<?php
namespace NamelessCoder\TYPO3RepositoryClient;

/**
 * Class Deleter
 */
class Deleter
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * Deleter constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection = null)
    {
        $this->connection = $connection ?: new Connection();
    }

    /**
     * @param string $extensionKey
     * @param string $version
     * @param string $username
     * @param string $password
     * @return array
     * @throws \SoapFault
     */
    public function deleteExtensionVersion($extensionKey, $version, $username, $password)
    {
        $payload = [
            'extensionKey' => $extensionKey,
            'version' => $version
        ];
        return $this->connection->call(
            Connection::FUNCTION_DELETEVERSION,
            $payload,
            $username,
            $password
        );
    }
}
