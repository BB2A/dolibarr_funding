--
-- Script run to make a migration of module version x.x.x to module version y.y.y
--
ALTER TABLE llx_funding_funding ADD COLUMN date_end_calculated date AFTER date_delivery;
ALTER TABLE llx_funding_funding CHANGE COLUMN fk_invoice billed smallint;
ALTER TABLE llx_funding_funding MODIFY COLUMN last_main_doc varchar(255) AFTER model_pdf;
ALTER TABLE llx_funding_funding MODIFY COLUMN billed smallint AFTER last_main_doc;

UPDATE llx_funding_funding SET entity = 1 WHERE entity IS NULL;

UPDATE llx_funding_funding SET date_end_calculated = date_end WHERE date_end_calculated IS NULL;