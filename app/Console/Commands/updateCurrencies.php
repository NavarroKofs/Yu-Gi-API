<?php

namespace App\Console\Commands;

use App\Currency;
use Illuminate\Console\Command;
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
        $ruta_divisas = "https://frankfurter.app/latest?amount=1&from=USD&to=MXN";
        $convertCurrency = file_get_contents($ruta_divisas);
        $value = json_decode($convertCurrency, true);
        //$currency = Currency::create(["moneda" => 'MXN', "valor" =>'20.00']);
        $update = Currency::findOrFail(1);
        $update->valor = $value['rates']['MXN'];
        $update->moneda = 'MXN';
        $update->save();
    }
}
