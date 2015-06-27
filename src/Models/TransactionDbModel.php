<?php

/**
 * Model to manage transactions in DB.
 *
 * @author Toni Lopez <antonio.lopez.zapata@gmail.com>
 * @package MTP\Models
 */

namespace MTP\Models;

use PDO;
use MTP\Transaction;
use Exception;

class TransactionDbModel
{
    /**
     * @var PDO
     */
    private $db;

    /**
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @param Transaction $transaction
     * @throws Exception when there is an error saving the transaction
     */
    public function save(Transaction $transaction)
    {
        $query = <<<SQL
insert into transaction (
  id, userId, currencyFrom, currencyTo, amountSell, amountBuy, rate, timePlaced, originatingCountry
) values(
  :id, :userId, :currencyFrom, :currencyTo, :amountSell, :amountBuy, :rate, :timePlaced, :originatingCountry
)
SQL;
        $statement = $this->db->prepare($query);

        // db fields and Transaction properties have the same name
        $transactionParams = get_object_vars($transaction);

        if (!$statement->execute($transactionParams)) {
            throw new Exception('Error saving transaction into db: ' . $statement->errorInfo()[2]);
        }
    }

    /**
     * Gets a map of all the transactions in DB. Depending on the volume
     * we should consider to paginate it or filter by time.
     *
     * @return array
     */
    public function get()
    {
        $query = 'select * from transaction order by timePlaced';
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS, '\MTP\Transaction');
    }
}
