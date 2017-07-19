create table tp_tauthclient
(
   id INT(20) not null AUTO_INCREMENT,
   appkey VARCHAR(32) not null,
   appsecret VARCHAR(32) not null,
   siteurl TEXT not null,
   primary key (id)
);