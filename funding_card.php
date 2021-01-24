<?php
/* Copyright (C) 2017 		Laurent Destailleur  	<eldy@users.sourceforge.net>
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
 *   	\file       funding_card.php
 *		\ingroup    funding
 *		\brief      Page to create/edit/view funding
 */

//if (! defined('NOREQUIREDB'))              define('NOREQUIREDB','1');					// Do not create database handler $db
//if (! defined('NOREQUIREUSER'))            define('NOREQUIREUSER','1');				// Do not load object $user
//if (! defined('NOREQUIRESOC'))             define('NOREQUIRESOC','1');				// Do not load object $mysoc
//if (! defined('NOREQUIRETRAN'))            define('NOREQUIRETRAN','1');				// Do not load object $langs
//if (! defined('NOSCANGETFORINJECTION'))    define('NOSCANGETFORINJECTION','1');		// Do not check injection attack on GET parameters
//if (! defined('NOSCANPOSTFORINJECTION'))   define('NOSCANPOSTFORINJECTION','1');		// Do not check injection attack on POST parameters
//if (! defined('NOCSRFCHECK'))              define('NOCSRFCHECK','1');					// Do not check CSRF attack (test on referer + on token if option MAIN_SECURITY_CSRF_WITH_TOKEN is on).
//if (! defined('NOTOKENRENEWAL'))           define('NOTOKENRENEWAL','1');				// Do not roll the Anti CSRF token (used if MAIN_SECURITY_CSRF_WITH_TOKEN is on)
//if (! defined('NOSTYLECHECK'))             define('NOSTYLECHECK','1');				// Do not check style html tag into posted data
//if (! defined('NOREQUIREMENU'))            define('NOREQUIREMENU','1');				// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))            define('NOREQUIREHTML','1');				// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))            define('NOREQUIREAJAX','1');       	  	// Do not load ajax.lib.php library
//if (! defined("NOLOGIN"))                  define("NOLOGIN",'1');						// If this page is public (can be called outside logged session). This include the NOIPCHECK too.
//if (! defined('NOIPCHECK'))                define('NOIPCHECK','1');					// Do not check IP defined into conf $dolibarr_main_restrict_ip
//if (! defined("MAIN_LANG_DEFAULT"))        define('MAIN_LANG_DEFAULT','auto');					// Force lang to a particular value
//if (! defined("MAIN_AUTHENTICATION_MODE")) define('MAIN_AUTHENTICATION_MODE','aloginmodule');		// Force authentication handler
//if (! defined("NOREDIRECTBYMAINTOLOGIN"))  define('NOREDIRECTBYMAINTOLOGIN',1);		// The main.inc.php does not make a redirect if not logged, instead show simple error message
//if (! defined("FORCECSP"))                 define('FORCECSP','none');					// Disable all Content Security Policies


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

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';

// BB2A Fichiers joints //Code isssue de funding_docuement.php
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
dol_include_once('/funding/class/funding.class.php');
dol_include_once('/funding/lib/funding_funding.lib.php');

// for other modules
require_once DOL_DOCUMENT_ROOT.'/core/lib/propal.lib.php';
require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

// Load translation files required by the page
$langs->loadLangs(array("funding@funding", "Propal", "Orders", "other"));

// Get parameters
$id 					= GETPOST('id', 'int');
$ref        			= GETPOST('ref', 'alpha');
$action 				= GETPOST('action', 'aZ09');
$confirm    			= GETPOST('confirm', 'alpha');
$cancel     			= GETPOST('cancel', 'aZ09');
$contextpage 			= GETPOST('contextpage', 'aZ') ?GETPOST('contextpage', 'aZ') : 'fundingcard'; // To manage different context of search
$backtopage 			= GETPOST('backtopage', 'alpha');
$backtopageforcancel 	= GETPOST('backtopageforcancel', 'alpha');
//$lineid   = GETPOST('lineid', 'int');

$typedoc				= GETPOST('typedoc', 'alpha');
$iddoc					= GETPOST('iddoc', 'int');
$crea					= GETPOST('crea', 'int');

// Initialize technical objects
$object = new Funding($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->funding->dir_output.'/temp/massgeneration/'.$user->id;
$hookmanager->initHooks(array('fundingcard', 'globalcard')); // Note that conf->hooks_modules contains array

// Fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

$search_array_options = $extrafields->getOptionalsFromPost($object->table_element, '', 'search_');

// Initialize array of search criterias
$search_all = trim(GETPOST("search_all", 'alpha'));
$search = array();
foreach ($object->fields as $key => $val)
{
	if (GETPOST('search_'.$key, 'alpha')) $search[$key] = GETPOST('search_'.$key, 'alpha');
}

 //BB2A Affichage de la fiche dans la proposition ou la commande
 if ($typedoc && $iddoc && empty($crea)){
	$sql = 'SELECT t.rowid, t.fk_propal, t.fk_order';
	$sql .= " FROM ".MAIN_DB_PREFIX.$object->table_element." as t";
	if ($object->ismultientitymanaged == 1) $sql .= " WHERE t.entity IN (".getEntity($object->element).")";
	else $sql .= " WHERE 1 = 1";
	//BB2A_Filtre si dans une proposition
	if ($typedoc == 'propal') $sql.= " AND t.fk_propal = ".$iddoc;
	//BB2A_Filtre si dans une commande
	if ($typedoc == 'order') $sql.= " AND t.fk_order = ".$iddoc;
	$resql = $db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		if ($num > 0)
		{
			$obj = $db->fetch_object($resql);
			$id = $obj->rowid;
		}
		else
		{
			$action = 'create';
		}
	}
	else
	{
		dol_print_error($db);
	}
 }

if (empty($action) && empty($id) && empty($ref)) $action = 'view';
// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php'; // Must be include, not include_once.

$permissiontoread = $user->rights->funding->funding->read;
$permissiontoadd = $user->rights->funding->funding->write; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
$permissiontodelete = $user->rights->funding->funding->delete || ($permissiontoadd && isset($object->status) && $object->status == $object::STATUS_DRAFT);
$permissionnote = $user->rights->funding->funding->write; // Used by the include of actions_setnotes.inc.php
$permissiondellink = $user->rights->funding->funding->write; // Used by the include of actions_dellink.inc.php
$permissionmanage = $user->rights->funding->funding->manage; //User by the function send_mail_org
$upload_dir = $conf->funding->multidir_output[isset($object->entity) ? $object->entity : 1];

// Security check - Protection if external user
//if ($user->socid > 0) accessforbidden();
//if ($user->socid > 0) $socid = $user->socid;
//$isdraft = (($object->statut == $object::STATUS_DRAFT) ? 1 : 0);
//$result = restrictedArea($user, 'funding', $object->id, '', '', 'fk_soc', 'rowid', $isdraft);

//if (!$permissiontoread) accessforbidden();

/*
 * Actions
 */

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	$error = 0;

	$backurlforlist = dol_buildpath('/funding/funding_list.php', 1);
	if(!empty($typedoc) && !empty($iddoc))
	{
		$typedoc == 'propo' ? $backurl = '/comm/propal/card.php?id='.$iddoc : '';
		$typedoc == 'order' ? $backurl = '/comm/propal/card.php?id='.$iddoc : '';
	}
	else
	{
		$backurl = $backurlforlist;
	}
	if (empty($backtopage) || ($cancel && empty($id))) {
		if (empty($backtopage) || ($cancel && strpos($backtopage, '__ID__'))) {
			if (empty($id) && (($action != 'add' && $action != 'create') || $cancel)) $backtopage = $backurl;
			else $backtopage = dol_buildpath('/funding/funding_card.php', 1).'?id='.($id > 0 ? $id : '__ID__').'&typedoc='.$typedoc.'&iddoc='.$iddoc;
		}
	}
	$triggermodname = 'FUNDING_FUNDING_MODIFY'; // Name of trigger action code to execute when we modify record

	// Positionne study number
	if ($action == 'setstudy_number' && $permissiontoadd)
	{
		$result = $object->set_study_number($user, GETPOST('study_number'));
		if ($result < 0)
		{
			setEventMessages($object->error, $object->errors, 'errors');
		}
	}
	// Positionne folder number
	if ($action == 'setfolder_number' && $permissiontoadd)
	{
		$result = $object->set_folder_number($user, GETPOST('folder_number'));
		if ($result < 0)
		{
			setEventMessages($object->error, $object->errors, 'errors');
		}
	}
	
		// Actions cancel, add, update, update_extras, confirm_validate, confirm_delete, confirm_deleteline, confirm_clone, confirm_close, confirm_setdraft, confirm_reopen
	include DOL_DOCUMENT_ROOT.'/core/actions_addupdatedelete.inc.php';

	// Actions when linking object each other
	include DOL_DOCUMENT_ROOT.'/core/actions_dellink.inc.php';

	// Actions when printing a doc from card
	include DOL_DOCUMENT_ROOT.'/core/actions_printing.inc.php';

	// Action to move up and down lines of object
	//include DOL_DOCUMENT_ROOT.'/core/actions_lineupdown.inc.php';

	// Action to build doc
	include DOL_DOCUMENT_ROOT.'/core/actions_builddoc.inc.php';

	if ($action == 'confirm_refresh' && $confirm == 'yes' && $permissiontoadd)
	{
		$res = $object->update($user);
		//if ($res > 0) $object->status = self::STATUS_STUDY_REQUEST;
	}
	
	if ($action == 'send_org' && $confirm == 'yes' && $permissiontoadd)
	{
		$res = $object->send_org($user);
		//if ($res > 0) $object->status = self::STATUS_STUDY_REQUEST;
	}
	
	if ($action == 'set_thirdparty' && $permissiontoadd)
	{
		$object->setValueFrom('fk_soc', GETPOST('fk_soc', 'int'), '', '', 'date', '', $user, 'FUNDING_MODIFY');
	}
	if ($action == 'classin' && $permissiontoadd)
	{
		$object->setProject(GETPOST('projectid', 'int'));
	}
	
	if ($action == 'setAcceptedRefused' && $permissionmanage && !GETPOST('cancel', 'alpha'))
	{
		if (!(GETPOST('statut', 'int') > 0))
		{
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("CloseAs")), null, 'errors');
			$action = 'statut';
		}
		else
		{
			// prevent browser refresh from closing proposal several times
			if ($object->status >= $object::STATUS_VALIDATED)
			{
				$db->begin();

				$result = $object->Set_AcceptedRefused($user, GETPOST('statut', 'int'), GETPOST('note', 'none'));
				if ($result <= 0)
				{
					setEventMessages($object->error, $object->errors, 'errors');
					$error++;
				}
				if (!$error)
				{
					$db->commit();
				}
				else
				{
					$db->rollback();
				}
			}
		}
	}
	if ($action == 'savedoc' && $permissiontoadd)
	{
		 // Ducument save
		$dir     = $conf->funding->multidir_output[$conf->entity]."/".$object->ref;
		$file_OK = is_uploaded_file($_FILES['doc1']['tmp_name']);
		if ($file_OK)
		{
			if (image_format_supported($_FILES['doc1']['name']))
			{
				dol_mkdir($dir);

				if (@is_dir($dir))
				{
					$newfile = $dir.'/'.dol_sanitizeFileName($_FILES['doc1']['name']);
					//$newfile = $dir.'/test.txt';
					$result = dol_move_uploaded_file($_FILES['doc1']['tmp_name'], $newfile, 1);

					if (!$result > 0)
					{
						$errors[] = "ErrorFailedToSaveFile";
					}
					else
					{
						// Create thumbs
						$object->addThumbs($newfile);
					}
				}
			}
		}
		else
		{
			switch ($_FILES['doc1']['error'])
			{
				case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
				case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
					$errors[] = "ErrorFileSizeTooLarge";
					break;
				case 3: //uploaded file was only partially uploaded
					$errors[] = "ErrorFilePartiallyUploaded";
					break;
			}
		}
		// Gestion des documents
	}
	
	// BB2A marque send mail
	// Actions to send emails
	$triggersendname = 'FUNDING_SENTBYMAIL';
	$autocopy = 'MAIN_MAIL_AUTOCOPY_FUNDING_TO';
	$trackid = 'funding'.$object->id;
	include DOL_DOCUMENT_ROOT.'/core/actions_sendmails.inc.php';
}

/*
 * View
 *
 * Put here all code to build page
 */
$form = new Form($db);
$formfile = new FormFile($db);
$formproject = new FormProjets($db);

$title = $langs->trans("Funding");
$help_url = '';
llxHeader('', $title, $help_url);
 
 // Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';

// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("NewObject", $langs->transnoentitiesnoconv("Funding")), '', 'object_'.$object->picto);

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'?crea=1&typedoc='.$typedoc.'&iddoc='.$iddoc.'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="add">';
	if ($backtopage) print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	if ($backtopageforcancel) print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';

	dol_fiche_head(array(), '');

	// Set some default values
	//if (! GETPOSTISSET('fieldname')) $_POST['fieldname'] = 'myvalue';

	print '<table class="border centpercent tableforfieldcreate">'."\n";

	// Common attributes
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_add.tpl.php';

	// Other attributes
	include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_add.tpl.php';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center">';
	print '<input type="submit" class="button" name="add" value="'.dol_escape_htmltag($langs->trans("Create")).'">';
	print '&nbsp; ';
	if(empty($iddoc))print '<input type="'.($backtopage ? "submit" : "button").'" class="button" name="cancel" value="'.dol_escape_htmltag($langs->trans("Cancel")).'"'.($backtopage ? '' : ' onclick="javascript:history.go(-1)"').'>'; // Cancel for create does not post form if we don't know the backtopage
	print '</div>';

	print '</form>';

	//dol_set_focus('input[name="ref"]');
	
	// if ($typedoc == 'propal') $backtopage = DOL_URL_ROOT.'/comm/propal/card.php?id='.$iddoc;
	// if ($typedoc == 'order') $backtopage = DOL_URL_ROOT.'/commande/card.php?id='.$iddoc;
}

// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("Funding"), '', 'object_'.$object->picto);
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'?typedoc='.$typedoc.'&iddoc='.$iddoc.'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	if ($backtopage) print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	if ($backtopageforcancel) print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';

	dol_fiche_head();

	print '<table class="border centpercent tableforfieldedit">'."\n";

	// Common attributes
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_edit.tpl.php';

	// Other attributes
	include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_edit.tpl.php';

	print '</table>';

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}

// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
	//Regarde si on est dans un document ou fiche funding
	 if (!empty($object->fk_propal) || $typedoc == 'propal')
	{
		//BB2A_Récupération table propal
		$prop = new Propal($db);
		if ($object->fk_propal > 0 || ! empty($ref))
		{
			$result = $prop->fetch($object->fk_propal);
		}
	}
	elseif (!empty($object->fk_order) || $typedoc == 'order') 
	{
		//BB2A_Récupération table order
		$ord = new Commande($db);
		if ($object->fk_order > 0 || ! empty($ref))
		{
			$result = $ord->fetch($object->fk_order);
		}
	}
		
	// BB2A Vérification si on est dans un document pour afficher la bonne entête
	if ($typedoc == 'propal')
	{
		//BB2A_Affichage encadrer propal
		$prop->fetch_thirdparty();
		
		$head = propal_prepare_head($prop);
		dol_fiche_head($head, 'Funding', $langs->trans("Proposal"), -1, 'propal');
	}
	elseif ($typedoc == 'order')
	{
		//BB2A_Affichage encadrer order
		$head = commande_prepare_head($ord);
		dol_fiche_head($head, 'Funding', $langs->trans("CustomerOrder"), -1, 'order');
	}
	else
	{
		//BB2A_Affichage encadrer funding
		$res = $object->fetch_optionals();
		$head = fundingPrepareHead($object);
		dol_fiche_head($head, 'card', $langs->trans("Funding"), -1, $object->picto);
	}
	$formconfirm = '';

	// Confirmation of action refresh
	if ($action == 'refresh')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&typedoc='.$typedoc.'&iddoc='.$iddoc, $langs->trans('RefreshFunding'), $langs->trans('ConfirmRefreshFunding'), 'confirm_refresh', '', 0, 1);
	}
	
	
	// Confirm send organization
	if ($action == 'SendOrg')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&typedoc='.$typedoc.'&iddoc='.$iddoc, $langs->trans('SendOrg'), $langs->trans('ConfirmSendOrg'), 'send_org', '', 0, 1);
	}
	
	// Selec a accepted/refused
	if ($action == 'AcceptedRefused')
	{
		//Form to close proposal (signed or not)
		$formquestion = array(
			array('type' => 'select', 'name' => 'statut', 'label' => '<span class="fieldrequired">'.$langs->trans("CloseAs").'</span>', 'values' => array(2=>$object->LibStatut($object::STATUS_ACCEPT), 3=>$object->LibStatut($object::STATUS_DENIED))),
			// BB2A Saisie d'un text
			// array('type' => 'text', 'name' => 'note', 'label' => $langs->trans("Note"), 'value' => '')
		);
		
		// BB2A Notification voir pour aline changement de statut
		/*if (!empty($conf->notification->enabled))
		{
			require_once DOL_DOCUMENT_ROOT.'/core/class/notify.class.php';
			$notify = new Notify($db);
			$formquestion = array_merge($formquestion, array(
				array('type' => 'onecolumn', 'value' => $notify->confirmMessage('FUNDING_ACCEPT_DENIED', $object->socid, $object)),
			));
		}*/

		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&typedoc='.$typedoc.'&iddoc='.$iddoc, $langs->trans('SetAcceptedRefused'), $text, 'setAcceptedRefused', $formquestion, '', 1, 200);
	}

	// Confirmation to delete
	if ($action == 'delete_object')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&typedoc='.$typedoc.'&iddoc='.$iddoc, $langs->trans('DeleteFunding'), $langs->trans('ConfirmDeleteFunding'), 'confirm_delete', '', 0, 1);
	}
	
	// Confirmation to delete line
	if ($action == 'deleteline')
	{
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&lineid='.$lineid, $langs->trans('DeleteLine'), $langs->trans('ConfirmDeleteLine'), 'confirm_deleteline', '', 0, 1);
	}
	// Clone confirmation
	/*if ($action == 'clone') {
		// Create an array for form
		$formquestion = array();
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('ToClone'), $langs->trans('ConfirmCloneAsk', $object->ref), 'confirm_clone', $formquestion, 'yes', 1);
	}*/

	// Confirmation of action xxxx
	if ($action == 'xxx')
	{
		$formquestion = array();
		/*
		$forcecombo=0;
		if ($conf->browser->name == 'ie') $forcecombo = 1;	// There is a bug in IE10 that make combo inside popup crazy
		$formquestion = array(
			// 'text' => $langs->trans("ConfirmClone"),
			// array('type' => 'checkbox', 'name' => 'clone_content', 'label' => $langs->trans("CloneMainAttributes"), 'value' => 1),
			// array('type' => 'checkbox', 'name' => 'update_prices', 'label' => $langs->trans("PuttingPricesUpToDate"), 'value' => 1),
			// array('type' => 'other',    'name' => 'idwarehouse',   'label' => $langs->trans("SelectWarehouseForStockDecrease"), 'value' => $formproduct->selectWarehouses(GETPOST('idwarehouse')?GETPOST('idwarehouse'):'ifone', 'idwarehouse', '', 1, 0, 0, '', 0, $forcecombo))
		);
		*/
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('XXX'), $text, 'confirm_xxx', $formquestion, 0, 1, 220);
	}

	// Call Hook formConfirm
	$parameters = array('formConfirm' => $formconfirm, 'lineid' => $lineid);
	$reshook = $hookmanager->executeHooks('formConfirm', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	if (empty($reshook)) $formconfirm .= $hookmanager->resPrint;
	elseif ($reshook > 0) $formconfirm = $hookmanager->resPrint;

	// Print form confirm
	print $formconfirm;


	// Object card
	// ------------------------------------------------------------
	//BB2A
	//$linkback = '<a href="'.dol_buildpath('/funding/funding_list.php', 1).'?restore_lastsearch_values=1'.(!empty($socid) ? '&socid='.$socid : '').'">'.$langs->trans("BackToList").'</a>';

	$morehtmlref = '<div class="refidno">';
	// Numbers
	$morehtmlref .= $form->editfieldkey("StudyNumber", 'study_number', $object->study_number, $object, $permissiontoadd, 'string', '', 0, 1);
	$morehtmlref .= $form->editfieldval("StudyNumber", 'study_number', $object->study_number, $object, $permissiontoadd, 'string', '', null, null, '', 1);
	$morehtmlref .= '<br/>'.$form->editfieldkey("FolderNumber", 'folder_number', $object->folder_number, $object, $permissiontoadd, 'string', '', 0, 1);
	$morehtmlref .= $form->editfieldval("FolderNumber", 'folder_number', $object->folder_number, $object, $permissiontoadd, 'string', '', null, null, '', 1);
	// Thirdparty
	$morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . (is_object($object->thirdparty) ? $object->thirdparty->getNomUrl(1) : '');
	$morehtmlref.=(($object->fk_propal) ? '<br>'.$langs->trans('Propal') . ' : ' . $prop->getNomUrl(1) : '');
	$morehtmlref.=(($object->fk_order) ? '<br>'.$langs->trans('Order') . ' : ' . $ord->getNomUrl(1) : '');
	$morehtmlref .= '</div>';
	
	dol_banner_tab($object, 'ref',	$morehtml, 0, 'ref', 'ref', $morehtmlref);
	print '<div class="fichecenter">';
	print '<div class="fichehalfleft">';
	print '<div class="underbanner clearboth"></div>';
	print '<table class="border centpercent">'."\n";

	// Common attributes
	//$keyforbreak='fieldkeytoswitchonsecondcolumn';	// We change column just before this field
	//unset($object->fields['fk_project']);				// Hide field already shown in banner
	//unset($object->fields['fk_soc']);					// Hide field already shown in banner
	include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_view.tpl.php';

	// Other attributes. Fields from hook formObjectOptions and Extrafields.
	include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_view.tpl.php';
	
	print '</table>';
	// Documents
	print '<h3>'.$langs->trans("DocumentsForFunding").'</h3>';
	print '<form enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&typedoc='.$typedoc.'&iddoc='.$iddoc.'" method="post" name="formdoc">';
	print '<input type="hidden" name="action" value="savedoc">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="entity" value="'.$object->entity.'">';
	print '<table>';
		//Document 1
		print '<tr class="hideonsmartphone">';
		print '<td>'.$form->editfieldkey('fundoc1', 'doc1input', '', $object, 0).'</td>';
		print '<td colspan="3">';
		if ($object->fundoc1) print $form->showphoto('societe', $object);
		$caneditfield = 1;
		if ($caneditfield)
		{
			if ($object->fundoc1) print "<br>\n";
			print '<table class="nobordernopadding">';
			if ($object->fundoc1) print '<tr><td><input type="checkbox" class="flat photodelete" name="deletephoto" id="photodelete"> '.$langs->trans("Delete").'<br><br></td></tr>';
			print '<td><input type="file" class="flat"  name="doc1" id="doc1input"></td>';
			print '</table>';
		}
		print '</td>';
		print '</tr>';
		//Document 2
		print '<tr class="hideonsmartphone">';
		print '<td>'.$form->editfieldkey('fundoc2', 'photo2input', '', $object, 0).'</td>';
		print '<td colspan="3">';
		if ($object->fundoc2) print $form->showphoto('societe', $object);
		$caneditfield = 1;
		if ($caneditfield)
		{
			if ($object->fundoc2) print "<br>\n";
			print '<table class="nobordernopadding">';
			if ($object->fundoc2) print '<tr><td><input type="checkbox" class="flat photodelete" name="deletephoto" id="photodelete"> '.$langs->trans("Delete").'<br><br></td></tr>';
			print '<td><input type="file" class="flat" name="doc2" id="doc2input"></td>';
			print '</table>';
		}
		print '</td>';
		print '</tr>';
		//Document 3
		print '<tr class="hideonsmartphone">';
		print '<td>'.$form->editfieldkey('fundoc3', 'doc3input', '', $object, 0).'</td>';
		print '<td colspan="3">';
		if ($object->fundoc3) print $form->showphoto('societe', $object);
		$caneditfield = 1;
		if ($caneditfield)
		{
			if ($object->fundoc3) print "<br>\n";
			print '<table class="nobordernopadding">';
			if ($object->fundoc3) print '<tr><td><input type="checkbox" class="flat photodelete" name="deletephoto" id="photodelete"> '.$langs->trans("Delete").'<br><br></td></tr>';
			print '<td><input type="file" class="flat" name="doc3[]" multiple id="doc3input"></td>';
			print '</table>';
		}
		print '</td>';
		print '</tr>';
		//Document 4
		print '<tr class="hideonsmartphone">';
		print '<td>'.$form->editfieldkey('fundoc4', 'doc4input', '', $object, 0).'</td>';
		print '<td colspan="3">';
		if ($object->fundoc4) print $form->showphoto('societe', $object);
		$caneditfield = 1;
		if ($caneditfield)
		{
			if ($object->fundoc4) print "<br>\n";
			print '<table class="nobordernopadding">';
			if ($object->fundoc4) print '<tr><td><input type="checkbox" class="flat photodelete" name="deletephoto" id="photodelete"> '.$langs->trans("Delete").'<br><br></td></tr>';
			print '<td><input type="file" class="flat" name="doc4[]" multiple id="doc4input"></td>';
			print '</table>';
		}
		print '</td>';
		print '</tr>';
	print '</table>';
	print '<div class="center">';
	print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; &nbsp; ';
	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';
	print '</form>';
	
	print '<h3>'.$langs->trans("FundingFolder").'</h3>';
	print '<form enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&typedoc='.$typedoc.'&iddoc='.$iddoc.'" method="post" name="formdoc">';
	print '<input type="hidden" name="action" value="savedocfund">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	print '<input type="hidden" name="entity" value="'.$object->entity.'">';
	print '<table>';
		//FundingFolderDoc 1
		print '<tr class="hideonsmartphone">';
		print '<td>'.$form->editfieldkey('funfoldoc1', 'funfoldoc1input', '', $object, 0).'</td>';
		print '<td colspan="3">';
		if ($object->funfoldoc1) print $form->showphoto('societe', $object);
		$caneditfield = 1;
		if ($caneditfield)
		{
			if ($object->funfoldoc1) print "<br>\n";
			print '<table class="nobordernopadding">';
			if ($object->funfoldoc1) print '<tr><td><input type="checkbox" class="flat photodelete" name="deletephoto" id="photodelete"> '.$langs->trans("Delete").'<br><br></td></tr>';
			print '<td><input type="file" class="flat"  name="funfoldoc1" id="funfoldoc1input"></td>';
			print '</table>';
		}
		print '</td>';
		print '</tr>';
		//FundingFolderDoc 2
		print '<tr class="hideonsmartphone">';
		print '<td>'.$form->editfieldkey('funfoldoc2', 'funfoldoc2input', '', $object, 0).'</td>';
		print '<td colspan="3">';
		if ($object->funfoldoc2) print $form->showphoto('societe', $object);
		$caneditfield = 1;
		if ($caneditfield)
		{
			if ($object->funfoldoc2) print "<br>\n";
			print '<table class="nobordernopadding">';
			if ($object->funfoldoc2) print '<tr><td><input type="checkbox" class="flat photodelete" name="deletephoto" id="photodelete"> '.$langs->trans("Delete").'<br><br></td></tr>';
			print '<td><input type="file" class="flat" name="funfoldoc2" id="funfoldoc2input"></td>';
			print '</table>';
		}
		print '</td>';
		print '</tr>';
		//FundingFolderDoc 3
		print '<tr class="hideonsmartphone">';
		print '<td>'.$form->editfieldkey('funfoldoc3', 'funfoldoc3input', '', $object, 0).'</td>';
		print '<td colspan="3">';
		if ($object->funfoldoc3) print $form->showphoto('societe', $object);
		$caneditfield = 1;
		if ($caneditfield)
		{
			if ($object->funfoldoc3) print "<br>\n";
			print '<table class="nobordernopadding">';
			if ($object->funfoldoc3) print '<tr><td><input type="checkbox" class="flat photodelete" name="deletephoto" id="photodelete"> '.$langs->trans("Delete").'<br><br></td></tr>';
			print '<td><input type="file" class="flat" name="funfoldoc3" id="funfoldoc3input"></td>';
			print '</table>';
		}
		print '</td>';
		print '</tr>';
	print '</table>';
	print '<div class="center">';
	print '<input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; &nbsp; ';
	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';
	print '</form>';
	print '</div>';
	print '</div>';

	print '<div class="clearboth"></div>';

	dol_fiche_end();


	/*
	 * Lines
	 */

	if (!empty($object->table_element_line))
	{
		// Show object lines
		$result = $object->getLinesArray();

		print '	<form name="addproduct" id="addproduct" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.(($action != 'editline') ? '#addline' : '#line_'.GETPOST('lineid', 'int')).'" method="POST">
		<input type="hidden" name="token" value="' . newToken().'">
		<input type="hidden" name="action" value="' . (($action != 'editline') ? 'addline' : 'updateline').'">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="id" value="' . $object->id.'">
		';

		if (!empty($conf->use_javascript_ajax) && $object->status == 0) {
			include DOL_DOCUMENT_ROOT.'/core/tpl/ajaxrow.tpl.php';
		}

		print '<div class="div-table-responsive-no-min">';
		if (!empty($object->lines) || ($object->status == $object::STATUS_DRAFT && $permissiontoadd && $action != 'selectlines' && $action != 'editline'))
		{
			print '<table id="tablelines" class="noborder noshadow" width="100%">';
		}

		if (!empty($object->lines))
		{
			$object->printObjectLines($action, $mysoc, null, GETPOST('lineid', 'int'), 1);
		}

		// Form to add new line
		if ($object->status == 0 && $permissiontoadd && $action != 'selectlines')
		{
			if ($action != 'editline')
			{
				// Add products/services form
				$object->formAddObjectLine(1, $mysoc, $soc);

				$parameters = array();
				$reshook = $hookmanager->executeHooks('formAddObjectLine', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
			}
		}

		if (!empty($object->lines) || ($object->status == $object::STATUS_DRAFT && $permissiontoadd && $action != 'selectlines' && $action != 'editline'))
		{
			print '</table>';
		}
		print '</div>';

		print "</form>\n";
	}


	// Buttons for actions

	if ($action != 'presend' && $action != 'editline') {
		print '<div class="tabsAction">'."\n";
		$parameters = array();
		$reshook = $hookmanager->executeHooks('addMoreActionsButtons', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
		if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

		if (empty($reshook))
		{
			// BB2A Send organization
			if (empty($user->socid)) {
				if ($permissionmanage)
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=SendOrg&typedoc='.$typedoc.'&iddoc'.$iddoc.'">'.$langs->trans('SendOrg').'</a>'."\n";
				}
			}

			// Set status accepted/refused
			if (empty($user->socid)) {
				if ($object->status >= $object::STATUS_VALIDATED && $permissionmanage) 
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=AcceptedRefused'.(empty($conf->global->MAIN_JUMP_TAG) ? '' : '#close').'&typedoc='.$typedoc.'&iddoc'.$iddoc.'">'.$langs->trans('SetAcceptedRefused').'</a>';
				}
			}

			// BB2A Envoie par mail	
			// Send
			/*
			if (empty($user->socid)) {
				print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=presend&mode=init#formmailbeforetitle">'.$langs->trans('SendMail').'</a>'."\n";
			}
			*/

			// BB2A Refresh
			if (empty($user->socid)) {
				if ($permissiontoadd)
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=refresh&typedoc='.$typedoc.'&iddoc='.$iddoc.'">'.$langs->trans('Refresh').'</a>'."\n";
				}
			}
			
			// Back to draft
			if ($object->status == $object::STATUS_VALIDATED)
			{
				if ($permissiontoadd)
				{
					print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_setdraft&confirm=yes">'.$langs->trans("SetToDraft").'</a>';
				}
			}

			// Modify
			if ($permissiontoadd)
			{
				print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=edit&typedoc='.$typedoc.'&iddoc='.$iddoc.'">'.$langs->trans("Modify").'</a>'."\n";
			}
			else
			{
				print '<a class="butActionRefused classfortooltip" href="#" title="'.dol_escape_htmltag($langs->trans("NotEnoughPermissions")).'">'.$langs->trans('Modify').'</a>'."\n";
			}

			// Validate
			if ($object->status == $object::STATUS_DRAFT)
			{
				if ($permissiontoadd)
				{
					if (empty($object->table_element_line) || (is_array($object->lines) && count($object->lines) > 0))
					{
						print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_validate&confirm=yes">'.$langs->trans("Validate").'</a>';
					}
					else
					{
						$langs->load("errors");
						print '<a class="butActionRefused" href="" title="'.$langs->trans("ErrorAddAtLeastOneLineFirst").'">'.$langs->trans("Validate").'</a>';
					}
				}
			}

			// Clone
			/*if ($permissiontoadd)
			{
				print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&socid='.$object->socid.'&action=clone&object=funding">'.$langs->trans("ToClone").'</a>'."\n";
			}*/

			/*
			if ($permissiontoadd)
			{
				if ($object->status == $object::STATUS_ENABLED)
				{
					print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=disable">'.$langs->trans("Disable").'</a>'."\n";
				}
				else
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=enable">'.$langs->trans("Enable").'</a>'."\n";
				}
			}
			if ($permissiontoadd)
			{
				if ($object->status == $object::STATUS_VALIDATED)
				{
					print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=close">'.$langs->trans("Cancel").'</a>'."\n";
				}
				else
				{
					print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=reopen">'.$langs->trans("Re-Open").'</a>'."\n";
				}
			}
			*/

			// Delete (need delete permission, or if draft, just need create/modify permission)
			if ($permissiontodelete || ($object->status == $object::STATUS_DRAFT && $permissiontoadd))
			{
				print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete_object&typedoc='.$typedoc.'&iddoc='.$iddoc.'">'.$langs->trans('Delete').'</a>'."\n";
			}
			else
			{
				print '<a class="butActionRefused classfortooltip" href="#" title="'.dol_escape_htmltag($langs->trans("NotEnoughPermissions")).'">'.$langs->trans('Delete').'</a>'."\n";
			}
		}
		print '</div>'."\n";
	}


	// Select mail models is same action as presend
	if (GETPOST('modelselected')) {
		$action = 'presend';
	}

	// BB2A Fichiers joints
	//Code isssue de funding_docuement.php
	
	/*
	* Actions
	*/
	if ($id > 0 || !empty($ref)) $upload_dir = $conf->funding->multidir_output[$object->entity ? $object->entity : $conf->entity]."/".dol_sanitizeFileName($object->ref);
	
	include_once DOL_DOCUMENT_ROOT.'/core/actions_linkedfiles.inc.php';

	print "<h3>".$langs->trans("DocumentsForFunding")."</h3>";
	// Build file list
	$filearray = dol_dir_list($upload_dir, "files", 0, '', '(\.meta|_preview.*\.png)$', $sortfield, (strtolower($sortorder) == 'desc' ?SORT_DESC:SORT_ASC), 1);
	$totalsize = 0;
	foreach ($filearray as $key => $file)
	{
		$totalsize += $file['size'];
	}
	
	$modulepart = 'funding';
	$permission = $permissiontoadd;
	$permtoedit = $permissiontoadd;
	$param = '&id='.$object->id;

	//$relativepathwithnofile='funding/' . dol_sanitizeFileName($object->id).'/';
	$relativepathwithnofile = dol_sanitizeFileName($object->ref).'/';
	
	include_once DOL_DOCUMENT_ROOT.'/core/tpl/document_actions_post_headers.tpl.php';

// BB2A désactive l'affichage des evenements, Document, Objets liés

	if ($action != 'presend')
	{
		print '<div class="fichecenter"><div class="fichehalfleft">';
		print '<a name="builddoc"></a>'; // ancre

		$includedocgeneration = 1;
/*
		// Documents
		if ($includedocgeneration) {
			$objref = dol_sanitizeFileName($object->ref);
			$relativepath = $objref . '/' . $objref . '.pdf';
			$filedir = $conf->funding->dir_output.'/'.$object->element.'/'.$objref;
			$urlsource = $_SERVER["PHP_SELF"] . "?id=" . $object->id;
			$genallowed = $user->rights->funding->funding->read;	// If you can read, you can build the PDF to read content
			$delallowed = $user->rights->funding->funding->write;	// If you can create/edit, you can remove a file on card
			print $formfile->showdocuments('funding:Funding', $object->element.'/'.$objref, $filedir, $urlsource, $genallowed, $delallowed, $object->model_pdf, 1, 0, 0, 28, 0, '', '', '', $langs->defaultlang);
		}
*/
		// Show links to link elements
		$linktoelem = $form->showLinkToObjectBlock($object, null, array('funding'));
		$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);


		print '</div><div class="fichehalfright"><div class="ficheaddleft">';

		$MAXEVENT = 10;

		$morehtmlright = '<a href="'.dol_buildpath('/funding/funding_agenda.php', 1).'?id='.$object->id.'">';
		$morehtmlright .= $langs->trans("SeeAll");
		$morehtmlright .= '</a>';

		// List of actions on element
		include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
		$formactions = new FormActions($db);
		$somethingshown = $formactions->showactions($object, $object->element, (is_object($object->thirdparty) ? $object->thirdparty->id : 0), 1, '', $MAXEVENT, '', $morehtmlright);
		
		print '</div></div></div>';
	}

	//Select mail models is same action as presend
	if (GETPOST('modelselected')) $action = 'presend';

	// Presend form
	$modelmail = 'funding';
	$defaulttopic = 'InformationMessage';
	$diroutput = $conf->funding->dir_output;
	$trackid = 'funding'.$object->id;

	include DOL_DOCUMENT_ROOT.'/core/tpl/card_presend.tpl.php';
}
// End of page
llxFooter();
$db->close();
