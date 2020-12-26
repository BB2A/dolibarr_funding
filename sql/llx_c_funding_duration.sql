create table llx_c_funding_duration
(
  rowid   	integer	AUTO_INCREMENT	PRIMARY KEY	NOT NULL,
  code    	varchar(12) NOT NULL,
  label 	varchar(30),
  active  	tinyint DEFAULT 1  NOT NULL
)ENGINE=innodb;

INSERT INTO `llx_c_funding_duration` (`rowid`, `code`, `label`, `active`) VALUES (NULL, '12', '12 Mois', '1');
INSERT INTO `llx_c_funding_duration` (`rowid`, `code`, `label`, `active`) VALUES (NULL, '24', '24 Mois', '1');
INSERT INTO `llx_c_funding_duration` (`rowid`, `code`, `label`, `active`) VALUES (NULL, '36', '36 Mois', '1');
INSERT INTO `llx_c_funding_duration` (`rowid`, `code`, `label`, `active`) VALUES (NULL, '48', '48 Mois', '1');
INSERT INTO `llx_c_funding_duration` (`rowid`, `code`, `label`, `active`) VALUES (NULL, '60', '60 Mois', '1');
