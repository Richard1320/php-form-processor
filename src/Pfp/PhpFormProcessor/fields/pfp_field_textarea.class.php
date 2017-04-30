<?php

namespace pfp\php_form_processor;

class pfp_field_textarea extends pfp_field_base {

  function field_html() {
    $output = '';

    ob_start();

    include dirname(__FILE__).'/../tpl/fields/textarea.tpl.php';

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
  }

}
