<?php
/**
 * Created by PhpStorm.
 * User: dilshod
 * Date: 2020-06-07
 * Time: 14:49
 */

namespace Currency\Command;

use Currency\Model\Currency;
use Noodlehaus\Config;

class CurrenciesUpdater
{
    protected $url;

    public function __construct()
    {
        $config = Config::load(APP_DIR . '/config/currency.php');
        $this->url = $config->get('currency_url');
    }

    public function run()
    {
        echo "\e[93m=== Updating... ===\e[0m" . PHP_EOL;

        $xml = new \SimpleXMLElement($this->url, 0, true);

        $currency = new Currency();
        $currency->delete();
        $currency->getConnection()->exec('ALTER TABLE currency AUTO_INCREMENT = 1');

        foreach ($xml->Valute as $item) {
            $row = [
                'name' => $item->Name,
                'code' => $item->CharCode,
                'rate' => (float) str_replace(',', '.', $item->Value),
            ];

            $currency->insert($row);
        }

        echo "\e[92m=== Currencies updated! ===\e[0m" . PHP_EOL;
    }
}
