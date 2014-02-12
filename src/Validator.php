<?php

namespace joshmoody\Validation;

class Validator
{
	public static function required($input = null)
	{
		return empty($input) ? false : true;
	}

	// Datetime validation from http://www.phpro.org/examples/Validate-Date-Using-PHP.html
	public static function date($input = null, $format = 'MM/DD/YYYY')
	{
		if (empty($input)) {
			return true;
		}
		
		switch($format) {
			case 'YYYY/MM/DD':
			case 'YYYY-MM-DD':
			list($y, $m, $d) = preg_split('/[-\.\/ ]/', $input);
			break;

			case 'YYYY/DD/MM':
			case 'YYYY-DD-MM':
			list($y, $d, $m) = preg_split('/[-\.\/ ]/', $input);
			break;

			case 'DD-MM-YYYY':
			case 'DD/MM/YYYY':
			list($d, $m, $y) = preg_split('/[-\.\/ ]/', $input);
			break;

			case 'MM-DD-YYYY':
			case 'MM/DD/YYYY':
			list($m, $d, $y) = preg_split('/[-\.\/ ]/', $input);
			break;

			case 'YYYYMMDD':
			$y = substr($input, 0, 4);
			$m = substr($input, 4, 2);
			$d = substr($input, 6, 2);
			break;

			case 'YYYYDDMM':
			$y = substr($input, 0, 4);
			$d = substr($input, 4, 2);
			$m = substr($input, 6, 2);
			break;

			default:
			throw new \InvalidArgumentException("Invalid Date Format");
		}
		return checkdate($m, $d, $y);
	}

	public static function minlength($input = null, $length = 0)
	{
		if (empty($input)) {
			return true;
		}

		return strlen(trim($input)) >= (int) $length ? true : false;
	}

	public static function maxlength($input = null, $length = 0)
	{
		if (empty($input)) {
			return true;
		}

		return strlen(trim($input)) <= (int) $length ? true : false;
	}

	public static function exactlength($input = null, $length = 0)
	{
		if (empty($input)) {
			return true;
		}

		return strlen(trim($input)) == (int) $length ? true : false;
	}

	public static function greaterthan($input = null, $min = 0)
	{
		if (empty($input)) {
			return true;
		}

		return (float) $input > (float) $min ? true : false;
	}

	public static function lessthan($input = null, $max = 0)
	{
		if (empty($input)) {
			return true;
		}

		return (float) $input < (float) $max ? true : false;
	}

	public static function alpha($input = null)
	{
		if (empty($input)) {
			return true;
		}

		return (bool) preg_match('/^([a-z])+$/i', $input);
	}

	public static function alphanumeric($input = null)
	{
		if (empty($input)) {
			return true;
		}

		return (bool) preg_match('/^([a-z0-9])+$/i', $input);

	}

	public static function integer($input = null)
	{
		if (empty($input)) {
			return true;
		}

		if (filter_var($input, FILTER_VALIDATE_INT) !== false) {
			return true;
		}

		return false;
	}

	public static function float($input = null)
	{
		if (empty($input)) {
			return true;
		}

		if (filter_var($input, FILTER_VALIDATE_FLOAT) !== false) {
			return true;
		}

		return false;
	}

	public static function numeric($input = null)
	{
		if (empty($input)) {
			return true;
		}

		return is_numeric($input) ? true : false;
	}

	public static function email($input = null)
	{
		if (empty($input)) {
			return true;
		}

		if (filter_var($input, FILTER_VALIDATE_EMAIL) !== false) {
			return true;
		}

		return false;
	}

	public static function url($input = null)
	{
		if (empty($input)) {
			return true;
		}

		if (filter_var($input, FILTER_VALIDATE_URL) !== false) {
			return true;
		}

		return false;
	}

	public static function phone($input = null)
	{
		if (empty($input)) {
			return true;
		}

		return (bool) preg_match('/^\(?([0-9]{3})\)?[- ]?([0-9]{3})[- ]?([0-9]{4})$/', $input);
	}

	public static function zipcode($input = null)
	{
		if (empty($input)) {
			return true;
		}

		return (bool) preg_match('/^\d{5}(-\d{4})?$/', $input);
	}

	public static function startswith($input = null, $match = null)
	{
		if (empty($input)) {
			return true;
		}

		return (bool) preg_match('/^' . preg_quote($match) . '/', $input);
	}

	public static function endswith($input = null, $match = null)
	{
		if (empty($input)) {
			return true;
		}

		return (bool) preg_match('/' . preg_quote($match) . '$/', $input);
	}

	public static function contains($input = null, $match = null)
	{
		if (empty($input)) {
			return true;
		}

		return (bool) preg_match('/' . preg_quote($match) . '/', $input);
	}

	public static function regex($input = null, $regex = null)
	{
		if (empty($input)) {
			return true;
		}

		return (bool) preg_match($regex, $input);
	}
}
