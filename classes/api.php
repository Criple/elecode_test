<?php

abstract class Api{

    public $apiName = '';

    protected $method = '';

    public $requestUri = [];

    protected $action = '';

    protected $X_API_Key = 123321;

    public function __construct() {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'],'/'));

        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }

    }

    protected function checkHeaders(){
        $headers = apache_request_headers();
        if(!empty($headers)){
            foreach ($headers as $header => $value) {
                switch ($header){
                    case 'X-API-KEY':
                        if ($this->X_API_Key == $value){
                            return true;
                        }else{
                            return false;
                        }
                        break;
                    default:
                        return false;
                        break;
                }
            }
        }
    }

    public function run() {

        if(array_shift($this->requestUri) !== 'api' || array_shift($this->requestUri) !== $this->apiName){
            throw new RuntimeException('API Not Found', 404);
        }
        $this->action = $this->getAction();

        if (method_exists($this, $this->action)) {
            return $this->{$this->action}();
        } else {
            throw new RuntimeException('Invalid Method', 405);
        }
    }

    protected function response($data, $status = 500) {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        return json_encode($data);
    }

    private function requestStatus($code) {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code])?$status[$code]:$status[500];
    }

    protected function getAction()
    {
        if (empty($this->requestUri)){
            return 'indexAction';
        }else{
            if (count($this->requestUri) == 1){
                return 'currencyAction';
            }
        }
    }

    abstract protected function indexAction();

}