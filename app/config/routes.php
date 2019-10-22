<?php
    use Phalcon\Mvc\Router;

    $router = new Router();
    $router->removeExtraSlashes(true);

    $router->add('/api/twitter/:action/:params', array(
        'controller' => 'twitter',
        'action' => 1,
        'params' => 2
    ));
    
    $router->add('/api/twitter', array(
        'controller' => 'twitter',
        'action' => 'index'
    ));

    // $router->add('/poker', array(
    //     'controller' => 'poker',
    //     'action' => 'index'
    // ));

    $router->add("/", array(
        'controller' => 'index',
        'action' => 'index'
    ));

    // $router->add('/poker/check', array(
    //     'controller' => 'poker',
    //     'action' => 'index'
    // ));

    //$router->setUriSource(Router::URI_SOURCE_SERVER_REQUEST_URI);

    return $router;
?>