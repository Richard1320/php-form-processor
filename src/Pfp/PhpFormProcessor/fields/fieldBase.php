<?php
namespace Pfp\PhpFormProcessor\fields;

class fieldBase {

  public $key; // id of field
  public $name; // name attribute of field
  public $errors; // List of errors
  public $required; // Is field required
  public $label; // Field label to display
  public $classes; // Array of classes on field div wrapper
  public $f_classes; // Array of classes on form input element
  public $attributes; // list of extra attribues as attribute name => value
  public $default_value; // Default value on field
  public $description; // Help description to display to user under form field
  public $multiple; // Allow field to accept multiple values
  public $value; // Value submitted by the user
  public $type; // Type of input field


  function __construct($key,$args) {
    $this->key           = $key;
    $this->value         = '';
    $this->errors        = array();
    $this->name          = (isset($args['name'])) ? $args['name'] : '';
    $this->required      = (isset($args['required'])) ? $args['required'] : false;
    $this->label         = (isset($args['label'])) ? $args['label'] : '';
    $this->classes       = (isset($args['classes'])) ? $args['classes'] : array();
    $this->f_classes     = (isset($args['f_classes'])) ? $args['f_classes'] : array();
    $this->attributes    = (isset($args['attributes'])) ? $args['attributes'] : array();
    $this->default_value = (isset($args['default_value'])) ? $args['default_value'] : '';
    $this->description   = (isset($args['description'])) ? $args['description'] : '';
    $this->multiple      = (isset($args['multiple'])) ? $args['multiple'] : false;
    $this->type          = (isset($args['type'])) ? (string)$args['type'] : 'text'; // type of input field

  } // End construct

  function field_html() {

    return '';
  }

  function render() {
    echo $this->field_html();
  }

  function render_attributes() {
    foreach ($this->attributes as $key => $value) {
      ?>
      <?php echo htmlspecialchars($key); ?>="<?php echo htmlspecialchars($value); ?>"
      <?php
    }
  }

  function is_filled_in($data) {
    // Default check to true to return false for errors
    $check = true;

    // Check if the parameter is an array
    if(is_array($data)) {
      // Loop through the initial dimension
      foreach($data as $key => $value) {
        // Let the function call itself over that particular node
        $check = $this->is_filled_in($value);

        // If any item in the loop returns false, break to return false
        if (!$check) {
          break;
        } // end empty check
      } // end foreach loop
    } // end array check

    // Check if the parameter is a string
    if(is_string($data)) {
      // If it is, perform a check on the string value
      return (empty($data)) ? false : true;
    }

    // Return the final check
    return $check;

  } // end is not empty check

  // checks if value is string or array
  function is_valid_type($data) {
    // Default check to true to return false for errors
    $check = true;

    // check if data is not array or string
    if (!is_array($data) && !is_string($data)) {
      $check = false;
    }

    // Return the final check
    return $check;

  } // end is valid type check

  function validation() {

    // Clear list of errors for clean re-validation
    $this->errors = array();

    // checks if value is empty
    if (!$this->is_filled_in($this->value)) {
      // register error if field is required
      if ($this->required) {
        $this->errors[] = array(
          'key'     => $this->key,
          'status'  => 'error_is_filled_in',
          'message' => $this->label .' field is required.',
        );

      }

      // Stop further checks on field if empty
      return false;
    }

    // checks if value is string or array
    if (!$this->is_valid_type($this->value) && $this->required) {
      $this->errors[] = array(
        'key'     => $this->key,
        'status'  => 'error_is_valid_type',
        'message' => $this->label .' field data contains an invalid type.',
      );
    }

    // Return errors
    return $this->errors;

  } // end validate data


}
