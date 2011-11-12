# Inspector

inspector |inˈspektər| - noun -  an official employed to ensure that official regulations are obeyed.

Inspector is you easy to use validations handler for any of your PHP applications. Inspector is easy to use and easy to learn more rules to impose on the data.

## Validaton examples

### Valid data

	// Example data that should be valid
    $data = array("username" => "simon", "email" => "simon@localhost.local", "password" => "secret", "password_confirmation" => "secret");

	// Create a new instance of inspector
	$inspector = new Inspector($data);

	// Ensure that the data is valid
	$inspector->ensure("username")->isAlpha("Wired characters in username")->isMin(3, "Username is to short")->isMax("Username is to long");

	$inspector->ensure("email")->isValidEmail("E-mail is not valid")->notNull("You need to supply an email");

	$inspector->ensure("password")->isSame("password_confirmation", "Passwords don't correspond")->isMin(6, "Password is to short");

	echo $inspector->hasErrors(); // Returns false

### Invalid data

	// Example data that should be valid
    $data = array("username" => "s", "email" => "simon@--", "password" => "s", "password_confirmation" => "b");

	// Create a new instance of inspector
	$inspector = new Inspector($data);

	// Ensure that the data is valid
	$inspector->ensure("username")->isAlpha("Wired characters in username")->isMin(3, "Username is to short")->isMax("Username is to long");

	$inspector->ensure("email")->isValidEmail("E-mail is not valid")->notNull("You need to supply an email");

	$inspector->ensure("password")->isSame("password_confirmation", "Passwords don't correspond")->isMin(6, "Password is to short");

	echo $inspector->hasErrors(); // Returns true

	print_r($inspector->errors());

	/*
	Array
	(
    	[username] => Array
        	(
            	[0] => Username is to short
        	)
		[email] => Array
			(
				[0] => E-mail is not valid
			)
		[password] => Array
			(
				[0] => Passwords don't correspond
				[1] => Password is to short
			)

	)
	*/

	// If you like you can throw an InspectorException
	
	$inspector->fuck(); // InspectorException

## License

Copyright (c) 2011 Simon Gate <simon@smgt.me>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.