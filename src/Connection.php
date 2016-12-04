<?php
namespace NamelessCoder\TYPO3RepositoryClient;

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
    public static function create($wsdl = self::WSDL_URL) {
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
     * @param string $function
     * @param array $parameters
     * @param string $username
     * @param string $password
     * @return array|bool
     * @throws \SoapFault
     */
    public function call($function, array $parameters, $username, $password)
    {
        $parameters = array_merge([
            'accountData' => [
                    'username' => $username,
                    'password' => $password
                ]
            ],
            $parameters
        );

        $output = $this->client->__soapCall($function, $parameters, ['exceptions' => true, 'trace' => true]);
        if (true === $output instanceof \SoapFault) {
            throw $output;
        }
        if (false === isset($output[self::SOAP_RETURN_CODE])) {
            throw new \RuntimeException('TER command "' . $function . '" failed without a return code');
        }
        if (self::SOAP_CODE_SUCCESS !== (integer) $output[self::SOAP_RETURN_CODE]) {
            throw new \RuntimeException('TER command "' . $function . '" failed; code was ' . $output[self::SOAP_RETURN_CODE]);
        }
        return $output;
    }
}
