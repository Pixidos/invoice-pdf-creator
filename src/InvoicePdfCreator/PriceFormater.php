<?php
/**
 * This file is part of the Pixidos (http://www.pixidos.com)
 *
 * Copyright (c) 2016 Ondra Votava Pixidos LTD  (ondra.votava@pixidos.com)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 *
 */

/**
 * Created by PhpStorm.
 * User: Ondra Votava
 * Date: 10.03.2016
 * Time: 17:28
 */

namespace Pixidos\InvoicePdfCreator;

/**
 * Class PriceFormater
 * @package Pixidos\InvoicePdfCreator
 * @author Ondra Votava <ondra.votava@pixidos.com>
 */

class PriceFormater implements IPriceFormater
{

    private $langToSymbol = [

        'czk' => [
            'cs' => 'Kč',
            'en' => 'CZK'
        ],
        'eur' => [
            'cs' => 'EUR',
            'en' => 'EUR'
        ],
        'usd' => [
            'cs' => 'USD',
            'en' => 'USD'
        ]
    ];


    /**
     * @param float $price
     * @param string $currencyCode
     * @param $lang
     * @return string
     */
    public function priceFormat($price, $currencyCode, $lang)
    {
        switch ($currencyCode) {

            case 'czk':
                return number_format($price, 2, ' ') . ' ' . $this->langToSymbol[$currencyCode][$lang];
            default:
                return $this->langToSymbol[$currencyCode][$lang] . ' ' . number_format($price, 2, ' ');
        }
    }


}