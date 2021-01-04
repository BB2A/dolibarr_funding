create table llx_c_funding_scale
(
  rowid   	integer	AUTO_INCREMENT	PRIMARY KEY	NOT NULL,
  code    	varchar(12) NOT NULL,
  label 	varchar(30),
  active  	tinyint DEFAULT 1  NOT NULL
)ENGINE=innodb;

INSERT INTO `llx_c_funding_scale` (`rowid`, `code`, `label`, `active`) VALUES (NULL, '1', 'Standard', '1');
INSERT INTO `llx_c_funding_scale` (`rowid`, `code`, `label`, `active`) VALUES (NULL, '2', 'Création', '1');
