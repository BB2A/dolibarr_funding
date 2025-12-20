<?php
/* Copyright (C) 2017  		Laurent Destailleur 	<eldy@users.sourceforge.net>
 * Copyright (C) 2020-2025	Anthony Berton 			<anthony.berton@bb2a.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    lib/funding_coefficient.lib.php
 * \ingroup funding
 * \brief   Library files with common functions for Coefficient
 */

/**
 * Prepare array of tabs for Coefficient
 *
 * @param	Coefficient	$object		Coefficient
 * @return 	array					Array of tabs
 */
function coefficientPrepareHead($object)
{
	global $db, $langs, $conf;

	$langs->load("funding@funding");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/funding/coefficient_card.php", 1).'?id='.$object->id;
	$head[$h][1] = $langs->trans("CoefCard");
	$head[$h][2] = 'card';
	$h++;

	if (version_compare(DOL_VERSION, '22.0.0', '>=')) {
		$head[$h][0] = dol_buildpath("/funding/coefficient_messaging.php", 1).'?id='.$object->id;
	} else {
		$head[$h][0] = dol_buildpath("/funding/coefficient_agenda.php", 1).'?id='.$object->id;
	}
	$head[$h][1] = $langs->trans("Events");
	$head[$h][2] = 'agenda';
	$h++;

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'coefficient@funding');

	complete_head_from_modules($conf, $langs, $object, $head, $h, 'coefficient@funding', 'remove');

	return $head;
}
