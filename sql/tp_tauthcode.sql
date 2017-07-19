create table tp_tauthcode
(
   id INT(20) not null AUTO_INCREMENT,
   uid INT(20) not null,
   appid INT(20) not null,
   code VARCHAR(32) not null,
   expire TEXT not null,
   primary key (id)
);