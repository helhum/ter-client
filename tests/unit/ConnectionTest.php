<?php
namespace NamelessCoder\TYPO3RepositoryClient\tests\unit;

use NamelessCoder\TYPO3RepositoryClient\Connection;

/**
 * Class ConnectionTest
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getCallArguments
     * @param string $function
     * @param mixed $output
     * @param string $expectedExceptionMessage
     */
    public function testCall($function, $output, $expectedExceptionMessage, $expectedExceptionType)
    {
        $parameters = [
            'accountData' => [
                'username' => 'usernamefoobar',
                'password' => 'passwordfoobar',
            ],
            'foo' => 'bar'
        ];
        $settings = ['exceptions' => true, 'trace' => true];
        $clientMock = $this->getMockBuilder('SoapClient')->disableOriginalConstructor()->getMock();
        $clientMock->expects($this->once())->method('__soapCall')->with($function, $parameters, $settings)->will($this->returnValue($output));
        $connection = new Connection($clientMock);
        if (null !== $expectedExceptionType) {
            $this->setExpectedException($expectedExceptionType, $expectedExceptionMessage);
        }
        $connection->call($function, $parameters, 'usernamefoobar', 'passwordfoobar');
    }

    /**
     * @return array
     */
    public function getCallArguments()
    {
        return [
            ['test', [Connection::SOAP_RETURN_CODE => Connection::SOAP_CODE_SUCCESS], null, null],
            ['test', [], 'TER command "test" failed without a return code', 'RuntimeException'],
            ['test', [Connection::SOAP_RETURN_CODE => 123], 'TER command "test" failed; code was 123', 'RuntimeException'],
            ['test', new \SoapFault('Server', 'Probe error'), 'Probe error', 'SoapFault']
        ];
    }
}
