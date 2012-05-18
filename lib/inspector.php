<?php
/**
 * Inspector 
 *
 * An easy and extensible validation library
 *
 * @package Inspector
 * @author Simon Gate
 * @copyright Copyright (c) 2011 - 2012
 * @link https://github.com/simon/inspector
 */

/**
 * Exception for Inspector class
 */
class InspectorException extends Exception {}

/**
 * Main Inspector class
 */
class Inspector {

  public static $_methods = array();
  public $_substance = array();
  protected $_string = null;
  protected $_key = null;
  protected $_errors = null;
  protected $_error_default_msg = null;

  /**
   * Creates a new inspector instance
   *
   * @param mixed
   * @param string
   */
  public function __construct($substance, $error_default_msg=null) {
    static::addDefaultMethods();
    $this->_substance = $substance;
    $this->_error_default_msg = $error_default_msg;
  }

  /**
   * Creates a validation ruleset
   *
   * @param string
   * @param string
   * @return $this
   */
  public function ensure($key_or_string, $error_default_msg=null) {
    if(!empty($this->_substance)) {
      $this->_string = $this->_substance[$key_or_string]; 
      $this->_key = $key_or_string;
      $this->_error_default_msg = $error_default_msg;
    }
    return $this;
  }

  /**
   * Adds a custom validation method
   *
   * @param string
   * @param callable
   */
  public static function addValidator($method, $callback) {
        static::$_methods[strtolower($method)] = $callback;
  }

  /**
   * Adds default validation rules
   */
  public static function addDefaultMethods() {
      static::$_methods['null'] = function($str) {
        return $str === null || $str === '';
      };
      static::$_methods['max'] = function($str, $max) {
          $len = strlen($str);
          return $len <= $max;
      };
      static::$_methods['min'] = function($str, $min) {
          $len = strlen($str);
          return $len >= $min;
      };
      static::$_methods['int'] = function($str) {
          return (string)$str === ((string)(int)$str);
      };
      static::$_methods['float'] = function($str) {
          return (string)$str === ((string)(float)$str);
      };
      static::$_methods['email'] = function($str) {
          return filter_var($str, FILTER_VALIDATE_EMAIL) !== false;
      };
      static::$_methods['url'] = function($str) {
          return filter_var($str, FILTER_VALIDATE_URL) !== false;
      };
      static::$_methods['ip'] = function($str) {
          return filter_var($str, FILTER_VALIDATE_IP) !== false;
      };
      static::$_methods['alnum'] = function($str) {
          return ctype_alnum($str);
      };
      static::$_methods['alpha'] = function($str) {
          return ctype_alpha($str);
      };
      static::$_methods['contains'] = function($str, $needle) {
          return strpos($str, $needle) !== false;
      };
      static::$_methods['sameas'] = function($str, $needle) {
          return (strcmp($str, $needle) === 0);
      };
      static::$_methods['regex'] = function($str, $pattern) {
          return preg_match($pattern, $str);
      };
      static::$_methods['chars'] = function($str, $chars) {
          return preg_match("/[$chars]+/i", $str);
      };
  }
  
  /**
   * Checks if there are validation errors
   *
   * @return bool
   */
  public function hasErrors() {
    return !empty($this->_errors);
  }

  /**
   * Returns list of validation errors
   *
   * @return array
   */
  public function errors() {
    return $this->_errors;
  }

  /**
   * Throw an exception if errors exist
   *
   * @return mixed
   */
  public function validate() {
    if(!empty($this->_errors)) {
      throw new InspectorException();
    }else{
      return true;
    }
  }

  /**
   * Method to simplify calling validation methods
   *
   * @param mixed
   * @param array
   * @return mixed
   */
  public function __call($method, $args) {
    $reverse = false;
    $validator_name = $method;
    $method_substr = substr($method, 0, 2);

    if ($method_substr === 'is') {       //is<$validator>()
        $validator_name = substr($method, 2);
    } elseif ($method_substr === 'no') { //not<$validator>()
        $validator_name = substr($method, 3);
        $reverse = true;
    }

    $validator_name = strtolower($validator_name);

    if (!$validator_name || !isset(static::$_methods[$validator_name])) {
        throw new ErrorException("Unknown method $method()");
    }

    $validator = static::$_methods[$validator_name];

    $ref = new ReflectionFunction($validator);
    $num_parameters = $ref->getNumberOfParameters(); 

    array_unshift($args, $this->_string);

    /* print "\nRunning test with ".$validator_name."\n"; */
    /* print_r($args); */
    /* echo $num_parameters." / ".sizeof($args) . "\n"; */
    if($num_parameters < sizeof($args))
    {
      $error_msg = array_pop($args);
    }
    /* print_r($args); */

    $result = call_user_func_array($validator, $args);

    $result = (bool)($result ^ $reverse);

    if($result == false) {

      if(!isset($this->_errors[$this->_key]) || !is_array($this->_errors[$this->_key])) {
        $this->_errors[$this->_key] = array();
      }

      if(!empty($this->_error_default_msg)) {
        if(!in_array($this->_error_default_msg, $this->_errors[$this->_key])) {
          $this->_errors[$this->_key][] = $this->_error_default_msg;
        }
      }

      if(!empty($error_msg)) {
        $this->_errors[$this->_key][] = $error_msg;
      } else {
        $this->_errors[$this->_key][] = "does not validate.";
      }

    }

    return $this; 
  }

}
