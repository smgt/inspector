# Inspector

> inspector [inˈspektər] - noun -  an official employed to ensure that official regulations are obeyed.

Inspector is your validations handler for PHP applications. Inspector is easy to use and easy to learn and imposes rules on the payload supplied.

## Validaton examples

### Valid payload

```php
<?php
// Example payload that is
$data = array(
  "username" => "simon",
  "email" => "simon@localhost.local",
  "password" => "secret",
  "password_confirmation" => "secret"
);

// Create a new instance of inspector
$inspector = new Inspector($data);

// Ensure that the data is valid
$inspector->ensure("username")
  ->isAlpha("is not only alpha characters")
  ->isMin(3, "is to short")
  ->isMax(10, "is to long");

$inspector->ensure("email")
  ->isValidEmail("is not valid")
  ->notNull("is empty");

$inspector->ensure("password")
  ->isSame("password_confirmation", "don't correspond")
  ->isMin(6, "is to short");

// Check if any errors exist
echo $inspector->hasErrors(); // Returns false

// Throw a exception if there are errors
$inspector->validate(); // Returns false
```

### Invalid payload

```php
<?php
// Example payload that is valid
$data = array(
  "username" => "s",
  "email" => "simon@--",
  "password" => "s",
  "password_confirmation" => "b"
);

// Create a new instance of inspector
$inspector = new Inspector($data);

// Ensure that the data is valid
$inspector->ensure("username")
  ->isAlpha("is not only alpha characters")
  ->isMin(3, "is to short")
  ->isMax(10, "is to long");

$inspector->ensure("email")
  ->isValidEmail("is not valid")
  ->notNull("can not be empty");

$inspector->ensure("password")
  ->isSame("password_confirmation", "don't correspond")
  ->isMin(6, "is to short");

echo $inspector->hasErrors(); // Returns true

print_r($inspector->errors());

/*
Array
(
  [username] => Array
    (
      [0] => is to short
    )
  [email] => Array
    (
      [0] => is not valid
    )
  [password] => Array
    (
      [0] => don't correspond
      [1] => is to short
    )
)
*/

// If you like you can throw an InspectorException

$inspector->validate(); // InspectorException
```

## Available validations

Inspector comes loaded with a few default validators.

```
isNull() / notNull()                     - Check if payload is null
isMax($int) / notMax($int)               - Check length of string
isMin($int) / notMin($int)               - Check length of string
isFloat() / notFloat()                   - Check if payload is a float
isInt() / notInt()                       - Check if payload is a int
isUrl() / notUrl()                       - Check if payload is a URL
isEmail() / notEmail()                   - Check if payload is a e-mail
isIp() / notIp()                         - Check if payload is a valid ip
isAlnum() / notAlnum()                   - Check if payload is alphanumeric
contains($needle) / notContains($needle) - Check if $needle exists in payload
isSameAs($needle) / notSameAs($needle)   - Check if $needle is exactly the same as payload
isRegex($regex) / notRegex($regex)       - Check if $regex matches payload
isChars($chars) / notChars($chars)       - Check if $chars exist inside string
```

## Add your own validator

It's easy to add your own validators to Inspector. Below I added a
validator to check if the payload contains _ninja_. You don't need to
specify ```is```/```not``` when you add validators, they gets added
magically. Also notice the number of arguments, ```$str``` is the string
getting tested. The error message argument is added automagically. There
is a small ceavat when doing it this way. You can't create validators
with optional arguments. 

```php
<?php

    Inspector::addValidator("ninja", function($str) {
      return strpos($str, "ninja") !== false;
    });

    $inspector = new Inspector(array(
      "name" => "Inspector 'ninja' Gadget",
      "email" => "inspector.gadget@localhost"
    ));
    
    // Check that "ninja" is present
    $inspector->ensure("name")->isNinja("ninja is missing");
    
    // Check that "ninja" is present (same as isNinja)
    $inspector->ensure("name")->ninja("ninja is missing");
    
    // Check that "ninja" is not present
    $inspector->ensure("email")->notNinja("ninja is present");
    $inspector->hasErrors(); // False
```

## License

Copyright (c) 2011 Simon Gate <simon@smgt.me>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
