<?php
namespace NamelessCoder\TYPO3RepositoryClient;

use NamelessCoder\TYPO3RepositoryClient\Security\CredentialsInterface;

/**
 * Class Connection
 */
class Connection
{
    const SOAP_RETURN_CODE = 'resultCode';
    const SOAP_RETURN_MESSAGES = 'resultMessages';
    const SOAP_RETURN_VERSION = 'version';
    const SOAP_CODE_SUCCESS = 10504;
    const WSDL_URL = 'https://typo3.org/wsdl/tx_ter_wsdl.php';
    const WSDL_NAMESPACE = 'https://www.typo3.org/wsdl/tx_ter/';
    const FUNCTION_UPLOAD = 'uploadExtension';
    const FUNCTION_DELETEVERSION = 'deleteExtension';

    /**
     * @var \SoapClient
     */
    private $client;

    /**
     * @param string $wsdl
     * @return Connection
     */
    public static function create($wsdl = self::WSDL_URL)
    {
        return new self(new \SoapClient($wsdl));
    }

    /**
     * @param \SoapClient $client
     */
    public function __construct(\SoapClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param CredentialsInterface $credentials
     * @param string $function
     * @param array $parameters
     * @return array|bool
     * @throws \SoapFault
     */
    public function call(CredentialsInterface $credentials, $function, array $parameters)
    {
        $parameters = array_merge(
            $credentials->createSoapAuthenticationData(),
            $parameters
        );

        $output = $this->client->__soapCall($function, $parameters, array('exceptions' => true, 'trace' => true));
        if ($output instanceof \SoapFault) {
            throw $output;
        }
        if (!isset($output[self::SOAP_RETURN_CODE])) {
            throw new \RuntimeException('TER command "' . $function . '" failed without a return code');
        }
        if (self::SOAP_CODE_SUCCESS !== (int)$output[self::SOAP_RETURN_CODE]) {
            throw new \RuntimeException('TER command "' . $function . '" failed; code was ' . $output[self::SOAP_RETURN_CODE]);
        }
        return $output;
    }

    /**
     * @param CredentialsInterface $credentials
     * @param array $extensionData
     * @return array|bool
     * @throws \SoapFault
     */
    public function upload(CredentialsInterface $credentials, array $extensionData)
    {
        return $this->call(
            $credentials,
            self::FUNCTION_UPLOAD,
            $extensionData
        );
    }
}
