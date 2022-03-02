# CHANGELOG FUNDING FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

## [1.0.3] - 02/2022

- FIX - Désactivation visibilité origin and origin_id
- FIX - Erreur sur fonction prolonguation
- FIX - Erreur massaction accept denied
- FIX - Substitution funding type
- FIX - Passage en commande reprend si le financment est accepté
- FIX - Retour brouillon init statut dossier

- NEW - Paramettre pour ne pas afficher les propositions de financements dans l'onglet financements des tiers
- NEW - Ajout des listes brouillon
- NEW - Ajout bouton pour indiquer l'envoie du dossier et ajout dans les mass actions
- NEW - Ajout bouton pour indiquer qu'il manque des piéces au dossier
- NEW - Ajout des mass actions
- NEW - Ajout du statut accépté avec retenu de garantie
- NEW - Ajout du statut dossier racheté ou dénoncé lors de la clôture d'un financement manuelement.
- NEW - Ajout de taches planifiés
- NEW - Ajout de substitutions
- NEW - Prise en compte des réglages par defaut
- NEW - Ajout retour liste sur les fiches

## [1.0.2] - 01/2022 (Désactiver et activer le module)

- FIX - Désactiver le boutton d'envoie à l'organisme pour plus de clarté car non foctionnel pour le moment
- FIX - Menu nouvelle proposition financière ne filtré pas sur le bon statut. (désactiver activer le module)
- FIX - Correction sur l'historique de certains événements
- FIX - Plus de contrainte si le document n'est pas validé ou déjà livré
- FIX - Correction des droits (problèmes constaté lorsque l'on afficher l'apercu d'un document)
- FIX - Correction affichage liste des documents sur mobile
- FIX - Correction filtrage par date dans les listes
- FIX - Le montant personnalisé ne peut plus être à zéro
- FIX - Erreur à la suppression d'un document sur proposition. La suppression sur une commande est possible uniquement si le statut est inférrieur à actif

- NEW - Ajout du changelog dans les paramètres du module
- NEW - Modification de "à propos" dans les paramettres du module
- NEW - L'osque le mode de règlement change sur le document
        - De financement à autre règlement -> clôture le financement
        - D'un autre règlement à financement -> Réouvre le financement à valider pour les commandes et à brouillon pour les propositions
- NEW - Le financement ne peut pas être validé si le mode de règlement du document n'est pas celui en paramètre (financement)
- NEW - Ajout d'un bouton annulé
- NEW - Amélioration de la page d'index des financements
- NEW - Un mail par défaut est paramétrable
- NEW - Ajout du document rachat signé (seulement si "rachat" est à oui)
- NEW - Validation automatique du financement sur une commande 
- NEW - Possibilité d'ajouter des images dans les pièces jointes (Elles sont transformées en PDF.)
- NEW - Liste déroulante pour sélectionner le paramètre du mode de règlement
- NEW - Liste déroulante pour sélectionner le paramètre du filtre des organismes de financement

## [1.0.1] - 10/2021

- Liste des demandes de pre-etude afficche correctement la liste
- Affichage de la liste compléte des financements si l'utilisateur peut voir tout les tiers et non le droit de managemente des financements

## [1.0.0]

Initial version
