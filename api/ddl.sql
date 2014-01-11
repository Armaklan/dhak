
CREATE TABLE IF NOT EXISTS session (
  session_id varchar(255) NOT NULL,
  session_value text NOT NULL,
  session_time int(11) NOT NULL,
  PRIMARY KEY (session_id)
); 

CREATE TABLE user_profil (
	name VARCHAR(10) NOT NULL
);

CREATE TABLE IF NOT EXISTS user (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  username varchar(32) NOT NULL,
  password varchar(32) NOT NULL,
  long_name varchar(50) NOT NULL,
  mail varchar(100) NOT NULL,
  city varchar(50) NOT NULL,
  post_code varchar(5) NOT NULL,
  adresse varchar(200) NOT NULL,
  tel varchar(14) NOT NULL,
  profil VARCHAR(10) DEFAULT NULL,
  formation_lvl int(10) DEFAULT NULL,
  commentaire LONGTEXT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (username)
);

CREATE TABLE province (
         id INT unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
         name VARCHAR(50) NOT NULL,
         section VARCHAR(1) NOT NULL,
    UNIQUE KEY (name, section)
);

CREATE TABLE district (
	id INT unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(50) NOT NULL,
	province_id INT unsigned NOT NULL,
	FOREIGN KEY (province_id) REFERENCES province(id) ON DELETE RESTRICT
);

CREATE TABLE branche (
	id INT unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(12) NOT NULL,
	UNIQUE KEY (name)
);

CREATE TABLE groupe (
	id INT unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(50) NOT NULL,
	district_id INT unsigned NOT NULL,
	UNIQUE KEY(name),
	FOREIGN KEY (district_id) REFERENCES district(id) ON DELETE RESTRICT
);

CREATE TABLE unite (
	id INT unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	groupe_id INT unsigned NOT NULL,
	branche_id INT unsigned NOT NULL,
	size INT unsigned,
	FOREIGN KEY (groupe_id) REFERENCES groupe(id) ON DELETE RESTRICT,
	FOREIGN KEY (branche_id) REFERENCES branche(id) ON DELETE RESTRICT
);

CREATE TABLE asso_unite_user (
	user_id INT unsigned NOT NULL,
	unite_id INT unsigned NOT NULL,
	UNIQUE KEY(user_id, unite_id),
	FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
	FOREIGN KEY (unite_id) REFERENCES unite(id) ON DELETE CASCADE
);

CREATE TABLE has_access (
	user_id INT unsigned NOT NULL,
	unite_id INT unsigned NOT NULL,
	UNIQUE KEY(user_id, unite_id),
	FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
	FOREIGN KEY (unite_id) REFERENCES unite(id) ON DELETE CASCADE
);

CREATE TABLE formation (
	lvl INT unsigned NOT NULL,
	name VARCHAR(50) NOT NULL,
	shortname VARCHAR(5) NOT NULL,
	UNIQUE KEY(name)
);

ALTER TABLE  `user` ADD  `firstname` VARCHAR( 200 ) NULL AFTER  `long_name` ,
ADD  `birthday` DATE NULL AFTER  `firstname` ;

ALTER TABLE  `unite` ADD  `nb_sizaine` INT NULL ,
ADD  `commentaire` LONGTEXT NULL ;



ALTER TABLE `asso_unite_user` ADD `camp` INT NOT NULL;


CREATE TABLE camp (
	id INT unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	unite_id INT unsigned NOT NULL,
	duree INT unsigned,
	distance INT unsigned,
	lieu VARCHAR(200),
	FOREIGN KEY (unite_id) REFERENCES unite(id) ON DELETE RESTRICT
);
