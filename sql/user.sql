create table tp_user
(
   id INT(20) not null AUTO_INCREMENT,
   username VARCHAR(16) not null,
   password VARCHAR(32) not null,
   nickname VARCHAR(32),
   email TEXT not null,
   token TEXT not null,
   primary key (id)
);