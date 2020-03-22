<?php

namespace PROVISION\Logger;

class Stdout extends Filehandle
{
	function __construct($minimum, $dateformat = "r")
	{
		return parent::__construct($minimum, STDOUT, $dateformat);
	}
}
?>