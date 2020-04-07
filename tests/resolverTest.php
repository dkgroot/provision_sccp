<?php declare(strict_types=1);

require(implode(DIRECTORY_SEPARATOR, array(
    __DIR__,
    '..',
    'vendor',
    'autoload.php'
)));

use PHPUnit\Framework\TestCase;
use PROVISION\ConfigParser as ConfigParser;
use PROVISION\Resolve as Resolve;
use PROVISION\ResolveResult as ResolveResult;

final class ResolverTest extends TestCase
{
    private function getConfig()
    {
	$base_path = realpath(__DIR__ . DIRECTORY_SEPARATOR . "..");
	$configParser = new ConfigParser($base_path, "config.ini");
	return $configParser->getConfiguration();
    }    

    public function testCanBeCreated(): void
    {
    	$config = $this->getConfig();
        $resolve = new Resolve($config);
        $this->assertInstanceOf(
            Resolve::class,
            $resolve
        );
    }

    private $test_cases = Array(
            Array('request' => 'jar70sccp.9-4-2ES26.sbn', 'expected' => '/data/firmware/7970/jar70sccp.9-4-2ES26.sbn'),
            Array('request' => 'Russian_Russian_Federation/be-sccp.jar', 'expected' => '/data/locales/languages/Russian_Russian_Federation/be-sccp.jar'),
            Array('request' => 'Spain/g3-tones.xml', 'expected' => '/data/locales/countries/Spain/g3-tones.xml'),
            Array('request' => '320x196x4/Chan-SCCP-b.png', 'expected' => '/data/wallpapers/320x196x4/Chan-SCCP-b.png'),
            Array('request' => 'XMLDefault.cnf.xml', 'expected' => '/data/settings/XMLDefault.cnf.xml'),
            Array('request' => '../XMLDefault.cnf.xml', 'expected' => ResolveResult::RequestContainsPathWalk),
            Array('request' => 'XMLDefault.cnf.xml/../../text.xml', 'expected' => ResolveResult::RequestContainsPathWalk),
    );
    
    public function testCanResolveFiles(): void
    {
    	$base_path = realpath(__DIR__ . DIRECTORY_SEPARATOR . "..");
    	$config = $this->getConfig();
        
        $resolve = new Resolve($config);
        foreach($this->test_cases as $test) {
            try {
                $result = $resolve->resolve($test['request']);
                if (is_string($result)) {
                    $this->assertStringContainsString($result, $base_path . $test['expected']);
                } else {
                    $this->assertEquals($result, $test['expected']);
                }
            } catch (Exception $e) {
                print("'" . $test['request'] . "' => throws error as expected\n");
                print("Exception: " . $e->getMessage() . "\n");
            }
        }
        unset($resolve);
    }
}

