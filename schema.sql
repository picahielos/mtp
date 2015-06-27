create schema mtp;

use mtp;

create table transaction (
  id int unsigned not null auto_increment primary key,
  userId int unsigned not null,
  currencyFrom char(3) not null,
  currencyTo char(3) not null,
  amountSell float unsigned not null,
  amountBuy float unsigned not null,
  rate float unsigned not null,
  timePlaced timestamp not null,
  originatingCountry char(2) not null
);
