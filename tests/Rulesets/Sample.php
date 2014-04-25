<?php

namespace werx\Validation\Tests\Rulesets;

use werx\Validation\Ruleset;

class Sample extends Ruleset
{
	public function __construct()
	{
		$this->addRule('firstname', 'First Name', 'required|minlength[2]');
		$this->addRule('lastname', 'Last Name', 'required');
		$this->addRule('dob', 'Date of Birth', 'required|date');
	}
}
