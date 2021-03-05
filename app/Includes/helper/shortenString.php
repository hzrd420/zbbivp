<?php
// Shorten a string to specific length and add ...
return function($string, $length) {
  return trim(mb_substr($string, 0, $length)) . (mb_strlen($string) > $length ? '...' :'');
} // function()
?>