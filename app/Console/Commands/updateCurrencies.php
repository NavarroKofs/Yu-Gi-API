<?php

namespace App\Console\Commands;

use App\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\Print_;

class updateCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will update the current currency';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currencyLink = "https://frankfurter.app/latest?amount=1&from=USD&to=MXN";
        $currencyInfo = self::getContent($currencyLink);
        if($currencyInfo->getStatusCode() != '200'){
            return response()->json([
                "errors"=> ["code"=> "ERROR-6",
                "title"=>  "Unavailable Service",
                "description"=> 'The server does not respond. Try later.'
                ]]  , 503);
        }
        $value = json_decode($currencyInfo->getBody(), true);
        $mexicanPesos = $value['rates']['MXN'];
        $currency = Currency::find(1);
        if ($currency === null) {
            $currency = Currency::create(["moneda" => 'MXN', "valor" => $mexicanPesos]);
        } else {
            $currency->valor = $mexicanPesos;
            $currency->moneda = 'MXN';
            $currency->save();
        }
    }

    public function getContent($currencyLink){
        try{
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $currencyLink);
            return $response;
        }catch(\GuzzleHttp\Exception\RequestException $e){
            return $e->getResponse();
        }
    }
}