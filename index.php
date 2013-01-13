<?php

/**
 * Scaffold
 * Light Weight PHP API Framework.
 *
 * Do not edit this file, instead, create a custom
 * bootstrap.php file in the application folder.
 * Editing this file could lead to unexpected results.
 *
 * @author  Nathaniel Higgins http://nath.is
 * @author  Claudio Albertin  http://twitter.com/ClaudioAlbertin
 * @license GPL               http://www.gnu.org/copyleft/gpl.html
 */

/**
 * Determine how long a particular request takes to compute.
 */
defined('START') or define('START', microtime(true));

/**
 * Determine if a file in the framework is being requested directly or
 * via the framework.
 */
defined('SCAFFOLD') or define('SCAFFOLD', true);

/**
 * What enviroment are we working with?
 */
defined('ENVIROMENT') or define('ENVIROMENT', getenv('SCAFFOLD_ENV'));

/**
 * Standard directory seperator.
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

/**
 * Access to the root folder, independent of the
 * location of the file using it.
 */
defined('ROOT') or define('ROOT', dirname(__FILE__) . DS);

/**
 * Access to the system folder, independent of the
 * location of the file using it.
 */
defined('SYSTEM') or define('SYSTEM', ROOT . 'system' . DS);

/**
 * Access to the application folder, independent
 * of the location of the file using it.
 */
defined('APPLICATION') or define('APPLICATION', ROOT . 'application' . DS);

/**
 * Boot Scaffold
 */
require(SYSTEM . 'bootstrap.php');

/**
 * Register standard route and run router
 */
$router = Service::get('router.default');
$router->run();
