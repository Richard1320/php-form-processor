<?php
class pfp_field_input_radio_checkbox extends pfp_field_options_list {

  function field_html() {
    $output = '';

    ob_start();

    include dirname(__FILE__).'/../tpl/fields/input_radio_checkbox.tpl.php';

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
  }

}
