# Install ZBB-IVP

## Requirements:

+ PHP 8 (tested with PHP 8.0)
+ Apache2
+ Composer
+ PDO with MySQL driver
+ MySQL or MariaDB

## Installation

1. Import dump from file db.sql into your MySQL installation.
2. Place the content of the project folder in your webroot directory.
3. Copy "app/config.ini.dist" to "app/config.ini".
4. Edit "app/config.ini" (database, pagination, email settings). Read comments in the ini file for further information.
5. Add cron job one per day to the route /steps/remind to allow sending reminder emails
6. Run and test the functions.
7. To log in, use 'admin' as username and 'Test123!' as password.
  Note: Change the data and the password immediately after first login!