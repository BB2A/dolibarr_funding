<?php
/* Copyright (C) 2001-2005 	Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 	Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 	Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      	Jean-François Ferry	<jfefe@aternatik.fr>
 * Copyright (C) 2020 		BERTON Anthony 			<bertonanthony@gmail.com>
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
 *	\file       funding/fundingindex.php
 *	\ingroup    funding
 *	\brief      Home page of funding top menu
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) { $i--; $j--; }
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) $res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) $res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) $res = @include "../main.inc.php";
if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (!$res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

dol_include_once('/funding/class/funding.class.php');
dol_include_once('/funding/lib/funding.lib.php');

// Load translation files required by the page
$langs->loadLangs(array("funding@funding"));

$action = GETPOST('action', 'alpha');


// Security check
//if (! $user->rights->funding->myobject->read) accessforbidden();
$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0) {
	$action = '';
	$socid = $user->socid;
}

$max = 5;
$now = dol_now();

$permissiontoread = $user->rights->funding->read;
$permissiontoadd = $user->rights->funding->write;
$permissiontodelete = $user->rights->funding->delete;
$permissionmanage = $user->rights->funding->manage; //User by the function send_mail_org

/*
 * Actions
 */

// None


/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);
$funding = new Funding($db);
$companystatic = new Societe($db);

llxHeader("", $langs->trans("Funding"));

print load_fiche_titre($langs->trans("Funding"), '', 'index_funding@funding');

print '<div class="fichecenter"><div class="fichethirdleft">';

//print getCustomerFundingPieChart($socid); //Affichage du graph
print '<br>';


// New Funding
if (!empty($conf->funding->enabled) && $permissiontoread) {
	$sql = "SELECT f.rowid, f.ref, f.status, f.amount_rent_edit, f.fk_soc, f.fk_soc_invoice, f.fk_org, f.fk_user_comm, f.fk_user_creat, f.fk_user_modif";
	$sql .= ", s.rowid as socid, s.nom as name, s.client, s.canvas, s.code_client, s.email, s.entity, s.code_compta";
	$sql.= " FROM ".MAIN_DB_PREFIX."funding_funding as f";
	$sql .= ", ".MAIN_DB_PREFIX."societe as s";
	$sql.= " WHERE f.status = 1";
	$sql.= " AND f.origin = 'order'";
	if ($socid > 0) {
		$sql.= " AND f.fk_soc = ".$socid." OR f.fk_soc_invoice = ".$socid." OR f.fk_org = ".$socid;
	}
	if (!$permissionmanage || empty($user->rights->societe->client->voir)) {
		$sql .= " AND f.fk_user_comm = ".$user->id;
	}
	$sql .= " ORDER BY f.ref DESC";
	$resql = $db->query($sql);

	if ($resql) {
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="4">'.$langs->trans("FundindBoxValidate").($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th></tr>';

		$var = true;
		if ($num > 0) {
			$i = 0;
			while ($i < $num and $i <> 10) {
				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven"><td class="nowrap tdoverflowmax100">';
				$funding->id=$obj->rowid;
				$funding->ref=$obj->ref;
				$funding->status=$obj->status;
				print $funding->getNomUrl(1);
				print '</td>';
				$companystatic->id=$obj->fk_soc;
				$companystatic->id = $obj->socid;
				$companystatic->name = $obj->name;
				$companystatic->client = $obj->client;
				$companystatic->code_client = $obj->code_client;
				$companystatic->code_fournisseur = $obj->code_fournisseur;
				$companystatic->canvas = $obj->canvas;
				$companystatic->entity = $obj->entity;
				$companystatic->email = $obj->email;
				print '<td class="nowrap tdoverflowmax100">'.$companystatic->getNomUrl(1).'</td>';
				print '<td class="right" class="nowrap"><span class="amount">'.price($obj->amount_rent_edit).'</span></td>';
				print '<td align="center" width="14">'.$funding->getLibStatut(3).'</td></tr>';
				$i++;
				$total += $obj->amount_rent_edit;//$obj->total_ttc;
			}
			if ($total>0) {
				print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price($total)."</td><td></td></tr>";
			}
		} else {
			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("NoFunding").'</td><td></td></tr>';
			print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price(0)."</td><td></td></tr>";
		}
		print "</table><br>";
		$db->free($resql);
	} else {
		dol_print_error($db);
	}
}


print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';


$NBMAX = 3;
$max = 3;

//print getCustomerFundingPieChart2($socid);  //Affichage du graph
print '<br>';

if (! empty($conf->funding->enabled) && $permissiontoread) {
	// Tableau bis
	$sql = "SELECT f.rowid, f.ref, f.status, f.amount_rent_edit, f.fk_soc, f.fk_soc_invoice, f.fk_org, f.fk_user_comm, f.fk_user_creat, f.fk_user_modif";
	$sql .= ", s.rowid as socid, s.nom as name, s.client, s.canvas, s.code_client, s.email, s.entity, s.code_compta";
	$sql.= " FROM ".MAIN_DB_PREFIX."funding_funding as f";
	$sql .= ", ".MAIN_DB_PREFIX."societe as s";
	$sql.= " WHERE f.status = 1";
	$sql.= " AND f.origin = 'propal'";
	if ($socid > 0) {
		$sql.= " AND f.fk_soc = ".$socid." OR f.fk_soc_invoice = ".$socid." OR f.fk_org = ".$socid;
	}
	if (!$permissionmanage || empty($user->rights->societe->client->voir)) {
		$sql .= " AND f.fk_user_comm = ". $user->id;
	}
	$sql .= " ORDER BY f.ref DESC";
	$resql = $db->query($sql);

	if ($resql) {
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="4">'.$langs->trans("FundindBoxValidatePropal").($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th></tr>';

		$var = true;
		if ($num > 0) {
			$i = 0;
			while ($i < $num and $i <> 10) {
				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven"><td class="nowrap tdoverflowmax100">';
				$funding->id=$obj->rowid;
				$funding->ref=$obj->ref;
				$funding->status=$obj->status;
				print $funding->getNomUrl(1);
				print '</td>';
				$companystatic->id=$obj->fk_soc;
				$companystatic->id = $obj->socid;
				$companystatic->name = $obj->name;
				$companystatic->client = $obj->client;
				$companystatic->code_client = $obj->code_client;
				$companystatic->code_fournisseur = $obj->code_fournisseur;
				$companystatic->canvas = $obj->canvas;
				$companystatic->entity = $obj->entity;
				$companystatic->email = $obj->email;
				print '<td class="nowrap tdoverflowmax100">'.$companystatic->getNomUrl(1).'</td>';
				print '<td class="right" class="nowrap"><span class="amount">'.price($obj->amount_rent_edit).'</span></td>';
				print '<td align="center" width="14">'.$funding->getLibStatut(3).'</td></tr>';
				$i++;
				$total += $obj->amount_rent_edit;//$obj->total_ttc;
			}
			if ($total>0) {
				print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price($total)."</td><td></td></tr>";
			}
		} else {
			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("NoFunding").'</td><td></td></tr>';
			print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price(0)."</td><td></td></tr>";
		}
		print "</table><br>";
		$db->free($resql);
	} else {
		dol_print_error($db);
	}
}

print '</div></div></div>';

print '<div class="fichecenter"><div class="fichethirdleft">';

print '<br>';

// Financement mise à jour
if (!empty($conf->funding->enabled) && $permissiontoread) {
	$sql = "SELECT f.rowid, f.ref, f.status, f.amount_rent_edit, f.fk_soc, f.fk_soc_invoice, f.fk_org, f.fk_user_comm, f.fk_user_creat, f.fk_user_modif";
	$sql .= ", s.rowid as socid, s.nom as name, s.client, s.canvas, s.code_client, s.email, s.entity, s.code_compta";
	$sql.= " FROM ".MAIN_DB_PREFIX."funding_funding as f";
	$sql .= ", ".MAIN_DB_PREFIX."societe as s";
	$sql.= " WHERE f.status = 2";
	$sql.= " AND f.origin = 'order'";
	if ($socid > 0) {
		$sql.= " AND f.fk_soc = ".$socid." OR f.fk_soc_invoice = ".$socid." OR f.fk_org = ".$socid;
	}
	if (!$permissionmanage || empty($user->rights->societe->client->voir)) {
		$sql .= " AND f.fk_user_comm = ". $user->id;
	}
	$sql .= " ORDER BY f.ref DESC";
	$resql = $db->query($sql);

	if ($resql) {
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="4">'.$langs->trans("FundindBoxUpdate").($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th></tr>';

		$var = true;
		if ($num > 0) {
			$i = 0;
			while ($i < $num and $i <> 10) {
				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven"><td class="nowrap tdoverflowmax100">';
				$funding->id=$obj->rowid;
				$funding->ref=$obj->ref;
				$funding->status=$obj->status;
				print $funding->getNomUrl(1);
				print '</td>';
				$companystatic->id=$obj->fk_soc;
				$companystatic->id = $obj->socid;
				$companystatic->name = $obj->name;
				$companystatic->client = $obj->client;
				$companystatic->code_client = $obj->code_client;
				$companystatic->code_fournisseur = $obj->code_fournisseur;
				$companystatic->canvas = $obj->canvas;
				$companystatic->entity = $obj->entity;
				$companystatic->email = $obj->email;
				print '<td class="nowrap tdoverflowmax100">'.$companystatic->getNomUrl(1).'</td>';
				print '<td class="right" class="nowrap"><span class="amount">'.price($obj->amount_rent_edit).'</span></td>';
				print '<td align="center" width="14">'.$funding->getLibStatut(3).'</td></tr>';
				$i++;
				$total += $obj->amount_rent_edit;//$obj->total_ttc;
			}
			if ($total>0) {
				print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price($total)."</td><td></td></tr>";
			}
		} else {
			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("NoFunding").'</td><td></td></tr>';
			print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price(0)."</td><td></td></tr>";
		}
		print "</table><br>";
		$db->free($resql);
	} else {
		dol_print_error($db);
	}
}


print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';


$NBMAX = 3;
$max = 3;


print '<br>';

if (! empty($conf->funding->enabled) && $permissiontoread) {
	// Tableau bis
	$sql = "SELECT f.rowid, f.ref, f.status, f.amount_rent_edit, f.fk_soc, f.fk_soc_invoice, f.fk_org, f.fk_user_comm, f.fk_user_creat, f.fk_user_modif";
	$sql .= ", s.rowid as socid, s.nom as name, s.client, s.canvas, s.code_client, s.email, s.entity, s.code_compta";
	$sql.= " FROM ".MAIN_DB_PREFIX."funding_funding as f";
	$sql .= ", ".MAIN_DB_PREFIX."societe as s";
	$sql.= " WHERE f.status = 2";
	$sql.= " AND f.origin = 'propal'";
	if ($socid > 0) {
		$sql.= " AND f.fk_soc = ".$socid." OR f.fk_soc_invoice = ".$socid." OR f.fk_org = ".$socid;
	}
	if (!$permissionmanage || empty($user->rights->societe->client->voir)) {
		$sql .= " AND f.fk_user_comm = ". $user->id;
	}
	$sql .= " ORDER BY f.ref DESC";
	$resql = $db->query($sql);

	if ($resql) {
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="4">'.$langs->trans("FundindBoxUpdatePropal").($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th></tr>';

		$var = true;
		if ($num > 0) {
			$i = 0;
			while ($i < $num and $i <> 10) {
				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven"><td class="nowrap tdoverflowmax100">';
				$funding->id=$obj->rowid;
				$funding->ref=$obj->ref;
				$funding->status=$obj->status;
				print $funding->getNomUrl(1);
				print '</td>';
				$companystatic->id=$obj->fk_soc;
				$companystatic->id = $obj->socid;
				$companystatic->name = $obj->name;
				$companystatic->client = $obj->client;
				$companystatic->code_client = $obj->code_client;
				$companystatic->code_fournisseur = $obj->code_fournisseur;
				$companystatic->canvas = $obj->canvas;
				$companystatic->entity = $obj->entity;
				$companystatic->email = $obj->email;
				print '<td class="nowrap tdoverflowmax100">'.$companystatic->getNomUrl(1).'</td>';
				print '<td class="right" class="nowrap"><span class="amount">'.price($obj->amount_rent_edit).'</span></td>';
				print '<td align="center" width="14">'.$funding->getLibStatut(3).'</td></tr>';
				$i++;
				$total += $obj->amount_rent_edit;//$obj->total_ttc;
			}
			if ($total>0) {
				print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price($total)."</td><td></td></tr>";
			}
		} else {
			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("NoFunding").'</td><td></td></tr>';
			print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price(0)."</td><td></td></tr>";
		}
		print "</table><br>";
		$db->free($resql);
	} else {
		dol_print_error($db);
	}
}

print '</div></div></div>';

print '<div class="fichecenter"><div class="fichethirdleft">';

print '<br>';

// Financements accepté
if (!empty($conf->funding->enabled) && $permissiontoread) {
	$sql = "SELECT f.rowid, f.ref, f.status, f.amount_rent_edit, f.fk_soc, f.fk_soc_invoice, f.fk_org, f.fk_user_comm, f.fk_user_creat, f.fk_user_modif";
	$sql .= ", s.rowid as socid, s.nom as name, s.client, s.canvas, s.code_client, s.email, s.entity, s.code_compta";
	$sql.= " FROM ".MAIN_DB_PREFIX."funding_funding as f";
	$sql .= ", ".MAIN_DB_PREFIX."societe as s";
	$sql.= " WHERE f.status = 4";
	$sql.= " AND f.origin = 'order'";
	if ($socid > 0) {
		$sql.= " AND f.fk_soc = ".$socid." OR f.fk_soc_invoice = ".$socid." OR f.fk_org = ".$socid;
	}
	if (!$permissionmanage || empty($user->rights->societe->client->voir)) {
		$sql .= " AND f.fk_user_comm = ". $user->id;
	}
	$sql .= " ORDER BY f.ref DESC";
	$resql = $db->query($sql);

	if ($resql) {
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="4">'.$langs->trans("FundindBoxAccept").($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th></tr>';

		$var = true;
		if ($num > 0) {
			$i = 0;
			while ($i < $num and $i <> 10) {
				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven"><td class="nowrap tdoverflowmax100">';
				$funding->id=$obj->rowid;
				$funding->ref=$obj->ref;
				$funding->status=$obj->status;
				print $funding->getNomUrl(1);
				print '</td>';
				$companystatic->id=$obj->fk_soc;
				$companystatic->id = $obj->socid;
				$companystatic->name = $obj->name;
				$companystatic->client = $obj->client;
				$companystatic->code_client = $obj->code_client;
				$companystatic->code_fournisseur = $obj->code_fournisseur;
				$companystatic->canvas = $obj->canvas;
				$companystatic->entity = $obj->entity;
				$companystatic->email = $obj->email;
				print '<td class="nowrap tdoverflowmax100">'.$companystatic->getNomUrl(1).'</td>';
				print '<td class="right" class="nowrap"><span class="amount">'.price($obj->amount_rent_edit).'</span></td>';
				print '<td align="center" width="14">'.$funding->getLibStatut(3).'</td></tr>';
				$i++;
				$total += $obj->amount_rent_edit;//$obj->total_ttc;
			}
			if ($total>0) {
				print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price($total)."</td><td></td></tr>";
			}
		} else {
			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("NoFunding").'</td><td></td></tr>';
			print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price(0)."</td><td></td></tr>";
		}
		print "</table><br>";
		$db->free($resql);
	} else {
		dol_print_error($db);
	}
}

print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';


$NBMAX = 3;
$max = 3;


print '<br>';

if (! empty($conf->funding->enabled) && $permissiontoread) {
	// Tableau bis
	$sql = "SELECT f.rowid, f.ref, f.status, f.amount_rent_edit, f.fk_soc, f.fk_soc_invoice, f.fk_org, f.fk_user_comm, f.fk_user_creat, f.fk_user_modif";
	$sql .= ", s.rowid as socid, s.nom as name, s.client, s.canvas, s.code_client, s.email, s.entity, s.code_compta";
	$sql.= " FROM ".MAIN_DB_PREFIX."funding_funding as f";
	$sql .= ", ".MAIN_DB_PREFIX."societe as s";
	$sql.= " WHERE f.status = 4";
	$sql.= " AND f.origin = 'propal'";
	if ($socid > 0) {
		$sql.= " AND f.fk_soc = ".$socid." OR f.fk_soc_invoice = ".$socid." OR f.fk_org = ".$socid;
	}
	if (!$permissionmanage || empty($user->rights->societe->client->voir)) {
		$sql .= " AND f.fk_user_comm = ". $user->id;
	}
	$sql .= " ORDER BY f.ref DESC";
	$resql = $db->query($sql);

	if ($resql) {
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="4">'.$langs->trans("FundindBoxAcceptPropal").($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th></tr>';

		$var = true;
		if ($num > 0) {
			$i = 0;
			while ($i < $num and $i <> 10) {
				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven"><td class="nowrap tdoverflowmax100">';
				$funding->id=$obj->rowid;
				$funding->ref=$obj->ref;
				$funding->status=$obj->status;
				print $funding->getNomUrl(1);
				print '</td>';
				$companystatic->id=$obj->fk_soc;
				$companystatic->id = $obj->socid;
				$companystatic->name = $obj->name;
				$companystatic->client = $obj->client;
				$companystatic->code_client = $obj->code_client;
				$companystatic->code_fournisseur = $obj->code_fournisseur;
				$companystatic->canvas = $obj->canvas;
				$companystatic->entity = $obj->entity;
				$companystatic->email = $obj->email;
				print '<td class="nowrap tdoverflowmax100">'.$companystatic->getNomUrl(1).'</td>';
				print '<td class="right" class="nowrap"><span class="amount">'.price($obj->amount_rent_edit).'</span></td>';
				print '<td align="center" width="14">'.$funding->getLibStatut(3).'</td></tr>';
				$i++;
				$total += $obj->amount_rent_edit;//$obj->total_ttc;
			}
			if ($total>0) {
				print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price($total)."</td><td></td></tr>";
			}
		} else {
			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("NoFunding").'</td><td></td></tr>';
			print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price(0)."</td><td></td></tr>";
		}
		print "</table><br>";
		$db->free($resql);
	} else {
		dol_print_error($db);
	}
}

print '</div></div></div>';

print '<div class="fichecenter"><div class="fichethirdleft">';

print '<br>';

// Financement refusé
if (!empty($conf->funding->enabled) && $permissiontoread) {
	$sql = "SELECT f.rowid, f.ref, f.status, f.amount_rent_edit, f.fk_soc, f.fk_soc_invoice, f.fk_org, f.fk_user_comm, f.fk_user_creat, f.fk_user_modif";
	$sql .= ", s.rowid as socid, s.nom as name, s.client, s.canvas, s.code_client, s.email, s.entity, s.code_compta";
	$sql.= " FROM ".MAIN_DB_PREFIX."funding_funding as f";
	$sql .= ", ".MAIN_DB_PREFIX."societe as s";
	$sql.= " WHERE f.status = 5";
	$sql.= " AND f.origin = 'order'";
	if ($socid > 0) {
		$sql.= " AND f.fk_soc = ".$socid." OR f.fk_soc_invoice = ".$socid." OR f.fk_org = ".$socid;
	}
	if (!$permissionmanage || empty($user->rights->societe->client->voir)) {
		$sql .= " AND f.fk_user_comm = ". $user->id;
	}
	$sql .= " ORDER BY f.ref DESC";
	$resql = $db->query($sql);

	if ($resql) {
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="4">'.$langs->trans("FundindBoxDenied").($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th></tr>';

		$var = true;
		if ($num > 0) {
			$i = 0;
			while ($i < $num and $i <> 10) {
				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven"><td class="nowrap tdoverflowmax100">';
				$funding->id=$obj->rowid;
				$funding->ref=$obj->ref;
				$funding->status=$obj->status;
				print $funding->getNomUrl(1);
				print '</td>';
				$companystatic->id=$obj->fk_soc;
				$companystatic->id = $obj->socid;
				$companystatic->name = $obj->name;
				$companystatic->client = $obj->client;
				$companystatic->code_client = $obj->code_client;
				$companystatic->code_fournisseur = $obj->code_fournisseur;
				$companystatic->canvas = $obj->canvas;
				$companystatic->entity = $obj->entity;
				$companystatic->email = $obj->email;
				print '<td class="nowrap tdoverflowmax100">'.$companystatic->getNomUrl(1).'</td>';
				print '<td class="right" class="nowrap"><span class="amount">'.price($obj->amount_rent_edit).'</span></td>';
				print '<td align="center" width="14">'.$funding->getLibStatut(3).'</td></tr>';
				$i++;
				$total += $obj->amount_rent_edit;//$obj->total_ttc;
			}
			if ($total>0) {
				print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price($total)."</td><td></td></tr>";
			}
		} else {
			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("NoFunding").'</td><td></td></tr>';
			print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price(0)."</td><td></td></tr>";
		}
		print "</table><br>";
		$db->free($resql);
	} else {
		dol_print_error($db);
	}
}

print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';


$NBMAX = 3;
$max = 3;


print '<br>';

if (! empty($conf->funding->enabled) && $permissiontoread) {
	// Tableau bis
	$sql = "SELECT f.rowid, f.ref, f.status, f.amount_rent_edit, f.fk_soc, f.fk_soc_invoice, f.fk_org, f.fk_user_comm, f.fk_user_creat, f.fk_user_modif";
	$sql .= ", s.rowid as socid, s.nom as name, s.client, s.canvas, s.code_client, s.email, s.entity, s.code_compta";
	$sql.= " FROM ".MAIN_DB_PREFIX."funding_funding as f";
	$sql .= ", ".MAIN_DB_PREFIX."societe as s";
	$sql.= " WHERE f.status = 5";
	$sql.= " AND f.origin = 'propal'";
	if ($socid > 0) {
		$sql.= " AND f.fk_soc = ".$socid." OR f.fk_soc_invoice = ".$socid." OR f.fk_org = ".$socid;
	}
	if (!$permissionmanage || empty($user->rights->societe->client->voir)) {
		$sql .= " AND f.fk_user_comm = ". $user->id;
	}
	$sql .= " ORDER BY f.ref DESC";
	$resql = $db->query($sql);

	if ($resql) {
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="4">'.$langs->trans("FundindBoxDeniedPropal").($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th></tr>';

		$var = true;
		if ($num > 0) {
			$i = 0;
			while ($i < $num and $i <> 10) {
				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven"><td class="nowrap tdoverflowmax100">';
				$funding->id=$obj->rowid;
				$funding->ref=$obj->ref;
				$funding->status=$obj->status;
				print $funding->getNomUrl(1);
				print '</td>';
				$companystatic->id=$obj->fk_soc;
				$companystatic->id = $obj->socid;
				$companystatic->name = $obj->name;
				$companystatic->client = $obj->client;
				$companystatic->code_client = $obj->code_client;
				$companystatic->code_fournisseur = $obj->code_fournisseur;
				$companystatic->canvas = $obj->canvas;
				$companystatic->entity = $obj->entity;
				$companystatic->email = $obj->email;
				print '<td class="nowrap tdoverflowmax100">'.$companystatic->getNomUrl(1).'</td>';
				print '<td class="right" class="nowrap"><span class="amount">'.price($obj->amount_rent_edit).'</span></td>';
				print '<td align="center" width="14">'.$funding->getLibStatut(3).'</td></tr>';
				$i++;
				$total += $obj->amount_rent_edit;//$obj->total_ttc;
			}
			if ($total>0) {
				print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price($total)."</td><td></td></tr>";
			}
		} else {
			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("NoFunding").'</td><td></td></tr>';
			print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price(0)."</td><td></td></tr>";
		}
		print "</table><br>";
		$db->free($resql);
	} else {
		dol_print_error($db);
	}
}

print '</div></div></div>';

print '<div class="fichecenter"><div class="fichethirdleft">';

print '<br>';

// Financement Running
if (!empty($conf->funding->enabled) && $permissiontoread) {
	$sql = "SELECT f.rowid, f.ref, f.status, f.amount_rent_edit, f.fk_soc, f.fk_soc_invoice, f.fk_org, f.fk_user_comm, f.fk_user_creat, f.fk_user_modif";
	$sql .= ", s.rowid as socid, s.nom as name, s.client, s.canvas, s.code_client, s.email, s.entity, s.code_compta";
	$sql.= " FROM ".MAIN_DB_PREFIX."funding_funding as f";
	$sql .= ", ".MAIN_DB_PREFIX."societe as s";
	$sql.= " WHERE f.status = 6";
	$sql.= " AND f.origin = 'order'";
	if ($socid > 0) {
		$sql.= " AND f.fk_soc = ".$socid." OR f.fk_soc_invoice = ".$socid." OR f.fk_org = ".$socid;
	}
	if (!$permissionmanage || empty($user->rights->societe->client->voir)) {
		$sql .= " AND f.fk_user_comm = ". $user->id;
	}
	$sql .= " ORDER BY f.ref DESC";
	$resql = $db->query($sql);

	if ($resql) {
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="4">'.$langs->trans("FundindBoxRunning").($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th></tr>';

		$var = true;
		if ($num > 0) {
			$i = 0;
			while ($i < $num and $i <> 10) {
				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven"><td class="nowrap tdoverflowmax100">';
				$funding->id=$obj->rowid;
				$funding->ref=$obj->ref;
				$funding->status=$obj->status;
				print $funding->getNomUrl(1);
				print '</td>';
				$companystatic->id=$obj->fk_soc;
				$companystatic->id = $obj->socid;
				$companystatic->name = $obj->name;
				$companystatic->client = $obj->client;
				$companystatic->code_client = $obj->code_client;
				$companystatic->code_fournisseur = $obj->code_fournisseur;
				$companystatic->canvas = $obj->canvas;
				$companystatic->entity = $obj->entity;
				$companystatic->email = $obj->email;
				print '<td class="nowrap tdoverflowmax100">'.$companystatic->getNomUrl(1).'</td>';
				print '<td class="right" class="nowrap"><span class="amount">'.price($obj->amount_rent_edit).'</span></td>';
				print '<td align="center" width="14">'.$funding->getLibStatut(3).'</td></tr>';
				$i++;
				$total += $obj->amount_rent_edit;//$obj->total_ttc;
			}
			if ($total>0) {
				print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price($total)."</td><td></td></tr>";
			}
		} else {
			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("NoFunding").'</td><td></td></tr>';
			print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price(0)."</td><td></td></tr>";
		}
		print "</table><br>";
		$db->free($resql);
	} else {
		dol_print_error($db);
	}
}

print '</div><div class="fichetwothirdright"><div class="ficheaddleft">';


$NBMAX = 3;
$max = 3;


print '<br>';

// Financement Rend
if (! empty($conf->funding->enabled) && $permissiontoread) {
	// Tableau bis
	$sql = "SELECT f.rowid, f.ref, f.status, f.amount_rent_edit, f.fk_soc, f.fk_soc_invoice, f.fk_org, f.fk_user_comm, f.fk_user_creat, f.fk_user_modif";
	$sql .= ", s.rowid as socid, s.nom as name, s.client, s.canvas, s.code_client, s.email, s.entity, s.code_compta";
	$sql.= " FROM ".MAIN_DB_PREFIX."funding_funding as f";
	$sql .= ", ".MAIN_DB_PREFIX."societe as s";
	$sql.= " WHERE f.status = 7";
	$sql.= " AND f.origin = 'order'";
	if ($socid > 0) {
		$sql.= " AND f.fk_soc = ".$socid." OR f.fk_soc_invoice = ".$socid." OR f.fk_org = ".$socid;
	}
	if (!$permissionmanage || empty($user->rights->societe->client->voir)) {
		$sql .= " AND f.fk_user_comm = ". $user->id;
	}
	$sql .= " ORDER BY f.ref DESC";
	$resql = $db->query($sql);

	if ($resql) {
		$total = 0;
		$num = $db->num_rows($resql);

		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre">';
		print '<th colspan="4">'.$langs->trans("FundindBoxEnd").($num?'<span class="badge marginleftonlyshort">'.$num.'</span>':'').'</th></tr>';

		$var = true;
		if ($num > 0) {
			$i = 0;
			while ($i < $num and $i <> 10) {
				$obj = $db->fetch_object($resql);
				print '<tr class="oddeven"><td class="nowrap tdoverflowmax100">';
				$funding->id=$obj->rowid;
				$funding->ref=$obj->ref;
				$funding->status=$obj->status;
				print $funding->getNomUrl(1);
				print '</td>';
				$companystatic->id=$obj->fk_soc;
				$companystatic->id = $obj->socid;
				$companystatic->name = $obj->name;
				$companystatic->client = $obj->client;
				$companystatic->code_client = $obj->code_client;
				$companystatic->code_fournisseur = $obj->code_fournisseur;
				$companystatic->canvas = $obj->canvas;
				$companystatic->entity = $obj->entity;
				$companystatic->email = $obj->email;
				print '<td class="nowrap tdoverflowmax100">'.$companystatic->getNomUrl(1).'</td>';
				print '<td class="right" class="nowrap"><span class="amount">'.price($obj->amount_rent_edit).'</span></td>';
				print '<td align="center" width="14">'.$funding->getLibStatut(3).'</td></tr>';
				$i++;
				$total += $obj->amount_rent_edit;//$obj->total_ttc;
			}
			if ($total>0) {
				print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price($total)."</td><td></td></tr>";
			}
		} else {
			print '<tr class="oddeven"><td colspan="3" class="opacitymedium">'.$langs->trans("NoFunding").'</td><td></td></tr>';
			print '<tr class="liste_total"><td>'.$langs->trans("Total").'</td><td colspan="2" class="right">'.price(0)."</td><td></td></tr>";
		}
		print "</table><br>";
		$db->free($resql);
	} else {
		dol_print_error($db);
	}
}

print '</div></div></div>';

// End of page
llxFooter();
$db->close();
