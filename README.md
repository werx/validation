# Validation Library

Simple input validation.

[![Build Status](https://travis-ci.org/werx/validation.png?branch=master)](https://travis-ci.org/werx/validation) [![Total Downloads](https://poser.pugx.org/werx/validation/downloads.png)](https://packagist.org/packages/werx/validation) [![Latest Stable Version](https://poser.pugx.org/werx/validation/v/stable.png)](https://packagist.org/packages/werx/validation)

## Usage
There are two components to this library. A set of validation methods and an input validation engine.

### Validators

The Validator class can be used to quickly validate a single piece of input.

``` php
include 'vendor/autoload.php';

use werx\Validation\Validator;

$input = 'foo';
$valid = Validator::minlength($input, 4);
var_dump($valid);

/*
bool(true)
*/
```

The following validators are available. Each validator returns a bool. `true` = passed validation, `false` = failed validation.

``` php
bool required (mixed $input)
bool date (mixed $input [, $input_format = MM/DD/YYYY])
	# Other input formats available YYYY/MM/DD, YYYY-MM-DD, YYYY/DD/MM, YYYY-DD-MM, DD-MM-YYYY, DD/MM/YYYY, MM-DD-YYYY, MM/DD/YYYY, YYYYMMDD, YYYYDDMM
bool minlength(mixed $input, int $min)
bool maxlength(mixed $input, int $max)
bool exactlength(mixed $input, int $length)
bool greaterthan($input, int $min)
bool lessthan(mixed $input, int $max)
bool alpha(mixed $input)
bool alphanumeric(mixed $input)
bool integer(mixed $input)
bool float(mixed $input)
bool numeric(mixed $input)
bool email(mixed $input)
bool url(mixed $input)
bool phone(mixed $input)
bool zipcode(mixed $input)
bool startswith(mixed $input, string $match)
bool endswith(mixed $input, string $match)
bool contains(mixed $input, string $match)
bool regex(mixed $input, string $regex)
bool inlist(mixed $input, array $list)
bool json(mixed $input)
```

### Validation Engine
The Validation Engine is used to validate a set of data against a set of rules.

#### Usage
First, get an instance of the Validation Engine:

``` php
use werx\Validation\Engine as ValidationEngine;

$validator = new ValidationEngine;
```

Then add rules:

``` php
$validator->addRule('firstname', 'First Name', 'required|minlength[2]|alpha');
```
##### Parameters

- Form input name / array key of the element you are validating
- User friendly label for the element
- Pipe-delimited list of rules
	- Each rule corresponds to a method name from the Validator class
	- If the method accepts arguments, the args should be in square brackets after the rule name
		- Example: `minlength[2]`
		- Exception: methods which accept an array as parameter should be in curly brackets after the rule name.
			- Example: `inlist{red,white,blue}`
	- Except for the `required` validator, all validators will return true if the input is empty.
		- In other words, `minlength[2]` will only actually fire if you also add a `required` rule.

Now you can get a validation result.

``` php
$valid = $validator->validate($_POST);
```

##### Validating Input Arrays
Sometimes you aren't using a simple string as your input field name. Let's say your HTML input form is something like this:

```html
<input type="text" name="volunteer[name]">
<input type="text" name="volunteer[email]">
```

To build a rule for in this scenario, separate the array name and key name with a period when adding your rule.

``` php
$validator->addRule('volunteer.name', 'Name', 'required|minlength[2]|alpha');
$validator->addRule('volunteer.email', 'Email Address', 'required|email');
```

#### Closures
In addition to predefined validation methods from the `Validator` class, you can also use [closures](http://www.php.net/manual/en/functions.anonymous.php) to create custom validation methods.

``` php
$closure = function ($data, $id, $label) {
	$message = null;
	$success = $data[$id] == 'Foo';
	
	if (!$success) {
		$message = sprintf('%s must equal "Foo"', $label);
	}
	
	return [$success, $message];
};

$validator->addRule('firstname', 'First Name', $closure);

$valid = $validator->validate($_POST);
```
Three values will be passed to your closure:

1. The full data set being validated.
2. The id of the element being validated.
3. The label for the element being validated.

The closure is expected to return an array.

- The first element of the array should be the validation result (`bool`).
- The second element of the array should be an error message to display if validation failed.
	- If validation passed, message may be null.

#### Rulesets
What if you want to save groups of rules instead of adding each rule individually every time you want to validate them?  We've got you covered.

Create a new class that extends `werx\Validation\Ruleset` and add your rules in the constructor.

``` php
namespace your\namespace\Rulesets;

use werx\Validation\Ruleset;

class Contact extends Ruleset
{
	public function __construct()
	{
		$this->addRule('firstname', 'First Name', 'required|minlength[2]');
		$this->addRule('lastname', 'Last Name', 'required');
		$this->addRule('phone', 'Phone Number', 'required|phone');
		$this->addRule('email', 'Email Address', 'required|email');
	}
}
```

Then when you are ready to validate this group of rules:

``` php
$contact_rules = new your\namespace\Rulesets\Contact;
$validator->addRuleset($contact_rules);
$valid = $validator->validate();
```

#### Utility Methods

There are a couple utilities to make dealing with validation results easier.

##### getErrorSummary()
Returns a simple array containing a list of validation error messages.

``` php
if (!$valid) {
	$summary = $validator->getErrorSummary();
}

/*
Array
(
	[0] => First Name must only contain the letters A-Z.
	[1] => First Name must be at least 2 characters long.
	[2] => Last Name is a required field.
)
*/
```

##### getErrorSummaryFormatted()
Returns the error summary formatted as an html unordered list (`<ul>`).

##### getErrorFields()
Returns list of fields that had an error. Useful if you want to apply some decoration to your form indicating which fields had a validation errors.

``` php
if (!$valid) {
	$error_fields = $validator->getErrorFields();
}

/*
Array
(
	[0] => firstname
	[1] => lastname
)
*/
```

##### getRequiredFields()
Once you've added your rules, you can get back a list of required fields. This is useful when you want to indicate on your form
which fields must be completed.

``` php

$validator->addRule('firstname', 'First Name', 'required');
$validator->addRule('lastname', 'Last Name', 'required');
$validator->addRule('age', 'Age', 'required|integer');

$required = $validator->getRequiredFields();

/*
Array
(
	[0] => firstname
	[1] => lastname
	[2] => age
)
*/
```

##### addCustomMessage()
Allows you to set custom error messages.

When displaying the error messages, `{name}` will be replaced with the name of the field being validated. The rest of the field
is parsed with [`sprintf()`](http://php.net/sprintf) so that parameters like `minlength` can be placed in the returned error message.

Examples:

``` php

$validator->addCustomMessage('required', "You didn't provide a value for {name}!");
$validator->addCustomMessage('minlength', "Oops, {name} must be at least %d characters long.");
```


## Installation
This package is installable and autoloadable via Composer as [werx/validation](https://packagist.org/packages/werx/validation). If you aren't familiar with the Composer Dependency Manager for PHP, [you should read this first](https://getcomposer.org/doc/00-intro.md).

```bash
$ composer require werx/validation --prefer-dist
```

## Contributing

### Unit Testing

``` bash
$ vendor/bin/phpunit
```

### Coding Standards
This library uses [PHP_CodeSniffer](http://www.squizlabs.com/php-codesniffer) to ensure coding standards are followed.

I have adopted the [PHP FIG PSR-2 Coding Standard](http://www.php-fig.org/psr/psr-2/) EXCEPT for the tabs vs spaces for indentation rule. PSR-2 says 4 spaces. I use tabs. No discussion.

To support indenting with tabs, I've defined a custom PSR-2 ruleset that extends the standard [PSR-2 ruleset used by PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/PSR2/ruleset.xml). You can find this ruleset in the root of this project at PSR2Tabs.xml

Executing the codesniffer command from the root of this project to run the sniffer using these custom rules.

``` bash
$ ./codesniffer
```