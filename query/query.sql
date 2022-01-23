create table admins(
  admin_id int(11) not null primary key auto_increment,
  username varchar(60) not null,
  password varchar(60) not null,
  created_at datetime
);

create table staffs(
  staff_id int(11) not null primary key auto_increment,
  fullname varchar(60) not null,
  gender enum('male', 'female') not null,
  age smallint(5) not null,
  phone varchar(30) not null,
  email varchar(60) not null,
  dissabilities text not null,
  year_of_experience varchar(10) not null,
  on_leave enum('off', 'on') not null,
  reg_no varchar(20) not null,
  passport varchar(30) not null
);

create table rosters(
  roster_id int(11) not null primary key auto_increment,
  staff_id int(11) not null,
  duty_id int(11) not null,
  attendance_code int(11) not null,
  status enum('present', 'absent') not null,
  date_created date,
  FOREIGN KEY (staff_id) REFERENCES staffs(staff_id),
  FOREIGN KEY (duty_id) REFERENCES duties(duty_id)
);

create table duties(
  duty_id int(11) not null primary key auto_increment,
  duty varchar(250) not null,
  period_from char(30) not null,
  period_to char(30) not null,
  concentration_level smallint not null
);
