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
    const FUNCTION_DELETEVERSION = 'deleteExtensionVersion';

    /**
     * @var string
     */
    protected $wsdl = null;

    /**
     * @param string $wsdl
     */
    public function __construct($wsdl = self::WSDL_URL)
    {
        $this->wsdl = $wsdl;
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

        $client = $this->getSoapClientForWsdl($this->wsdl);
        $output = $client->__soapCall($function, $parameters, ['exceptions' => true, 'trace' => true]);
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

    /**
     * @param string $wsdl
     * @return \SoapClient
     */
    protected function getSoapClientForWsdl($wsdl)
    {
        return new \SoapClient($wsdl);
    }
}
