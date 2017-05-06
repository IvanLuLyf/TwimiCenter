create table tp_comments
(
   cmid INT(20) not null AUTO_INCREMENT,
   tid INT(20) not null,
   username VARCHAR(16) not null,
   nickname VARCHAR(32),
   message TEXT,
   timeline TEXT not null,
   primary key (cmid)
);