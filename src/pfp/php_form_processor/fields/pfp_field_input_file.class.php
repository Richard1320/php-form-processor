<?php
namespace pfp\php_form_processor;

class pfp_field_input_file extends pfp_field_base {

  protected $maxsize; // max filesize in bytes
  public    $allowed_extensions; // array of allowed extensions

  function __construct($key,$args) {

    // call parent constructor
    parent::__construct($key,$args);

    $this->maxsize            = (isset($args['maxsize'])) ? (int)$args['maxsize'] : 1048600;
    $this->allowed_extensions = (isset($args['allowed_extensions'])) ? (array)$args['allowed_extensions'] : false;

  } // End construct
  function field_html() {
    $output = '';

    ob_start();

    include dirname(__FILE__).'/../tpl/fields/input_file.tpl.php';

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
  }

  function validation() {
    // Run parent validation tests
    $validation = parent::validation();
    if ($validation === false) return $validation;

    $temp_file = $this->value['tmp_name'];
    $size      = $this->value['size'];
    $filename  = $this->value['name'];
    $extension = pathinfo($temp_file, PATHINFO_EXTENSION);

    // empty file
    if (!file_exists($temp_file)) {
      $this->errors[] = $this->label .' file is missing. Please contact system administrator.';

      // Don't run any more checks on file that does not exist
      return false;
    }

    // Check if file extension is valid
    if($this->allowed_extensions && !in_array($extension,$this->allowed_extensions) ) {
      $this->errors[] = 'Only '. implode(', ',$this->allowed_extensions) .' allowed in '. $this->label .' field.';
    }

    // Check if a max size is set
    if ($size > $this->maxsize) {
      $this->errors[] = 'Max file size is '. $this->maxsize / 1048600 .' MB.';
    }

    // Return errors
    return $this->errors;

  } // end validate data

}
