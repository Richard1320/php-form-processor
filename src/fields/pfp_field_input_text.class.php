<?php
class pfp_field_input_text extends pfp_field_base {

  public $maxlength;

  function __construct($key,$args) {

    // call parent constructor
    parent::__construct($key,$args);

    $this->maxlength = (isset($args['maxlength'])) ? (int)$args['maxlength'] : false;  // max string length of submitted value

  } // End construct

  function field_html() {
    $output = '';

    ob_start();

    include dirname(__FILE__).'/../tpl/fields/input_text.tpl.php';

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
  }
  function validation() {
    // Run parent validation tests
    $validation = parent::validation();
    if ($validation === false) return $validation;

    // check if string is longer than allowed length
    if ($this->maxlength && strlen($this->value) > $this->maxlength) {
      $this->errors[] = 'Maximum '. $this->maxlength .' characters in '. $label .'.';
    }

    if ($this->type == 'email' && !filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
      $this->errors[] = $label .' field contains an invalid email address.';
    }

    // Return errors
    return $this->errors;

  } // end validate data

}
