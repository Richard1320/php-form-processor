<?php
namespace Pfp\PhpFormProcessor;

use Pfp\PhpFormProcessor\fields\fieldSelect;
use Pfp\PhpFormProcessor\fields\fieldTextarea;
use Pfp\PhpFormProcessor\fields\fieldInputRadioCheckbox;
use Pfp\PhpFormProcessor\fields\fieldInputFile;
use Pfp\PhpFormProcessor\fields\fieldInputText;

class form {

  public    $errors;
  protected $fields;
  protected $captcha;
  protected $config;

  function __construct($params) {
    $this->create_field_objects($params['fields']);

    $this->config  = (isset($params['config'])) ? $params['config'] : array();
    $this->captcha = (isset($params['captcha'])) ? $params['captcha'] : false;
    $this->errors  = (isset($_SESSION['form_post']['errors'])) ? $_SESSION['form_post']['errors'] : array();
  }// End construct
  function create_field_objects($array) {
    $this->fields = new \stdClass();

    foreach($array as $key => $field_params) {
      $type      = (isset($field_params['type'])) ? $field_params['type'] : 'text';
      $pfp_class = (isset($field_params['pfp_class'])) ? (string)$field_params['pfp_class'] : false;

      if (empty($pfp_class)) {
        // Use default classes

        switch ($type) {
          case 'select':
            $this->fields->$key = new fieldSelect($key,$field_params);
            break;
          case 'textarea':
            $this->fields->$key = new fieldTextarea($key,$field_params);
            break;
          case 'radio':
          case 'checkbox':
            $this->fields->$key = new fieldInputRadioCheckbox($key,$field_params);
            break;
          case 'file':
            $this->fields->$key = new fieldInputFile($key,$field_params);
            break;
          case 'text':
          case 'password':
          default:
            $this->fields->$key = new fieldInputText($key,$field_params);
            break;
        } // end switch type
      } else {
        $this->fields->$key = new $pfp_class($key,$field_params);
      }

    } // end field array loop
    return $this->fields;
  } // end create field objects
  function render_form($action=NULL, $params=array()) {
    require_once dirname(__FILE__).'/helpers/sanitize_alphanumeric.helper.php';

    $method        = (isset($params['method'])) ? $params['method'] : 'post';
    $submit_name   = (isset($params['submit_name'])) ? $params['submit_name'] : 'submit';
    $submit_value  = (isset($params['submit_value'])) ? $params['submit_value'] : 'Submit';
    $action_html   = (isset($params['action_html'])) ? (array)$params['action_html'] : array();
    $classes       = (isset($params['classes'])) ? sanitize_alphanumeric_array($params['classes']) : array();
    $enctype       = (isset($params['enctype'])) ? $params['enctype'] : false;

    // check if self submit
    if (empty($action)) {
      $action = htmlspecialchars($_SERVER['REQUEST_URI']);
    }

    if (empty($enctype)) {
      // find out if any inputs are file fields
      $filefield = false;
      foreach($this->fields as $field) {
        if ($field->type == 'file') {
          $filefield = true;
          break;
        }
      }
      $enctype = ($filefield) ? 'multipart/form-data' : 'application/x-www-form-urlencoded';
    }

    include dirname(__FILE__).'/tpl/form/_wrapper-open.tpl.php';

    foreach($this->fields as $field) {
      $field->render();
    } // end field array loop

    // print form captcha
    if ($this->captcha) {
      include dirname(__FILE__).'/tpl/form/captcha.tpl.php';
    } // captcha check

    include dirname(__FILE__).'/tpl/form/form-bottom.tpl.php';

    include dirname(__FILE__).'/tpl/form/_wrapper-close.tpl.php';
  } // end render form

  function cross_reference_multidimensional_field_name_value($data, $array_names) {
    $first_value = (isset($array_names[0])) ? array_shift( $array_names ) : '';
    if (isset($data[$first_value])) {
      return $this->cross_reference_multidimensional_field_name_value($data[$first_value],$array_names);
    } else {
      return $data;
    }
  }
  function get_field_value($key,$data=array()) {
    $value = '';
    if (!empty($data)) {
      $field_name = $this->fields->$key->name;
      $value      = (isset($data[$field_name])) ? $data[$field_name] : '';

      // Check if name has square brackets and thus, is an array
      $pos = strpos($field_name, '[');

      if ($pos !== false) {
        // Grab all names inside square brackets
        preg_match_all("/\[([^\]]*)\]/", $field_name, $matches);
        $sub_array = $matches[1];

        // First level item in data array
        $root_name = substr($field_name, 0, $pos);

        // Recursive loop to retrieve submitted value
        $value = $this->cross_reference_multidimensional_field_name_value($data[$root_name],$sub_array);

      }

    } else if (isset($this->fields->$key->value) && !empty($this->fields->$key->value)) {
      // Check if value is already set
      $value = $this->fields->$key->value;
    } else {
      // Retrieve form errors from session
      if (session_status() == PHP_SESSION_NONE && !headers_sent()) {
        session_start();
      }
      if (isset($_SESSION['form_post'][$key]) && !empty(isset($_SESSION['form_post'][$key]))) {
        $value = $_SESSION['form_post'][$key];
      }
    }

    return $value;
  } // end get field data

  function submit_form_data($data=false) {
    // If no data is passed, attempt to retrieve from global variables
    if (!$data) {
      if (!empty($_POST)) {
        $data = $_POST;
      } else if (!empty($_GET)) {
        $data = $_GET;
      } else if (!empty($_REQUEST)) {
        $data = $_REQUEST;
      } else {
        $data = array();
      }
    }

    // print_r($data);

    // Clear all errors
    $this->errors = array();

    foreach($this->fields as $key => $field) {

      // grab submitted field data
      if ($field->type == 'file') {

        if ($field->multiple) {
          require_once dirname(__FILE__).'/helpers/remap_files_array.helper.php';

          // recreate files array
          $field_data = array_map('remap_files_array',
            (array) $_FILES[$field->name]['name'],
            (array) $_FILES[$field->name]['type'],
            (array) $_FILES[$field->name]['tmp_name'],
            (array) $_FILES[$field->name]['error'],
            (array) $_FILES[$field->name]['size']
          );
        } else {
          $field_data = (isset($_FILES[$field->name])) ? $_FILES[$field->name] : '';
        }
      } else {
        $field_data = $this->get_field_value($key,$data);
      }

      // Save submitted field data
      $this->fields->$key->value = $field_data;

      // Validate field data
      $field->validation();

      // Append list of field errors
      $this->errors = array_merge($this->errors, $field->errors);

    } // end form data submit field loop

    if ($this->captcha) {
      $captcha_code = (isset($data['g-recaptcha-response'])) ? $data['g-recaptcha-response'] : '';
      $ip           = $_SERVER['REMOTE_ADDR'];
      $secret_key   = $this->config['recaptcha_secret_key'];
      $response     = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$captcha_code.'&remoteip='.$ip);
      $responseKeys = json_decode($response,true);

      if (empty($secret_key)) {
        // Make sure key is available as a config
        $this->errors[] = array(
          'key'     => 'recaptcha_secret_key',
          'status'  => 'error_empty_secret_key',
          'message' => 'Captcha secret key is invalid. Please contact system administrator.',
        );
      }
      if(intval($responseKeys['success']) !== 1) {
        $this->errors[] = array(
          'key'     => 'g-recaptcha-response',
          'status'  => 'error_invalid_captcha_response',
          'message' => 'Captcha is incorrect. Please contact Google.',
        );
      } // end captcha error

    } // end captcha check

    // Save result in session
    if (session_status() == PHP_SESSION_NONE && !headers_sent()) {
      session_start();
    }
    if (isset($_SESSION)) {
      if (empty($this->errors)) {
        // Form success. Delete session to prevent confusion
        unset($_SESSION['form_post']);
      } else {
        // Save form errors in session
        $_SESSION['form_post'] = $data;
        $_SESSION['form_post']['errors'] = $this->errors;
      }
    }

  } // end submit form

  function print_errors() {
    // Retrieve form errors from session
    if (session_status() == PHP_SESSION_NONE && !headers_sent()) {
      session_start();
    }
    $this->errors = (isset($_SESSION['form_post']['errors'])) ? (array)$_SESSION['form_post']['errors'] : array();

    include dirname(__FILE__).'/tpl/form/errors.tpl.php';
  } // end print errors


} // End process form
