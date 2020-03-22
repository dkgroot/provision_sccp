<?php

namespace PROVISION\Logger;

class Syslog extends Logger
{
	function log($priority, $message)
	{
		if($this->shouldlog($priority))
			syslog($priority,$message);
	}
}
?>