<?php
    use Phalcon\Loader;
    use Phalcon\Config;
    use Phalcon\Di\FactoryDefault;
    use Phalcon\Mvc\View;
    use Phalcon\Mvc\Url as UrlProvider;
    use Phalcon\Mvc\Application;

    define("BASE_PATH", dirname(__DIR__));
    define("APP_PATH", BASE_PATH.'/app');

    $loader = new Loader();
    $loader->registerDirs(array(
        APP_PATH.'/controllers/',
        APP_PATH.'/models/',
    ))->register();

    $di = new FactoryDefault();
    
    $di->set('config', function() {
        $configData = require APP_PATH.'/config/config.php';
        return new Config($configData);
    });

    $di->set('router', function() {
        require APP_PATH.'/config/routes.php';
        return $router;
    });

    $di->set('view', function() {
        $view = new View();
        $view->setViewsDir(APP_PATH.'/views/');
        return $view;
    });

    // $di->set('url',  function() {
    //     $url = new UrlProvider();
    //     $url->setBaseUri("/");
    //     return $url;
    // });

    $application = new Application($di);
    
    try
    {
        $response = $application->handle();
        $response->send();
    }
    catch(Exception $ex)
    {
        echo 'Exception: '.$ex->getMessage();
    }
?>