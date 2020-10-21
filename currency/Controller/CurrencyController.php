<?php
/**
 * Created by PhpStorm.
 * User: dilshod
 * Date: 2020-06-06
 * Time: 13:53
 */
namespace Currency\Controller;

use Currency\Model\Currency;
use Klein\Request;
use Klein\Response;

class CurrencyController
{
    const PER_PAGE = 5;

    public function currencies(Request $request, Response $response)
    {
        $page = (int) $request->page;

        $currency = new Currency();
        $currencies = $currency->pagination($page, self::PER_PAGE);
        $count = !empty($currencies) ? ceil($currency->count() / self::PER_PAGE) : 0;

        return $response->json([
            'data' => $currencies,
            'total' => $count
        ]);
    }

    public function currency(Request $request, Response $response)
    {
        $id = (int) $request->id;

        $currency = new Currency();
        $currency_item = $currency->getById($id);

        return $response->json([
            'data' => $currency_item
        ]);
    }
}
