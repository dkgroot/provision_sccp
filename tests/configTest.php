<?php declare(strict_types=1);

require(implode(DIRECTORY_SEPARATOR, array(
    __DIR__,
    '..',
    'vendor',
    'autoload.php'
)));

use PHPUnit\Framework\TestCase;
use PROVISION\ConfigParser as ConfigParser;

final class ConfigParserTest extends TestCase
{
    public function testCanBeCreated(): void
    {
	$base_path = realpath(__DIR__ . DIRECTORY_SEPARATOR . "..");
	$configParser = new ConfigParser($base_path, "config.ini");
        $this->assertInstanceOf(
            ConfigParser::class,
            $configParser
        );
    }
    public function testCanGetConfig(): void
    {
	$base_path = realpath(__DIR__ . DIRECTORY_SEPARATOR . "..");
	$configParser = new ConfigParser($base_path, "config.ini");
	$config = $configParser->getConfiguration();
	$this->assertNotEmpty($config);
    }
}
