create table tp_tauthbind
(
   id INT(20) not null AUTO_INCREMENT,
   appid INT(20) not null,
   uid INT(20) not null,
   buid INT(20) not null,
   token VARCHAR(32) not null,
   expire TEXT not null,
   primary key (id)
);