create table tp_weibos
(
   tid INT(20) not null AUTO_INCREMENT,
   username VARCHAR(16) not null,
   nickname VARCHAR(32),
   source VARCHAR(48) not null,
   message TEXT,
   timeline TEXT not null,
   primary key (tid)
);