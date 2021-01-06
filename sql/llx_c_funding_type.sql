create table llx_c_funding_type
(
  rowid   	integer	AUTO_INCREMENT	PRIMARY KEY	NOT NULL,
  code    	varchar(12) NOT NULL,
  label 	varchar(30),
  active  	tinyint DEFAULT 1  NOT NULL
)ENGINE=innodb;

INSERT INTO `llx_c_funding_type` (`rowid`, `code`, `label`, `active`) VALUES (NULL, 'LOC', 'Location', '1');
INSERT INTO `llx_c_funding_type` (`rowid`, `code`, `label`, `active`) VALUES (NULL, 'CREDIT_BAIL', 'Crédit bail', '1');
