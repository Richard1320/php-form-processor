<?php
use PHPUnit\Framework\TestCase;
use Pfp\PhpFormProcessor\form;


class formTests extends TestCase
{

  public function test_field_input_text()
  {
    // Setup params
    $params = array(
      'fields' => array(
        'key_title' => array(
          'label'     => 'Title',
          'type'      => 'text',
          'required'  => true,
          'name'      => 'title',
          'maxlength' => 40,
        )
      )
    );

    // Initialize form
    $form = new Pfp\PhpFormProcessor\form($params);
    $this->assertEquals(0, count($form->errors));

    // Test empty
    $data = array();
    $form->submit_form_data($data);
    $this->assertEquals(count($form->errors), 1);
    $this->assertEquals($form->errors[0]['status'], 'error_is_filled_in');

    // Test value
    $data = array(
      'title' => 'Hello World!'
    );
    $form->submit_form_data($data);
    $this->assertEquals(0, count($form->errors));
    $this->assertEquals($form->get_field_value('key_title'), 'Hello World!');

    // Test max length
    $data = array(
      'title' => 'She sells seashells by the seashore. How much wood would a woodchuck chuck if a woodchuck could chuck wood?'
    );
    $form->submit_form_data($data);
    $this->assertEquals($form->errors[0]['status'], 'error_string_maxlength');

  } // end test input text
  public function test_field_input_email()
  {
    // Setup params
    $params = array(
      'fields' => array(
        'key_email' => array(
          'label'     => 'Email',
          'type'      => 'email',
          'required'  => true,
          'name'      => 'email',
        )
      )
    );

    // Initialize form
    $form = new Pfp\PhpFormProcessor\form($params);
    $this->assertEquals(0, count($form->errors));

    // Test without @
    $data = array(
      'email' => 'helloworld.com'
    );
    $form->submit_form_data($data);
    $this->assertEquals(1, count($form->errors));
    $this->assertEquals($form->errors[0]['status'], 'error_email_format');

    // Test without dot
    $data = array(
      'email' => 'hello@worldcom'
    );
    $form->submit_form_data($data);
    $this->assertEquals(1, count($form->errors));
    $this->assertEquals($form->errors[0]['status'], 'error_email_format');

    // Test proper format
    $data = array(
      'email' => 'hello@world.com'
    );
    $form->submit_form_data($data);
    $this->assertEquals(0, count($form->errors));

  } // end test input email
  public function test_field_input_checkboxes()
  {
    // Setup params
    $params = array(
      'fields' => array(
        'key_tags' => array(
        	'label'    => 'Tags',
        	'type'     => 'checkbox',
        	'name'     => 'tags',
          'required' => true,
          'multiple' => true,
        	'deep'     => array(
        		'1'=>'Reading',
        		'2'=>'Writing',
        		'3'=>'Grammar',
        		'4'=>'Spelling',
        	),
        )
      )
    );

    // Initialize form
    $form = new Pfp\PhpFormProcessor\form($params);
    $this->assertEquals(0, count($form->errors));

    // Test empty
    $data = array();
    $form->submit_form_data($data);
    $this->assertEquals(count($form->errors), 1);
    $this->assertEquals($form->errors[0]['status'], 'error_is_filled_in');

    // Test value
    $data = array(
      'tags' => array('1','2','4'),
    );
    $form->submit_form_data($data);
    $this->assertEquals(0, count($form->errors));
    $this->assertEquals($form->get_field_value('key_tags'), array('1','2','4'));

    // Test invalid value
    $data = array(
      'tags' => array('1','2','5'),
    );
    $form->submit_form_data($data);
    $this->assertEquals(1, count($form->errors));
    $this->assertEquals($form->errors[0]['status'], 'error_option_allowed_value');

  } // End test checkbox
  public function test_field_input_file()
  {
    // Setup params
    $params = array(
      'fields' => array(
        'key_pdf' => array(
          'label'              => 'PDF Download',
        	'type'               => 'file',
        	'name'               => 'pdf',
          'required'           => true,
        	'allowed_extensions' => array('pdf'),
        )
      )
    );

    // Initialize form
    $form = new Pfp\PhpFormProcessor\form($params);
    $this->assertEquals(0, count($form->errors));

    // Test empty
    $data = array();
    $form->submit_form_data();
    $this->assertEquals(count($form->errors), 1);
    $this->assertEquals($form->errors[0]['status'], 'error_is_filled_in');

    // Test missing file
    unset($_FILES['pdf']);
    $_FILES['pdf']['name']     = 'hello_world.pdf';
    $_FILES['pdf']['type']     = 'application/pdf';
    $_FILES['pdf']['tmp_name'] = dirname(__FILE__).'/files/hello_world.pdf';
    $_FILES['pdf']['error']    = 0;
    $_FILES['pdf']['size']     = 45395;

    $form->submit_form_data();
    // fwrite(STDERR, print_r($form->errors, TRUE));
    $this->assertEquals(1, count($form->errors));
    $this->assertEquals($form->errors[0]['status'], 'error_file_exists');

    // Test docx with docx extension
    unset($_FILES['pdf']);
    $_FILES['pdf']['name']     = 'docx_with_docx_extension.docx';
    $_FILES['pdf']['type']     = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    $_FILES['pdf']['tmp_name'] = dirname(__FILE__).'/files/docx_with_docx_extension.docx';
    $_FILES['pdf']['error']    = 0;
    $_FILES['pdf']['size']     = 45395;

    $form->submit_form_data();
    // fwrite(STDERR, print_r($form->errors, TRUE));
    $this->assertEquals(1, count($form->errors));
    $this->assertEquals($form->errors[0]['status'], 'error_allowed_extensions');

    // Test pdf with pdf extension
    unset($_FILES['pdf']);
    $_FILES['pdf']['name']     = 'pdf_with_pdf_extension.pdf';
    $_FILES['pdf']['type']     = 'application/pdf';
    $_FILES['pdf']['tmp_name'] = dirname(__FILE__).'/files/pdf_with_pdf_extension.pdf';
    $_FILES['pdf']['error']    = 0;
    $_FILES['pdf']['size']     = 74124;

    $form->submit_form_data();
    $this->assertEquals(0, count($form->errors));
    $this->assertEquals($form->get_field_value('key_pdf')['name'], 'pdf_with_pdf_extension.pdf');

  } // End test file
  public function test_field_input_file_multiple()
  {
    // Setup params
    $params = array(
      'fields' => array(
        'key_documents' => array(
          'label'              => 'Documents Download',
        	'type'               => 'file',
        	'name'               => 'documents',
          'required'           => true,
          'multiple'           => true,
        	'allowed_extensions' => array('pdf','docx'),
        )
      )
    );

    // Initialize form
    $form = new Pfp\PhpFormProcessor\form($params);
    $this->assertEquals(0, count($form->errors));

    // Test docx with docx extension
    unset($_FILES['documents']);
    $_FILES['documents']['name'][0]     = 'docx_with_docx_extension.docx';
    $_FILES['documents']['type'][0]     = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    $_FILES['documents']['tmp_name'][0] = dirname(__FILE__).'/files/docx_with_docx_extension.docx';
    $_FILES['documents']['error'][0]    = 0;
    $_FILES['documents']['size'][0]     = 45395;

    $form->submit_form_data();
    // fwrite(STDERR, print_r($form->errors, TRUE));
    $this->assertEquals(0, count($form->errors));

    // Test pdf with pdf extension
    $_FILES['documents']['name'][1]     = 'pdf_with_pdf_extension.pdf';
    $_FILES['documents']['type'][1]     = 'application/pdf';
    $_FILES['documents']['tmp_name'][1] = dirname(__FILE__).'/files/pdf_with_pdf_extension.pdf';
    $_FILES['documents']['error'][1]    = 0;
    $_FILES['documents']['size'][1]     = 74124;

    $form->submit_form_data();
    $this->assertEquals(0, count($form->errors));

  } // End test file multiple
  public function test_field_input_file_maxsize()
  {
    // Setup params
    $params = array(
      'fields' => array(
        'key_pdf' => array(
          'label'              => 'PDF Download',
        	'type'               => 'file',
        	'name'               => 'pdf',
          'required'           => true,
        	'allowed_extensions' => array('pdf'),
          'maxsize'            => 70000,
        )
      )
    );

    // Initialize form
    $form = new Pfp\PhpFormProcessor\form($params);
    $this->assertEquals(0, count($form->errors));

    // Test pdf with pdf extension
    unset($_FILES['pdf']);
    $_FILES['pdf']['name']     = 'pdf_with_pdf_extension.pdf';
    $_FILES['pdf']['type']     = 'application/pdf';
    $_FILES['pdf']['tmp_name'] = dirname(__FILE__).'/files/pdf_with_pdf_extension.pdf';
    $_FILES['pdf']['error']    = 0;
    $_FILES['pdf']['size']     = 74124;

    $form->submit_form_data();
    $this->assertEquals(1, count($form->errors));
    $this->assertEquals($form->errors[0]['status'], 'error_file_maxsize');

  } // End test file maxsize
  public function test_field_input_file_image()
  {
    // Setup params
    $params = array(
      'fields' => array(
        'key_image' => array(
        	'label'     => 'Image file',
        	'type'      => 'file',
        	'name'      => 'image',
        	'pfp_class' => 'Pfp\PhpFormProcessor\fields\fieldInputFileImage',
        )
      )
    );

    // Initialize form
    $form = new Pfp\PhpFormProcessor\form($params);
    $this->assertEquals(0, count($form->errors));

    // Test docx with jpg extension
    unset($_FILES['image']);
    $_FILES['image']['name']     = 'docx_with_jpg_extension.jpg';
    $_FILES['image']['type']     = 'image/jpeg';
    $_FILES['image']['tmp_name'] = dirname(__FILE__).'/files/docx_with_jpg_extension.jpg';
    $_FILES['image']['error']    = 0;
    $_FILES['image']['size']     = 45395;

    $form->submit_form_data();
    // fwrite(STDERR, print_r($form->errors, TRUE));
    $this->assertEquals(1, count($form->errors));
    $this->assertEquals($form->errors[0]['status'], 'error_image_check');

    // Test jpg with jpg extension
    unset($_FILES['image']);
    $_FILES['image']['name']     = 'jpg_with_jpg_extension.jpg';
    $_FILES['image']['type']     = 'image/jpeg';
    $_FILES['image']['tmp_name'] = dirname(__FILE__).'/files/jpg_with_jpg_extension.jpg';
    $_FILES['image']['error']    = 0;
    $_FILES['image']['size']     = 114643;

    $form->submit_form_data();
    $this->assertEquals(0, count($form->errors));

  } // End test file image
}
