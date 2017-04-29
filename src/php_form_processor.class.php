<?php

class php_form_processor {

  public    $errors;
  protected $fields;
  protected $captcha;
  protected $config;

  function __construct($params) {
    $this->load_config();
    $this->create_field_objects($params['fields']);

    $this->captcha = (isset($params['captcha'])) ? $params['captcha'] : false;
    $this->errors  = (isset($_SESSION['form_post']['errors'])) ? $_SESSION['form_post']['errors'] : array();
  }// End construct
  function autoload($className) {
    require_once dirname(__FILE__).'/fields/'. $className .'.class.php';
  }
  function create_field_objects($array) {
    $this->fields = new stdClass();

    spl_autoload_register(array('php_form_processor', 'autoload'));
    
    foreach($array as $key => $field_params) {
      $type = (isset($field_params['type'])) ? $field_params['type'] : 'text';

      switch ($type) {
        case 'select':
          $this->fields->$key = new pfp_field_select($key,$field_params);
          break;
        case 'textarea':
          $this->fields->$key = new pfp_field_textarea($key,$field_params);
          break;
        case 'radio':
        case 'checkbox':
          $this->fields->$key = new pfp_field_input_radio_checkbox($key,$field_params);
          break;
        case 'file':
          $this->fields->$key = new pfp_field_input_file($key,$field_params);
          break;
        case 'image':
          $this->fields->$key = new pfp_field_input_file_image($key,$field_params);
          break;
        case 'text':
        case 'password':
        default:
          $this->fields->$key = new pfp_field_input_text($key,$field_params);
          break;
      } // end switch type

    } // end field array loop
    return $this->fields;
  } // end create field objects
  function render_form($action=NULL, $params=array()) {

    $method        = (isset($params['method'])) ? $params['method'] : 'post';
    $submit_name   = (isset($params['submit_name'])) ? $params['submit_name'] : 'submit';
    $submit_value  = (isset($params['submit_value'])) ? $params['submit_value'] : 'Submit';
    $action_html   = (isset($params['classes'])) ? (array)$params['classes'] : array();
    $classes       = (isset($params['classes'])) ? (array)$params['classes'] : array();
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

  function load_config() {
    $config = array();

    // Load default configurations
    include dirname(__FILE__).'/../config/config.default.php';

    // Load overwritten configurations
    if (file_exists(dirname(__FILE__).'/../config/config.php')) {
      include dirname(__FILE__).'/../config/config.php';
    }

    $this->config = $config;

  }

  function get_field_value($key) {
    return $this->fields->$key->value;
  } // end get field data

  function submit_form_data($data=false) {
    // If no data is passed, attempt to retrieve from global variables
    if (!$data) {
      if (!empty($_POST)) {
        $data = $_POST;
      } else if (!empty($_GET)) {
        $data = $_GET;
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
          $field_data = (isset($_FILES[$field->name])) ? $_FILES[$field->name] : false;
        }
      } else {
        $field_data = (isset($data[$field->name])) ? $data[$field->name] : false;
      }

      // Save submitted field data
      $this->fields->$key->value = $field_data;

      // Validate field data
      $field->validation();

      // Append list of field errors
      $this->errors = array_merge($this->errors, $field->errors);

    } // end form data submit field loop

    if ($this->captcha) {
      $captcha_code = '';
      if (isset($_POST['g-recaptcha-response'])) {
        $captcha_code = trim($_POST['g-recaptcha-response']);
      } else if (isset($_GET['g-recaptcha-response'])) {
        $captcha_code = trim($_GET['g-recaptcha-response']);
      } // end get captcha

      $ip           = $_SERVER['REMOTE_ADDR'];
      $secret_key    = $this->config['recaptcha_secret_key'];
      $response     = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$captcha_code.'&remoteip='.$ip);
      $responseKeys = json_decode($response,true);

      if (empty($secret_key)) {
        // Make sure key is available as a config (Default declared location: /config/config.php)
        $this->errors[] = 'Captcha secret key is invalid. Please contact system administrator.';
      }
      if(intval($responseKeys['success']) !== 1) {
        $this->errors[] = 'Captcha is incorrect. Please contact Google.';
      } // end captcha error

    } // end captcha check

    // Save result in session
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
    if (empty($this->errors)) {
      // Form success. Delete session to prevent confusion
      unset($_SESSION['form_post']);
    } else {
      // Save form errors in session
      $_SESSION['form_post'] = $data;
      $_SESSION['form_post']['errors'] = $this->errors;
    }

  } // end submit form

  function print_errors() {
    // Retrieve form errors from session
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
    $this->errors = (isset($_SESSION['form_post']['errors'])) ? (array)$_SESSION['form_post']['errors'] : array();

    if (!empty($this->errors)) {
      ?>
      <div class="error-wrapper">
      <?php
      foreach($this->errors as $value) {
        ?>
        <p class="error">Error: <?php echo $value; ?><p>
        <?php
      } // end error loop
      ?>
      </div><!-- // error wrapper -->
      <?php
    } // end error check
  } // end print errors


} // End process form
