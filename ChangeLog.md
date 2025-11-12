# CHANGELOG FUNDING FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

## [1.1.2] - 11/2025 Dolibarr 22
- FIX - 09/11/2025 - Update send mail

## [1.1.2] - 06/2025 Dolibarr 22
- FIX - 05/09/2025 - Correction affichage des tableaux


## [1.1.1] - 06/2025
- FIX - 04/06/2025 - Corrections vérificartion date de livraison renseigné sur le document avec retrocompatible
- FIX - 04/06/2025 - Dans le trigger appel à la fonction setRun pour avoir le même comprtement avec le bouton actif 

## [1.1.0] - 04/2025

- NEW - 20/04/2025 - Ajout du parametre durée de validité
- FIX - 20/04/2025 - Corrections sur ajout date de vilidité
- FIX - 20/04/2025 - PHP erreurs onglet  événements d'un financement
- FIX - 19/04/2025 - Lors de la réation d'une commande on vérifie le lien avec propo pour copier le finanacement. Si le financement n'exister pas sur la propo ou pas de lien vers une propo erreur php.
- FIX - 03/04/2025 - Modification de triggers nouveau fonctionnement element@module
- FIX - 03/04/2025 - Changement du nom de l'editeur
- NEW - 03/04/2025 - Ajout d'un champ date d'acceptation et un champ date de validité.
- NEW - 03/04/2025 - Ajout d'un paramettre générale du module "nombres de jours validité" qui permet de calculer la date de fin de validité

## [1.0.10] - 03/2025
- FIX - La mise à jour forcé des taux ne mettait pas à jour le taux de la retenu de garantie
- FIX - La date de livraison n'etait plus pris en compte quand le document etait passer en facture avant la livraison le statut actif du financement bloqué la mise a jour de la date de signature.

## [1.0.9] - 08/2024
- FIX - Validation d'une commande, on clone le financement à partir d'une proposition. Modification du filtre de recherche de la proposition lié à cette commande en ajoutant 'c.targettype = 'commande'', car les id de commande ou de proposition pouvait être les mêmes qu'un financement ce qui faisait récupérer le mauvais financement.
- FIX - Changement $object->element pour etre conforme avec dolibarr

## [1.0.8] - 02/2024
- FIX - Compatibilitée à partir de la version 18.0.0 dans les commandes le champ date_livraison est maintenant delivery_date.

- NEW - Ajout d'un avertissement sur les propositions financiére l'orsque une commande est faite pour celle-ci.
- NEW - Permission Add plutôt que manage pour le retour en brouillon.
- NEW - Ajout d'une colone dans la liste des propositions et des commandes

## [1.0.7] - 07/2023
- FIX - Loyer personnalisé uniquement sur les propositions pour l'affichage sur les PDF et les mail. Sur les commandes et factures aucun intéré doit refléter le montant réel.
- FIX - Amélioration sur l'envoie du mail pour les financements bientot à therme.

- NEW - Ajout de la recherche des 4 premiers fichier dans un autre financement
- NEW - Loyer personalisé arrondi à l'euro supérieur.
- NEW - Si ouverture proposition financiere indique si un financement existe pour la commande ou facture correspondante
- NEW - Ajout de "entity" en base sur funding pour l'implémentation du multisociété(Coef et retenue de garantie commun au société à définir plus tard)
- NEW - Ajout de "fk-invoice" en base sur funding pour le ratachement future à la facture.

## [1.0.6] - 07/2023
- FIX - Ammélioration de la gestion des erreurs et messages sur l'envoie de fichier
- FIX - Pas de mise à jour du taux et de la retenu de garantie si il y à pas de modification du montant.

- NEW - Ajout de la recherche du RIB dans un autre financement
- NEW - Ajout d'un boutton pour forcer la mise a jour avec taux et retenu de garantie

## [1.0.5] - 05/2023
- FIX - Trigger FUNDING_UPDATE was doubled to update funding
- FIX - Erreur de retour apres la modification d'une commande avec financement annuler $result n'etait pas initialisé

- NEW - Ajout des triggers pour les coeficients et les retenu de garantie
- NEW - Ajout du script sql lors de la mise à jour du module
- NEW - Ajout du positionement de la colonne de selection à gauche avec l'option MAIN_CHECKBOX_LEFT_COLUMN
- NEW - Ajout du script sql lors de la mise à jour de dolibarr

## [1.0.5] - 10/2022

- FIX - Update security check
- FIX - Le retour apres suppression se fait correctement sur la fiche du document ou sur la liste
- FIX - Création de la fonction [sendDocumentFunding()] pour une meilleur gestion des fichiers envoyer
- FIX - Le coef et la retenu de garantie n'ai pas mise à jour si le montant ne change pas
- FIX - Pré-envoyer un e-mail et ajouter une pièce jointe envoyer le e-mail automatiquement

- NEW - Afficher si le RIB est bien présent (Faire attention si le RIB est sur le Tiers Ou sur le financement).
- NEW - Ajouter un bouton pour nettoyer le montant de maintenance et le loyer personnalisé.

## [1.0.4] - 03/2022

- FIX - Php V8 warning class html form
- FIX - Enregistrement d'une piéce demandé plus de message d'erreur pas de fichier
- FIX - Permission pour la suppression de document
- FIX - Finnancement pouvant etre actif sans date de livraison
- FIX - Sécurité si utilisateur externe ne vois rien

- NEW - Ajout hook notification Dolibarr V16
- NEW - Ajout hook dernier financement sur comm card du tiers
- NEW - Amélioration pour affichage technicien
- NEW - Ajout de badge dans les onglets pour connaitre le nombre de financement à partir de Dolibarr V16

## [1.0.3] - 02/2022

- FIX - Désactivation visibilité origin and origin_id
- FIX - Erreur sur fonction prolonguation
- FIX - Erreur massaction accept denied
- FIX - Substitution funding type
- FIX - Passage en commande reprend si le financment est accepté
- FIX - Retour brouillon init statut dossier
- FIX - Supprimer fk_propal et fk_order dans la base (non utile) remplacé par origin et orign_id
- FIX - Recherche sur une fiche tier renvoyer sur la liste compléte.

- NEW - Paramettre pour ne pas afficher les propositions de financements dans l'onglet financements des tiers
- NEW - Ajout des listes brouillon
- NEW - Ajout bouton pour indiquer l'envoie du dossier et ajout dans les mass actions
- NEW - Ajout bouton pour indiquer qu'il manque des piéces au dossier
- NEW - Ajout des mass actions
- NEW - Ajout du statut accépté avec retenu de garantie
- NEW - Ajout du statut dossier racheté ou dénoncé lors de la clôture d'un financement manuelement.
- NEW - Ajout des taches planifiés
- NEW - Ajout de substitutions
- NEW - Prise en compte des réglages par defaut
- NEW - Ajout retour liste sur les fiches
- NEW - Ajout du montant de retenue de garantie
- NEW - Ajout commentaire financement terminé
- NEW - Indiquer les documents demandé
- NEW - Ajout du pointage des financements

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
