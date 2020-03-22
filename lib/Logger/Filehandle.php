<?php

namespace PROVISION\Logger;

class Filehandle extends Logger
{
	private $priority_map = array(
		LOG_DEBUG => "D",
		LOG_INFO => "I",
		LOG_NOTICE => "N",
		LOG_WARNING => "W",
		LOG_ERR => "E",
		LOG_CRIT => "C",
		LOG_ALERT => "A",
		LOG_EMERG => "!"
	);
	function __construct($minimum, $filehandle, $dateformat = "r")
	{
		$this->filehandle = $filehandle;
		$this->dateformat = $dateformat;
		return parent::__construct($minimum);
	}

	function log($priority, $message)
	{
		if($this->shouldlog($priority))
			fwrite($this->filehandle, date($this->dateformat) . ": " . $this->priority_map[$priority] . " $message\n");
	}
}

class Filename extends Filehandle
{
	function __construct($minimum, $filename, $dateformat = "r")
	{
		return parent::__construct($minimum, fopen($filename, "a"), $dateformat);
	}
}

class Stderr extends Filehandle
{
	function __construct($minimum, $dateformat = "r")
	{
		return parent::__construct($minimum, STDERR, $dateformat);
	}
}
class Stdout extends Filehandle
{
	function __construct($minimum, $dateformat = "r")
	{
		return parent::__construct($minimum, STDOUT, $dateformat);
	}
}

?>