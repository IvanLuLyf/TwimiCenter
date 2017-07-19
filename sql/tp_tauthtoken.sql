create table tp_tauthtoken
(
   id INT(20) not null AUTO_INCREMENT,
   uid INT(20) not null,
   appkey VARCHAR(32) not null,
   token VARCHAR(32) not null,
   expire TEXT not null,
   primary key (id)
);