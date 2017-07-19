create table tp_api
(
   id INT(20) not null AUTO_INCREMENT,
   uid INT(20) not null,
   appname VARCHAR(16) not null,
   appkey VARCHAR(32) not null,
   appsecret VARCHAR(32) not null,
   appurl TEXT not null,
   primary key (id)
);