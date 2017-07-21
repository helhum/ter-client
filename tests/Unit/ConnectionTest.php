<?php
namespace Helhum\TerClient\Tests\Unit;

use Helhum\TerClient\Connection;
use Helhum\TerClient\Security\UsernamePasswordCredentials;

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
     * @param mixed $expectedExceptionType
     */
    public function testCall($function, $output, $expectedExceptionMessage, $expectedExceptionType)
    {
        $parameters = array(
            'accountData' => array(
                'username' => 'usernamefoobar',
                'password' => 'passwordfoobar',
            ),
            'foo' => 'bar',
        );
        $settings = array('exceptions' => true, 'trace' => true);
        $clientMock = $this->getMockBuilder('SoapClient')->disableOriginalConstructor()->getMock();
        $clientMock->expects($this->once())->method('__soapCall')->with($function, $parameters, $settings)->will($this->returnValue($output));
        $connection = new Connection($clientMock);
        if (null !== $expectedExceptionType) {
            $this->setExpectedException($expectedExceptionType, $expectedExceptionMessage);
        }
        $connection->call(new UsernamePasswordCredentials('usernamefoobar', 'passwordfoobar'), $function, $parameters);
    }

    /**
     * @return array
     */
    public function getCallArguments()
    {
        return array(
            array('test', array(Connection::SOAP_RETURN_CODE => Connection::SOAP_CODE_SUCCESS), null, null),
            array('test', array(), 'TER command "test" failed without a return code', 'RuntimeException'),
            array('test', array(Connection::SOAP_RETURN_CODE => 123), 'TER command "test" failed; code was 123', 'RuntimeException'),
            array('test', new \SoapFault('Server', 'Probe error'), 'Probe error', 'SoapFault'),
        );
    }
}
