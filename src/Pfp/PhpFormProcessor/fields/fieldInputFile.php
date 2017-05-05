<?php
namespace Pfp\PhpFormProcessor\fields;

use Pfp\PhpFormProcessor\fields\fieldBase;

class fieldInputFile extends fieldBase {

  protected $maxsize; // max filesize in bytes
  public    $allowed_extensions; // array of allowed extensions

  function __construct($key,$args) {

    // call parent constructor
    parent::__construct($key,$args);

    $this->maxsize            = (isset($args['maxsize'])) ? (int)$args['maxsize'] : 8388608;
    $this->allowed_extensions = (isset($args['allowed_extensions'])) ? (array)$args['allowed_extensions'] : false;

  } // End construct

  function recursive_array_validation_file($function_name, $data) {
    // Default check to true to return false for errors
    $check = true;

    // Check if the parameter is an array
    if(!isset($data['tmp_name'])) {
      // Loop through the initial dimension
      foreach($data as $value) {
        // Let the function call itself over that particular node
        $check = $this->recursive_array_validation_file($function_name, $value);

        // If any item in the loop returns false, break to return false
        if (!$check) {
          break;
        } // end empty check
      } // end foreach loop
    } // end array check

    // Check if the parameter is a string
    if(isset($data['tmp_name'])) {
      // If it is, perform a check on the string value
      return $this->$function_name($data);
    }

    // Return the final check
    return $check;

  }


  function field_html() {
    $output = '';

    ob_start();

    include dirname(__FILE__).'/../tpl/fields/input_file.tpl.php';

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
  }
  function is_file_exists($data) {
    // Check if file exists
    return file_exists($data['tmp_name']);

  } // end is file exists

  function is_allowed_extension($data) {

    // Check if extension is allowed
    $temp_file = $data['tmp_name'];
    $extension = pathinfo($temp_file, PATHINFO_EXTENSION);

    return in_array($extension,$this->allowed_extensions);

  } // end is in allowed files

  function is_under_maxsize($data) {
    // Check if size is smaller than max size
    $size = $data['size'];
    return ($size <= $this->maxsize);

  } // end is under maxsize

  function validation() {
    // Run parent validation tests
    $validation = parent::validation();
    if ($validation === false) return $validation;

    // empty file
    if (!$this->recursive_array_validation_file('is_file_exists',$this->value)) {
      $this->errors[] = array(
        'key'     => $this->key,
        'status'  => 'error_file_exists',
        'message' => $this->label .' file is missing. Please contact system administrator.',
      );

      // Don't run any more checks on file that does not exist
      return false;
    }

    // Check if file extension is valid
    if($this->allowed_extensions && !$this->recursive_array_validation_file('is_allowed_extension',$this->value) ) {
      $this->errors[] = array(
        'key'     => $this->key,
        'status'  => 'error_allowed_extensions',
        'message' => 'Only '. implode(', ',$this->allowed_extensions) .' allowed in '. $this->label .' field.',
      );
    }

    // Check if a max size is set
    if (!$this->recursive_array_validation_file('is_under_maxsize',$this->value)) {
      $this->errors[] = array(
        'key'     => $this->key,
        'status'  => 'error_file_maxsize',
        'message' => 'Max file size is '. $this->maxsize / 1048600 .' MB.',
      );
    }

    // Return errors
    return $this->errors;

  } // end validate data

}
