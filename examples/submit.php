<?php
// Load form classes
require_once '../src/php_form_processor.class.php';

// Get form fields
require_once 'page.params.php';
$params = array('fields'=>$fields);

// Create form instance
$form = new php_form_processor($params);

// Submit form data
$form->submit_form_data($_POST);

if (empty($form->errors)) {
	// Form has no errors. Process data.
	header('location:success.php');
} else {
	// Form has errors. Go to page and call print_errors method to display errors
	header('location:fail.php');
}
exit();
