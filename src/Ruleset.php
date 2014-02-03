<?php

namespace joshmoody\Validation;

class Ruleset
{
	public $rules;

	public function __construct()
	{
		$this->rules = [];
	}

	public function addRule($field = null, $label = null, $rules = null)
	{
		$this->rules[] = ['field' => $field, 'label' => $label, 'rules' => $rules];
	}
}
