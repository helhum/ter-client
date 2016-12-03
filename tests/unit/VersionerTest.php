<?php
namespace NamelessCoder\TYPO3RepositoryClient\tests\unit;

use NamelessCoder\TYPO3RepositoryClient\Versioner;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamWrapper;

/**
 * Class VersionerTest
 */
class VersionerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var array
     */
    protected static $fixture = [
        'title' => 'Dummy title', 'description' => 'Dummy description',
        'category' => 'misc', 'shy' => 0, 'version' => '1.2.3', 'dependencies' => 'cms,extbase,fluid',
        'conflicts' => '', 'priority' => '', 'loadOrder' => '', 'module' => '', 'state' => 'beta',
        'uploadfolder' => 0, 'createDirs' => '', 'modify_tables' => '', 'clearcacheonload' => 1,
        'lockType' => '', 'author' => 'Author Name', 'author_email' => 'author@domain.com',
        'author_company' => '', 'CGLcompliance' => '', 'CGLcompliance_note' => '',
        'constraints' => ['depends' => ['typo3' => '6.1.0-6.2.99', 'cms' => ''], 'conflicts' => [], 'suggests' => []],
        '_md5_values_when_last_written' => ''
    ];

    /**
     * @var string
     */
    protected static $fixtureString = null;

    public static function setUpBeforeClass()
    {
        self::$fixtureString = '<' . '?php' . PHP_EOL . '$EM_CONF[$_EXTKEY] = ' . var_export(self::$fixture, true) . ';' . PHP_EOL;
        $emConf = new vfsStreamFile(Versioner::FILENAME_EXTENSIONCONFIGURATION);
        $emConf->setContent(self::$fixtureString);
        $composer = new vfsStreamFile(Versioner::FILENAME_COMPOSER);
        $composer->setContent(json_encode(self::$fixture, JSON_UNESCAPED_SLASHES));
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('temp', 0777));
        vfsStreamWrapper::getRoot()->addChild($emConf);
        vfsStreamWrapper::getRoot()->addChild($composer);
    }

    public function testRead()
    {
        $return = [
            Versioner::PARAMETER_VERSION => '1.2.3',
            Versioner::PARAMETER_STABILITY => Versioner::STABILITY_STABLE
        ];
        $versioner = $this->getMock(
            'NamelessCoder\\TYPO3RepositoryClient\\Versioner',
            ['getExtensionConfigurationFilename', 'readExtensionConfigurationFile']
        );
        $versioner->expects($this->once())->method('getExtensionConfigurationFilename');
        $versioner->expects($this->once())->method('readExtensionConfigurationFile')->will($this->returnValue($return));
        $result = $versioner->read('.');
        $this->assertEquals(['1.2.3', Versioner::STABILITY_STABLE], $result);
    }

    /**
     * @param bool $composerUnwritable
     * @param bool $extensionConfigurationUnwritable
     * @dataProvider getWriteTestValues
     */
    public function testWrite($composerUnwritable, $extensionConfigurationUnwritable)
    {
        $versioner = $this->getMock(
            'NamelessCoder\\TYPO3RepositoryClient\\Versioner',
            ['getExtensionConfigurationFilename', 'getComposerFilename', 'writeComposerFile', 'writeExtensionConfigurationFile']
        );
        $versioner->expects($this->once())->method('getExtensionConfigurationFilename');
        $versioner->expects($this->once())->method('getComposerFilename');
        if (true === $composerUnwritable) {
            $this->setExpectedException('RuntimeException');
            $versioner->expects($this->once())->method('writeComposerFile')->will($this->returnValue(false));
        } else {
            $versioner->expects($this->once())->method('writeComposerFile')->will($this->returnValue(true));
            if (true === $extensionConfigurationUnwritable) {
                $versioner->expects($this->once())->method('writeExtensionConfigurationFile')->will($this->returnValue(true));
            } else {
                $this->setExpectedException('RuntimeException');
                $versioner->expects($this->once())->method('writeExtensionConfigurationFile')->will($this->returnValue(false));
            }
        }
        $result = $versioner->write('.', '1.2.3', 'stable');
        if (false === $composerUnwritable && false === $extensionConfigurationUnwritable) {
            $this->assertTrue($result);
        }
    }

    /**
     * @return array
     */
    public function getWriteTestValues()
    {
        return [
            [false, false],
            [true, false],
            [false, true],
            [true, true],
        ];
    }

    /**
     * @dataProvider getGetComposerFilenameTestValues
     * @param string $directory
     * @param string $expected
     */
    public function testGetComposerFilename($directory, $expected)
    {
        $versioner = new Versioner();
        $method = new \ReflectionMethod($versioner, 'getComposerFilename');
        $method->setAccessible(true);
        $result = $method->invokeArgs($versioner, [$directory]);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getGetComposerFilenameTestValues()
    {
        return [
            ['/foo/bar', '/foo/bar/composer.json'],
            ['/foo/bar/', '/foo/bar/composer.json']
        ];
    }

    /**
     * @dataProvider getGetExtensionConfigurationFilenameTestValues
     * @param string $directory
     * @param string $expected
     */
    public function testExtensionConfigurationFilename($directory, $expected)
    {
        $versioner = new Versioner();
        $method = new \ReflectionMethod($versioner, 'getExtensionConfigurationFilename');
        $method->setAccessible(true);
        $result = $method->invokeArgs($versioner, [$directory]);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getGetExtensionConfigurationFilenameTestValues()
    {
        return [
            ['/foo/bar', '/foo/bar/ext_emconf.php'],
            ['/foo/bar/', '/foo/bar/ext_emconf.php']
        ];
    }

    /**
     * @param string $filename
     * @param string $data
     * @param bool $expectsException
     * @dataProvider getReadComposerFileTestValues
     */
    public function testReadComposerFile($filename, $expectedData, $expectsException)
    {
        $versioner = new Versioner();
        $method = new \ReflectionMethod($versioner, 'readComposerFile');
        $method->setAccessible(true);
        if (true === $expectsException) {
            $this->setExpectedException('RuntimeException');
        }
        $result = $method->invokeArgs($versioner, [$filename]);
        $this->assertEquals($expectedData, $result);
    }

    /**
     * @return array
     */
    public function getReadComposerFileTestValues()
    {
        return [
            [vfsStream::url('temp/' . Versioner::FILENAME_COMPOSER), self::$fixture, false],
            [vfsStream::url('temp-does-not-exist/' . Versioner::FILENAME_COMPOSER), null, true],
        ];
    }

    /**
     * @param string $filename
     * @param string $data
     * @param bool $expectsException
     * @dataProvider getReadExtensionConfigurationFileTestValues
     */
    public function testReadExtensionConfigurationFile($filename, $expectedData, $expectsException)
    {
        $versioner = new Versioner();
        $method = new \ReflectionMethod($versioner, 'readExtensionConfigurationFile');
        $method->setAccessible(true);
        if (true === $expectsException) {
            $this->setExpectedException('RuntimeException');
        }
        $result = $method->invokeArgs($versioner, [$filename]);
        $this->assertEquals($expectedData, $result);
    }

    /**
     * @return array
     */
    public function getReadExtensionConfigurationFileTestValues()
    {
        return [
            [vfsStream::url('temp/' . Versioner::FILENAME_EXTENSIONCONFIGURATION), self::$fixture, false],
            [vfsStream::url('temp-does-not-exist/' . Versioner::FILENAME_EXTENSIONCONFIGURATION), null, true],
        ];
    }

    /**
     * @param string $filename
     * @param string $version
     * @param bool $expectsException
     * @dataProvider getWriteComposerFileTestValues
     */
    public function testWriteComposerFile($filename, $version, $expectsException)
    {
        $expectedData = json_encode(array_merge(self::$fixture, ['version' => $version]), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $versioner = new Versioner();
        $method = new \ReflectionMethod($versioner, 'writeComposerFile');
        $method->setAccessible(true);
        if (true === $expectsException) {
            $this->setExpectedException('RuntimeException');
        }
        $result = $method->invokeArgs($versioner, [$filename, $version]);
        $this->assertStringEqualsFile($filename, $expectedData);
    }

    /**
     * @return array
     */
    public function getWriteComposerFileTestValues()
    {
        return [
            [vfsStream::url('temp/' . Versioner::FILENAME_COMPOSER), '1.2.3', false],
            [vfsStream::url('temp/' . Versioner::FILENAME_COMPOSER), '3.2.1', false],
            [vfsStream::url('temp-does-not-exist/' . Versioner::FILENAME_COMPOSER), '1.2.3', true],
        ];
    }

    public function testWriteComposerFileReturnsWithoutWritingFileIfFileDoesNotContainVersion()
    {
        $versioner = new Versioner();
        $method = new \ReflectionMethod($versioner, 'writeComposerFile');
        $method->setAccessible(true);
        $fixture = self::$fixture;
        unset($fixture['version']);
        $noVersionFile = Versioner::FILENAME_COMPOSER . '.noversion.json';
        $newComposerFile = new vfsStreamFile($noVersionFile);
        $newComposerFile->setContent(json_encode($fixture, JSON_UNESCAPED_SLASHES));
        vfsStreamWrapper::getRoot()->addChild($newComposerFile);
        $vfsUrl = vfsStream::url('temp/' . $noVersionFile);
        $result = $method->invokeArgs($versioner, [$vfsUrl, '1.2.3']);
        $this->assertTrue($result);
        $this->assertNotContains('1.2.3', file_get_contents($vfsUrl));
    }

    /**
     * @param string $filename
     * @param string $version
     * @param string $stability
     * @param bool $expectsException
     * @dataProvider getWriteExtensionConfigurationFileTestValues
     */
    public function testWriteExtensionConfigurationFile($filename, $version, $stability, $expectsException)
    {
        $fixture = self::$fixture;
        $fixture['version'] = $version;
        $fixture['state'] = $stability;
        $expectedData = '<' . '?php' . PHP_EOL . '$EM_CONF[$_EXTKEY] = ' . var_export($fixture, true) . ';' . PHP_EOL;
        $versioner = new Versioner();
        $method = new \ReflectionMethod($versioner, 'writeExtensionConfigurationFile');
        $method->setAccessible(true);
        if (true === $expectsException) {
            $this->setExpectedException('RuntimeException');
        }
        $result = $method->invokeArgs($versioner, [$filename, $version, $stability]);
        $this->assertStringEqualsFile($filename, $expectedData);
    }

    /**
     * @return array
     */
    public function getWriteExtensionConfigurationFileTestValues()
    {
        return [
            [vfsStream::url('temp/' . Versioner::FILENAME_EXTENSIONCONFIGURATION), '1.2.3', Versioner::STABILITY_STABLE, false],
            [vfsStream::url('temp/' . Versioner::FILENAME_EXTENSIONCONFIGURATION), '3.2.1', Versioner::STABILITY_BETA, false],
            [vfsStream::url('temp-does-not-exist/' . Versioner::FILENAME_EXTENSIONCONFIGURATION), '1.2.3', Versioner::STABILITY_STABLE, true],
        ];
    }
}
