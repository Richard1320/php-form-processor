<!DOCTYPE html>
<html lang="en">
<head>

<title>PHP Form Processor</title>

</head>

<body>
	<h1>Form failed to submit!</h1>
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
	?>

	<a href="./index.php">Back to form</a>

</body>
</html>
