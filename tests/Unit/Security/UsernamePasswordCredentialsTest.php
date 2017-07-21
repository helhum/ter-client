<?php
namespace Helhum\TerClient\Tests\Unit;

use Helhum\TerClient\Security\UsernamePasswordCredentials;

/**
 * Class UsernamePasswordCredentialsTest
 */
class UsernamePasswordCredentialsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createsExpectedSoapData()
    {
        $upc = new UsernamePasswordCredentials('user', 'pass');
        $this->assertSame(
            array(
                'accountData' => array(
                    'username' => 'user',
                    'password' => 'pass'
                )
            ),
            $upc->createSoapAuthenticationData()
        );
    }
}
