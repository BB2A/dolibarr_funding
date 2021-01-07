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
	ref varchar(128) DEFAULT '(STUD)' NOT NULL, 
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
	date_end date, 
	redemption smallint NOT NULL, 
	fk_funding_type smallint NOT NULL, 
	fk_org integer NOT NULL, 
	fk_soc integer, 
	fk_propal integer, 
	fk_order integer, 
	fk_user_comm integer, 
	description text, 
	note_public text, 
	note_private text, 
	date_creation datetime NOT NULL, 
	tms timestamp, 
	fk_user_creat integer NOT NULL, 
	fk_user_modif integer, 
	import_key varchar(14), 
	model_pdf varchar(255), 
	status_folder smallint NOT NULL, 
	status smallint NOT NULL
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;
