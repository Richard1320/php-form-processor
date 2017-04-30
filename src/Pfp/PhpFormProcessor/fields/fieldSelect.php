<?php
namespace Pfp\PhpFormProcessor\fields;

use Pfp\PhpFormProcessor\fields\fieldOptionsList;

class fieldSelect extends fieldOptionsList {

  function field_html() {
    $output = '';

    ob_start();

    include dirname(__FILE__).'/../tpl/fields/select.tpl.php';

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
  }

}
