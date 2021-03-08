<?php
/* Copyright (C) 2017  		Laurent Destailleur 	<eldy@users.sourceforge.net>
 * Copyright (C) 2020 		BERTON Anthony 			<bertonanthony@gmail.com>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file        core/substitutions/functions_funding.lib.php
 * \ingroup     funding
 * \brief       This file is a CRUD lib file for Funding (substitutions)
 */
 dol_include_once('/funding/class/funding.class.php');

 //$langs->loadLangs(array("funding@funding", "Propal", "Orders", "other"));
 
 
 
 
/** 		Function called to complete substitution array (before generating on ODT, or a personalized email)
 * 		functions xxx_completesubstitutionarray are called by make_substitutions() if file
 * 		is inside directory htdocs/core/substitutions
 * 
 *		@param	array		$substitutionarray	Array with substitution key=>val
 *		@param	Translate	$langs			Output langs
 *		@param	Object		$object			Object to use to get values
 * 		@return	void					The entry parameter $substitutionarray is modified
 */
function funding_completesubstitutionarray(&$substitutionarray,$langs,$object)
{
   global $langs, $conf,$db;
	if (is_object($object) && $object->element = 'funding'){
		$substitutionarray['__FUNDING_STUDY_NIMBER__'] = isset($object->study_number) ? $object->study_number : '';
		$substitutionarray['__FUNDING_FOLDER_NUMBER__'] = isset($object->folder_number) ? $object->folder_number : '';
		$substitutionarray['__FUNDING_AMOUNT__'] = isset($object->amount) ? price($object->amount, 0, $outputlangs, 0, 0, -1, $conf->currency) : '';
		$substitutionarray['__FUNDING_AMOUNT_MAINT__'] = isset($object->amount_maint) ? price($object->amount_maint, 0, $outputlangs, 0, 0, -1, $conf->currency) : '';
		$substitutionarray['__FUNDING_AMOUNT_TOTAL__'] = isset($object->amount_total) ? price($object->amount_total, 0, $outputlangs, 0, 0, -1, $conf->currency) : '';
		$obj = new Funding($db);
		$duration = $obj->fetch_duration($object->fk_duration);
		$substitutionarray['__FUNDING_DURATION__'] = isset($duration->label) ? $duration->label : '';
		$substitutionarray['__FUNDING_COEF__'] = isset($object->coef) ? $object->coef : '';
		$scale = $obj->fetch_scale($object->fk_scale);
		$substitutionarray['__FUNDING_SCALE__'] = isset($scale->label) ? $scale->label : '';
	    $substitutionarray['__FUNDING_AMOUNT_RENT__'] = isset($object->amount_rent) ? price($object->amount_rent, 0, $outputlangs, 0, 0, -1, $conf->currency) : '';
		$substitutionarray['__FUNDING_AMOUNT_RENT_EDIT__'] = isset($object->amount_rent_edit) ? price($object->amount_rent_edit, 0, $outputlangs, 0, 0, -1, $conf->currency) : '';
		$substitutionarray['__FUNDING_DATE_DELIVERY__'] = isset($object->date_delivery) ? dol_print_date($object->date_delivery, 'day', 0, $outputlangs) : '';
		$substitutionarray['__FUNDING_DATE_END__'] = isset($object->date_end) ? dol_print_date($object->date_end, 'day', 0, $outputlangs) : '';
		$substitutionarray['__FUNDING_REDEMPTION__'] = isset($object->redemption) ? ($object->redemption == 1 ? $langs->trans("Yes") : $langs->trans("No")) : '';
		$type = $obj->fetch_type($object->fk_funding_type);
		$substitutionarray['__FUNDING_TYPE__'] = isset($type->label) ? $type->label : '';
		$substitutionarray['__FUNDING_USER_COMM_ID__'] = isset($object->fk_user_comm) ? $object->fk_user_comm : '';
		if (!empty($obj->fk_user_comm)){
			$user_comm = new User($db);
			$result = $user_comm->fetch($object->fk_user_comm);
			$substitutionarray['__FUNDING_USER_COMM__'] = isset($result) ? $user_comm->getFullName($outputlangs) : '';
		}
		$substitutionarray['__FUNDING_DESCRIPTION__'] = isset($object->description) ? $object->description : '';
		
		//Organisme
		$org = $obj->fetch_soc($object->fk_org);
		$substitutionarray['__FUNDING_ORG_NAME__'] = isset($org->nom) ? $org->nom : '';
		$substitutionarray['__FUNDING_ORG_ALIAS__'] = isset($org->name_alias) ? $org->name_alias : '';
		$substitutionarray['__FUNDING_ORG_ZIP__'] = isset($org->zip) ? $org->zip : '';
		$substitutionarray['__FUNDING_ORG_TOWN__'] = isset($org->town) ? $org->town : '';
		$substitutionarray['__FUNDING_ORG_ADDRESS__'] = isset($org->address) ? $org->address : '';
		$substitutionarray['__FUNDING_ORG_PHONE__'] = isset($org->phone) ? $org->phone : '';
		$substitutionarray['__FUNDING_ORG_MAIL__'] = isset($org->email) ? $org->email : '';
		$substitutionarray['__FUNDING_ORG_IDPROF1__'] = isset($org->siren) ? $org->siren : '';
		$substitutionarray['__FUNDING_ORG_IDPROF2__'] = isset($org->siret) ? $org->siret : '';
		
		//Contact CUSTOMER propal
		if($object->origin == 'propal')$doc = new Propal($db);
		if($object->origin == 'order')$doc = new Commande($db);
		if(isset($doc)){
			$result = $doc->fetch($object->origin_id);
			$contacid = $doc->getIdContact('external', 'CUSTOMER');
		}
		
		
		$contact = new Contact($db);
		$result = $contact->fetch($contacid[0]);
		$contactname = $contact->getFullName($langs,'1');
		$substitutionarray['__FUNDING_CONTACT_NAME_CUSTOMER__'] = isset($contactname) ? $contactname : '';

		//Tiers de facturation
		$soc_invoice = $obj->fetch_soc($object->fk_soc_invoice);
		$substitutionarray['__FUNDING_SOC_INVOICE_NAME__'] = isset($soc_invoice->nom) ? $soc_invoice->nom : '';
		$substitutionarray['__FUNDING_SOC_INVOICE_ALIAS__'] = isset($soc_invoice->name_alias) ? $soc_invoice->name_alias : '';
		$substitutionarray['__FUNDING_SOC_INVOICE_ZIP__'] = isset($soc_invoice->zip) ? $soc_invoice->zip : '';
		$substitutionarray['__FUNDING_SOC_INVOICE_TOWN__'] = isset($soc_invoice->town) ? $soc_invoice->town : '';
		$substitutionarray['__FUNDING_SOC_INVOICE_ADDRESS__'] = isset($soc_invoice->address) ? $soc_invoice->address : '';
		$substitutionarray['__FUNDING_SOC_INVOICE_PHONE__'] = isset($soc_invoice->phone) ? $soc_invoice->phone : '';
		$substitutionarray['__FUNDING_SOC_INVOICE_MAIL__'] = isset($soc_invoice->email) ? $soc_invoice->email : '';
		$substitutionarray['__FUNDING_SOC_INVOICE_IDPROF1__'] = isset($soc_invoice->siren) ? $soc_invoice->siren : '';
		$substitutionarray['__FUNDING_SOC_INVOICE_IDPROF2__'] = isset($soc_invoice->siret) ? $soc_invoice->siret : '';

	}
	// Contact
	//__CONTACT_NAME_CUSTOMER__
}