<?php

/**
 * Represents a transaction.
 *
 * @author Toni Lopez <antonio.lopez.zapata@gmail.com>
 * @package MTP
 */

namespace MTP;

use Exception;

class Transaction
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var string
     */
    public $currencyFrom;

    /**
     * @var string
     */
    public $currencyTo;

    /**
     * @var float
     */
    public $amountSell;

    /**
     * @var float
     */
    public $amountBuy;

    /**
     * @var float
     */
    public $rate;

    /**
     * @var string date
     */
    public $timePlaced;

    /**
     * @var string
     */
    public $originatingCountry;

    /**
     * Extract a Transaction object from a JSON.
     *
     * @param string $json
     * @return Transaction
     * @throws Exception if it's not a valid JSON
     */
    public static function extractFromJson($json)
    {
        $map = json_decode($json, true);
        if (!is_array($map)) {
            throw new Exception('Transaction is not a valid json.');
        }

        $transaction = new Transaction();

        foreach ($map as $key => $value) {
            if (property_exists($transaction, $key)) {
                $transaction->$key = $value;
            }
        }

        $transaction->timePlaced = date('Y-m-d H:i:s', strtotime($transaction->timePlaced));

        return $transaction;
    }

    /**
     * Checks if the object contains valid data.
     *
     * @throws Exception if the transaction contains invalid data
     */
    public function checkSanity()
    {
        $errors = [];

        if (!is_numeric($this->userId)) {
            $errors[] = 'User id has to be numeric.';
        }
        if (!preg_match('/^[A-Z]{3}$/', $this->currencyFrom)) {
            $errors[] = 'Currency from has to be a 3 capital letters string.';
        }
        if (!preg_match('/^[A-Z]{3}$/', $this->currencyTo)) {
            $errors[] = 'Currency to has to be a 3 capital letters string.';
        }
        if (!is_numeric($this->amountSell) || $this->amountSell< 0) {
            $errors[] = 'Amount sell has to be a positive number.';
        }
        if (!is_numeric($this->amountBuy) || $this->amountBuy < 0) {
            $errors[] = 'Amount buy has to be a positive number.';
        }
        if (!is_numeric($this->rate) || $this->rate < 0) {
            $errors[] = 'Amount buy has to be a positive.';
        }
        if (!strtotime($this->timePlaced)) {
            $errors[] = 'Time place has to be a valid date.';
        }
        if (!preg_match('/^[A-Z]{2}$/', $this->originatingCountry)) {
            $errors[] = 'Originating country has to be a 2 capital letters string.';
        }

        if (!empty($errors)) {
            throw new Exception('Some errors in the transaction: ' . implode('; ', $errors));
        }
    }
}
