<?php

namespace werx\Validation;

class Ruleset
{
	public $rules = [];

	public function addRule($field = null, $label = null, $rules = null)
	{
		$this->rules[] = ['field' => $field, 'label' => $label, 'rules' => $rules];
	}
}
