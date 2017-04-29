<?php
// check multidimentional array for file type inputs
if(!function_exists('remap_files_array')) {
  // function to reverse files array for multiple file uploads
  function remap_files_array($name, $type, $tmp_name, $error, $size) {
    return array(
      'name' => $name,
      'type' => $type,
      'tmp_name' => $tmp_name,
      'error' => $error,
      'size' => $size,
    );
  } // end remap files array
}
