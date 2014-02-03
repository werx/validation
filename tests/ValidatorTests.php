<?php
namespace joshmoody\Validation\Tests;

use joshmoody\Validation\Validator;

class ValidatorTests extends \PHPUnit_Framework_TestCase
{
	public function testShouldFailRequired()
	{
		$this->assertFalse(Validator::required(null));
	}

	public function testShouldPassRequired()
	{
		$this->assertTrue(Validator::required('123'));
		$this->assertTrue(Validator::required(123));
	}

	public function testShouldFailValidDate()
	{
		$this->assertFalse(Validator::date('02/29/2014'));
		$this->assertFalse(Validator::date('2014-02-29', 'YYYY-MM-DD'));
		$this->assertFalse(Validator::date('04/31/2014'));
		$this->assertFalse(Validator::date('06/31/2014'));
		$this->assertFalse(Validator::date('09/31/2014'));
		$this->assertFalse(Validator::date('11/31/2014'));
	}

	public function testShouldPassValidDate()
	{
		$this->assertTrue(Validator::date('02/29/2012'));
		$this->assertTrue(Validator::date('2012-02-29', 'YYYY-MM-DD'));
		$this->assertTrue(Validator::date('01/31/2014'));
		$this->assertTrue(Validator::date('03/31/2014'));
		$this->assertTrue(Validator::date('05/31/2014'));
		$this->assertTrue(Validator::date('07/31/2014'));
		$this->assertTrue(Validator::date('08/31/2014'));
		$this->assertTrue(Validator::date('10/31/2014'));
		$this->assertTrue(Validator::date('12/31/2014'));
	}
	
	public function testShouldFailMinLength()
	{
		$this->assertFalse(Validator::minlength('12345', 6));
	}

	public function testShouldPassMinLength()
	{
		$this->assertTrue(Validator::minlength(null));
		$this->assertTrue(Validator::minlength('12345', 5));
		$this->assertTrue(Validator::minlength('123456', 5));
	}

	public function testShouldFailMaxLength()
	{
		$this->assertFalse(Validator::maxlength('12345', 4));
	}

	public function testShouldPassMaxLength()
	{
		$this->assertTrue(Validator::maxlength(null));
		$this->assertTrue(Validator::maxlength('1234', 5));
		$this->assertTrue(Validator::maxlength('12345', 5));
	}

	public function testShouldFailExactLength()
	{
		$this->assertFalse(Validator::exactlength('1234', 5));
		$this->assertFalse(Validator::exactlength('123456', 5));
	}

	public function testShouldPassExactLength()
	{
		$this->assertTrue(Validator::exactlength(null, 5));
		$this->assertTrue(Validator::exactlength('12345', 5));
	}

	public function testShouldFailGreaterThan()
	{
		$this->assertFalse(Validator::greaterthan(100, 200));
		$this->assertFalse(Validator::greaterthan('100', '200'));
		$this->assertFalse(Validator::greaterthan('100', '100'));
	}

	public function testShouldPassGreaterThan()
	{
		$this->assertTrue(Validator::greaterthan(null, 99));
		$this->assertTrue(Validator::greaterthan(100, 99));
		$this->assertTrue(Validator::greaterthan('100', '99'));
	}

	public function testShouldFailLessThan()
	{
		$this->assertFalse(Validator::lessthan(200, 100));
		$this->assertFalse(Validator::lessthan('200', '100'));
		$this->assertFalse(Validator::lessthan('100', '100'));
	}

	public function testShouldPassLessThan()
	{
		$this->assertTrue(Validator::lessthan(null, 100));
		$this->assertTrue(Validator::lessthan(99, 100));
		$this->assertTrue(Validator::lessthan('99', '100'));
	}

	public function testShouldFailAlpha()
	{
		$this->assertFalse(Validator::alpha('A1'));
	}

	public function testShouldPassAlpha()
	{
		$this->assertTrue(Validator::alpha(null));
		$this->assertTrue(Validator::alpha('ABC'));
	}

	public function testShouldFailAlphaNumeric()
	{
		$this->assertFalse(Validator::alphanumeric('A>'));
	}

	public function testShouldPassAlphaNumeric()
	{
		$this->assertTrue(Validator::alphanumeric(null));
		$this->assertTrue(Validator::alphanumeric('A1'));
	}

	public function testShouldFailInteger()
	{
		$this->assertFalse(Validator::integer('ABC'));
		$this->assertFalse(Validator::integer('!@#'));
		$this->assertFalse(Validator::integer('123.45'));
		$this->assertFalse(Validator::integer(123.45));
	}

	public function testShouldPassInteger()
	{
		$this->assertTrue(Validator::integer(null));
		$this->assertTrue(Validator::integer(123));
		$this->assertTrue(Validator::integer('123'));
		$this->assertTrue(Validator::integer(-123));
		$this->assertTrue(Validator::integer('-123'));
	}

	public function testShouldFailFloat()
	{

		$this->assertFalse(Validator::float('$123.01'));
		$this->assertFalse(Validator::float('ABC'));
	}

	public function testShouldPassFloat()
	{
		$this->assertTrue(Validator::float(null));
		$this->assertTrue(Validator::float(123));
		$this->assertTrue(Validator::float('123'));
		$this->assertTrue(Validator::float(123.01));
		$this->assertTrue(Validator::float('123.01'));
	}

	public function testShouldFailNumeric()
	{
		$this->assertFalse(Validator::numeric('ABC1'));
	}

	public function testShouldPassNumeric()
	{
		$this->assertTrue(Validator::numeric(null));
		$this->assertTrue(Validator::numeric(123));
		$this->assertTrue(Validator::numeric(123.04));
		$this->assertTrue(Validator::numeric('123'));
	}

	public function testShouldFailEmail()
	{
		$this->assertFalse(Validator::email('foo'));
		$this->assertFalse(Validator::email('foo.com'));
	}

	public function testShouldPassEmail()
	{
		$this->assertTrue(Validator::email(null));
		$this->assertTrue(Validator::email('me@example.com'));
		$this->assertTrue(Validator::email('firstname.lastname@example.com'));
	}

	public function testShouldFailUrl()
	{
		$this->assertFalse(Validator::url('//localhost.com'));
		$this->assertFalse(Validator::url('www.localhost'));
	}

	public function testShouldPassUrl()
	{
		$this->assertTrue(Validator::url(null));
		$this->assertTrue(Validator::url('http://localhost.com'));
		$this->assertTrue(Validator::url('http://localhost.com/'));
		$this->assertTrue(Validator::url('http://localhost.com/123'));
		$this->assertTrue(Validator::url('https://localhost.com/123'));
	}

	public function testShouldFailPhone()
	{
		$this->assertFalse(Validator::url('123456789'));
	}

	public function testShouldPassPhone()
	{
		$this->assertTrue(Validator::phone(null));
		$this->assertTrue(Validator::phone('1234567890'));
	}

	public function testShouldFailZipcode()
	{
		$this->assertFalse(Validator::zipcode('7220'));
		$this->assertFalse(Validator::zipcode('722011'));
	}

	public function testShouldPassZipcode()
	{
		$this->assertTrue(Validator::zipcode(null));
		$this->assertTrue(Validator::zipcode('72201'));
		$this->assertTrue(Validator::zipcode('72201-1111'));
	}

	public function testShouldFailStartswith()
	{
		$this->assertFalse(Validator::startswith('ABC', 'BCD'));
	}

	public function testShouldPassStartswith()
	{
		$this->assertTrue(Validator::startswith(null));
		$this->assertTrue(Validator::startswith('ABCDEFG', 'ABC'));
	}

	public function testShouldFailEndswith()
	{
		$this->assertFalse(Validator::endswith('ABC', 'CD'));
	}

	public function testShouldPassEndswith()
	{
		$this->assertTrue(Validator::endswith(null));
		$this->assertTrue(Validator::endswith('ABCDEFG', 'EFG'));
	}

	public function testShouldFailContains()
	{
		$this->assertFalse(Validator::contains('ABCD', 'EFG'));
	}

	public function testShouldPassContains()
	{
		$this->assertTrue(Validator::contains(null));
		$this->assertTrue(Validator::contains('ABCDEFG', 'ABC'));
		$this->assertTrue(Validator::contains('ABCDEFG', 'EFG'));
		$this->assertTrue(Validator::contains('ABCDEFG', 'DEF'));
	}

	public function testShouldFailRegex()
	{
		$this->assertFalse(Validator::regex('12345', '/[A-Z]+/'));
		$this->assertFalse(Validator::regex('ABC', '/[\d]+/'));
	}

	public function testShouldPassRegex()
	{
		$this->assertTrue(Validator::regex(null));
		$this->assertTrue(Validator::regex('12345', '/[\d]+/'));
		$this->assertTrue(Validator::regex('ABCD', '/[A-Z]+/'));
		$this->assertTrue(Validator::regex('abcd', '/[a-z]+/'));
		$this->assertTrue(Validator::regex('abcdDEFG', '/[a-zA-Z]+/'));
		$this->assertTrue(Validator::regex('ABCDEFG123', '/[a-zA-Z0-9]+/'));
	}
}
