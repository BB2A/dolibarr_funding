<?php
/* Copyright (C) 2020 BERTON Anthony <a.berton@gest-mag.com>
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
 * \file    funding/lib/funding.lib.php
 * \ingroup funding
 * \brief   Library files with common functions for Funding
 */
 
/**
 * Prepare admin pages header
 *
 * @return array
 */
function fundingAdminPrepareHead()
{
	global $langs, $conf;

	$langs->load("funding@funding");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/funding/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	/*
	$head[$h][0] = dol_buildpath("/funding/admin/myobject_extrafields.php", 1);
	$head[$h][1] = $langs->trans("ExtraFields");
	$head[$h][2] = 'myobject_extrafields';
	$h++;
	*/

	$head[$h][0] = dol_buildpath("/funding/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@funding:/funding/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@funding:/funding/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'funding');

	return $head;
}



/**
 * Return a HTML table that contains a pie chart of customer proposals
 *
 * @param	int		$socid		(Optional) Show only results from the customer with this id
 * @return	string				A HTML table that contains a pie chart of customer invoices
 */
function getCustomerFundingPieChart($socid = 0)
{
	global $conf, $db, $langs, $user;

	$result= '';

	if (empty($conf->propal->enabled) || empty($user->rights->propal->lire)) {
		return '';
	}

	$listofstatus = array(Funding::STATUS_DRAFT, Funding::STATUS_VALIDATED, Funding::STATUS_ACCEPT, Funding::STATUS_DENIED, Funding::STATUS_RUNNING);

	$fundingstatic = new Funding($db);

	$sql = "SELECT count(f.rowid) as nb, f.status as status, f.fk_user_comm, f.origin";
	$sql .= " FROM ".MAIN_DB_PREFIX."funding_funding as f";
	$sql .= " WHERE f.origin = 'order'";
	//$sql .= " AND f.status IN (".$db->sanitize(implode(" ,", $listofstatus)).")";
	$sql .= " GROUP BY f.status";
	$resql = $db->query($sql);
	if ($resql) {
		$num = $db->num_rows($resql);
		$i = 0;
		$total = 0;
		$totalinprocess = 0;
		$dataseries = array();
		$colorseries = array();
		$vals = array();

		while ($i < $num) {
			$obj = $db->fetch_object($resql);
			if ($obj) {
				$vals[$obj->status] = $obj->nb;
				$totalinprocess += $obj->nb;

				$total += $obj->nb;
			}
			$i++;
		}
		$db->free($resql);

		include DOL_DOCUMENT_ROOT.'/theme/'.$conf->theme.'/theme_vars.inc.php';

		$result = '<div class="div-table-responsive-no-min">';
		$result .= '<table class="noborder nohover centpercent">';

		$result .=  '<tr class="liste_titre">';
		$result .=  '<td colspan="2">'.$langs->trans("Statistics").' - '.$langs->trans("Fundings").'</td>';
		$result .=  '</tr>';

		foreach ($listofstatus as $status) {
			$dataseries[] = array($fundingstatic->LibStatut($status, 1), (isset($vals[$status]) ? (int) $vals[$status] : 0));
			if ($status == Funding::STATUS_DRAFT) {
				$colorseries[$status] = '-'.$badgeStatus0;
			}
			if ($status == Funding::STATUS_VALIDATED) {
				$colorseries[$status] = $badgeStatus1;
			}
			if ($status == Funding::STATUS_ACCEPT) {
				$colorseries[$status] = $badgeStatus2;
			}
			if ($status == Funding::STATUS_DENIED) {
				$colorseries[$status] = $badgeStatus3;
			}
			if ($status == Funding::STATUS_RUNNING) {
				$colorseries[$status] = $badgeStatus4;
			}

			if (empty($conf->use_javascript_ajax)) {
				$result .=  '<tr class="oddeven">';
				$result .=  '<td>'.$fundingstatic->LibStatut($status, 0).'</td>';
				$result .=  '<td class="right"><a href="list.php?statut='.$status.'">'.(isset($vals[$status]) ? $vals[$status] : 0).'</a></td>';
				$result .=  "</tr>\n";
			}
		}

		if ($conf->use_javascript_ajax) {
			$result .=  '<tr>';
			$result .=  '<td align="center" colspan="2">';

			include_once DOL_DOCUMENT_ROOT.'/core/class/dolgraph.class.php';
			$dolgraph = new DolGraph();
			$dolgraph->SetData($dataseries);
			$dolgraph->SetDataColor(array_values($colorseries));
			$dolgraph->setShowLegend(2);
			$dolgraph->setShowPercent(1);
			$dolgraph->SetType(array('pie'));
			$dolgraph->setHeight('150');
			$dolgraph->setWidth('300');
			$dolgraph->draw('idgraphthirdparties');
			$result .=  $dolgraph->show($total ? 0 : 1);

			$result .=  '</td>';
			$result .=  '</tr>';
		}

		//if ($totalinprocess != $total)
		//{
		//	print '<tr class="liste_total">';
			//	print '<td>'.$langs->trans("Total").' ('.$langs->trans("CustomersOrdersRunning").')</td>';
			//	print '<td class="right">'.$totalinprocess.'</td>';
		//	print '</tr>';
		//}

		$result .=  '<tr class="liste_total">';
		$result .=  '<td>'.$langs->trans("Total").'</td>';
		$result .=  '<td class="right">'.$total.'</td>';
		$result .=  '</tr>';

		$result .=  '</table>';
		$result .=  '</div>';
		$result .=  '<br>';
	} else {
		dol_print_error($db);
	}

	return $result;
}

/**
 * Return a HTML table that contains a pie chart of customer proposals
 *
 * @param	int		$socid		(Optional) Show only results from the customer with this id
 * @return	string				A HTML table that contains a pie chart of customer invoices
 */
function getCustomerFundingPieChart2($socid = 0)
{
	global $conf, $db, $langs, $user;

	$result= '';

	if (empty($conf->propal->enabled) || empty($user->rights->propal->lire)) {
		return '';
	}

	$listofstatus = array(Funding::STATUS_DRAFT, Funding::STATUS_VALIDATED, Funding::STATUS_ACCEPT, Funding::STATUS_DENIED, Funding::STATUS_RUNNING);

	$fundingstatic = new Funding($db);

	$sql = "SELECT count(f.rowid) as nb, f.status as status, f.fk_user_comm, f.origin";
	$sql .= " FROM ".MAIN_DB_PREFIX."funding_funding as f";
	$sql .= " WHERE f.origin = 'order'";
	//$sql .= " AND f.status IN (".$db->sanitize(implode(" ,", $listofstatus)).")";
	$sql .= " GROUP BY f.status";
	$resql = $db->query($sql);
	if ($resql) {
		$num = $db->num_rows($resql);
		$i = 0;
		$total = 0;
		$totalinprocess = 0;
		$dataseries = array();
		$colorseries = array();
		$vals = array();

		while ($i < $num) {
			$obj = $db->fetch_object($resql);
			if ($obj) {
				$vals[$obj->status] = $obj->nb;
				$totalinprocess += $obj->nb;

				$total += $obj->nb;
			}
			$i++;
		}
		$db->free($resql);

		include DOL_DOCUMENT_ROOT.'/theme/'.$conf->theme.'/theme_vars.inc.php';

		$result = '<div class="div-table-responsive-no-min">';
		$result .= '<table class="noborder nohover centpercent">';

		$result .=  '<tr class="liste_titre">';
		$result .=  '<td colspan="2">'.$langs->trans("Statistics").' - '.$langs->trans("Fundings").'</td>';
		$result .=  '</tr>';

		foreach ($listofstatus as $status) {
			$dataseries[] = array($fundingstatic->LibStatut($status, 1), (isset($vals[$status]) ? (int) $vals[$status] : 0));
			if ($status == Funding::STATUS_DRAFT) {
				$colorseries[$status] = '-'.$badgeStatus0;
			}
			if ($status == Funding::STATUS_VALIDATED) {
				$colorseries[$status] = $badgeStatus1;
			}
			if ($status == Funding::STATUS_ACCEPT) {
				$colorseries[$status] = $badgeStatus2;
			}
			if ($status == Funding::STATUS_DENIED) {
				$colorseries[$status] = $badgeStatus3;
			}
			if ($status == Funding::STATUS_RUNNING) {
				$colorseries[$status] = $badgeStatus4;
			}

			if (empty($conf->use_javascript_ajax)) {
				$result .=  '<tr class="oddeven">';
				$result .=  '<td>'.$fundingstatic->LibStatut($status, 0).'</td>';
				$result .=  '<td class="right"><a href="list.php?statut='.$status.'">'.(isset($vals[$status]) ? $vals[$status] : 0).'</a></td>';
				$result .=  "</tr>\n";
			}
		}

		if ($conf->use_javascript_ajax) {
			$result .=  '<tr>';
			$result .=  '<td align="center" colspan="2">';

			include_once DOL_DOCUMENT_ROOT.'/core/class/dolgraph.class.php';
			$dolgraph = new DolGraph();
			$dolgraph->SetData($dataseries);
			$dolgraph->SetDataColor(array_values($colorseries));
			$dolgraph->setShowLegend(2);
			$dolgraph->setShowPercent(1);
			$dolgraph->SetType(array('pie'));
			$dolgraph->setHeight('150');
			$dolgraph->setWidth('300');
			$dolgraph->draw('idgraphthirdparties');
			$result .=  $dolgraph->show($total ? 0 : 1);

			$result .=  '</td>';
			$result .=  '</tr>';
		}

		//if ($totalinprocess != $total)
		//{
		//	print '<tr class="liste_total">';
			//	print '<td>'.$langs->trans("Total").' ('.$langs->trans("CustomersOrdersRunning").')</td>';
			//	print '<td class="right">'.$totalinprocess.'</td>';
		//	print '</tr>';
		//}

		$result .=  '<tr class="liste_total">';
		$result .=  '<td>'.$langs->trans("Total").'</td>';
		$result .=  '<td class="right">'.$total.'</td>';
		$result .=  '</tr>';

		$result .=  '</table>';
		$result .=  '</div>';
		$result .=  '<br>';
	} else {
		dol_print_error($db);
	}

	return $result;
}