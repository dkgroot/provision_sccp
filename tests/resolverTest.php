<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

include_once "lib/resolve.php";
use SCCP\Resolve as Resolve;

final class ResolverTest extends TestCase
{
    private function getConfig()
    {
    	global $base_path;
	$configParser = new SCCP\Config\ConfigParser($base_path, "config.ini");
	return $configParser->getConfiguration();
    }    

    public function testCanBeCreated(): void
    {
        //global $config;
    	$config = $this->getConfig();
        $resolve = new \SCCP\Resolve\Resolve($config);
        $this->assertInstanceOf(
            \SCCP\Resolve\Resolve::class,
            $resolve
        );
    }

    private $test_cases = Array(
            Array('request' => 'jar70sccp.9-4-2ES26.sbn', 'expected' => '/data/firmware/7970/jar70sccp.9-4-2ES26.sbn'),
            Array('request' => 'Russian_Russian_Federation/be-sccp.jar', 'expected' => '/data/locales/languages/Russian_Russian_Federation/be-sccp.jar'),
            Array('request' => 'Spain/g3-tones.xml', 'expected' => '/data/locales/countries/Spain/g3-tones.xml'),
            Array('request' => '320x196x4/Chan-SCCP-b.png', 'expected' => '/data/wallpapers/320x196x4/Chan-SCCP-b.png'),
            Array('request' => 'XMLDefault.cnf.xml', 'expected' => '/data/settings/XMLDefault.cnf.xml'),
            Array('request' => '../XMLDefault.cnf.xml', 'expected' => Resolve\ResolveResult::RequestContainsPathWalk),
            Array('request' => 'XMLDefault.cnf.xml/../../text.xml', 'expected' => Resolve\ResolveResult::RequestContainsPathWalk),
    );
    
    public function testCanResolveFiles(): void
    {
    	global $base_path;
    	$config = $this->getConfig();
        //global $config;
        
        $resolve = new \SCCP\Resolve\Resolve($config);
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

