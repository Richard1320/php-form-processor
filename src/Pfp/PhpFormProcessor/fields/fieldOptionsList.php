<?php
namespace Pfp\PhpFormProcessor\fields;

use Pfp\PhpFormProcessor\fields\fieldBase;

class fieldOptionsList extends fieldBase {

  public $deep;

  function __construct($key,$args) {

    // call parent constructor
    parent::__construct($key,$args);

    $this->deep = (isset($args['deep'])) ? $args['deep'] : array();
  } // End construct

  function is_allowed_value($data) {
    // Check if data is a valid option
    return array_key_exists($data,$this->deep);

  } // end isEmpty check


  function validation() {
    // Run parent validation tests
    $validation = parent::validation();
    if ($validation === false) return $validation;

    // Check if option is in allowed list
    if (!$this->recursive_array_validation('is_allowed_value',$this->value)) {
      $this->errors[] = array(
        'key'     => $this->key,
        'status'  => 'error_option_allowed_value',
        'message' => $this->label .' contains an invalid option ',
      );
    }

    // Return errors
    return $this->errors;

  } // end validate data

}
