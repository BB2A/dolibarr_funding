--
-- Script run when an upgrade of Dolibarr is done. Whatever is the Dolibarr version.
--

UPDATE llx_funding_funding SET entity = 1 WHERE entity IS NULL;