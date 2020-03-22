<?php
declare(strict_types=1);

namespace PROVISION\Device;

use PROVISION\Device\Type as Type;
use PROVISION\Device\Family as Family;
use PROVISION\Device\Protocol as Protocol;
use PROVISION\Device\Flags as Flags;

abstract class Device
{
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
?>
