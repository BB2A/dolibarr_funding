-- Copyright (C) ---Put here your own copyright and developer email---
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see https://www.gnu.org/licenses/.


CREATE TABLE llx_funding_funding(
	-- BEGIN MODULEBUILDER FIELDS
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL, 
	ref varchar(128) DEFAULT '(PROV)' NOT NULL, 
	study_number varchar(128), 
	folder_number varchar(128), 
	amount double DEFAULT NULL, 
	amount_maint double DEFAULT NULL, 
	amount_total double DEFAULT NULL, 
	fk_duration integer NOT NULL, 
	coef real, 
	fk_scale integer NOT NULL, 
	amount_rent double DEFAULT NULL, 
	amount_rent_edit double DEFAULT NULL, 
	date_delivery date, 
	date_signature date, 
	date_end date, 
	fk_funding_type smallint NOT NULL, 
	redemption smallint NOT NULL, 
	redemption_number varchar(128), 
	retention smallint NOT NULL, 
	retention_rate real, 
	retention_mount double DEFAULT NULL,
	fk_org integer NOT NULL, 
	fk_soc integer NOT NULL, 
	fk_soc_invoice integer, 
	fk_user_comm integer, 
	description text, 
	fundoc1 varchar(255), 
	fundoc1check smallint,
	fundoc2 varchar(255), 
	fundoc2check smallint,
	fundoc3 varchar(255), 
	fundoc3check smallint,
	fundoc4 varchar(255), 
	fundoc4check smallint,
	fundoc5 varchar(255), 
	fundoc5check smallint, 
	funfoldoc1 varchar(255), 
	funfoldoc2 varchar(255), 
	funfoldoc3 varchar(255), 
	funfoldoc4 varchar(255), 
	funfoldoc5 varchar(255), 
	funfoldoc6 varchar(255), 
	extension smallint DEFAULT 0, 
	note_public text, 
	note_private text, 
	date_creation datetime NOT NULL, 
	tms timestamp, 
	fk_user_creat integer NOT NULL, 
	fk_user_modif integer, 
	origin varchar(128) NOT NULL, 
	origin_id integer NOT NULL, 
	last_main_doc varchar(255), 
	import_key varchar(14), 
	model_pdf varchar(255), 
	status_folder smallint, 
	status smallint NOT NULL
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;

ALTER TABLE llx_funding_funding ADD COLUMN redemption_number varchar(128) AFTER redemption;
ALTER TABLE llx_funding_funding ADD COLUMN retention_mount double AFTER retention_rate;
ALTER TABLE llx_funding_funding ADD COLUMN date_signature date AFTER date_delivery;
ALTER TABLE llx_funding_funding ADD COLUMN fundoc5 varchar(255) AFTER fundoc4;
ALTER TABLE llx_funding_funding ADD COLUMN funfoldoc6 varchar(255) AFTER funfoldoc5;
ALTER TABLE llx_funding_funding ADD COLUMN extension smallint AFTER funfoldoc6;
ALTER TABLE llx_funding_funding ADD COLUMN fundoc1check smallint AFTER fundoc1;
ALTER TABLE llx_funding_funding ADD COLUMN fundoc2check smallint AFTER fundoc2;
ALTER TABLE llx_funding_funding ADD COLUMN fundoc3check smallint AFTER fundoc3;
ALTER TABLE llx_funding_funding ADD COLUMN fundoc4check smallint AFTER fundoc4;
ALTER TABLE llx_funding_funding ADD COLUMN fundoc5check smallint AFTER fundoc5;
ALTER TABLE llx_funding_funding DROP COLUMN pre_study ;
ALTER TABLE llx_funding_funding DROP COLUMN fk_propal;
ALTER TABLE llx_funding_funding DROP COLUMN fk_order;

