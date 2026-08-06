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
	entity integer DEFAULT 1 NOT NULL,    -- multi company id
	study_number varchar(128), 
	folder_number varchar(128), 
	fk_org integer NOT NULL,  
	fk_soc integer NOT NULL, 
	fk_soc_invoice integer, 
	amount double DEFAULT NULL, 
	amount_maint double DEFAULT NULL, 
	amount_total double DEFAULT NULL, 
	fk_duration integer NOT NULL, 
	coef real, 
	fk_scale integer NOT NULL, 
	amount_rent double DEFAULT NULL, 
	amount_rent_edit double DEFAULT NULL, 
	date_accepted date, 
	date_endvalidity date, 
	date_delivery date, 
	date_end_calculated date,
	date_signature date, 
	date_end date, 
	fk_funding_type smallint NOT NULL, 
	redemption smallint, 
	redemption_number varchar(128), 
	retention smallint, 
	retention_rate real, 
	retention_mount double DEFAULT NULL,
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
	fundoc6 varchar(255), 
	fundoc6check smallint, 
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
	import_key varchar(14), 
	model_pdf varchar(255), 
	last_main_doc varchar(255), 
	billed smallint, 
	funcheck smallint,
	status_folder smallint, 
	status smallint NOT NULL
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;

-- UPDATE llx_actioncomm SET elementtype = 'funding_funding' WHERE elementtype = 'funding';
-- UPDATE llx_element_element SET targettype = 'funding_funding' WHERE targettype = 'funding';