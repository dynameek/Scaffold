<?php defined('SCAFFOLD') or die;

abstract class Controller {

    public $request  = null;
    public $response = null;

    public function __construct($request, $response) {
        $this->request  = $request;
        $this->response = $response;
    }

    public function before() {}

    public function resource($id) {
        return false;
    }

    public function after() {}

}
