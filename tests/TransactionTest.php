<?php

namespace MTP\Tests;

use Exception;
use MTP\Transaction;
use PHPUnit_Framework_TestCase;

class TransactionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Exception
     */
    public function testInvalidJson()
    {
        Transaction::extractFromJson("random[]\as");
    }

    public function testCreateTransaction()
    {
        $json = <<<JSON
{
    "userId": "134256",
    "currencyFrom": "EUR",
    "currencyTo": "GBP",
    "amountSell": 1000,
    "amountBuy": 747.1,
    "rate": 0.7471,
    "timePlaced": "24-JAN-15 10:27:44",
    "originatingCountry": "FR"
}
JSON;

        $transaction = Transaction::extractFromJson($json);

        $expected = new Transaction();
        $expected->userId = 134256;
        $expected->currencyFrom = 'EUR';
        $expected->currencyTo = 'GBP';
        $expected->amountSell = 1000;
        $expected->amountBuy = 747.1;
        $expected->rate = 0.7471;
        $expected->timePlaced = '2015-01-24 10:27:44';
        $expected->originatingCountry = 'FR';

        $this->assertEquals($expected, $transaction);

        $transaction->checkSanity();
    }

    public function providerWrongTransaction()
    {
        return array(
            array (
                '{"userId":"id","currencyFrom":"EUR","currencyTo":"GBP","amountSell":1000,"amountBuy":747.10,"rate":0.7471,"timePlaced":"24-JAN-15 10:27:44","originatingCountry":"FR"}',
            ), array (
                '{"userId":"123","currencyFrom":"EURO","currencyTo":"GBP","amountSell":1000,"amountBuy":747.10,"rate":0.7471,"timePlaced":"24-JAN-15 10:27:44","originatingCountry":"FR"}',
            ), array (
                '{"userId":"123","currencyFrom":"EUR","currencyTo":"GBPS","amountSell":1000,"amountBuy":747.10,"rate":0.7471,"timePlaced":"24-JAN-15 10:27:44","originatingCountry":"FR"}',
            ), array (
                '{"userId":"123","currencyFrom":"EUR","currencyTo":"GBP","amountSell":-123,"amountBuy":747.10,"rate":0.7471,"timePlaced":"24-JAN-15 10:27:44","originatingCountry":"FR"}',
            ), array (
                '{"userId":"123","currencyFrom":"EUR","currencyTo":"GBP","amountSell":"money","amountBuy":747.10,"rate":0.7471,"timePlaced":"24-JAN-15 10:27:44","originatingCountry":"FR"}',
            ), array (
                '{"userId":"123","currencyFrom":"EUR","currencyTo":"GBP","amountSell":1000,"amountBuy":"random","rate":0.7471,"timePlaced":"24-JAN-15 10:27:44","originatingCountry":"FR"}',
            ), array (
                '{"userId":"123","currencyFrom":"EUR","currencyTo":"GBP","amountSell":1000,"amountBuy":-747.10,"rate":0.7471,"timePlaced":"24-JAN-15 10:27:44","originatingCountry":"FR"}',
            ), array (
                '{"userId":"123","currencyFrom":"EUR","currencyTo":"GBP","amountSell":1000,"amountBuy":747.10,"rate":"a lot","timePlaced":"24-JAN-15 10:27:44","originatingCountry":"FR"}',
            ), array (
                '{"userId":"123","currencyFrom":"EUR","currencyTo":"GBP","amountSell":1000,"amountBuy":747.10,"rate":-0.7471,"timePlaced":"24-JAN-15 10:27:44","originatingCountry":"FR"}',
            ), array (
                '{"userId":"123","currencyFrom":"EUR","currencyTo":"GBP","amountSell":1000,"amountBuy":747.10,"rate":0.7471,"timePlaced":"not a date","originatingCountry":"FR"}',
            ), array(
                '{"userId":"123","currencyFrom":"EUR","currencyTo":"GBP","amountSell":1000,"amountBuy":747.10,"rate":0.7471,"timePlaced":"24-JAN-15 10:27:44","originatingCountry":"FRANCE"}',
            )
        );
    }

    /**
     * @expectedException Exception
     * @dataProvider providerWrongTransaction
     * @param string $json
     */
    public function testCheckSanityFails($json)
    {
        $transaction = Transaction::extractFromJson($json);

        $transaction->checkSanity();
    }
}
