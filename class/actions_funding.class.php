<?php
/* Copyright (C) 2017       Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2021-2026	Anthony Berton			<anthony.berton@bb2a.fr>
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
 * \file    funding/class/actions_funding.class.php
 * \ingroup funding
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */

/**
 * Class ActionsFunding
 */
class ActionsFunding
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var array Errors
	 */
	public $errors = array();


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 * Execute action
	 *
	 * @param	array			$parameters		Array of parameters
	 * @param	CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	string			$action      	'add', 'update', 'view'
	 * @return	int         					<0 if KO,
	 *                           				=0 if OK but we want to process standard actions too,
	 *                            				>0 if OK and we want to replace standard actions.
	 */
	public function getNomUrl($parameters, &$object, &$action)
	{
		global $db, $langs, $conf, $user;
		$this->resprints = '';
		return 0;
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter
		// Affichage dans listes Propos, Commandes et Factures
		if (in_array($parameters['currentcontext'], array('propallist', 'orderlist', 'invoicelist'))) {	    // do something only for the context 'somecontext1' or 'somecontext2'
			$parameters['arrayfields']['funding.status'] = array('label'=>'Funding', 'checked'=>1, 'enabled'=>1, 'visible'=>-1, 'position'=>1000 );
		}

		return 0; // or return 1 to replace standard code
	}


	/**
	 * Overloading the doMassActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doMassActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			foreach ($parameters['toselect'] as $objectid) {
				// Do action on each object id
			}
		}

		if (!$error) {
			$this->results = array('myreturn' => 999);
			$this->resprints = 'A text to show';
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}


	/**
	 * Overloading the addMoreMassActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreMassActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter
		$disabled = 1;

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			$this->resprints = '<option value="0"'.($disabled ? ' disabled="disabled"' : '').'>'.$langs->trans("FundingMassAction").'</option>';
		}

		if (!$error) {
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}



	/**
	 * Execute action
	 *
	 * @param	array	$parameters     Array of parameters
	 * @param   Object	$object		   	Object output on PDF
	 * @param   string	$action     	'add', 'update', 'view'
	 * @return  int 		        	<0 if KO,
	 *                          		=0 if OK but we want to process standard actions too,
	 *  	                            >0 if OK and we want to replace standard actions.
	 */
	public function beforePDFCreation($parameters, &$object, &$action)
	{
		global $conf, $user, $langs;
		global $hookmanager;

		$outputlangs = $langs;

		$ret = 0; $deltemp = array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
		}

		return $ret;
	}

	/**
	 * Execute action
	 *
	 * @param	array	$parameters     Array of parameters
	 * @param   Object	$pdfhandler     PDF builder handler
	 * @param   string	$action         'add', 'update', 'view'
	 * @return  int 		            <0 if KO,
	 *                                  =0 if OK but we want to process standard actions too,
	 *                                  >0 if OK and we want to replace standard actions.
	 */
	public function afterPDFCreation($parameters, &$pdfhandler, &$action)
	{
		global $conf, $user, $langs;
		global $hookmanager;

		$outputlangs = $langs;

		$ret = 0; $deltemp = array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {
			// do something only for the context 'somecontext1' or 'somecontext2'
		}

		return $ret;
	}



	/**
	 * Overloading the loadDataForCustomReports function : returns data to complete the customreport tool
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function loadDataForCustomReports($parameters, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$langs->load("funding@funding");

		$this->results = array();

		$head = array();
		$h = 0;

		if ($parameters['tabfamily'] == 'funding') {
			$head[$h][0] = dol_buildpath('/module/index.php', 1);
			$head[$h][1] = $langs->trans("Home");
			$head[$h][2] = 'home';
			$h++;

			$this->results['title'] = $langs->trans("Funding");
			$this->results['picto'] = 'funding@funding';
		}

		$head[$h][0] = 'customreports.php?objecttype='.$parameters['objecttype'].(empty($parameters['tabfamily']) ? '' : '&tabfamily='.$parameters['tabfamily']);
		$head[$h][1] = $langs->trans("CustomReports");
		$head[$h][2] = 'customreports';

		$this->results['head'] = $head;

		return 1;
	}



	/**
	 * Overloading the restrictedArea function : check permission on an object
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int 		      			  	<0 if KO,
	 *                          				=0 if OK but we want to process standard actions too,
	 *  	                            		>0 if OK and we want to replace standard actions.
	 */
	public function restrictedArea($parameters, &$action, $hookmanager)
	{
		global $user;

		if ($parameters['features'] == 'myobject') {
			if ($user->rights->funding->myobject->read) {
				$this->results['result'] = 1;
				return 1;
			} else {
				$this->results['result'] = 0;
				return 1;
			}
		}

		return 0;
	}

	/**
	 * Execute action completeTabsHead
	 *
	 * @param   array           $parameters     Array of parameters
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         'add', 'update', 'view'
	 * @param   Hookmanager     $hookmanager    hookmanager
	 * @return  int                             <0 if KO,
	 *                                          =0 if OK but we want to process standard actions too,
	 *                                          >0 if OK and we want to replace standard actions.
	 */
	/*public function completeTabsHead(&$parameters, &$object, &$action, $hookmanager)
	{
		global $langs, $conf, $user;

		if (!isset($parameters['object']->element)) {
			return 0;
		}
		if ($parameters['mode'] == 'remove') {
			// utilisé si on veut faire disparaitre des onglets.
			return 0;
		} elseif ($parameters['mode'] == 'add') {
			$langs->load('funding@funding');
			// utilisé si on veut ajouter des onglets.
			$counter = count($parameters['head']);
			$element = $parameters['object']->element;
			$id = $parameters['object']->id;
			// verifier le type d'onglet comme member_stats où ça ne doit pas apparaitre
			// if (in_array($element, ['societe', 'member', 'contrat', 'fichinter', 'project', 'propal', 'commande', 'facture', 'order_supplier', 'invoice_supplier'])) {
			if (in_array($element, ['context1', 'context2'])) {
				$datacount = 0;

				$parameters['head'][$counter][0] = dol_buildpath('/funding/funding_list.php?socid=', 1).$parameters['object']->element.'&amp;module='.$element;
				$parameters['head'][$counter][1] = $langs->trans('Fundings');
				if ($datacount > 0) {
					$parameters['head'][$counter][1] .= '<span class="badge marginleftonlyshort">' . $datacount . '</span>';
				}
				$parameters['head'][$counter][2] = 'mymoduleemails';
				$counter++;
			}
			if ($counter > 0 && (int) DOL_VERSION < 14) {
				$this->results = $parameters['head'];
				// return 1 to replace standard code
				return 1;
			} else {
				// en V14 et + $parameters['head'] est modifiable par référence
				return 0;
			}
		}
	}*/

	/**
	 * Overloading the emailElementlist function : add email_template
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @return  int 		      			  	<0 if KO,
	 */
	public function emailElementlist($parameters)
	{
		global $langs, $conf;
		$contexts = explode(':', $parameters['context']);

		$myvalue= img_picto('', 'fa-piggy-bank', 'class="paddingright"').dol_escape_htmltag($langs->trans('Fundings'));
		$this->results = array('funding_send' => $myvalue);


		return 0;
	}

	/**
	 * Overloading the initSendToSocid function : add email_template
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   Object			$object		   	Object output on PDF
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @return  int 		      			  	<0 if KO,
	 *
	 */
	public function initSendToSocid($parameters, &$object, &$action)
	{
		global $langs, $conf;
		$contexts = explode(':', $parameters['context']);

		// $sendtosocid = $object->fk_org;

		//$myvalue = $sendtosocid = $object->fk_org;
		//$object->results = $sendtosocid = $this->fk_org;
		//$reshook = $hookmanager->executeHooks('getFormMail', $parameters, $this);

		return 0;
	}

	/**
	 * Overloading the notifsupported function
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   Object			$object		   	Object output on PDF
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @return  int 		      			  	array,
	 *
	 */
	public function notifsupported($parameters, &$object, &$action)
	{
		global $langs, $conf;
		$contexts = explode(':', $parameters['context']);

		$this->results = array('arrayofnotifsupported' => array('FUNDING_SENTBYMAIL', 'PROPAL_SENTBYMAIL'));

		return 0;
	}

	/**
	 * Overloading the last cli card
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   Object			$object		   	Object output on PDF
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @return  int 		      			  	array,
	 *
	 */
	public function addMoreRecentObjects($parameters, &$object, &$action)
	{
		global $langs, $conf, $db, $user;

		if ($user->rights->funding->lists) {
			$contexts = explode(':', $parameters['context']);

			$result = '';
			$MAXLIST = $conf->global->MAIN_SIZE_SHORTLIST_LIMIT;

			if (!empty($conf->funding->enabled) && $user->rights->funding->read) {
				dol_include_once('/funding/class/funding.class.php');

				$sql = 'SELECT rowid, ref, amount_total, amount_rent, fk_duration, date_end, origin, status';
				$sql .= " FROM ".MAIN_DB_PREFIX."funding_funding as f";
				$sql .= " WHERE f.fk_soc = ".((int) $object->id);
				// Paramettre de ne pas afficher les propositions financiéres
				if (empty($conf->global->FUNDING_LISTE_THIRDPARTY_PROPAL_SHORTLIST)) {
					$sql.= " AND origin <> 'propal'";
				}
				$sql .= " ORDER BY f.date_creation DESC";

				$resql = $db->query($sql);
				if ($resql) {
					$fundingstatic = new Funding($db);

					$num = $db->num_rows($resql);
					if ($num > 0) {
						$result .= '<div class="div-table-responsive-no-min">';
						$result .= '<table class="noborder centpercent lastrecordtable">';

						$result .= '<tr class="liste_titre">';
						$result .= '<td colspan="5"><table width="100%" class="nobordernopadding"><tr><td>'.$langs->trans("LastFunding", ($num <= $MAXLIST ? "" : $MAXLIST)).'</td><td class="right"><a class="notasortlink" href="'.DOL_URL_ROOT.'/custom/funding/funding_list.php?socid='.$object->id.'">'.$langs->trans("AllFunding").'<span class="badge marginleftonlyshort">'.$num.'</span></a></td>';
						$result .= '<td width="20px" class="right"><a href="'.DOL_URL_ROOT.'/custom/funding/fundingindex.php">'.img_picto($langs->trans("Statistics"), 'stats').'</a></td>';
						$result .= '</tr></table></td>';
						$result .= '</tr>';
					}

					$i = 0;
					while ($i < $num && $i < $MAXLIST) {
						$objp = $db->fetch_object($resql);

						$fundingstatic->id = $objp->rowid;
						$fundingstatic->ref = $objp->ref;
						$fundingstatic->amount_total = $objp->amount_total;
						$fundingstatic->amount_rent = $objp->amount_rent;
						$fundingstatic->fk_duration = $objp->fk_duration;
						$fundingstatic->date_end = $objp->date_end;
						$fundingstatic->statut = $objp->status;

						$result .= '<tr class="oddeven">';
						$result .= '<td class="nowraponall">';
						$result .= $fundingstatic->getNomUrl(1);
						$result .= '</td>';
						if (!empty($objp->date_end)) {
							$result .= '<td class="right" width="80px">'.dol_print_date($db->jdate($objp->date_end), 'day').'</td>';
						} else {
							$result .= '<td class="right"><b>!!!</b></td>';
						}
						$result .= '<td class="right" style="min-width: 60px">';
						$result .= price($objp->amount_rent);
						$result .= '</td>';

						/*if (!empty($conf->global->MAIN_SHOW_PRICE_WITH_TAX_IN_SUMMARIES)) {
							$result .= '<td class="right" style="min-width: 60px">';
							$result .= price($objp->total_ttc);
							$result .= '</td>';
						}*/

						$result .= '<td class="nowrap right" style="min-width: 60px">'.$fundingstatic->LibStatut($objp->status, 5).'</td>';
						$result .= "</tr>\n";
						$i++;
					}
					$db->free($resql);

					if ($num > 0) {
						$result .= "</table>";
						$result .= '</div>';
					}
				} else {
					dol_print_error($db);
				}
			}
		}


		$this->resprints = $result;
		//$this->results = array();
		return 0;
	}

	/**
	 * Overloading the notifsupported function
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   Object			$object		   	Object output on PDF
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @return  int 		      			  	array,
	 *
	 */
	public function amountPropalSign($parameters, &$object, &$action)
	{
		global $langs, $conf, $db;
		$contexts = explode(':', $parameters['context']);

		$result = 0;
		$resultprint = '';

		if ($object->mode_reglement_id == $conf->global->FUNDING_ID_REGLEMENT && $parameters['source'] == 'proposal') {
			dol_include_once('/funding/class/funding.class.php');

			$sql = 'SELECT rowid, amount_rent_edit, fk_duration, status';
			$sql .= " FROM ".MAIN_DB_PREFIX."funding_funding as f";
			$sql .= " WHERE f.fk_soc = ".((int) $object->socid);
			$sql.= " AND origin = 'propal'";
			$sql.= " AND origin_id = ".((int) $object->id);
			$resql = $db->query($sql);
			$num = $db->num_rows($resql);
			if ($resql) {
				if ($num > 0) {
					$objp = $db->fetch_object($resql);


					$fundingstatic = new Funding($db);
					$fundingstatic->amount_rent_edit = $objp->amount_rent_edit;
					$fundingstatic->duration = $fundingstatic->fetchDuration($objp->fk_duration);

					// Amount rent
					$resultprint .= '<tr class="CTableRow2"><td class="CTableRow2">'.$langs->trans("Rent");
					$resultprint .= '</td><td class="CTableRow2">';
					$resultprint .= '<b>'.price($fundingstatic->amount_rent_edit, 0, $langs, 1, -1, -1, $conf->currency).'</b>';
					$resultprint .= '</td></tr>'."\n";

					// Duration rent
					$resultprint .= '<tr class="CTableRow2"><td class="CTableRow2">'.$langs->trans("duration");
					$resultprint .= '</td><td class="CTableRow2">';
					$resultprint .= '<b>'.$fundingstatic->duration->label.'</b>';
					$resultprint .= '</td></tr>'."\n";
				}
			} else {
				dol_print_error($db);
			}
			$db->free($resql);
			$result = 1;
		}

		$this->resprints = $resultprint;
		return $result;
	}

	/**
	 * Overloading the completeFieldsToSearchAll function
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   Object			$object		   	Object output on PDF
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @return  int 		      			  	array,
	 *
	 */
	public function completeFieldsToSearchAll($parameters, &$object, &$action)
	{
		global $langs, $conf, $db;
		$contexts = explode(':', $parameters['context']);
		return 0;
	}

	/**
	 * Overloading the printFieldListSelect function
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   Object			$object		   	Object output on PDF
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @return  int 		      			  	array,
	 *
	 */
	public function printFieldListSelect($parameters, &$object, &$action)
	{
		global $langs, $conf, $db;
		$sql = '';
		// Affichage dans listes Propos, Commandes et Factures
		if (in_array($parameters['currentcontext'], array('propallist', 'orderlist', 'invoicelist'))) {
			$sql .= ',funding.rowid as fundrowid, funding.ref as fundref, funding.status as fundstatus';
		}
		$this->resprints = $sql;
		return 0;
	}

	/**
	 * Overloading the printFieldListFrom function
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   Object			$object		   	Object output on PDF
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @return  int 		      			  	array,
	 *
	 */
	public function printFieldListFrom($parameters, &$object, &$action)
	{
		global $langs, $conf, $db;
		$sql = '';
		// Affichage dans listes Propos et Commandes
		if (in_array($parameters['currentcontext'], array('propallist'))) {
			$sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'funding_funding as funding ON (p.rowid = funding.origin_id and funding.origin="propal")';
		}

		if (in_array($parameters['currentcontext'], array('orderlist'))) {
			$sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'funding_funding as funding ON (c.rowid = funding.origin_id and funding.origin="order")';
		}


		$this->resprints = $sql;
		return 0;
	}

	/**
	 * Overloading the printFieldListHaving function
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   Object			$object		   	Object output on PDF
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @return  int 		      			  	array,
	 *
	 */
	public function printFieldListHaving($parameters, &$object, &$action)
	{
		global $langs, $conf, $db;
		$sql = '';
		// Affichage dans listes Propos et Commandes
		if (in_array($parameters['currentcontext'], array('propallist', 'orderlist', 'invoicelist'))) {
		}
		$this->resprints = $sql;
		return 0;
	}

	/**
	 * Overloading the printFieldListSearchParam function
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   Object			$object		   	Object output on PDF
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @return  int 		      			  	array,
	 *
	 */
	public function printFieldListSearchParam($parameters, &$object, &$action)
	{
		global $langs, $conf, $db;
		$param = '';
		$search_funding_status = '';
		// Affichage dans listes Propos et Commandes
		if (in_array($parameters['currentcontext'], array('propallist', 'orderlist', 'invoicelist')) && !empty($search_funding_status)) {
			$parameters['param'] .= '&search_funding_status='.urlencode($search_funding_status);
		}

		$this->resprints = $param;
		return 0;
	}

	/**
	 * Overloading the printFieldPreListTitle function
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   Object			$object		   	Object output on PDF
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @return  int 		      			  	array,
	 *
	 */
	public function printFieldPreListTitle($parameters, &$object, &$action)
	{
		global $langs, $conf, $db;
		$param = '';

		$this->resprints = $param;
		return 0;
	}

	/**
	 * Overloading the printFieldListOption function
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   Object			$object		   	Object output on PDF
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @return  int 		      			  	array,
	 *
	 */
	public function printFieldListOption($parameters, &$object, &$action)
	{
		global $langs, $conf, $db;
		$moreforfilter = '';
		// Affichage dans listes Propos et Commandes
		if (in_array($parameters['currentcontext'], array('propallist', 'orderlist', 'invoicelist')) && !empty($parameters['arrayfields']['funding.status']['checked'])) {
			$moreforfilter .= '<td class="liste_titre">';
			$moreforfilter .= '</td>';
		}

		$this->resprints = $moreforfilter;
		return 0;
	}

	/**
	 * Overloading the printFieldListTitl function
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   Object			$object		   	Object output on PDF
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @return  int 		      			  	array,
	 *
	 */
	public function printFieldListTitle($parameters, &$object, &$action)
	{
		global $langs, $conf, $db;
		$result = '';
		// Affichage dans listes Propos et Commandes
		if (in_array($parameters['currentcontext'], array('propallist', 'orderlist', 'invoicelist')) && !empty($parameters['arrayfields']['funding.status']['checked'])) {
			$result = print_liste_field_titre('Funding', $_SERVER['PHP_SELF'], '', '', $parameters['param'], 'class="center"', $parameters['sortfield'], $parameters['sortorder']);
			$parameters['totalarray']['nbfield']++;
		}

		$this->resprints = $result;
		return 0;
	}

	/**
	 * Overloading the printFieldListValue function
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   Object			$object		   	Object output on PDF
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @return  int 		      			  	array,
	 *
	 */
	public function printFieldListValue($parameters, &$object, &$action)
	{
		global $langs, $conf, $db, $object;
		$result = '';
		$value = img_picto('uncheck', 'uncheck');
		// Affichage dans listes Propos et Commandes
		if (in_array($parameters['currentcontext'], array('propallist', 'orderlist', 'invoicelist')) && !empty($parameters['arrayfields']['funding.status']['checked'])) {
			dol_include_once('/funding/class/funding.class.php');
			$funding = new funding($db);
			if (isset($parameters['obj']->fundstatus)) {
				$funding->fetch($parameters['obj']->fundrowid);
				$value = $funding->LibStatut($parameters['obj']->fundstatus, 3).' '.$funding->getNomUrl(1);
			}
			// $funding->fetch($parameters['obj']->fundstatus);
			$result .= '<td align="center" class="nowrap">';
			$result .=  $value;
			$result .=  '</td>';
			if (!$parameters['i']) {
				$parameters['totalarray']['nbfield']++;
			}
		}
		$this->resprints = $result;
		return 0;
	}

	/* Add here any other hooked methods... */
}
