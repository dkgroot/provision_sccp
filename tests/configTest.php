<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

include_once "lib/config.php";
use SCCP\Config as Config;

final class ConfigTest extends TestCase
{
    public function testCanBeCreated(): void
    {
    	global $base_path;
	$configParser = new Config\ConfigParser($base_path, "config.ini");
        $this->assertInstanceOf(
            Config\ConfigParser::class,
            $configParser
        );
    }
    public function testCanGetConfig(): void
    {
    	global $base_path;
	$configParser = new Config\ConfigParser($base_path, "config.ini");
	$config = $configParser->getConfiguration();
	$this->assertNotEmpty($config);
    }
}
