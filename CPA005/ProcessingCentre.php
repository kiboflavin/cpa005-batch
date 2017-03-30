<?php

namespace CPA005;

class ProcessingCentre {
	const REGINA    = '00278';
	const VANCOUVER = '00300';
	const MONTREAL  = '00310';
	const TORONTO   = '00320';
	const HALIFAX   = '00330';
	const WINNIPEG  = '00370';
	const CALGARY   = '00390';

	/**
	 * Check whether the provided string is a valid processing centre id
	 * 
	 * @param  string $centre
	 * @return bool
	 */
	public static function valid($centre): bool
	{
		$reflection = new \ReflectionObject(new self);

		return in_array($centre, $reflection->getConstants());
	}
}