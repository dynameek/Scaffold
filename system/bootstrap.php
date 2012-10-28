<?php defined('SCAFFOLD') or die();

/**
 * Scaffold Framework bootstrap
 *
 * Do not edit this file, instead, create a custom
 * bootstrap.php file in the application folder.
 * Editing this file could lead to unexpected results.
 */

/**
 * We need to load functions.php because it's contents
 * are used elsewhere in Scaffold.
 */
require(SYSTEM . 'functions.php');

/**
 * Include our Autoloader
 *
 * This is the only class we should be including manually.
 */
load_file('classes' . DS . 'autoload.php');

/**
 * Run the Autoloader
 *
 * This will enable us to use classes without having
 * to manually include them everytime.
 */
Autoload::run();

/**
 * Register framework services
 */
Service::register('dummy', function() {
    return new ServiceDummy();
});

Service::register('request', function($uri = null) {
    return new Request($uri);
});

Service::register('response.json', function() {
    return new Response();
}, true);

Service::register('controller', function($controller, Request $request = null, Response $response = null) {
    $request  = ($request !== null) ? $request : Service::get('request');
    $response = ($response !== null) ? $response : Service::get('response');

    $controller = 'Controller' . ucfirst(Inflector::singularize($controller));

    return new $controller($request, $response);
});

Service::register('router.blank', function() {
    return new Router();
}, true);

Service::register('router.default', function() {
    $router = Service::get('router');

    // automatically send response
    $router->add_hook(function($controller) {
        if ($controller instanceof Controller) $controller->response->send();
    });

    $router->all('/', 'index');
    $router->all('/:controller/:id');

    return $router;
});

Service::register('database.driver', function($config) {
    $parent = 'DatabaseDriver';
    $type = $config['type'];
    $class = $parent . $type;
    $driver = strtolower($type);

    if (!Autoload::load($class) && in_array($driver, PDO::getAvailableDrivers())) {
        $class = $parent . 'PDO';
    }

    // @TODO: decide what type of builder to use
    $builder = new DatabaseQueryBuilderSQL();

    return new $class($builder, $config);
});

Service::singleton('config', function() {
    return new Config;
});
