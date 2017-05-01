PHP Form Processor
===================

PHP Form Processor allows you to create forms on your website in minutes. It has standard validation checks built in, and you can easily extend the various classes to add your own.

### Features

  - A lot of standard validation checks
  - Easily extendable classes to create your own custom fields
  - Separated template files for easy altering of the form's HTML markup
  - Google reCAPTCHA integration

## Documentation

The more in-depth features are explained with pages on the [wiki]

# Installation

PHP Form Processor is installed via Composer.

```
composer require pfp/php_form_processor
```

### <a name="initialize"></a>Initialization

After installing it through Composer, you will have to create your field parameters before initializing the form. For a full list of available fields and their parameters, please check out the [list of fields] page on the wiki.

```
$fields = array();

$fields['name'] = array(
	'label'         => 'Name',
	'type'          => 'text',
	'required'      => true,
	'name'          => 'name',
);

$fields['email'] = array(
	'label'         => 'Email',
	'type'          => 'email',
	'required'      => true,
	'name'          => 'email',
);

$fields['body'] = array(
	'label'         => 'Message',
	'type'          => 'textarea',
	'required'      => true,
	'name'          => 'body',
);

$form_params = array(
	'fields' => $fields,
);

$form = new Pfp\PhpFormProcessor\form($form_params);
```

### Display the form

After creating a new form object, you can print it out with the `render_form` method.

```
$form->render_form('submissions.php');
```

### Submit the form

On the submissions page, you will have to [initialize](#initialize) the form object again with the same parameters. You will also have to pass the form inputted data into the form in order for the validation functions to run.

```
$form->submit_form_data($_POST);
```

### Validation

The plugin provides numerous validation checks to all the fields. See the full list of [validation functions] on the wiki.

### Errors

If there are any errors after running the `submit_form_data` method, the errors and the submitted data is saved in the `$_SESSION` superglobal variable.

```
$form->print_errors();
```

### Data Retrieval

Data is retrieved with the `get_field_value` method after `submit_form_data`. You will have to pass it the key from the fields array that was set during initialization.

```
$email = $form->get_field_value('email');
```

[wiki]: <https://github.com/Richard1320/php-form-processor/wiki>
[list of fields]: <https://github.com/Richard1320/php-form-processor/wiki/Field-Parameters>
[validation functions]: <https://github.com/Richard1320/php-form-processor/wiki/Field-Class-Tree-&-Validation-Functions>
