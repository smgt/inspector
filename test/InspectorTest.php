<?php

include_once(__DIR__ . '/../lib/inspector.php');

class ValidatorTest extends PHPUnit_Framework_TestCase {

  protected $input1 = array(
    "username" => "inspector",
    "age" => 60,
    "email" => "inspector@smgt.me",
    "homepage" => "http://github.com/simon/inspector",
    "password" => "asdf",
    "password_confirmation" => "asdf"
  );
  protected $input2 = array(
    "username" => "%",
    "age" => "sixty",
    "email" => "inspector@smgt",
    "homepage" => "https:///github.com/simon/inspector",
    "password" => "asdf",
    "password_confirmation" => "qwer"
  );

  function testValidChain() {
    $inspector = new Inspector($this->input1);
    $inspector->ensure("username")->isAlpha()->isMin(2)->isMax(10);
    $inspector->ensure("email")->isEmail();
    $inspector->ensure("homepage")->isUrl();
    
    $this->assertFalse( $inspector->hasErrors() );    
  }

  function testInvalidChain() {
    $inspector = new Inspector($this->input2);
    $inspector->ensure("username", "is not only alpha characthers")->isAlpha("alphaerror")->isMin(2, "To short")->isMax(10, "To long");
    $inspector->ensure("email")->isEmail("is not a valid email");
    $inspector->ensure("homepage")->isUrl() ;

    $this->assertTrue( $inspector->hasErrors() );
  }

  function testDefaultErrorMessages() {
    $inspector = new Inspector($this->input2);
    $inspector->ensure("email")->isEmail();

    $errors = $inspector->errors();

    $this->assertEquals("does not validate.", $errors['email'][0]);
  }

  function testCustomErrorMessages() {
    $inspector = new Inspector($this->input2);
    $inspector->ensure("email")->isEmail("not a valid email");
    $inspector->ensure("username")->isAlpha("not only alpha characters");
    $errors = $inspector->errors();

    $this->assertEquals("not a valid email", $errors['email'][0]);
    $this->assertEquals("not only alpha characters", $errors['username'][0]);
  }

  function testCustomValidatorMethod() {
    Inspector::addValidator("ninja", function($str) {
      return strpos($str, "ninja") !== false;
    });
    $inspector = new Inspector(array("name" => "Inspector 'ninja' Gadget"));
    $inspector->ensure("name")->isNinja();
    $this->assertFalse( $inspector->hasErrors() );
  }

  function testException() {
    $this->setExpectedException('InspectorException'); 
    $inspector = new Inspector($this->input2);
    $inspector->ensure("email")->isEmail("Not valid e-mail");
    $inspector->validate();
  }

  // Validator null
  function testValidatorNull() {
    $inspector = new Inspector(array("one" => null, "two" => "not null"));
    $inspector->ensure("one")->notNull("is null");
    $inspector->ensure("two")->isNull("is not null");
    $this->assertTrue( $inspector->hasErrors() );
  }

  // Validator max
  function testValidatorMaxFalse() {
    $inspector = new Inspector($this->input1);
    $inspector->ensure("username")->isMax(20, "is to long");
    $this->assertFalse( $inspector->hasErrors() );
  }

  function testValidatorMaxTrue() {
    $inspector = new Inspector($this->input1);
    $inspector->ensure("username")->isMax(3, "is to long");
    $this->assertTrue( $inspector->hasErrors() );
  }

  // Validator min
  function testValidatorMinFalse() {
    $inspector = new Inspector($this->input1);
    $inspector->ensure("username")->isMin(1, "is to short");
    $this->assertFalse( $inspector->hasErrors() );
  }

  function testValidatorMinTrue() {
    $inspector = new Inspector($this->input1);
    $inspector->ensure("username")->isMin(20, "is to short");
    $this->assertTrue( $inspector->hasErrors() );
  }

  // Validator int
  function testValidatorInt() {
    $inspector = new Inspector($this->input1);
    $inspector->ensure("age")->isInt("is not a integer");
    $inspector->ensure("username")->notInt("is a integer");
    $this->assertFalse( $inspector->hasErrors() );
  }

  // Validator float
  function testValidatorFloat() {
    $inspector = new Inspector(array("apple" => 10.7, "pear" => 10, "orange" => "five"));
    $inspector->ensure("apple")->isFloat();
    $inspector->ensure("pear")->isFloat();
    $inspector->ensure("orange")->notFloat();
    $this->assertFalse( $inspector->hasErrors() );
  }

  // Validator email
  function testValidatorEmail() {
    $inspector = new Inspector($this->input1);
    $inspector->ensure("email")->isEmail();
    $inspector->ensure("username")->notEmail();
    $this->assertFalse( $inspector->hasErrors() );
  }

  // Validator url
  function testValidatorUrl() {
    $inspector = new Inspector($this->input1);
    $inspector->ensure("homepage")->isUrl();
    $inspector->ensure("username")->notUrl();
    $this->assertFalse( $inspector->hasErrors() );
  }

  // Validator ip
  function testValidatorIp() {
    $inspector = new Inspector(array("localhost" => "127.0.0.1", "username" => "inspector"));
    $inspector->ensure("localhost")->isIp();
    $inspector->ensure("username")->notIp();
    $this->assertFalse( $inspector->hasErrors() );
  }

  // Validator alnum
  function testValidatorAlnum() {
    $inspector = new Inspector(array("username" => "ninja44", "name" => "Inspector Gadget", "email" => "inspector@smgt.me"));
    $inspector->ensure("username")->isAlnum();
    $inspector->ensure("name")->notAlnum();
    $inspector->ensure("email")->notAlnum();
    $this->assertFalse( $inspector->hasErrors() );
  }

  // Validator alpha
  function testValidatorAlpha() {
    $inspector = new Inspector($this->input1);
    $inspector->ensure("username")->isAlpha();
    $inspector->ensure("age")->notAlpha();
    $this->assertFalse( $inspector->hasErrors() );
  }

  // Validator contains 
  function testValidatorContains() {
    $inspector = new Inspector($this->input1);
    $inspector->ensure("username")->contains("nspec");
    $inspector->ensure("username")->notContains("shit");
    $this->assertFalse( $inspector->hasErrors() );
  }

  // Validator sameAs
  function testValidatorSameAs() {
    $inspector = new Inspector($this->input1);
    $inspector->ensure("password")->isSameAs($this->input1['password_confirmation']);
    $this->assertFalse( $inspector->hasErrors() );
  }

  // Validator regex
  function testValidatorRegex() {
    $inspector = new Inspector($this->input1);
    $inspector->ensure("username")->isRegex("/[a-z]+/");
    $inspector->ensure("email")->isRegex("/[a-z]+@[a-z]+.[a-z]{2,3}/");
    $inspector->ensure("age")->notRegex("/[a-z]+/");
    $this->assertFalse( $inspector->hasErrors() );
  }

  // Validator chars
  function testValidatorChars() {
    $inspector = new Inspector($this->input1);
    $inspector->ensure("username")->isChars("pec", "username");
    $inspector->ensure("password")->notChars("qwer", "password");
    $this->assertFalse( $inspector->hasErrors() );
  }


}
