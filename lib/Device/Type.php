<?php
declare(strict_types=1);

namespace PROVISION\Device;

abstract class Type extends \SplEnum
{
	const CiscoIPCommunicator = 30016;
	const CiscoIPPhone6901 = 547;
	const CiscoIPPhone6911 = 548;
	const CiscoIPPhone6921 = 495;
	const CiscoIPPhone6941 = 496;
	const CiscoIPPhone6945 = 564;
	const CiscoIPPhone6961 = 497;
	const CiscoIPPhone7902 = 30008;
	const CiscoIPPhone7905 = 20000;
	const CiscoIPPhone7906 = 369;
	const CiscoIPPhone7910 = 006;
	const CiscoIPPhone7911 = 307;
	const CiscoIPPhone7912 = 30007;
	const CiscoIPPhone7920 = 30002;
	const CiscoIPPhone7921 = 365;
	const CiscoIPPhone7925 = 484;
	const CiscoIPPhone7926 = 577;
	const CiscoIPPhone7931 = 348;
	const CiscoIPPhone7935 = 9; // Conference Phone
	const CiscoIPPhone7936 = 30019;
	const CiscoIPPhone7937 = 431; // Conference Phone
	const CiscoIPPhone7940 = 8;
	const CiscoIPPhone7941 = 115;
	const CiscoIPPhone7941GE = 309;
	const CiscoIPPhone7942 = 434;
	const CiscoIPPhone7945 = 435;
	const CiscoIPPhone7960 = 7;
	const CiscoIPPhone7961 = 5;
	const CiscoIPPhone7961G = 30018;
	const CiscoIPPhone7961GE = 308;
	const CiscoIPPhone7962 = 404;
	const CiscoIPPhone7965 = 436;
	const CiscoIPPhone7970 = 30006;
	const CiscoIPPhone7971 = 119;
	const CiscoIPPhone7975 = 437;
	const CiscoIPPhone7985 = 302;
	const CiscoIPPhone8941 = 586;
	const CiscoIPPhone8945 = 585;

	const CiscoSPA303G = 80011;
	const CiscoSPA502G = 80003;
	const CiscoSPA504G = 80004;
	const CiscoSPA509G = 80007;
	const CiscoSPA512G = 80012;
	const CiscoSPA514G = 80013;
	const CiscoSPA521S = 80000;
	const CiscoSPA521SG = 80001;
	const CiscoSPA525G2 = 80009;
	const CiscoSPA525G = 80005;
	const NokiaESeries = 275;
	const NokiaICCclient = 376;

	const CiscoIPAddon7914 = 124; //14-Button Line Expansion Module
	const CiscoIPAddon7915_1 = 227; //12-Button Line Expansion Module
	const CiscoIPAddon7915_2 = 228; //24-Button Line Expansion Module
	const CiscoIPAddon7916_1 = 229; //12-Button Line Expansion Module
	const CiscoIPAddon7916_2 = 230; //24-Button Line Expansion Module
}
?>
