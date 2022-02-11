<?php 
error_reporting(E_ALL | E_STRICT);
// Set up composer autoloader:
require_once('lib/autoload.php');

/** @var \Base $f3 */
$f3 = \Base::instance();
// Set starting time for calculate rendering time
$f3->set('startTime', microtime(true));
// Set up F3 autoloader
$f3->set('AUTOLOAD', 'app/;app/Includes/');
$f3->set('PREFIX', 'lng.');
$f3->config('config.ini');

// Treat specific errors from trigger_error as ErrorException:
function exception_error_handler($severity, $message, $file, $line) {
  if (!(error_reporting() & $severity)) {
    return;
  } // if
  throw new ErrorException($message, 0, $severity, $file, $line);
} // exception_error_handler()
set_error_handler('exception_error_handler');
$f3->set('LOGGABLE', [400, 401, 403, 500, 501, 502, 503]);

// Set up Dice and dependencies:
$dice = new \Dice\Dice();
$rules = [
  \AuthenticationHelper::class => [
    'shared' => true
  ]
];
$dice = $dice->addRules($rules);

$f3->set('CONTAINER', function($class) use ($dice) {
  return $dice->create($class);
});

// Enable and configure F3 cache
$f3->set('CACHE',true);
$f3->set('CORTEX.queryParserCache', true);

// Program informations:
$f3->set('PROGRAM_NAME', 'ZBB-IVP');
$f3->set('PROGRAM_VERSION', '1.0');
$f3->set('PROGRAM_RELEASE_DATE', '2021-05-04');

// Set routes
$f3->config('app/routes.ini');

// Set development mode:
if ($f3->get('DEV')) {
  $f3->set('DEBUG', 3);
} else {
  $f3->set('DEBUG', 0);
  $f3->set('ONERROR', '\ErrorHandler->handle');
} // else

// Setup database:
try {
  $db = $f3->get('database');
  switch ($db['type']) {
    case 'mysql':
      $dsn = 'mysql:host=' . $db['host'] . ';port=' . $db['port'] . ';dbname=' . $db['name'];
      $f3->set('db', new \DB\SQL($dsn, $db['user'], $db['password'], [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]));
    break;
    default:
      exit('<p>' . $f3->get('lng.error.dbType', $f3->get('database.type')) . '</p>');
    break;
  } // switch
} catch (\PDOException $e) {
  echo('<p>' . $f3->get('lng.error.dbConnection') . '</p>');
  if ($f3->get('DEV'))
    echo ('<p>' . $e->getMessage() . '</p>');
  exit();
} // catch

// Save the full url to the program:
$scheme = $f3->get('SCHEME');
$host = $f3->get('HOST');
$port = $f3->get('PORT');
$base = $f3->get('BASE');
$f3->set('URL', $scheme . '://' . $host . ':' . $port . $base);

// Authentication and access if not in CLI mode:
if (!$f3->get('CLI')) {
  // Cookie settings:
  $f3->set('JAR.samesite', 'Strict');

  // Setup session:
  new DB\SQL\Session($f3->get('db'), 'sessions', true, null, 'CSRF');

  $user = $dice->create('\AuthenticationHelper')->getUser();
  $admin = $dice->create('\AuthenticationHelper')->getAdmin();
  $root = $dice->create('\AuthenticationHelper')->getRoot();

  $f3->set('USER', $user);
  $f3->set('ADMIN', $admin);
  $f3->set('ROOT', $root);
  
  // Route access control:
  $f3->config('app/access.ini');
  $access = \Access::instance();

  /**null check to set Role from \AuthenticationHelper
   * checks $user, if null = unauthenticated
   * otherwise check $admin, if null = user
  */
  $subject = is_null($user) ? 'unauthenticated' : (is_null($admin) ? 'user' : (is_null($root) ? 'admin' : 'root'));

  
  $access->authorize($subject, function ($route, $subject) use ($f3) {
    if ($subject == 'unauthenticated') { // Redirect to login page
      // Remove request type from $route to set in origin for login to reroute back after successful login
      if (preg_match('/.* (.*)/', $route, $matches) === 1)
        $route = $matches[1];
      $query = $f3->get('QUERY');
      if (!empty($query))
        $route .= '?' . $query;
      $f3->reroute($f3->alias('login', [], ['origin' => $route]));
    } // if
    // Show message and reroute to home:
    \Flash::instance()->addMessage($f3->get('lng.error.noAccess'), 'danger');
    $f3->reroute('/');
  });
} // if

echo $subject;

// Start validation engine and add some custom filters:
$validation = \Validation::instance();
$validation->addFilter('optionalInt', function($value, $params = null) {
  $value = trim($value);
  if (is_null($value) || $value === '')
    return null;
  else if (is_int($value) || (is_string($value) && ctype_digit($value))) {
    if ((int) $value == 0 && $params[0] === 'true')
      return null;
    return (int) $value;
  } // if
  return null;
});

$validation->addFilter('hashPassword', function($value, $params = null) {
  // Only hash plain passwords (no rehash):
  $algo = password_get_info($value)['algo'];
  if ($algo === 0 || is_null($algo))
    return password_hash($value, PASSWORD_DEFAULT);
  return $value;
});

$validation->onError(function($text, $key) use ($f3) {
  $f3->set('validationError', ['key' => $key, 'text' => $text]);
});
$validation->loadLang();

// Run f3 (Routing engine)
$f3->run();