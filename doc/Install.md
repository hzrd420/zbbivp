# Install ZBB-IVP

## Requirements:

+ PHP 7 (tested with PHP 7.4)
+ Apache2
+ Composer
+ PDO with MySQL driver
+ MySQL or MariaDB

## Installation

1. Import dump from file db.sql into your MySQL installation.
2. Place the content of the project folder in your webroot directory.
3. Copy "app/config.ini.dist" to "app/config.ini".
4. Edit "app/config.ini" (database settings, pagination settings). Read comments in the ini file for further information.
5. Run and test the functions.
6. To log in, use 'admin' as username and 'Test123!' as password.
  Note: Change the data and the password imediately after first login!