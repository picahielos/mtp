# Market Trade Processor (MTP) #

## Dependencies ##
* [composer](https://getcomposer.org/doc/00-intro.md)
* [RabbitMQ Server](https://www.rabbitmq.com/download.html)
* MySQL with user root@localhost with no password.

## Installation ##
Composer dependencies:
```
composer install
```
Log file:
```
touch mtp.log
chmod 777 mtp.log
```
Import MySQL schema from ```schema.sql```.

## Usage ##

### Message listener ###
POST /mtp/transactions
Body JSON example:
```
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
```
It will listen for HTTP POST requests and will send the messages to the RabbitMQ queue.

### Message Processor ###
```
php scripts/processor.php
```
It will listen to the RabbitMQ and process the messages, checking they are valid and
storing them into MySQL.

### Message Frontend ###
GET /mtp/

## Tests ##
```
./vendor/bin/phpunit
```
