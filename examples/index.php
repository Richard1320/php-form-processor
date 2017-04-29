<!DOCTYPE html>
<html lang="en">
<head>

<title>PHP Form Processor</title>

</head>

<body>

	<?php
	// Load form classes
	require_once '../src/php_form_processor.class.php';

	// Get form fields
	require_once 'page.params.php';
	$params = array('fields'=>$fields);

	// Create form instance
	$form = new php_form_processor($params);

	// Display errors, if there are any
	$form->print_errors();

	// Display the form markup
	$form->render_form('submit.php');
	?>

</body>
</html>
