<?php
namespace pfp\php_form_processor;

class pfp_field_input_file_image extends pfp_field_input_file {


  function __construct($key,$args) {

    // call parent constructor
    parent::__construct($key,$args);

    // Default image extensions
    if (empty($this->allowed_extensions)) {
      $this->allowed_extensions = array('jpg','png','gif');
    }

  } // End construct

  // check if file is actually an image file
  function image_check($path) {
		/*
		$a = getimagesize($path);
		$image_type = $a[2];
		*/
		// exif_imagetype throws "Read error!" if file is too small
		if (filesize($path) < 12) {
			return false;
		}

		$image_type = exif_imagetype($path);
		$allowed    = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);

		// Check if image is real
		if(in_array($image_type , $allowed))
		{
			return true;
		}
		return false;
	}// End image_check

  function validation() {
    // Run parent validation tests
    $validation = parent::validation();
    if ($validation === false) return $validation;

    $temp_file = $this->value['tmp_name'];

    // check if string is longer than allowed length
    if (!$this->image_check($temp_file)) {
      $this->errors[] = $this->label .' is not a valid image.';
    }

    // Return errors
    return $this->errors;

  } // end validate data

  function field_html() {
    // Set field type to file for HTML display
    $og_type = $this->type;
    $this->type = 'file';

    $output = parent::field_html();

    $this->type = $og_type;

    return $output;
  }

}