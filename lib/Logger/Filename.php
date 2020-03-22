<?php

namespace PROVISION\Logger;

class Filename extends Filehandle
{
	function __construct($minimum, $filename, $dateformat = "r")
	{
		return parent::__construct($minimum, fopen($filename, "a"), $dateformat);
	}
}
?>