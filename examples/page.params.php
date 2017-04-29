<?php
// Check if form post session has data
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
$title    = (isset($_SESSION['form_post']['title'])) ? trim($_SESSION['form_post']['title']) : '';
$body     = (isset($_SESSION['form_post']['body'])) ? trim($_SESSION['form_post']['body']) : '';
$tags     = (isset($_SESSION['form_post']['tags'])) ? trim($_SESSION['form_post']['tags']) : '';
$template = (isset($_SESSION['form_post']['template'])) ? trim($_SESSION['form_post']['template']) : '';
$comment  = (isset($_SESSION['form_post']['comment'])) ? trim($_SESSION['form_post']['comment']) : '';

$fields = array();

$fields['title'] = array(
	'label'         => 'Title',
	'type'          => 'text',
	'required'      => true,
	'name'          => 'title',
	'default_value' => $title,
	'maxlength'     => 120,
);

$fields['body'] = array(
	'label'         => 'Body',
	'type'          => 'textarea',
	'required'      => true,
	'name'          => 'body',
	'default_value' => $body,
);

$fields['template'] = array(
	'label'         => 'Template',
	'type'          => 'select',
	'name'          => 'template',
	'default_value' => $template,
	'deep'          => array(
		'grid'       => 'Content Within Grid',
		'full_width' => 'Full Width Content',
		'l_sidebar'  => 'Content With Left Sidebar',
		'r_sidebar'  => 'Content With Right Sidebar'
	),
);

$fields['image'] = array(
	'label' => 'Image',
	'type'  => 'file',
	'name'  => 'image',
);

$fields['pdf'] = array(
	'label'              => 'PDF Download',
	'type'               => 'file',
	'name'               => 'pdf',
	'allowed_extensions' => array('pdf'),
);

$fields['tags'] = array(
	'label'         => 'Tags',
	'type'          => 'checkbox',
	'name'          => 'tags',
	'default_value' => $tags,
	'deep'          => array(
		'1'=>'Reading',
		'2'=>'Writing',
		'3'=>'Grammar',
		'4'=>'Spelling'
	),
);

$fields['comment'] = array(
	'label'         => 'Allow Comments',
	'type'          => 'radio',
	'name'          => 'comment',
	'default_value' => $comment,
	'deep'          => array(
		'1'=>'Yes',
		'0'=>'No'
	),
);
