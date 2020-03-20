<?php
declare(strict_types=1);

namespace SCCP;
//include_once("config.php");
//include_once("utils.php");
//include_once("resolveCache.php");
abstract class DeviceType
{
	const CiscoIPCommunicator = 30016;
	const CiscoIPPhone6901 = 547;
	const CiscoIPPhone6911 = 548;
	const CiscoIPPhone6921 = 495;
	const CiscoIPPhone6941 = 496;
	const CiscoIPPhone6945 = 564;
	const CiscoIPPhone6961 = 497;
	const CiscoIPPhone7902 = 30008;
	const CiscoIPPhone7905 = 20000;
	const CiscoIPPhone7906 = 369;
	const CiscoIPPhone7910 = 006;
	const CiscoIPPhone7911 = 307;
	const CiscoIPPhone7912 = 30007;
	const CiscoIPPhone7920 = 30002;
	const CiscoIPPhone7921 = 365;
	const CiscoIPPhone7925 = 484;
	const CiscoIPPhone7926 = 577;
	const CiscoIPPhone7931 = 348;
	const CiscoIPPhone7935 = 9; // Conference Phone
	const CiscoIPPhone7936 = 30019;
	const CiscoIPPhone7937 = 431; // Conference Phone
	const CiscoIPPhone7940 = 8;
	const CiscoIPPhone7941 = 115;
	const CiscoIPPhone7941GE = 309;
	const CiscoIPPhone7942 = 434;
	const CiscoIPPhone7945 = 435;
	const CiscoIPPhone7960 = 7;
	const CiscoIPPhone7961 = 5;
	const CiscoIPPhone7961G = 30018;
	const CiscoIPPhone7961GE = 308;
	const CiscoIPPhone7962 = 404;
	const CiscoIPPhone7965 = 436;
	const CiscoIPPhone7970 = 30006;
	const CiscoIPPhone7971 = 119;
	const CiscoIPPhone7975 = 437;
	const CiscoIPPhone7985 = 302;
	const CiscoIPPhone8941 = 586;
	const CiscoIPPhone8945 = 585;

	const CiscoSPA303G = 80011;
	const CiscoSPA502G = 80003;
	const CiscoSPA504G = 80004;
	const CiscoSPA509G = 80007;
	const CiscoSPA512G = 80012;
	const CiscoSPA514G = 80013;
	const CiscoSPA521S = 80000;
	const CiscoSPA521SG = 80001;
	const CiscoSPA525G2 = 80009;
	const CiscoSPA525G = 80005;
	const NokiaESeries = 275;
	const NokiaICCclient = 376;

	const CiscoIPAddon7914 = 124; //14-Button Line Expansion Module
	const CiscoIPAddon7915_1 = 227; //12-Button Line Expansion Module
	const CiscoIPAddon7915_2 = 228; //24-Button Line Expansion Module
	const CiscoIPAddon7916_1 = 229; //12-Button Line Expansion Module
	const CiscoIPAddon7916_2 = 230; //24-Button Line Expansion Module
}

abstract class DeviceFamily {
	const PreCisco = 0;
	const Cisco = 1;
	const CiscoJava = 2;
	const Spa = 3;
	const CiscoAddon = 4;
	const SpaAddon = 5;
	const ThirdParty = 6; 
}

abstract class DeviceProtocol {
	const SKINNY = 0;
	const SIP = 1;
	const BOTH = 2;
}

abstract class DeviceFlags {
	const Conference = 1<<0;
	const Communicator = 1<<1;
}

 class Device {
	private $name;
	private $family;
	private $prococol;
	private $flags;
	function __construct($name, $family, $protocol, $flags = null) {
		$this->name = $name;
		$this->family = $family;
		$this->protocol = $protocol;
		$this->flags = $flags;
	}
	function getName()
	{
		return $this->name;
	}
}

class DeviceFactory
{
	private $models = Array(
		DeviceType::CiscoIPCommunicator => Array('name' => "Communicator", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::SKINNY, 'flags' => DeviceFlags::Communicator),
		DeviceType::CiscoIPPhone6901 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::SKINNY, 'flags' => null),
		DeviceType::CiscoIPPhone6911 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::SKINNY, 'flags' => null),
		DeviceType::CiscoIPPhone6921 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::SKINNY, 'flags' => null),
		DeviceType::CiscoIPPhone6941 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::SKINNY, 'flags' => null),
		DeviceType::CiscoIPPhone6945 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::SKINNY, 'flags' => null),
		DeviceType::CiscoIPPhone6961 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::SKINNY, 'flags' => null),
		DeviceType::CiscoIPPhone7902 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::SKINNY, 'flags' => null),
		DeviceType::CiscoIPPhone7905 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::SKINNY, 'flags' => null),
		DeviceType::CiscoIPPhone7906 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::SKINNY, 'flags' => null),
		DeviceType::CiscoIPPhone7910 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::SKINNY, 'flags' => null),
		DeviceType::CiscoIPPhone7911 => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7912 => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7920 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7921 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7925 => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7926 => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7931 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::SKINNY, 'flags' => null),
		DeviceType::CiscoIPPhone7935 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::BOTH, 'flags' => DeviceFlags::Conference), // Conference Phone
		DeviceType::CiscoIPPhone7936 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::BOTH, 'flags' => DeviceFlags::Conference), // Conference Phone
		DeviceType::CiscoIPPhone7937 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::BOTH, 'flags' => DeviceFlags::Conference), // Conference Phone
		DeviceType::CiscoIPPhone7940 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7941 => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7941GE => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7942 => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7945 => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7960 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7961 => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7961G => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7961GE => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7962 => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7965 => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7970 => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7971 => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7975 => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone7985 => Array('name' => "", 'family' => DeviceFamily::CiscoJava, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone8941 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoIPPhone8945 => Array('name' => "", 'family' => DeviceFamily::Cisco, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),

		DeviceType::CiscoSPA303G => Array('name' => "", 'family' => DeviceFamily::Spa, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoSPA502G => Array('name' => "", 'family' => DeviceFamily::Spa, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoSPA504G => Array('name' => "", 'family' => DeviceFamily::Spa, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoSPA509G => Array('name' => "", 'family' => DeviceFamily::Spa, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoSPA512G => Array('name' => "", 'family' => DeviceFamily::Spa, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoSPA514G => Array('name' => "", 'family' => DeviceFamily::Spa, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoSPA521S => Array('name' => "", 'family' => DeviceFamily::Spa, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoSPA521SG => Array('name' => "", 'family' => DeviceFamily::Spa, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoSPA525G2 => Array('name' => "", 'family' => DeviceFamily::Spa, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		DeviceType::CiscoSPA525G => Array('name' => "", 'family' => DeviceFamily::Spa, 'protocol' => DeviceProtocol::BOTH, 'flags' => null),
		
		DeviceType::NokiaESeries => Array('name' => "", 'family' => DeviceFamily::ThirdParty, 'protocol' => DeviceProtocol::SKINNY, 'flags' => null),
		DeviceType::NokiaICCclient => Array('name' => "", 'family' => DeviceFamily::ThirdParty, 'protocol' => DeviceProtocol::SKINNY, 'flags' => null),

		DeviceType::CiscoIPAddon7914 => Array('name' => "", 'family' => DeviceFamily::CiscoAddon, 'protocol' => DeviceProtocol::BOTH, 'flags' => null), //14-Button Line Expansion Module
		DeviceType::CiscoIPAddon7915_1 => Array('name' => "", 'family' => DeviceFamily::CiscoAddon, 'protocol' => DeviceProtocol::BOTH, 'flags' => null), //12-Button Line Expansion Module
		DeviceType::CiscoIPAddon7915_2 => Array('name' => "", 'family' => DeviceFamily::CiscoAddon, 'protocol' => DeviceProtocol::BOTH, 'flags' => null), //24-Button Line Expansion Module
		DeviceType::CiscoIPAddon7916_1 => Array('name' => "", 'family' => DeviceFamily::CiscoAddon, 'protocol' => DeviceProtocol::BOTH, 'flags' => null), //12-Button Line Expansion Module
		DeviceType::CiscoIPAddon7916_2 => Array('name' => "", 'family' => DeviceFamily::CiscoAddon, 'protocol' => DeviceProtocol::BOTH, 'flags' => null), //24-Button Line Expansion Module
	);

	/**
	 * Prevent direct object creation
	 */
	final private function __construct() {
	}
	
    /**
     * Returns new or existing Singleton instance
     * @return Singleton
     */
    final public static function getInstance(){
        if(null !== static::$_instance){
            return static::$_instance;
        }
        static::$_instance = new static();
        return static::$_instance;
    }	

	public function createFromString($name) {
		foreach($this->models as $model) {
			if ($model['name'] == $name) {
				return new Device($model['name'], $model['family'], $model['protocol'], $model['flags']);
			}
		}
		return null;
	}
	public function createFromModelNo($modelno) {
		if (array_key_exists($modelno, $this->models)) {
			$model = $models[$modelno];
			return new Device($model['name'], $model['family'], $model['protocol'], $model['flags']);
		}
		return null;
	}
}
?>
