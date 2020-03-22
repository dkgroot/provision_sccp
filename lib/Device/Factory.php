<?php
declare(strict_types=1);

namespace PROVISION\Device;

use PROVISION\Device\Type as Type;
use PROVISION\Device\Family as Family;
use PROVISION\Device\Protocol as Protocol;
use PROVISION\Device\Flags as Flags;

class DeviceFactory
{
	private $models = Array(
		Type::CiscoIPCommunicator => Array('name' => "Communicator", 'family' => Family::CiscoJava, 'protocol' => Protocol::SKINNY, 'flags' => Flags::Communicator),
		Type::CiscoIPPhone6901 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::SKINNY, 'flags' => null),
		Type::CiscoIPPhone6911 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::SKINNY, 'flags' => null),
		Type::CiscoIPPhone6921 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::SKINNY, 'flags' => null),
		Type::CiscoIPPhone6941 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::SKINNY, 'flags' => null),
		Type::CiscoIPPhone6945 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::SKINNY, 'flags' => null),
		Type::CiscoIPPhone6961 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::SKINNY, 'flags' => null),
		Type::CiscoIPPhone7902 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::SKINNY, 'flags' => null),
		Type::CiscoIPPhone7905 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::SKINNY, 'flags' => null),
		Type::CiscoIPPhone7906 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::SKINNY, 'flags' => null),
		Type::CiscoIPPhone7910 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::SKINNY, 'flags' => null),
		Type::CiscoIPPhone7911 => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7912 => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7920 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7921 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7925 => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7926 => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7931 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::SKINNY, 'flags' => null),
		Type::CiscoIPPhone7935 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::BOTH, 'flags' => Flags::Conference), // Conference Phone
		Type::CiscoIPPhone7936 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::BOTH, 'flags' => Flags::Conference), // Conference Phone
		Type::CiscoIPPhone7937 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::BOTH, 'flags' => Flags::Conference), // Conference Phone
		Type::CiscoIPPhone7940 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7941 => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7941GE => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7942 => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7945 => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7960 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7961 => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7961G => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7961GE => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7962 => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7965 => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7970 => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7971 => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7975 => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone7985 => Array('name' => "", 'family' => Family::CiscoJava, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone8941 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoIPPhone8945 => Array('name' => "", 'family' => Family::Cisco, 'protocol' => Protocol::BOTH, 'flags' => null),

		Type::CiscoSPA303G => Array('name' => "", 'family' => Family::Spa, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoSPA502G => Array('name' => "", 'family' => Family::Spa, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoSPA504G => Array('name' => "", 'family' => Family::Spa, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoSPA509G => Array('name' => "", 'family' => Family::Spa, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoSPA512G => Array('name' => "", 'family' => Family::Spa, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoSPA514G => Array('name' => "", 'family' => Family::Spa, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoSPA521S => Array('name' => "", 'family' => Family::Spa, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoSPA521SG => Array('name' => "", 'family' => Family::Spa, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoSPA525G2 => Array('name' => "", 'family' => Family::Spa, 'protocol' => Protocol::BOTH, 'flags' => null),
		Type::CiscoSPA525G => Array('name' => "", 'family' => Family::Spa, 'protocol' => Protocol::BOTH, 'flags' => null),
		
		Type::NokiaESeries => Array('name' => "", 'family' => Family::ThirdParty, 'protocol' => Protocol::SKINNY, 'flags' => null),
		Type::NokiaICCclient => Array('name' => "", 'family' => Family::ThirdParty, 'protocol' => Protocol::SKINNY, 'flags' => null),

		Type::CiscoIPAddon7914 => Array('name' => "", 'family' => Family::CiscoAddon, 'protocol' => Protocol::BOTH, 'flags' => null), //14-Button Line Expansion Module
		Type::CiscoIPAddon7915_1 => Array('name' => "", 'family' => Family::CiscoAddon, 'protocol' => Protocol::BOTH, 'flags' => null), //12-Button Line Expansion Module
		Type::CiscoIPAddon7915_2 => Array('name' => "", 'family' => Family::CiscoAddon, 'protocol' => Protocol::BOTH, 'flags' => null), //24-Button Line Expansion Module
		Type::CiscoIPAddon7916_1 => Array('name' => "", 'family' => Family::CiscoAddon, 'protocol' => Protocol::BOTH, 'flags' => null), //12-Button Line Expansion Module
		Type::CiscoIPAddon7916_2 => Array('name' => "", 'family' => Family::CiscoAddon, 'protocol' => Protocol::BOTH, 'flags' => null), //24-Button Line Expansion Module
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
	
	private function createDeviceByModel($model) {
		switch($model['family']) {
			case Family::PreCisco:
				return new PreCiscoDevice($model['name'], $model['family'], $model['protocol'], $model['flags']);
			case Family::Cisco:
				return new CiscoDevice($model['name'], $model['family'], $model['protocol'], $model['flags']);
			case Family::CiscoJava:
				return new JavaDevice($model['name'], $model['family'], $model['protocol'], $model['flags']);
			case Family::PreCisco:
				return new SpaDevice($model['name'], $model['family'], $model['protocol'], $model['flags']);
			case Family::CiscoAddon:
				return new AddonDevice($model['name'], $model['family'], $model['protocol'], $model['flags']);
			case Family::SpaAddon:
				return new SpaAddonDevice($model['name'], $model['family'], $model['protocol'], $model['flags']);
			case Family::ThirdParty:
				return new ThirdPartyDevice($model['name'], $model['family'], $model['protocol'], $model['flags']);
			default:
				// return error
				return new Device($model['name'], $model['family'], $model['protocol'], $model['flags']);
		}
	}	

	public function createFromString($name) {
		foreach($this->models as $model) {
			if ($model['name'] == $name) {
				return $this->createDeviceByModel($model);
			}
		}
		return null;
	}
	public function createFromModelNo($modelno) {
		if (array_key_exists($modelno, $this->models)) {
			$model = $models[$modelno];
			return $this->createDeviceByModel($model);
		}
		return null;
	}
}
?>
