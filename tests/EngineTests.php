<?php

namespace joshmoody\Validation\Tests;

use joshmoody\Validation\Engine;
use joshmoody\Validation\Tests\Rulesets as Rulesets;

class EngineTests extends \PHPUnit_Framework_TestCase
{
	public $validator;

	public function __construct()
	{

	}

	public function testCanLoadCustomValidator()
	{
		$validator = new Engine(new \StdClass());
		$this->assertEquals('stdclass', strtolower(get_class($validator->validator)));
	}

	public function testCanAddRule()
	{
		$validator = new Engine();
		$validator->addRule('firstname', 'First Name', 'required|minlength[8]');

		$this->assertTrue(array_key_exists('firstname', $validator->fields));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddRuleMissingArgsShouldThrowException()
	{
		$validator = new Engine();
		$validator->addRule();
	}

	public function testCanParseRuleString()
	{
		$validator = new Engine();
		$input = 'required|email|minlength[8]|between[8,10]';

		$output = $validator->parseRule($input);

		$this->assertEquals(4, count($output));
	}

	public function testCanParseRuleStringWithArray()
	{
		$validator = new Engine();
		$validator->addRule('color', 'Color', 'required|inlist{red,white,blue}');

		$this->assertTrue($validator->validate(['color' => 'white']));
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

	public function testNoDefaultMessageShoulReturnGenericMessage()
	{
		$validator = new Engine();

		$message = $validator->getMessage('Foo', 'doesnotexist');
		$this->assertEquals('Foo is invalid.', $message);
	}

	public function testCanGetCustomMessages()
	{
		$validator = new Engine();

		$validator->addCustomMessage('required', "You didn't provide a value for {name}!");
		$message = $validator->getMessage('Foo', 'required');
		$this->assertEquals("You didn't provide a value for Foo!", $message);

		$validator->addCustomMessage('minlength', "Oops, {name} must be at least %d characters long.");
		$message = $validator->getMessage('Foo', 'minlength', [8]);
		$this->assertEquals('Oops, Foo must be at least 8 characters long.', $message);
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
		$this->assertStringStartsWith('<div', $summary);
		$this->assertStringEndsWith('div>', $summary);
	}

	public function testNoErrorsErrorSummaryFormattedShouldReturnNull()
	{
		$validator = new Engine();
		$validator->addRule('lastname', 'Last Name', 'required');
		$result = $validator->validate(['lastname' => 'Foo']);

		$summary = $validator->getErrorSummaryFormatted();

		$this->assertEquals(null, $summary);
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

	public function testCanProcessRuleSet()
	{
		$validator = new Engine();
		$ruleset = new Rulesets\Sample;
		$validator->addRuleset($ruleset);

		$result = $validator->validate(['firstname' => 'Josh', 'lastname' => 'Moody', 'dob' => '02/29/2014']);

		$error_fields = $validator->getErrorFields();

		$this->assertEquals(1, count($error_fields), 'There should be 1 field with errors here.');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidRuleSetShouldThrowException()
	{
		$validator = new Engine();
		$validator->addRuleSet(new \stdclass());
	}

	public function testCanGetErrorDetail()
	{
		$validator = new Engine();
		$validator->addRule('name', 'Name', 'required');
		$validator->addRule('city', 'City', 'required');
		$validator->addRule('state', 'State', 'required');
		$validator->addRule('zip', 'Zip Code', 'required|zipcode');
		$validator->addRule('phone', 'Phone Number', 'phone');
		$validator->addRule('email', 'Email Address', 'required|email');
		$result = $validator->validate(['zip' => '7220A', 'email' => 'josh@', 'phone' => '555']);

		$error_detail = $validator->getErrorDetail();

		$this->assertEquals(6, count($error_detail), 'There should be 6 fields with errors here.');
	}
	
	public function testCanGetDataField()
	{
		$validator = new Engine();
		$validator->addRule('name', 'Name', 'required');
		
		$result = $validator->validate(['name' => 'Josh']);
		
		$this->assertEquals('Josh', $validator->getData('name'));
	}

	public function testCanGetDataArray()
	{
		$validator = new Engine();
		$validator->addRule('name', 'Name', 'required');
		
		$result = $validator->validate(['name' => 'Josh']);
		
		$this->assertInternalType('array', $validator->getData());
	}

	public function testCanGetDataMissingReturnsNull()
	{
		$validator = new Engine();
		$validator->addRule('name', 'Name', 'required');
		
		$result = $validator->validate(['name' => 'Josh']);
		
		$this->assertEquals(null, $validator->getData('foo'));
	}
}
