<?php

require_once 'api.php';

class cbrDailyApi extends Api
{



    public $apiName = 'exchange';

    protected $cbrDailyData = '';

    public function __construct() {
        parent::__construct();
        $this->cbrDailyData = json_decode(file_get_contents('https://www.cbr-xml-daily.ru/daily_json.js'));
    }

    function num2word($num = 0, $words = ['рубль', 'рубля', 'рублей'])
    {

        $tempNum     = (int) $num;
        $cases   = array(2, 0, 1, 1, 1, 2);
        return $num . ' ' . $words[($tempNum % 100 > 4 && $tempNum % 100 < 20) ? 2 : $cases[min($tempNum % 10, 5)]];
    }

    public function indexAction()
    {
        if (!$this->checkHeaders()){
            throw new RuntimeException('API Key is invalid!', 405);
        }
        if($this->method != 'GET'){
            throw new RuntimeException('Invalid request method!', 405);
        }

        $valutes = get_object_vars($this->cbrDailyData->Valute);
        $valute = $valutes[array_rand($valutes)];
        $responseArr = array($valute->CharCode => $valute->Nominal . ' ' . $valute->Name . ' равен ' . $this->num2word($valute->Value));
        return $this->response($responseArr, 200);
    }

    public function currencyAction()
    {
        if (!$this->checkHeaders()){
            throw new RuntimeException('API Key is invalid!', 404);
        }

        $requestedValute = strtoupper($this->requestUri[0]);

        $valutes = get_object_vars($this->cbrDailyData->Valute);
        if (!array_key_exists($requestedValute, $valutes)){
            return $this->response(array('error' => 'Valute not found!'), 404);
        }
        $valute = $valutes[$requestedValute];
        $responseArr = array($valute->CharCode => $valute->Nominal . ' ' . $valute->Name . ' равен ' . $this->num2word($valute->Value));
        return $this->response($responseArr, 200);
    }

}