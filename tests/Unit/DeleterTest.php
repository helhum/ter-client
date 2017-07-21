<?php
namespace Helhum\TerClient\Tests\Unit;

use Helhum\TerClient\Connection;
use Helhum\TerClient\Deleter;

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
