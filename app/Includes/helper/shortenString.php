<?php
declare(strict_types=1);
// Shorten a string to specific length and add ...
return function(?string $string, int $length): ?string {
  if (!is_string($string))
    return null;
  return trim(mb_substr($string, 0, $length)) . (mb_strlen($string) > $length ? '...' :'');
} // function()
?>