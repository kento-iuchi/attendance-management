drop table if exists members;
create table members(
  id int not null auto_increment primary key,
  name varchar(80),
  join_date date
);

drop table if exists reasons;
create table reasons(
  id int not null auto_increment primary key,
  name varchar(80)
);

drop table if exists history;
create table history(
  reason_id int,
  reason_detail text,
  member_name int,
  apply_date date,
  late_time time

);

desc members;
desc reasons;
desc history;
