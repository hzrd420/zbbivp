<?php
class H {
  static $func = array();

  public static function __callstatic($name, $arguments) {
    if (!isset(self::$func[$name])) {
      if (is_file(__DIR__ . '/helper/' . $name . '.php'))
        self::$func[$name] = include(__DIR__ . '/helper/' . $name . '.php');
    } // if

    if (!empty(self::$func[$name]))
      return call_user_func_array(self::$func[$name], $arguments);
    else
      throw new \Exception('Helper not found: ' . $name, E_USER_ERROR);
  } // __callstatic()
} // class