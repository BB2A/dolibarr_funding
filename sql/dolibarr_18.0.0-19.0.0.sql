--
-- Script run when an upgrade of Dolibarr is done. Upgrade Dolibarr version 18.0.0 to 19.0.0.
--
update llx_actioncomm set elementtype = 'funding@funding' where elementtype = 'funding';
update llx_actioncomm set elementtype = 'coefficient@funding' where elementtype = 'coefficient';
update llx_actioncomm set elementtype = 'retention@funding' where elementtype = 'retention';