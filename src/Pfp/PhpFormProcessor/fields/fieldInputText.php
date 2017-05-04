<?php
namespace Pfp\PhpFormProcessor\fields;

use Pfp\PhpFormProcessor\fields\fieldBase;

class fieldInputText extends fieldBase {

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
      $this->errors[] = array(
        'key'     => $this->key,
        'status'  => 'error_string_maxlength',
        'message' => 'Maximum '. $this->maxlength .' characters in '. $this->label .'.',
      );
    }

    if ($this->type == 'email' && !filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
      $this->errors[] = array(
        'key'     => $this->key,
        'status'  => 'error_email_format',
        'message' => $this->label .' field contains an invalid email address.',
      );
    }

    // Return errors
    return $this->errors;

  } // end validate data

}
