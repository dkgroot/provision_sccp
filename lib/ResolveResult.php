<?php declare(strict_types=1);

namespace PROVISION;

//abstract class ResolveResult extends \SplEnum {
abstract class ResolveResult {
	const Ok = 0;
	const EmptyRequest = 1;
	const RequestNotAString = 2;
	const RequestContainsInvalidChar = 3;
	const RequestContainsPathWalk = 4;
	const FileNotFound = 5;
	const InvalidFilename = 6;
	const InvalidPath = 7;
}
?>