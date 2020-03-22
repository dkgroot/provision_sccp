<?php
declare(strict_types=1);

namespace PROVISION\Device;

abstract class Family extends \SplEnum {
	const PreCisco = 0;
	const Cisco = 1;
	const CiscoJava = 2;
	const Spa = 3;
	const CiscoAddon = 4;
	const SpaAddon = 5;
	const ThirdParty = 6; 
}
?>