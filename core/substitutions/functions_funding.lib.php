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
   global $conf,$db;
	if (is_object($object) && $object->element = 'funding'){
		$substitutionarray['__FUNDING_STUDY_NIMBER__'] = isset($object->study_number) ? $object->study_number : '';
		$substitutionarray['__FUNDING_FOLDER_NUMBER__'] = isset($object->folder_number) ? $object->folder_number : '';
		$substitutionarray['__FUNDING_AMOUNT__'] = isset($object->amount) ? price($object->amount, 0, $outputlangs, 0, 0, -1, $conf->currency) : '';
		$substitutionarray['__FUNDING_AMOUNT_MAINT__'] = isset($object->amount_maint) ? price($object->amount_maint, 0, $outputlangs, 0, 0, -1, $conf->currency) : '';
		$substitutionarray['__FUNDING_AMOUNT_TOTAL__'] = isset($object->amount_total) ? price($object->amount_total, 0, $outputlangs, 0, 0, -1, $conf->currency) : '';
		$substitutionarray['__FUNDING_DURATION__'] = isset($object->fk_duration) ? $object->fk_duration : '';
		$substitutionarray['__FUNDING_COEF__'] = isset($object->coef) ? $object->coef : '';
		$substitutionarray['__FUNDING_SCALE__'] = isset($object->fk_scale) ? $object->fk_scale : '';
	    $substitutionarray['__FUNDING_AMOUNT_RENT__'] = isset($object->amount_rent) ? price($object->amount_rent, 0, $outputlangs, 0, 0, -1, $conf->currency) : '';
		$substitutionarray['__FUNDING_AMOUNT_RENT_EDIT__'] = isset($object->amount_rent_edit) ? price($object->amount_rent_edit, 0, $outputlangs, 0, 0, -1, $conf->currency) : '';
		$substitutionarray['__FUNDING_DATE_DELIVERY__'] = isset($object->date_delivery) ? $object->date_delivery : '';
		$substitutionarray['__FUNDING_DATE_END__'] = isset($object->date_end) ? $object->date_end : '';
		$substitutionarray['__FUNDING_REDEMPTION__'] = isset($object->redemption) ? $object->redemption : '';
		$substitutionarray['__FUNDING_TYPE__'] = isset($object->fk_funding_type) ? $object->fk_funding_type : '';
		$substitutionarray['__FUNDING_USER_COMM__'] = isset($object->fk_user_comm) ? $object->fk_user_comm : '';
		$substitutionarray['__FUNDING_DESCRIPTION__'] = isset($object->description) ? $object->description : '';
	}

	/*
	public $fk_org;
	public $fk_soc_invoice;


   __CONTACT_NAME_CUSTOMER__*/
}