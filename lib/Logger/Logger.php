<?php

namespace PROVISION\Logger;

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
?>