<?php
// Remove all characters that are not alphanumeric


if(!function_exists('sanitize_alphanumeric_string')) {
  // function to sanitize string
  function sanitize_alphanumeric_string($string) {
    $new_string = preg_replace("/[^a-zA-Z0-9_-]/", "", $string);
    return $new_string;
  } // end sanitize alphanumeric string
}

if(!function_exists('sanitize_alphanumeric_array')) {
  // function to run array through sanitizer
  function sanitize_alphanumeric_array($array) {
    return array_map('sanitize_alphanumeric_string', (array)$array);
  } // end sanitize alphanumeric array
}
