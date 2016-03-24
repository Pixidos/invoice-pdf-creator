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
 * Time: 17:26
 */

namespace Pixidos\InvoicePdfCreator;


interface IPriceFormater
{

    const CZK = 'czk';
    const USD = 'usd';
    const EUR = 'eur';
    const GPB = 'gpb';

    /**
     * @param float $price
     * @param string $currencyCode
     * @param string $lang
     * @return string
     */
    public function priceFormat($price, $currencyCode, $lang);


}