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
    require_once dirname(__FILE__).'/../helpers/sanitize_alphanumeric.helper.php';

    $this->key           = $key;
    $this->value         = '';
    $this->errors        = array();
    $this->name          = (isset($args['name'])) ? $args['name'] : '';
    $this->required      = (isset($args['required'])) ? $args['required'] : false;
    $this->label         = (isset($args['label'])) ? $args['label'] : '';
    $this->attributes    = (isset($args['attributes'])) ? $args['attributes'] : array();
    $this->default_value = (isset($args['default_value'])) ? $args['default_value'] : '';
    $this->description   = (isset($args['description'])) ? $args['description'] : '';
    $this->multiple      = (isset($args['multiple'])) ? $args['multiple'] : false;
    $this->type          = (isset($args['type'])) ? (string)$args['type'] : 'text'; // type of input field

    // Set form item classes
    $field_wrapper_custom_classes   = (isset($args['classes'])) ? (array)$args['classes'] : array();
    $field_wrapper_addition_classes = array('form-item', 'form-type-'. $this->type, 'form-name-'. $this->name );
    $field_wrapper_full_classes     = array_merge($field_wrapper_addition_classes, $field_wrapper_custom_classes);
    $this->classes                  = sanitize_alphanumeric_array($field_wrapper_full_classes);

    // Set form input classes
    $field_input_custom_classes     = (isset($args['f_classes'])) ? (array)$args['f_classes'] : array();
    $field_input_addition_classes   = array('form-'. $this->type );
    $field_input_full_classes       = array_merge($field_input_addition_classes, $field_input_custom_classes);
    $this->f_classes                = sanitize_alphanumeric_array($field_input_full_classes);


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

  function recursive_array_validation($function_name, $data) {
    // Default check to true to return false for errors
    $check = true;

    // Check if the parameter is an array
    if(is_array($data)) {
      // Loop through the initial dimension
      foreach($data as $value) {
        // Let the function call itself over that particular node
        $check = $this->recursive_array_validation($function_name, $value);

        // If any item in the loop returns false, break to return false
        if (!$check) {
          break;
        } // end empty check
      } // end foreach loop
    } // end array check

    // Check if the parameter is a string
    if(is_string($data)) {
      // If it is, perform a check on the string value
      return $this->$function_name($data);
    }

    // Return the final check
    return $check;

  }

  function is_filled_in($data) {
    // Return true if data is NOT empty
    return (empty($data)) ? false : true;
  } // end is not empty check

  // checks if value is string or array
  function is_valid_amount($data) {
    // Default check to true to return false for errors
    $check = true;

    // check if non-multiple check is a string
    if (!$this->multiple && !is_string($data) && $this->type != 'checkbox') {
      $check = false;
    }

    // check if multiple is an array or string
    if ($this->multiple && !is_array($data) && !is_string($data)) {
      $check = false;
    }

    // Return the final check
    return $check;

  } // end is valid type check

  function validation() {

    // Clear list of errors for clean re-validation
    $this->errors = array();

    // checks if value is empty
    if (!$this->recursive_array_validation('is_filled_in',$this->value)) {
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

    // checks if value is string or array for single / multiple allowed values
    if ($this->type != 'file' && !$this->is_valid_amount($this->value)) {
      $this->errors[] = array(
        'key'     => $this->key,
        'status'  => 'error_is_valid_amount',
        'message' => $this->label .' field data contains an invalid type.',
      );
    }

    // Return errors
    return $this->errors;

  } // end validate data


}
