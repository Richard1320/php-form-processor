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
    // Default check to true to return false for errors
    $check = array('status'=>true);

    // Check if the parameter is an array
    if(is_array($data)) {
      // Loop through the initial dimension
      foreach($data as $value) {
        // Let the function call itself over that particular node
        $check = $this->is_allowed_value($value);

        // If any item in the loop returns false, break to return false
        if (!$check['status']) {
          break;
        } // end empty check
      } // end foreach loop
    } // end array check

    // Check if the value is a string
    if(is_string($data)) {
      // If it is, perform a check on the string value
      if (array_key_exists($data,$this->deep)) {
        $check = array('status'=>true);
      } else {
        $check = array('status'=>false,'invalid_value'=>$data);
      }
    }

    // Return the final check
    return $check;

  } // end isEmpty check


  function validation() {
    // Run parent validation tests
    $validation = parent::validation();
    if ($validation === false) return $validation;

    // Check if option is in allowed list
    $is_allowed_value = $this->is_allowed_value($this->value);
    if (!$is_allowed_value['status']) {
      $this->errors[] = array(
        'key'     => $this->key,
        'status'  => 'error_option_allowed_value',
        'message' => $is_allowed_value['invalid_value'] .' is not in the list of availble options for '. $this->label,
      );
    }

    // Return errors
    return $this->errors;

  } // end validate data

}
