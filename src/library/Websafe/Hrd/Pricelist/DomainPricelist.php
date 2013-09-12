<?php

/**
 * DomainPricelist Class.
 *
 * @link http://github.com/websafe/websafe-php-lib-hrd-pricelist GitHub project
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
 * ALTERNATIVELY, websafe-php-lib-awd is free software: you can
 * redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * websafe-php-lib-awd is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with websafe-php-lib-awd.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 */

namespace Websafe\Hrd\Pricelist;

use DOMDocument;
use DOMXPath;
use Zend\I18n\Filter\NumberFormat;

class DomainPricelist
{
    protected $htmlFile;
    protected $html;
    /**
     *
     * @var array 
     */
    protected $pricelist = array();
    /**
     *
     * @var array 
     */
    protected $items = array();
    
    /**
     * 
     * @param string $html
     */
    public function __construct($htmlFile)
    {
        $this->htmlFile = $htmlFile;
        if(is_readable($this->htmlFile))
        {
            if(is_file($this->htmlFile))
            {
                $this->init();
            }
            else
            {
                // not a file
                echo 'ERROR: ' . $this->htmlFile . ' is not a file!';
            }
        }
        else
        {
            // not readable
            echo 'ERROR: ' . $this->htmlFile . ' is not readable!' ;
        }
    }
    /**
     * 
     * @return \DOMNode
     */
    protected function getTable()
    {
        $xml = new DOMDocument();
        $xml->validateOnParse = false;
        $xml->recover = true;
        $xml->strictErrorChecking = false;
        @$xml->loadHTMLFile($this->htmlFile);
        $xpath = new DOMXPath($xml);
        $table = $xpath->query("//table[@class='cennik']")->item(0);
        return $table;
    }
    /**
     * 
     * @return \DOMNodeList
     */
    protected function getRows()
    {
        $table = $this->getTable();
        $rows = $table->getElementsByTagName("tr");
        return $rows;
    }
    /**
     * 
     * @return DOMNodeList
     */
    protected function getCells($row)
    {
        $cells = $row->getElementsByTagName('td');
        return $cells;
    }
    /**
     * 
     * @return string
     */
    protected function rowExtractDomainType($row)
    {
        $cells = $this->getCells($row);
        foreach ($cells as $cell)
        {
            if ('head' == $cell->getAttribute('class'))
            {
                return trim($cell->nodeValue);
            }
        }
    }
    /**
     * 
     * @return array
     */
    protected function rowExtractPurchasePrices($row)
    {
        $cells = $this->getCells($row);
        $purchasePrices = array();
        foreach ($cells as $cell)
        {
            if ('cenaKupna' == $cell->getAttribute('class'))
            {
                $purchasePriceString = trim($cell->nodeValue);
                $filter = $this->getNumberFormatter();
                $purchasePrice = floatval(
                    $filter->filter(
                        $purchasePriceString
                    )
                );
                //
                $purchasePrices[] = $purchasePrice;
            }
        }
        return $purchasePrices;
    }
    /**
     * 
     * @return NumberFormat
     */
    protected function getNumberFormatter()
    {
        $filter = new NumberFormat();
        $filter->setLocale('pl_PL');
        return $filter;
    }
    /**
     * @return string
     */
    protected function cellExtractSellPrice($cell)
    {
        $inputFields = $cell->getElementsByTagName('input');
        if (0 < $inputFields->length)
        {
            // get first input field
            $inputField = $inputFields->item(0);
            // extract name value from input field
            $sellPriceString = trim($inputField->getAttribute('value'));
            $filter = $this->getNumberFormatter();
            $sellPrice = floatval($filter->filter($sellPriceString));
        }
        else
        {
            $sellPrice = null;
        }
        //
        return $sellPrice;
    }
    /**
     * @return string
     */
    protected function cellExtractPriceId($cell)
    {
        $inputFields = $cell->getElementsByTagName('input');
        $priceId = null;
        if (0 < $inputFields->length)
        {
            // get first input field
            $inputField = $inputFields->item(0);
            // extract name value from input field
            $nameValue = trim($inputField->getAttribute('name'));
            // extract price ID
            preg_match(
                '/f\[cena\]\[[0-9]{1,}\]\[([a-zA-Z_]{1,})\]/', $nameValue,
                $matches
            );
            if (array_key_exists(1, $matches))
            {
                //
                $priceId = $matches[1];
            }
        }
        //
        return $priceId;
    }
    /**
     * 
     * @return array
     */
    protected function rowExtractPriceIds($row)
    {
        $cells = $this->getCells($row);
        $priceIds = array();
        foreach ($cells as $cell)
        {
            if ('cenaSprzedarzy' == $cell->getAttribute('class'))
            {
                $priceIds[] = $this->cellExtractPriceId($cell);
            }
        }
        return $priceIds;
    }
    /**
     * 
     * @return array
     */
    protected function tableExtractPriceIds()
    {
        $rows = $this->getRows();
        foreach ($rows as $row)
        {
            $cells = $this->getCells($row);
            $priceIds = array();
            foreach ($cells as $cell)
            {
                if ('cenaSprzedarzy' == $cell->getAttribute('class'))
                {
                    $priceIds[] = $this->cellExtractPriceId($cell);
                }
            }
        }
        return $priceIds;
    }
    /**
     * 
     * @return array
     */
    protected function rowExtractSellPrices($row)
    {
        $cells = $this->getCells($row);
        $sellPrices = array();
        foreach ($cells as $cell)
        {
            if ('cenaSprzedarzy' == $cell->getAttribute('class'))
            {
                $sellPrices[] = $this->cellExtractSellPrice($cell);
            }
        }
        return $sellPrices;
    }
    /**
     * 
     * @return string
     */
    protected function cellExtractDomainId($cell)
    {
        $inputFields = $cell->getElementsByTagName('input');
        $domainId = null;
        if (0 < $inputFields->length)
        {
            // get first input field
            $inputField = $inputFields->item(0);
            // extract name value from input field
            $nameValue = trim($inputField->getAttribute('name'));
            // extract domain IDd
            preg_match(
                '/f\[cena\]\[([0-9]{1,})\]\[[a-zA-Z_]{1,}\]/', $nameValue,
                $matches
            );
            if (array_key_exists(1, $matches))
            {
                //
                $domainId = $matches[1];
            }
        }
        //
        return $domainId;
    }
    /**
     * 
     * @param mixed $row
     * @return array
     */
    protected function rowExtractDomainIds($row)
    {
        $cells = $this->getCells($row);
        $domainIds = array();
        foreach ($cells as $cell)
        {
            if ('cenaSprzedarzy' == $cell->getAttribute('class'))
            {
                $domainIds[] = $this->cellExtractDomainId($cell);
            }
        }
        return $domainIds;
    }
    /**
     * 
     * @param mixed $row
     * @return string
     */
    protected function rowExtractDomainId($row)
    {
        $cells = $this->getCells($row);
        foreach ($cells as $cell)
        {
            if (null !== ($domainId = $this->cellExtractDomainId($cell)))
            {
                return $domainId;
            }
        }
    }
    /**
     * 
     */
    protected function init()
    {
        $this->initPass1();
        $this->initPass2();
    }

    /**
     * 
     */
    protected function initPass1()
    {
        $rows = $this->getRows();
        // Expecting 11 columns in each dataset
        $expectedColumnCount = 11;
        // PASS 1 - extract data and verify data
        foreach ($rows as $row)
        {
            //
            $rowIsInvalid = false;
            //
            // --------------------------------------------------------------- #
            //
            //  EXTRACT ROW COLUMNS
            //
            // --------------------------------------------------------------- #
            // 
            $domainId = $this->rowExtractDomainId($row);
            // 
            $domainType = $this->rowExtractDomainType($row);
            //
            $purchasePrices = $this->rowExtractPurchasePrices($row);
            //
            $sellPrices = $this->rowExtractSellPrices($row);
            // 
            $domainIds = $this->rowExtractDomainIds($row);
            //
            $priceIds = $this->rowExtractPriceIds($row);
            //
            // --------------------------------------------------------------- #
            //
            //  VERIFY ROW COLUMN SETS
            //
            // --------------------------------------------------------------- #
            //
            // Verify column count in purchasePrices
            if ($expectedColumnCount !== count($purchasePrices))
            {
                // log error
                $rowIsInvalid = true;
            }
            // Verify column count in sellPrices
            if ($expectedColumnCount !== count($sellPrices))
            {
                // log error
                $rowIsInvalid = true;
            }
            // Verify column count in domainIds
            if ($expectedColumnCount !== count($domainIds))
            {
                // log error
                $rowIsInvalid = true;
            }
            // Verify column count in priceIds
            if ($expectedColumnCount !== count($priceIds))
            {
                // log error
                $rowIsInvalid = true;
            }
            //
            // --------------------------------------------------------------- #
            //
            //  GENERATE STRUCTURED PRICELIST ARRAY
            //
            // --------------------------------------------------------------- #
            //
            // If row is valid then generate structured pricelist array
            if (false === $rowIsInvalid)
            {
                //
                $prices = array();
                //
                foreach ($priceIds as $index => $priceId)
                {
                    //
                    if (!empty($priceId))
                    {
                        $prices[$priceId] = array(
                            'purchase' => $purchasePrices[$index],
                            'sell' => $sellPrices[$index],
                        );
                    }
                }
                $this->pricelist[$domainType] = array(
                    'id' => $domainId,
                    'type' => $domainType,
                    'prices' => $prices
                );
            }
            else
            {
                // log invalid row
            }
        }
        //
        //print_r($this->pricelist);
        //
        return $this;
    }
    /**
     * 
     */
    protected function initPass2()
    {
        // Pass 2 - converting structured data into objects
        foreach ($this->pricelist as $domainType => $domainData)
        {
            $item = new \Websafe\Hrd\Pricelist\Entry\Domain($domainData);
            $this->items[] = $item;
        }
        //
        return $this;
    }
    
    public function getItems()
    {
        return $this->items;
    }
    public function getItemsCount()
    {
        return count($this->items);
    }
    public function getItemsIndex()
    {
        return array_keys($this->items);
    }
    /**
     * 
     * @param type $type
     * @return \Websafe\Hrd\Pricelist\Entry\Domain
     */    
    public function getItemByIndex($index)
    {
        $item = null;
        if(array_key_exists($index, $this->items))
        {
            $item = $this->items[$index];
        }
        return $item;
    }
    /**
     * 
     * @param type $type
     * @return \Websafe\Hrd\Pricelist\Entry\Domain
     */
    public function getItemByType($type)
    {
        $item = null;
        $itemsIndex = $this->getItemsIndex();
        foreach($itemsIndex as $index)
        {
            $item = $this->getItemByIndex($index);
            if($type == $item->getType())
            {
                return $item;
            }
        }
    }
        /**
     * 
     * @param type $type
     * @return \Websafe\Hrd\Pricelist\Entry\Domain
     */
    public function getItemById($id)
    {
        $item = null;
        $itemsIndex = $this->getItemsIndex();
        foreach($itemsIndex as $index)
        {
            $item = $this->getItemByIndex($index);
            if($id == $item->getId())
            {
                return $item;
            }
        }
    }
    public function getPriceTypes()
    {
        //
        return array(
             'value_register_individual',
             'value_register',
             'value_renew',
             'special_renew',
             'trade',
             'transfer',
             'reactivate',
             'transferq',
             'future',
             'taste',
             'daily_renew',
         );
    }
}

