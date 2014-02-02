<?php

namespace joshmoody\Validation\Tests;

use joshmoody\Validation\Engine;

class EngineTests extends \PHPUnit_Framework_TestCase
{
	public $validator;
	
	public function __construct()
	{
	
	}
	
	public function testCanAddRule()
	{
		$validator = new Engine();
		$validator->addRule('firstname', 'First Name', 'required|minlength[8]');
		
		$this->assertTrue(array_key_exists('firstname', $validator->fields));
	}
	
	public function testCanParseRuleString()
	{
		$validator = new Engine();
		$input = 'required|email|minlength[8]|between[8,10]';
		
		$output = $validator->parseRule($input);
		
		$this->assertEquals(4, count($output));
	}

	public function testCanGetDefaultMessages()
	{
		$validator = new Engine();
		
		$message = $validator->getMessage('Foo', 'required');
		$this->assertEquals('Foo is a required field.', $message);

		$message = $validator->getMessage('Foo', 'minlength', [8]);
		$this->assertEquals('Foo must be at least 8 characters long.', $message);

		$message = $validator->getMessage('Foo', 'maxlength', [8]);
		$this->assertEquals('Foo cannot be longer than 8 characters.', $message);
	}
	
	public function testShouldFailValidation()
	{
		$validator = new Engine();
		$validator->addRule('firstname', 'First Name', 'required|minlength[2]');
		
		$result = $validator->validate(['firstname' => 'J']);
		
		$this->assertFalse($result);

		$validator->reset();
		
		$validator->addRule('firstname', 'First Name', 'required|minlength[2]');
		$result = $validator->validate();
		
		$this->assertFalse($result);
	}

	public function testShouldPassValidation()
	{
		$validator = new Engine();
		$validator->addRule('firstname', 'First Name', 'required|minlength[2]');
		$result = $validator->validate(['firstname' => 'Josh']);
		$this->assertTrue($result);
	}
	
	public function testCanGetErrorSummary()
	{
		$validator = new Engine();
		$validator->addRule('firstname', 'First Name', 'required|alpha|minlength[2]');
		$validator->addRule('lastname', 'Last Name', 'required');
		$result = $validator->validate(['firstname' => '1']);

		$summary = $validator->getErrorSummary();
		
		$this->assertEquals(3, count($summary), 'There should be three failures here.');
	}
	
	public function testCanGetErrorSummaryFormatted()
	{
		$validator = new Engine();
		$validator->addRule('firstname', 'First Name', 'required|alpha|minlength[2]');
		$validator->addRule('lastname', 'Last Name', 'required');
		$result = $validator->validate(['firstname' => '1']);

		$summary = $validator->getErrorSummaryFormatted();
	}

	public function testCanGetErrorFields()
	{
		$validator = new Engine();
		$validator->addRule('firstname', 'First Name', 'required|alpha|minlength[2]');
		$validator->addRule('lastname', 'Last Name', 'required');
		$result = $validator->validate(['firstname' => '1']);

		$error_fields = $validator->getErrorFields();
		
		$this->assertEquals(2, count($error_fields), 'There should be 2 fields with errors here.');
	}
	
	public function testCanGetRequiredFields()
	{
		$validator = new Engine();
		$validator->addRule('firstname', 'First Name', 'required');
		$validator->addRule('lastname', 'Last Name', 'required');
		$validator->addRule('middlename', 'Middle Name', 'exactlength[1]');
		
		$required = $validator->getRequiredFields();

		$this->assertEquals(2, count($required), 'There should be 2 required fields here.');
	}
}
