<?php
declare(strict_types=1);

namespace PROVISION\Device;

abstract class Protocol extends \SplEnum {
	const SKINNY = 0;
	const SIP = 1;
	const BOTH = 2;
}
?>
