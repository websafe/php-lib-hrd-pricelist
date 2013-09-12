<?php

/**
 * Domain Pricelist Entry Class.
 *
 * @link http://github.com/websafe/php-lib-hrd-pricelist GitHub project page
 * @author Thomas Szteliga <ts@websafe.pl>
 * @copyright Copyright (c) 2013 Thomas Szteliga (https://websafe.pl/)
 * @license http://directory.fsf.org/wiki?title=License:FreeBSD BSD-2-Clause
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL-3.0+
 */
/**
 * Copyright (c) 2013, Thomas Szteliga <ts@websafe.pl>, http://websafe.pl/
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in
 *    the documentation and/or other materials provided with the
 *    distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * ALTERNATIVELY, php-lib-hrd-pricelist is free software: you can
 * redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * php-lib-hrd-pricelist is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with php-lib-hrd-pricelist.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 */

namespace Websafe\Hrd\Pricelist\Entry;

use Websafe\Hrd\Pricelist\Entry\AbstractPricelistEntry;

class Domain extends AbstractPricelistEntry
{
    protected $type;
    
    public function setPrice($priceId, $purchase, $sell)
    {
        $this->prices[$priceId] = array(
            'purchase' => $purchase,
            'sell' => $sell
        );
        //
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * 
     * @param string $priceId Price identificator
     * @param string $priceType Price type identificator (purchase, sell)
     */
    public function hasPrice($priceId, $priceType)
    {
        $hasPrice = false;
        if(array_key_exists($priceId, $this->prices))
        {
            if(array_key_exists($priceType, $this->prices[$priceId]))
            {
                $hasPrice = true;
            }
        }
        //
        return $hasPrice;
    }

    /**
     * 
     * @param string $priceId Price identificator
     * @param string $priceType Price type identificator (purchase, sell)
     */
    public function getPrice($priceId, $priceType)
    {
        $price = null;
        if($this->hasPrice($priceId, $priceType))
        {
            $price = $this->prices[$priceId][$priceType];
        }
        //
        return $price;
    }
    public function getPurchasePrice($priceId)
    {
        return $this->getPrice($priceId, 'purchase');
    }

    public function getSellPrice($priceId)
    {
        return $this->getPrice($priceId, 'sell');
    }
    
    public function setOptions($options = array())
    {
        //
        if(array_key_exists('id', $options))
        {
            $this->id = $options['id'];
        }
        //
        if(array_key_exists('type', $options))
        {
            $this->type = $options['type'];
        }
        //
        if(array_key_exists('prices', $options))
        {
            if(is_array($options['prices']))
            {
                foreach($options['prices'] as $priceId => $priceData)
                {
                    if(array_key_exists('purchase', $priceData))
                    {
                        if(array_key_exists('sell', $priceData))
                        {
                            $this->setPrice(
                                $priceId, 
                                $priceData['purchase'], 
                                $priceData['sell']
                            );
                        }
                        else
                        {
                            // missing sell price
                        }
                    }
                    else
                    {
                        // missing purchase price
                    }
                }
            }
            else
            {
                // prices is not an array
            }
        }
        else
        {
            // missing prices
        }
    }
}