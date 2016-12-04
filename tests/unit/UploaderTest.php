<?php
namespace NamelessCoder\TYPO3RepositoryClient\tests\unit;

use NamelessCoder\TYPO3RepositoryClient\Connection;
use NamelessCoder\TYPO3RepositoryClient\ExtensionUploadPacker;
use NamelessCoder\TYPO3RepositoryClient\Uploader;

/**
 * Class UploaderTest
 */
class UploaderTest extends \PHPUnit_Framework_TestCase
{
    public function testUpload()
    {
        $mockConnection = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $mockConnection->expects($this->once())->method('call')->will($this->returnValue('foobarbaz'));
        $mockPacker = $this->getMockBuilder(ExtensionUploadPacker::class)->getMock();
        $mockPacker->expects($this->once())->method('pack')->will($this->returnValue([]));
        $uploader = new Uploader($mockConnection, $mockPacker);
        $result = $uploader->upload('foo', 'bar', 'baz');
        $this->assertEquals('foobarbaz', $result);
    }
}
