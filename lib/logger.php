<?php
/* Note about the Logger class:
 * The "priority" and "minimum should be one of the constants used for syslog.
 * See: http://php.net/manual/en/function.syslog.php
 * They are: LOG_EMERG, LOG_ALERT, LOG_CRIT, LOG_ERR, LOG_WARNING, LOG_NOTICE,
 * 	     LOG_INFO, LOG_DEBUG
 * Note that LOG_EMERG, LOG_ALERT, and LOG_CRIT are not really relevant to a
 * tftp server - these represent instability in the entire operating system.
 * Note that the number they are represented by are in reverse order -
 * LOG_EMERG is the lowest, LOG_DEBUG the highest.
 */
abstract class Logger
{
	function __construct($minimum)
	{
		$this->minimum = $minimum;
	}

	function shouldlog($priority)
	{
		// Note: this looks reversed, but is correct
		// the priority must be AT LEAST the minimum,
		// because higher priorities represent lower numbers.
		return $priority <= $this->minimum;
	}

	abstract function log($priority, $message);
}

class Logger_Null extends Logger
{
	function log($priority, $message)
	{
	}
}

class Logger_Syslog extends Logger
{
	function log($priority, $message)
	{
		if($this->shouldlog($priority))
			syslog($priority,$message);
	}
}

class Logger_Filehandle extends Logger
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

class Logger_Filename extends Logger_Filehandle
{
	function __construct($minimum, $filename, $dateformat = "r")
	{
		return parent::__construct($minimum, fopen($filename, "a"), $dateformat);
	}
}

class Logger_Stderr extends Logger_Filehandle
{
	function __construct($minimum, $dateformat = "r")
	{
		return parent::__construct($minimum, STDERR, $dateformat);
	}
}
class Logger_Stdout extends Logger_Filehandle
{
	function __construct($minimum, $dateformat = "r")
	{
		return parent::__construct($minimum, STDOUT, $dateformat);
	}
}
?>