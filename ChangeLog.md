# CHANGELOG FUNDING FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

## [1.0.2] - 01/2022

- FIX - Désactiver envoie organisme pour plus de clarté car non foctionnel pour le moment
- FIX - Menu Nouvelle proposition financiere ne filtré pas sur le bon statut. (Désactiver activer le module)
- FIX - Correction sur l'historique de certain evénements
- FIX - Plus de contrainte si le document n'est pas vilidé ou déja livré
- FIX - Correction des droits probléme contstaté lorsque l'on afficher l'apercu d'un document
- FIX - Correction ffichage  liste des document sur mobile
- FIX - Correction filtrage par date dans les listes

- NEW - Ajout du changelog dans param du module
- NEW - Modification de à propo dans param du module
- NEW - Losque le mode de réglement change sur le document
    - Financement à autre réglement -> Cloture le financement
    - Réglement autre à financement -> Réouvre le financement à valider pour les commandes et à brouillon pour les propositions
- NEW - Le financement ne peut pas etre validé si le mode de réglement du document n'est pas celui en paramettre
- NEW - Ajout d'un bouton annulé
- NEW - Amélioration de la page d'index des financements


## [1.0.1] - 10/2021

- Liste des demandes de pre-etude afficche correctement la liste
- Affichage de la liste compléte des financements si l'utilisateur peut voir tout les tiers et non le droit de managemente des financements

## [1.0.0]

Initial version
