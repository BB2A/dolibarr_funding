--
-- Script run when an upgrade of Dolibarr is done. Upgrade Dolibarr version 18.0.0 to 19.0.0.
--
update llx_element_element set targettype = 'funding_funding' where targettype = 'funding';