<?php
declare(strict_types=1);

namespace PROVISION\Device;

abstract class Flags extends \SplEnum {
	const Conference = 1<<0;
	const Communicator = 1<<1;
}
?>
