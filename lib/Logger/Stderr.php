<?php

namespace PROVISION\Logger;

class Stderr extends Filehandle
{
	function __construct($minimum, $dateformat = "r")
	{
		return parent::__construct($minimum, STDERR, $dateformat);
	}
}
?>