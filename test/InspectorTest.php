<?php

include_once(__DIR__ . '/../lib/inspector.php');

class ValidatorTest extends PHPUnit_Framework_TestCase {

  protected $input1 = array("username" => "validator", "email" => "validator@smgt.me", "homepage" => "http://github.com/flattr/validator");
  protected $input2 = array("username" => "%", "email" => "validator@smgt", "homepage" => "https:///github.com/flattr/validator", "password" => "asdf", "password_confirmation" => "qwer");

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

  function testNull() {
    $inspector = new Inspector(array("one" => null, "two" => "not null"));
    $inspector->ensure("one")->notNull("is null");
    $inspector->ensure("two")->isNull("is not null");
    $this->assertTrue( $inspector->hasErrors() );
  }

  function testMinMax() {
    $inspector = new Inspector($this->input1);
    $inspector->ensure("username")->isMax(20, "First max");
    $inspector->ensure("username")->isMin(1, "Is min")->isMax(20,"Second max");
    $this->assertFalse( $inspector->hasErrors() );
  }
  function testSame() {
    $inspector = new Inspector($this->input2);
    $inspector->ensure("password")->isSame("password_confirmation", "Passwords are not the same");
    var_dump($inspector->errors());
    $this->assertTrue( $inspector->hasErrors() );
  }

}
