<?php

namespace Pfp\PhpFormProcessor\fields;

use Pfp\PhpFormProcessor\fields\fieldBase;

class fieldTextarea extends fieldBase {

  function field_html() {
    $output = '';

    ob_start();

    include dirname(__FILE__).'/../tpl/fields/textarea.tpl.php';

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
  }

}
