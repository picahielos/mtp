<?php

namespace MTP\Tests\Models;

use PHPUnit_Framework_TestCase;
use Exception;
use PDO;
use MTP\Transaction;
use MTP\Models\TransactionDbModel;

class TransactionDbModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PDO
     */
    private $db;

    public function setUp()
    {
        $this->db = new PDO('mysql:host=localhost;dbname=mtp', 'root', '');
        $this->db->beginTransaction();
        $this->db->exec('truncate table transaction;');

        $this->model = new TransactionDbModel($this->db);
    }

    public function tearDown()
    {
        $this->db->rollBack();
    }

    /**
     * @expectedException Exception
     */
    public function testSaveWrongTransaction()
    {
        $transaction = new Transaction();

        $this->model->save($transaction);
    }

    public function testSave()
    {
        $transaction = new Transaction();
        $transaction->userId = 134256;
        $transaction->currencyFrom = 'EUR';
        $transaction->currencyTo = 'GBP';
        $transaction->amountSell = 1000;
        $transaction->amountBuy = 747;
        $transaction->rate = 2;
        $transaction->timePlaced = '2015-01-24 10:27:44';
        $transaction->originatingCountry = 'FR';

        $this->model->save($transaction);

        $statement = $this->db->query('select * from transaction');
        $result = $statement->fetchObject('\MTP\Transaction');
        $transaction->id = 1;
        $this->assertEquals($result, $transaction);
    }

    public function testGetNoTransaction()
    {
        $results = $this->model->get();
        $this->assertEquals($results, []);
    }

    public function testGet()
    {
        $transaction1 = new Transaction();
        $transaction1->id = 1;
        $transaction1->userId = 134256;
        $transaction1->currencyFrom = 'EUR';
        $transaction1->currencyTo = 'GBP';
        $transaction1->amountSell = 1000;
        $transaction1->amountBuy = 747;
        $transaction1->rate = 2;
        $transaction1->timePlaced = '2015-01-24 10:27:44';
        $transaction1->originatingCountry = 'FR';

        $transaction2 = new Transaction();
        $transaction2->id = 2;
        $transaction2->userId = 7890;
        $transaction2->currencyFrom = 'GBP';
        $transaction2->currencyTo = 'EUR';
        $transaction2->amountSell = 440;
        $transaction2->amountBuy = 127;
        $transaction2->rate = 1;
        $transaction2->timePlaced = '2015-01-31 10:17:44';
        $transaction2->originatingCountry = 'ES';
        
        $query = <<<SQL
insert into transaction (
  id, userId, currencyFrom, currencyTo, amountSell, amountBuy, rate, timePlaced, originatingCountry
) values(
  :id, :userId, :currencyFrom, :currencyTo, :amountSell, :amountBuy, :rate, :timePlaced, :originatingCountry
)
SQL;
        $statement = $this->db->prepare($query);
        $statement->execute(get_object_vars($transaction1));
        $statement->execute(get_object_vars($transaction2));

        $results = $this->model->get();

        $this->assertEquals($results, [$transaction1, $transaction2]);
    }
}
