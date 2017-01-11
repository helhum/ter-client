<?php
namespace NamelessCoder\TYPO3RepositoryClient\tests\unit;

use NamelessCoder\TYPO3RepositoryClient\Security\UsernamePasswordCredentials;

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
