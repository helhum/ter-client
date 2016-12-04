<?php
namespace NamelessCoder\TYPO3RepositoryClient\tests\unit;

use NamelessCoder\TYPO3RepositoryClient\Connection;
use NamelessCoder\TYPO3RepositoryClient\Deleter;

/**
 * Class DeleterTest
 */
class DeleterTest extends \PHPUnit_Framework_TestCase
{
    public function testDelete()
    {
        $mockConnection = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $mockConnection->expects($this->once())->method('call')->will($this->returnValue('foobarbaz'));
        $deleter = new Deleter($mockConnection);
        $this->assertEquals('foobarbaz', $deleter->deleteExtensionVersion('foo', '1.2.3', 'user', 'pass'));
    }
}
