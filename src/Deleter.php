<?php
namespace Helhum\TerClient;

use Helhum\TerClient\Security\UsernamePasswordCredentials;

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
     * @throws \SoapFault
     * @return array
     */
    public function deleteExtensionVersion($extensionKey, $version, $username, $password)
    {
        $payload = array(
            'extensionKey' => $extensionKey,
            'version' => $version,
        );
        return $this->connection->call(
            new UsernamePasswordCredentials($username, $password),
            Connection::FUNCTION_DELETEVERSION,
            $payload
        );
    }
}
