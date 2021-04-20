<?php

namespace TextExtracts;
/**
 * Class StripState
 *
 * This is a horrible hack in order to get and use the same regex as the default StripState
 * @package TextExtracts
 */
class StripStateOverride extends \StripState{
	function getRegex(): string {
		return $this->regex;
	}
}
