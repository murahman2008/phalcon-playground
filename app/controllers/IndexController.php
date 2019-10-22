<?php
    use Phalcon\Mvc\Controller;

    class IndexController extends Controller
    {
        public function indexAction()
        {
            echo $this->config->twitter->api->api_key; die();
        }
    }
?>