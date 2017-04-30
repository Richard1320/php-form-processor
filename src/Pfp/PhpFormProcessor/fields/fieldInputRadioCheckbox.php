<?php
namespace Pfp\PhpFormProcessor\fields;

use Pfp\PhpFormProcessor\fields\fieldOptionsList;

class fieldInputRadioCheckbox extends fieldOptionsList {

  function field_html() {
    $output = '';

    ob_start();

    include dirname(__FILE__).'/../tpl/fields/input_radio_checkbox.tpl.php';

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
  }

}
