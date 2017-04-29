<?php
namespace pfp\php_form_processor;

class pfp_field_select extends pfp_field_options_list {

  function field_html() {
    $output = '';

    ob_start();

    include dirname(__FILE__).'/../tpl/fields/select.tpl.php';

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
  }

}
