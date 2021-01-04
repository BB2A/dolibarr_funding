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
 * \file        class/funding.class.php
 * \ingroup     funding
 * \brief       This file is a CRUD class file for Funding (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class for Funding
 */
class Funding extends CommonObject
{
	/**
	 * @var string ID to identify managed object
	 */
	public $element = 'funding';

	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'funding_funding';

	/**
	 * @var int  Does this object support multicompany module ?
	 * 0=No test on entity, 1=Test with field entity, 'field@table'=Test with link by field@table
	 */
	public $ismultientitymanaged = 0;

	/**
	 * @var int  Does object support extrafields ? 0=No, 1=Yes
	 */
	public $isextrafieldmanaged = 1;

	/**
	 * @var string String with name of icon for funding. Must be the part after the 'object_' into object_funding.png
	 */
	public $picto = 'funding@funding';

		
	const STATUS_DRAFT = 0;
	const STATUS_VALIDATED = 1;
	const STATUS_CANCELED = 9;


	/**
	 *  'type' if the field format ('integer', 'integer:ObjectClass:PathToClass[:AddCreateButtonOrNot[:Filter]]', 'varchar(x)', 'double(24,8)', 'real', 'price', 'text', 'html', 'date', 'datetime', 'timestamp', 'duration', 'mail', 'phone', 'url', 'password')
	 *         Note: Filter can be a string like "(t.ref:like:'SO-%') or (t.date_creation:<:'20160101') or (t.nature:is:NULL)"
	 *  'label' the translation key.
	 *  'enabled' is a condition when the field must be managed (Example: 1 or '$conf->global->MY_SETUP_PARAM)
	 *  'position' is the sort order of field.
	 *  'notnull' is set to 1 if not null in database. Set to -1 if we must set data to null if empty ('' or 0).
	 *  'visible' says if field is visible in list (Examples: 0=Not visible, 1=Visible on list and create/update/view forms, 2=Visible on list only, 3=Visible on create/update/view form only (not list), 4=Visible on list and update/view form only (not create). 5=Visible on list and view only (not create/not update). Using a negative value means field is not shown by default on list but can be selected for viewing)
	 *  'noteditable' says if field is not editable (1 or 0)
	 *  'default' is a default value for creation (can still be overwrote by the Setup of Default Values if field is editable in creation form). Note: If default is set to '(PROV)' and field is 'ref', the default value will be set to '(PROVid)' where id is rowid when a new record is created.
	 *  'index' if we want an index in database.
	 *  'foreignkey'=>'tablename.field' if the field is a foreign key (it is recommanded to name the field fk_...).
	 *  'searchall' is 1 if we want to search in this field when making a search from the quick search button.
	 *  'isameasure' must be set to 1 if you want to have a total on list for this field. Field type must be summable like integer or double(24,8).
	 *  'css' is the CSS style to use on field. For example: 'maxwidth200'
	 *  'help' is a string visible as a tooltip on field
	 *  'showoncombobox' if value of the field must be visible into the label of the combobox that list record
	 *  'disabled' is 1 if we want to have the field locked by a 'disabled' attribute. In most cases, this is never set into the definition of $fields into class, but is set dynamically by some part of code.
	 *  'arraykeyval' to set list of value if type is a list of predefined values. For example: array("0"=>"Draft","1"=>"Active","-1"=>"Cancel")
	 *  'autofocusoncreate' to have field having the focus on a create form. Only 1 field should have this property set to 1.
	 *  'comment' is not used. You can store here any text of your choice. It is not used by application.
	 *
	 *  Note: To have value dynamic, you can set value to 0 in definition and edit the value on the fly into the constructor.
	 */

	// BEGIN MODULEBUILDER PROPERTIES
	/**
	 * @var array  Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
	 */
	public $fields=array(
		'rowid' => array('type'=>'integer', 'label'=>'TechnicalID', 'enabled'=>'1', 'position'=>1, 'notnull'=>1, 'visible'=>0, 'noteditable'=>'1', 'index'=>1, 'comment'=>"Id"),
		'ref' => array('type'=>'varchar(128)', 'label'=>'Ref.', 'enabled'=>'1', 'position'=>10, 'notnull'=>1, 'visible'=>4, 'noteditable'=>'1', 'default'=>'(STUD)', 'index'=>1, 'searchall'=>1, 'showoncombobox'=>'1', 'comment'=>"Reference of object"),
		'study_number' => array('type'=>'varchar(128)', 'label'=>'StudyNumber', 'enabled'=>'1', 'position'=>10, 'notnull'=>0, 'visible'=>2, 'index'=>1, 'searchall'=>1, 'help'=>"Help_study_number", 'showoncombobox'=>'1',),
		'folder_number' => array('type'=>'varchar(128)', 'label'=>'FolderNumber', 'enabled'=>'1', 'position'=>10, 'notnull'=>0, 'visible'=>2, 'index'=>1, 'searchall'=>1, 'help'=>"Help_folder_number", 'showoncombobox'=>'1',),
		'amount' => array('type'=>'price', 'label'=>'Amount', 'enabled'=>'1', 'position'=>15, 'notnull'=>0, 'visible'=>5, 'noteditable'=>'1', 'default'=>'null', 'isameasure'=>'1', 'help'=>"Help_amount",),
		'amount_maint' => array('type'=>'price', 'label'=>'AmountMaint', 'enabled'=>'1', 'position'=>15, 'notnull'=>0, 'visible'=>1, 'default'=>'null', 'isameasure'=>'1', 'help'=>"Help_amount_maint",),
		'amount_total' => array('type'=>'price', 'label'=>'AmountTotal', 'enabled'=>'1', 'position'=>15, 'notnull'=>0, 'visible'=>5, 'noteditable'=>'1', 'default'=>'null', 'isameasure'=>'1', 'help'=>"Help_amount_total",),
		'fk_duration' => array('type'=>'integer', 'label'=>'Duration', 'enabled'=>'1', 'position'=>25, 'notnull'=>1, 'visible'=>-1, 'foreignkey'=>'c_funding_duration.code', 'help'=>"Help_duration", 'arrayofkeyval'=>array('12'=>'12 Mois', '24'=>'24 Mois', '36'=>'36 Mois', '48'=>'48 Mois', '60'=>'60 Mois'),),
		'coef' => array('type'=>'real', 'label'=>'Coef', 'enabled'=>'1', 'position'=>25, 'notnull'=>0, 'visible'=>-5, 'noteditable'=>'1', 'default'=>'0', 'isameasure'=>'1', 'css'=>'maxwidth75imp', 'help'=>"Help_coef",),
		'fk_scale' => array('type'=>'integer', 'label'=>'Scale', 'enabled'=>'1', 'position'=>30, 'notnull'=>1, 'visible'=>-1, 'foreignkey'=>'c_scale.code', 'help'=>"Help_Scale", 'arrayofkeyval'=>array('2'=>'Création', '1'=>'Standard'),),
		'amount_rent' => array('type'=>'price', 'label'=>'Rent', 'enabled'=>'1', 'position'=>35, 'notnull'=>0, 'visible'=>5, 'noteditable'=>'1', 'default'=>'null', 'isameasure'=>'1', 'help'=>"Help_amount_rent",),
		'date_delivery' => array('type'=>'date', 'label'=>'DateDelivery', 'enabled'=>'1', 'position'=>40, 'notnull'=>0, 'visible'=>5, 'noteditable'=>'1', 'help'=>"Help_date_delivery", 'showoncombobox'=>'1',),
		'date_end' => array('type'=>'date', 'label'=>'DateEnd', 'enabled'=>'1', 'position'=>45, 'notnull'=>0, 'visible'=>5, 'noteditable'=>'1', 'help'=>"Help_date_end",),
		'redemption' => array('type'=>'smallint', 'label'=>'Redemption', 'enabled'=>'1', 'position'=>50, 'notnull'=>1, 'visible'=>-1, 'default'=>'1', 'index'=>1, 'arrayofkeyval'=>array('1'=>'Non', '2'=>'Oui'),),
		'fk_org' => array('type'=>'integer:Societe:societe/class/societe.class.php::status=1 AND entity IN (__SHARED_ENTITIES__)', 'label'=>'Organization', 'enabled'=>'1', 'position'=>55, 'notnull'=>1, 'visible'=>1, 'index'=>1, 'help'=>"LinkToOrganization",),
		'fk_soc' => array('type'=>'integer:Societe:societe/class/societe.class.php::status=1 AND entity IN (__SHARED_ENTITIES__)', 'label'=>'ThirdParty', 'enabled'=>'1', 'position'=>60, 'notnull'=>0, 'visible'=>-2, 'noteditable'=>'1', 'index'=>1, 'help'=>"LinkToThirparty",),
		'fk_propal' => array('type'=>'integer:Propal:comm/propal/class/propal.class.php:', 'label'=>'Proposal', 'enabled'=>'1', 'position'=>65, 'notnull'=>-1, 'visible'=>-2, 'noteditable'=>'1', 'index'=>1,),
		'fk_order' => array('type'=>'integer:Commande:commande/class/commande.class.php:', 'label'=>'Order', 'enabled'=>'1', 'position'=>70, 'notnull'=>-1, 'visible'=>-2, 'noteditable'=>'1', 'index'=>1,),
		'fk_user_comm' => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'SalesRepresentative', 'enabled'=>'1', 'position'=>75, 'notnull'=>0, 'visible'=>-4, 'foreignkey'=>'user.rowid',),
		'description' => array('type'=>'text', 'label'=>'Description', 'enabled'=>'1', 'position'=>80, 'notnull'=>0, 'visible'=>-1,),
		'note_public' => array('type'=>'html', 'label'=>'NotePublic', 'enabled'=>'1', 'position'=>400, 'notnull'=>0, 'visible'=>0,),
		'note_private' => array('type'=>'html', 'label'=>'NotePrivate', 'enabled'=>'1', 'position'=>401, 'notnull'=>0, 'visible'=>0,),
		'date_creation' => array('type'=>'datetime', 'label'=>'DateCreation', 'enabled'=>'1', 'position'=>500, 'notnull'=>1, 'visible'=>-2,),
		'tms' => array('type'=>'timestamp', 'label'=>'DateModification', 'enabled'=>'1', 'position'=>501, 'notnull'=>0, 'visible'=>-2,),
		'fk_user_creat' => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'UserAuthor', 'enabled'=>'1', 'position'=>510, 'notnull'=>1, 'visible'=>-2, 'foreignkey'=>'user.rowid',),
		'fk_user_modif' => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'UserModif', 'enabled'=>'1', 'position'=>511, 'notnull'=>-1, 'visible'=>-2,),
		'import_key' => array('type'=>'varchar(14)', 'label'=>'ImportId', 'enabled'=>'1', 'position'=>1000, 'notnull'=>-1, 'visible'=>0,),
		'model_pdf' => array('type'=>'varchar(255)', 'label'=>'Model pdf', 'enabled'=>'1', 'position'=>1010, 'notnull'=>-1, 'visible'=>0,),
		'status_folder' => array('type'=>'smallint', 'label'=>'StatusFolder', 'enabled'=>'1', 'position'=>1000, 'notnull'=>1, 'visible'=>1, 'index'=>1, 'arrayofkeyval'=>array('0'=>'Non traitée', '1'=>'Nouvelle demande', '2'=>'En cours', '3'=>'Pièces manquantes', '4'=>'Valid&eacute', '5'=>'Refusé'),),
		'status' => array('type'=>'smallint', 'label'=>'Status', 'enabled'=>'1', 'position'=>1000, 'notnull'=>1, 'visible'=>1, 'default'=>'0', 'index'=>1, 'arrayofkeyval'=>array('0'=>'Brouillon', '1'=>'Valid&eacute;', '9'=>'Annul&eacute;'),),
	);
	public $rowid;
	public $ref;
	public $study_number;
	public $folder_number;
	public $amount;
	public $amount_maint;
	public $amount_total;
	public $fk_duration;
	public $coef;
	public $fk_scale;
	public $amount_rent;
	public $date_delivery;
	public $date_end;
	public $redemption;
	public $fk_org;
	public $fk_soc;
	public $fk_propal;
	public $fk_order;
	public $fk_user_comm;
	public $description;
	public $note_public;
	public $note_private;
	public $date_creation;
	public $tms;
	public $fk_user_creat;
	public $fk_user_modif;
	public $import_key;
	public $model_pdf;
	public $status_folder;
	public $status;
	// END MODULEBUILDER PROPERTIES


	// If this object has a subtable with lines

	/**
	 * @var int    Name of subtable line
	 */
	//public $table_element_line = 'funding_fundingline';

	/**
	 * @var int    Field with ID of parent key if this object has a parent
	 */
	//public $fk_element = 'fk_funding';

	/**
	 * @var int    Name of subtable class that manage subtable lines
	 */
	//public $class_element_line = 'Fundingline';

	/**
	 * @var array	List of child tables. To test if we can delete object.
	 */
	//protected $childtables = array();

	/**
	 * @var array    List of child tables. To know object to delete on cascade.
	 *               If name matches '@ClassNAme:FilePathClass;ParentFkFieldName' it will
	 *               call method deleteByParentField(parentId, ParentFkFieldName) to fetch and delete child object
	 */
	//protected $childtablesoncascade = array('funding_fundingdet');

	/**
	 * @var FundingLine[]     Array of subtable lines
	 */
	//public $lines = array();



	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		global $conf, $langs;

		$this->db = $db;

		if (empty($conf->global->MAIN_SHOW_TECHNICAL_ID) && isset($this->fields['rowid'])) $this->fields['rowid']['visible'] = 0;
		if (empty($conf->multicompany->enabled) && isset($this->fields['entity'])) $this->fields['entity']['enabled'] = 0;
		if (!empty($conf->global->FUNDING_FILTRE_ORGANIZATION) && isset($this->fields['fk_org'])) $this->fields['fk_org']['type'] = $this->fields['fk_org']['type']. "AND fk_typent=".$conf->global->FUNDING_FILTRE_ORGANIZATION;
		if (!empty($conf->global->FUNDING_DEFAULT_DURATION) && isset($this->fields['fk_duration'])) $this->fields['fk_duration']['default'] = $conf->global->FUNDING_DEFAULT_DURATION;
		if (!empty($conf->global->FUNDING_DEFAULT_REDEMPTION) && isset($this->fields['redemption'])) $this->fields['redemption']['default'] = $conf->global->FUNDING_DEFAULT_REDEMPTION;
		if (!empty($conf->global->FUNDING_DEFAULT_SCALE) && isset($this->fields['fk_scale'])) $this->fields['fk_scale']['default'] = $conf->global->FUNDING_DEFAULT_SCALE;

		//array('type'=>'integer:Societe:societe/class/societe.class.php::status=1 AND entity IN (__SHARED_ENTITIES__) AND fk_typent='
		// Example to show how to set values of fields definition dynamically
		/*if ($user->rights->funding->funding->read) {
			$this->fields['myfield']['visible'] = 1;
			$this->fields['myfield']['noteditable'] = 0;
		}*/

		// Unset fields that are disabled
		foreach ($this->fields as $key => $val)
		{
			if (isset($val['enabled']) && empty($val['enabled']))
			{
				unset($this->fields[$key]);
			}
		}

		// Translate some data of arrayofkeyval
		if (is_object($langs))
		{
			foreach ($this->fields as $key => $val)
			{
				if (is_array($val['arrayofkeyval']))
				{
					foreach ($val['arrayofkeyval'] as $key2 => $val2)
					{
						$this->fields[$key]['arrayofkeyval'][$key2] = $langs->trans($val2);
					}
				}
			}
		}

		//Chagement du dictionnaire duration
		$sql = 'SELECT c.rowid, c.code, c.label, c.active';
        $sql.= ' FROM '.MAIN_DB_PREFIX.'c_funding_duration as c';
        $sql.= ' WHERE c.active = 1';
        $sql.= ' ORDER BY c.label ASC';

        $resql = $db->query($sql);
        if ($resql)
        {
            $num = $db->num_rows($resql);
            if ($num > 0)
            {
                $arrayofkeyval = array();
                $i = 0;
                while ($i < $num)
                {
                    $obj = $db->fetch_object($resql);
                    $arrayofkeyval[$obj->code] = $obj->label;
                    $i = $i +1;
                }
                $this->fields['fk_duration']['arrayofkeyval'] = $arrayofkeyval;
            }
            $db->free($resql);
        }
        else
        {
            $this->errors[] = 'Error '.$this->db->lasterror();
			dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);
			return -1;
        }
		
		//Chagement du dictionnaire scale
		$sql = 'SELECT c.rowid, c.code, c.label, c.active';
        $sql.= ' FROM '.MAIN_DB_PREFIX.'c_funding_scale as c';
        $sql.= ' WHERE c.active = 1';
        $sql.= ' ORDER BY c.label ASC';

        $resql = $db->query($sql);
        if ($resql)
        {
            $num = $db->num_rows($resql);
            if ($num > 0)
            {
                $arrayofkeyval = array();
                $i = 0;
                while ($i < $num)
                {
                    $obj = $db->fetch_object($resql);
                    $arrayofkeyval[$obj->code] = $obj->label;
                    $i = $i +1;
                }
                $this->fields['fk_scale']['arrayofkeyval'] = $arrayofkeyval;
            }
            $db->free($resql);
        }
        else
        {
            $this->errors[] = 'Error '.$this->db->lasterror();
			dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);
			return -1;
        }
	}

	/**
	 * Create ref object
	 *
	 * @param  
	 * @param  
	 * @return ref
	 */
	public function createRef()
	{
		global $conf, $db;

		$dispo = -1;
		$num = $conf->global->FUNDING_NUM_FUND;
		if ($num >= 0)
		{
			while ($dispo != 0)
			{
				$num = $num + 1;
				$num = str_pad($num, 5, "0", STR_PAD_LEFT);
				$sql = "UPDATE llx_const SET value =".$num." WHERE name = \"FUNDING_NUM_FUND\"";
				$resqlupdate = $this->db->query($sql);
				
				$ref = "FUND-" . $num;
				
				$dispo = $this->verif_dispo($ref);
			}
			if($dispo == 0)
			{
				return $ref;
			}
		}
	}

	/**
	 * Vérifie la dispo ref object
	 *
	 * @param  
	 * @param  
	 * @return 0 = ok or -1 = nok
	 */
	public function verif_dispo($ref)
	{
		global $conf, $db;
		
		$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."funding_funding";
		$sql .= " WHERE ref = '".$ref."'";
		$resql = $this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql) == 0)
			{
				return 0;
			}
			else
			{
				return -1;
			}
			$db->free($resql);
		}
		else
		{
			$this->errors[] = 'Error '.$this->db->lasterror();
			dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);
			return -1;
		}
	}

	/**
	 * Récupére les commerciaux du tier
	 *
	 * @param  id thirdparty
	 * @param  
	 * @return $idcomm = ok or -1 = nok
	 */
	public function comm_tiers($socid)
	{
		global $conf, $db;

		$sql = "SELECT * FROM ".MAIN_DB_PREFIX."societe_commerciaux";
		$sql.= " WHERE fk_soc = ".$socid;
		$sql.= " ORDER BY rowid";
		$resql = $db->query($sql);
		if ($resql)
		{
			$row = $db->fetch_row($resql);
			$idcommercial = $row[2];
			return $idcommercial;
		}
		else
		{
			$this->errors[] = 'Error '.$this->db->lasterror();
			dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);
			return -1;
		}
	}
	
	/**
	 * Récupére les info document
	 *
	 * @param  id document
	 * @param  type document PROPAL ORDER
	 * @return $document = ok or -1 = nok
	 */
	public function info_doc($iddoc, $typedoc)
	{
		global $conf, $db;

		if ($typedoc == 'propal')$sql = "SELECT * FROM ".MAIN_DB_PREFIX."propal";
		if ($typedoc == 'order')$sql = "SELECT * FROM ".MAIN_DB_PREFIX."commande";
		$sql.= " WHERE rowid = ".$iddoc;
		$resql = $db->query($sql);
		if ($resql)
		{
			$document = $db->fetch_object($resql);
			return $document;
		}
		else
		{
			$this->errors[] = 'Error '.$this->db->lasterror();
			dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);
			return -1;
		}
	}

	/**
	 * Récupére le coef corespondant
	 *
	 * @param  
	 * @param  
	 * @param  
	 * @return $coef = ok or -1 = nok
	 */
	public function coef($total, $duration, $scale, $org)
	{
		global $conf, $db;

		$sql = "SELECT * FROM ".MAIN_DB_PREFIX.'funding_coefficient as c';
        $sql.= ' WHERE c.status = 1';
		$sql.= ' AND c.fk_org = '.$org;
		$sql.= ' AND c.fk_duration = '.$duration;
		$sql.= ' AND c.fk_scale = '.$scale;
		$sql.= ' AND c.amount_of <= '.$total;
		$sql.= ' AND c.amount_to >= '.$total;
		$resql = $db->query($sql);
		if ($resql)
		{
			$obj = $db->fetch_object($resql);
			return $obj->coef;
		}
		else
		{
			$this->errors[] = 'Error '.$this->db->lasterror();
			dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);
			return -1;
		}
	}

	/**
	 * Create object into database
	 *
	 * @param  User $user      User that creates
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @param  int id du document
	 * @param  alpha type de document
	 * @return int             <0 if KO, Id of created object if OK
	 */
	public function create(User $user, $notrigger = false)
	{
		$typedoc 	= GETPOST('typedoc', 'alpha');
		$iddoc 		= GETPOST('iddoc', 'int');
		//Initialise les information obligatoire non editable
		//Document
		if ($iddoc && $typedoc){
			$document			= $this->info_doc($iddoc,$typedoc);
			if ($document->fk_statut == 1){
				$this->fk_soc		= $document->fk_soc;
				$this->amount		= $document->total_ht;
				$this->amount_total	= empty($this->amount_maint) ? $document->total_ht : $document->total_ht + $this->amount_maint;
				$coef				= $this->coef($this->amount_total, $this->fk_duration, $this->fk_scale, $this->fk_org);
				if ($coef > 0){
					$this->coef			= $coef;
					$this->amount_rent	= $this->amount_total * $coef / 100;
					if ($document->date_livraison){
						$this->date_delivery= $document->date_livraison;
						//Ajoute la durée à la date de livraison pour avoir la date de fin
						$this->date_end		= date('Y-m-d', strtotime('+'.$this->fk_duration.' month',strtotime($document->date_livraison)));
					}
					if ($typedoc == 'propal') $this->fk_propal	= $iddoc;
					if ($typedoc == 'order') $this->fk_order	= $iddoc;
					//Commercial
					$idcomm				= $this->comm_tiers($document->fk_soc);
					if($idcomm > 0){
						$this->fk_user_comm = $idcomm;
						//Génére la référence
						$ref = $this->createRef();
						if($ref)
						{
							$this->ref = $ref;
							return $this->createCommon($user, $notrigger);
						}
						else
						{
							setEventMessages('Création ref impossible', null, 'errors');
						}
					}
					else
					{
						setEventMessages('Commercial introuvable', null, 'errors');
					}
				}
				else{
					setEventMessages('Coef introuvable', null, 'errors');
				}
			}
			else{
					setEventMessages('Document non validé', null, 'errors');
			}
		}
		else
		{
			setEventMessages('Parametres manquants', null, 'errors');
		}
	}

	/**
	 * Clone an object into another one
	 *
	 * @param  	User 	$user      	User that creates
	 * @param  	int 	$fromid     Id of object to clone
	 * @return 	mixed 				New object created, <0 if KO
	 */
	/*public function createFromClone(User $user, $fromid)
	{
		global $langs, $extrafields;
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		$object = new self($this->db);

		$this->db->begin();

		// Load source object
		$result = $object->fetchCommon($fromid);
		if ($result > 0 && !empty($object->table_element_line)) $object->fetchLines();

		// get lines so they will be clone
		//foreach($this->lines as $line)
		//	$line->fetch_optionals();

		// Reset some properties
		unset($object->id);
		unset($object->fk_user_creat);
		unset($object->import_key);


		// Clear fields
		$object->ref = empty($this->fields['ref']['default']) ? "copy_of_".$object->ref : $this->fields['ref']['default'];
		$object->label = empty($this->fields['label']['default']) ? $langs->trans("CopyOf")." ".$object->label : $this->fields['label']['default'];
		$object->status = self::STATUS_DRAFT;
		// ...
		// Clear extrafields that are unique
		if (is_array($object->array_options) && count($object->array_options) > 0)
		{
			$extrafields->fetch_name_optionals_label($this->table_element);
			foreach ($object->array_options as $key => $option)
			{
				$shortkey = preg_replace('/options_/', '', $key);
				if (!empty($extrafields->attributes[$this->element]['unique'][$shortkey]))
				{
					//var_dump($key); var_dump($clonedObj->array_options[$key]); exit;
					unset($object->array_options[$key]);
				}
			}
		}

		// Create clone
		$object->context['createfromclone'] = 'createfromclone';
		$result = $object->createCommon($user);
		if ($result < 0) {
			$error++;
			$this->error = $object->error;
			$this->errors = $object->errors;
		}

		if (!$error)
		{
			// copy internal contacts
			if ($this->copy_linked_contact($object, 'internal') < 0)
			{
				$error++;
			}
		}

		if (!$error)
		{
			// copy external contacts if same company
			if (property_exists($this, 'socid') && $this->socid == $object->socid)
			{
				if ($this->copy_linked_contact($object, 'external') < 0)
					$error++;
			}
		}

		unset($object->context['createfromclone']);

		// End
		if (!$error) {
			$this->db->commit();
			return $object;
		} else {
			$this->db->rollback();
			return -1;
		}
	}*/

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id   Id object
	 * @param string $ref  Ref
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null)
	{
		$result = $this->fetchCommon($id, $ref);
		if ($result > 0 && !empty($this->table_element_line)) $this->fetchLines();
		return $result;
	}

	/**
	 * Load object lines in memory from the database
	 *
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetchLines()
	{
		$this->lines = array();

		$result = $this->fetchLinesCommon();
		return $result;
	}


	/**
	 * Load list of objects in memory from the database.
	 *
	 * @param  string      $sortorder    Sort Order
	 * @param  string      $sortfield    Sort field
	 * @param  int         $limit        limit
	 * @param  int         $offset       Offset
	 * @param  array       $filter       Filter array. Example array('field'=>'valueforlike', 'customurl'=>...)
	 * @param  string      $filtermode   Filter mode (AND or OR)
	 * @return array|int                 int <0 if KO, array of pages if OK
	 */
	public function fetchAll($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, array $filter = array(), $filtermode = 'AND')
	{
		global $conf;

		dol_syslog(__METHOD__, LOG_DEBUG);

		$records = array();

		$sql = 'SELECT ';
		$sql .= $this->getFieldList();
		$sql .= ' FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
		if (isset($this->ismultientitymanaged) && $this->ismultientitymanaged == 1) $sql .= ' WHERE t.entity IN ('.getEntity($this->table_element).')';
		else $sql .= ' WHERE 1 = 1';
		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				if ($key == 't.rowid') {
					$sqlwhere[] = $key.'='.$value;
				}
				elseif (strpos($key, 'date') !== false) {
					$sqlwhere[] = $key.' = \''.$this->db->idate($value).'\'';
				}
				elseif ($key == 'customsql') {
					$sqlwhere[] = $value;
				}
				else {
					$sqlwhere[] = $key.' LIKE \'%'.$this->db->escape($value).'%\'';
				}
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ('.implode(' '.$filtermode.' ', $sqlwhere).')';
		}

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}
		if (!empty($limit)) {
			$sql .= ' '.$this->db->plimit($limit, $offset);
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < ($limit ? min($limit, $num) : $num))
			{
				$obj = $this->db->fetch_object($resql);

				$record = new self($this->db);
				$record->setVarsFromFetchObj($obj);

				$records[$record->id] = $record;

				$i++;
			}
			$this->db->free($resql);

			return $records;
		} else {
			$this->errors[] = 'Error '.$this->db->lasterror();
			dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);

			return -1;
		}
	}

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function update(User $user, $notrigger = false)
	{
		return $this->updateCommon($user, $notrigger);
	}

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function set_study_number($user, $study_number, $notrigger = 0)
	{
			$error = 0;

			$this->db->begin();

			$sql = "UPDATE ".MAIN_DB_PREFIX."funding_funding";
			$sql .= " SET study_number = ".($study_number != '' ? "'".$study_number."'" : 'null');
			$sql .= " WHERE rowid = ".$this->id;
			
			dol_syslog(__METHOD__.' $this->id='.$this->id.', study_number='.$study_number, LOG_DEBUG);
			
			$resql = $this->db->query($sql);
			if (!$resql)
			{
				$this->errors[] = $this->db->error();
				$error++;
			}

			if (!$error)
			{
				$this->oldcopy = clone $this;
				$this->study_number = $study_number;
			}

			if (!$notrigger && empty($error))
			{
				// Call trigger
				$result = $this->call_trigger('PROPAL_MODIFY', $user);
				if ($result < 0) $error++;
				// End call triggers
			}

			if (!$error)
			{
				$this->db->commit();
				return 1;
			}
			else
			{
				foreach ($this->errors as $errmsg)
				{
					dol_syslog(__METHOD__.' Error: '.$errmsg, LOG_ERR);
					$this->error .= ($this->error ? ', '.$errmsg : $errmsg);
				}
				$this->db->rollback();
				return -1 * $error;
			}
	}

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function set_folder_number($user, $folder_number, $notrigger = 0)
	{
			$error = 0;

			$this->db->begin();

			$sql = "UPDATE ".MAIN_DB_PREFIX."funding_funding";
			$sql .= " SET folder_number = ".($folder_number != '' ? "'".$folder_number."'" : 'null');
			$sql .= " WHERE rowid = ".$this->id;

			dol_syslog(__METHOD__.' $this->id='.$this->id.', folder_number='.$folder_number, LOG_DEBUG);
			
			$resql = $this->db->query($sql);
			if (!$resql)
			{
				$this->errors[] = $this->db->error();
				$error++;
			}

			if (!$error)
			{
				$this->oldcopy = clone $this;
				$this->folder_number = $folder_number;
			}

			if (!$notrigger && empty($error))
			{
				// Call trigger
				$result = $this->call_trigger('PROPAL_MODIFY', $user);
				if ($result < 0) $error++;
				// End call triggers
			}

			if (!$error)
			{
				$this->db->commit();
				return 1;
			}
			else
			{
				foreach ($this->errors as $errmsg)
				{
					dol_syslog(__METHOD__.' Error: '.$errmsg, LOG_ERR);
					$this->error .= ($this->error ? ', '.$errmsg : $errmsg);
				}
				$this->db->rollback();
				return -1 * $error;
			}
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user       User that deletes
	 * @param bool $notrigger  false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function delete(User $user, $notrigger = false)
	{
		return $this->deleteCommon($user, $notrigger);
		//return $this->deleteCommon($user, $notrigger, 1);
	}

	/**
	 *  Delete a line of object in database
	 *
	 *	@param  User	$user       User that delete
	 *  @param	int		$idline		Id of line to delete
	 *  @param 	bool 	$notrigger  false=launch triggers after, true=disable triggers
	 *  @return int         		>0 if OK, <0 if KO
	 */
	public function deleteLine(User $user, $idline, $notrigger = false)
	{
		if ($this->status < 0)
		{
			$this->error = 'ErrorDeleteLineNotAllowedByObjectStatus';
			return -2;
		}

		return $this->deleteLineCommon($user, $idline, $notrigger);
	}


	/**
	 *	Validate object
	 *
	 *	@param		User	$user     		User making status change
	 *  @param		int		$notrigger		1=Does not execute triggers, 0= execute triggers
	 *	@return  	int						<=0 if OK, 0=Nothing done, >0 if KO
	 */
	public function validate($user, $notrigger = 0)
	{
		global $conf, $langs;

		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

		$error = 0;

		// Protection
		if ($this->status == self::STATUS_VALIDATED)
		{
			dol_syslog(get_class($this)."::validate action abandonned: already validated", LOG_WARNING);
			return 0;
		}

		/*if (! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->funding->funding->write))
		 || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->funding->funding->funding_advance->validate))))
		 {
		 $this->error='NotEnoughPermissions';
		 dol_syslog(get_class($this)."::valid ".$this->error, LOG_ERR);
		 return -1;
		 }*/

		$now = dol_now();

		$this->db->begin();

		// Define new ref
		if (!$error && (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref))) // empty should not happened, but when it occurs, the test save life
		{
			$num = $this->getNextNumRef();
		}
		else
		{
			$num = $this->ref;
		}
		$this->newref = $num;

		if (!empty($num)) {
			// Validate
			$sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
			$sql .= " SET ref = '".$this->db->escape($num)."',";
			$sql .= " status = ".self::STATUS_VALIDATED;
			if (!empty($this->fields['date_validation'])) $sql .= ", date_validation = '".$this->db->idate($now)."',";
			if (!empty($this->fields['fk_user_valid'])) $sql .= ", fk_user_valid = ".$user->id;
			$sql .= " WHERE rowid = ".$this->id;

			dol_syslog(get_class($this)."::validate()", LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (!$resql)
			{
				dol_print_error($this->db);
				$this->error = $this->db->lasterror();
				$error++;
			}

			if (!$error && !$notrigger)
			{
				// Call trigger
				$result = $this->call_trigger('FUNDING_VALIDATE', $user);
				if ($result < 0) $error++;
				// End call triggers
			}
		}

		if (!$error)
		{
			$this->oldref = $this->ref;

			// Rename directory if dir was a temporary ref
			if (preg_match('/^[\(]?PROV/i', $this->ref))
			{
				// Now we rename also files into index
				$sql = 'UPDATE '.MAIN_DB_PREFIX."ecm_files set filename = CONCAT('".$this->db->escape($this->newref)."', SUBSTR(filename, ".(strlen($this->ref) + 1).")), filepath = 'funding/".$this->db->escape($this->newref)."'";
				$sql .= " WHERE filename LIKE '".$this->db->escape($this->ref)."%' AND filepath = 'funding/".$this->db->escape($this->ref)."' and entity = ".$conf->entity;
				$resql = $this->db->query($sql);
				if (!$resql) { $error++; $this->error = $this->db->lasterror(); }

				// We rename directory ($this->ref = old ref, $num = new ref) in order not to lose the attachments
				$oldref = dol_sanitizeFileName($this->ref);
				$newref = dol_sanitizeFileName($num);
				$dirsource = $conf->funding->dir_output.'/funding/'.$oldref;
				$dirdest = $conf->funding->dir_output.'/funding/'.$newref;
				if (!$error && file_exists($dirsource))
				{
					dol_syslog(get_class($this)."::validate() rename dir ".$dirsource." into ".$dirdest);

					if (@rename($dirsource, $dirdest))
					{
						dol_syslog("Rename ok");
						// Rename docs starting with $oldref with $newref
						$listoffiles = dol_dir_list($conf->funding->dir_output.'/funding/'.$newref, 'files', 1, '^'.preg_quote($oldref, '/'));
						foreach ($listoffiles as $fileentry)
						{
							$dirsource = $fileentry['name'];
							$dirdest = preg_replace('/^'.preg_quote($oldref, '/').'/', $newref, $dirsource);
							$dirsource = $fileentry['path'].'/'.$dirsource;
							$dirdest = $fileentry['path'].'/'.$dirdest;
							@rename($dirsource, $dirdest);
						}
					}
				}
			}
		}

		// Set new ref and current status
		if (!$error)
		{
			$this->ref = $num;
			$this->status = self::STATUS_VALIDATED;
		}

		if (!$error)
		{
			$this->db->commit();
			return 1;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}


	/**
	 *	Set draft status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, >0 if OK
	 */
	public function setDraft($user, $notrigger = 0)
	{
		// Protection
		if ($this->status <= self::STATUS_DRAFT)
		{
			return 0;
		}

		/*if (! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->funding->write))
		 || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->funding->funding_advance->validate))))
		 {
		 $this->error='Permission denied';
		 return -1;
		 }*/

		return $this->setStatusCommon($user, self::STATUS_DRAFT, $notrigger, 'FUNDING_UNVALIDATE');
	}

	/**
	 *	Set cancel status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, 0=Nothing done, >0 if OK
	 */
	public function cancel($user, $notrigger = 0)
	{
		// Protection
		if ($this->status != self::STATUS_VALIDATED)
		{
			return 0;
		}

		/*if (! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->funding->write))
		 || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->funding->funding_advance->validate))))
		 {
		 $this->error='Permission denied';
		 return -1;
		 }*/

		return $this->setStatusCommon($user, self::STATUS_CANCELED, $notrigger, 'FUNDING_CLOSE');
	}

	/**
	 *	Set back to validated status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, 0=Nothing done, >0 if OK
	 */
	public function reopen($user, $notrigger = 0)
	{
		// Protection
		if ($this->status != self::STATUS_CANCELED)
		{
			return 0;
		}

		/*if (! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->funding->write))
		 || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->funding->funding_advance->validate))))
		 {
		 $this->error='Permission denied';
		 return -1;
		 }*/

		return $this->setStatusCommon($user, self::STATUS_VALIDATED, $notrigger, 'FUNDING_REOPEN');
	}

	/**
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *  @param  int     $withpicto                  Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *  @param  string  $option                     On what the link point to ('nolink', ...)
	 *  @param  int     $notooltip                  1=Disable tooltip
	 *  @param  string  $morecss                    Add more css on link
	 *  @param  int     $save_lastsearch_value      -1=Auto, 0=No save of lastsearch_values when clicking, 1=Save lastsearch_values whenclicking
	 *  @return	string                              String with URL
	 */
	public function getNomUrl($withpicto = 0, $option = '', $notooltip = 0, $morecss = '', $save_lastsearch_value = -1)
	{
		global $conf, $langs, $hookmanager;

		if (!empty($conf->dol_no_mouse_hover)) $notooltip = 1; // Force disable tooltips

		$result = '';

		$label = '<u>'.$langs->trans("Funding").'</u>';
		$label .= '<br>';
		$label .= '<b>'.$langs->trans('Ref').':</b> '.$this->ref;
		if (isset($this->status)) {
			$label .= '<br><b>'.$langs->trans("Status").":</b> ".$this->getLibStatut(5);
		}

		$url = dol_buildpath('/funding/funding_card.php', 1).'?id='.$this->id;

		if ($option != 'nolink')
		{
			// Add param to save lastsearch_values or not
			$add_save_lastsearch_values = ($save_lastsearch_value == 1 ? 1 : 0);
			if ($save_lastsearch_value == -1 && preg_match('/list\.php/', $_SERVER["PHP_SELF"])) $add_save_lastsearch_values = 1;
			if ($add_save_lastsearch_values) $url .= '&save_lastsearch_values=1';
		}

		$linkclose = '';
		if (empty($notooltip))
		{
			if (!empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
			{
				$label = $langs->trans("ShowFunding");
				$linkclose .= ' alt="'.dol_escape_htmltag($label, 1).'"';
			}
			$linkclose .= ' title="'.dol_escape_htmltag($label, 1).'"';
			$linkclose .= ' class="classfortooltip'.($morecss ? ' '.$morecss : '').'"';
		}
		else $linkclose = ($morecss ? ' class="'.$morecss.'"' : '');

		$linkstart = '<a href="'.$url.'"';
		$linkstart .= $linkclose.'>';
		$linkend = '</a>';

		$result .= $linkstart;

		if (empty($this->showphoto_on_popup)) {
			if ($withpicto) $result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), ($notooltip ? (($withpicto != 2) ? 'class="paddingright"' : '') : 'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip ? 0 : 1);
		} else {
			if ($withpicto) {
				require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

				list($class, $module) = explode('@', $this->picto);
				$upload_dir = $conf->$module->multidir_output[$conf->entity]."/$class/".dol_sanitizeFileName($this->ref);
				$filearray = dol_dir_list($upload_dir, "files");
				$filename = $filearray[0]['name'];
				if (!empty($filename)) {
					$pospoint = strpos($filearray[0]['name'], '.');

					$pathtophoto = $class.'/'.$this->ref.'/thumbs/'.substr($filename, 0, $pospoint).'_mini'.substr($filename, $pospoint);
					if (empty($conf->global->{strtoupper($module.'_'.$class).'_FORMATLISTPHOTOSASUSERS'})) {
						$result .= '<div class="floatleft inline-block valignmiddle divphotoref"><div class="photoref"><img class="photo'.$module.'" alt="No photo" border="0" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$module.'&entity='.$conf->entity.'&file='.urlencode($pathtophoto).'"></div></div>';
					}
					else {
						$result .= '<div class="floatleft inline-block valignmiddle divphotoref"><img class="photouserphoto userphoto" alt="No photo" border="0" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$module.'&entity='.$conf->entity.'&file='.urlencode($pathtophoto).'"></div>';
					}

					$result .= '</div>';
				}
				else {
					$result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), ($notooltip ? (($withpicto != 2) ? 'class="paddingright"' : '') : 'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip ? 0 : 1);
				}
			}
		}

		if ($withpicto != 2) $result .= $this->ref;

		$result .= $linkend;
		//if ($withpicto != 2) $result.=(($addlabel && $this->label) ? $sep . dol_trunc($this->label, ($addlabel > 1 ? $addlabel : 0)) : '');

		global $action, $hookmanager;
		$hookmanager->initHooks(array('fundingdao'));
		$parameters = array('id'=>$this->id, 'getnomurl'=>$result);
		$reshook = $hookmanager->executeHooks('getNomUrl', $parameters, $this, $action); // Note that $action and $object may have been modified by some hooks
		if ($reshook > 0) $result = $hookmanager->resPrint;
		else $result .= $hookmanager->resPrint;

		return $result;
	}

	/**
	 *  Return label of the status
	 *
	 *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return	string 			       Label of status
	 */
	public function getLibStatut($mode = 0)
	{
		return $this->LibStatut($this->status, $mode);
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Return the status
	 *
	 *  @param	int		$status        Id status
	 *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return string 			       Label of status
	 */
	public function LibStatut($status, $mode = 0)
	{
		// phpcs:enable
		if (empty($this->labelStatus) || empty($this->labelStatusShort))
		{
			global $langs;
			//$langs->load("funding");
			$this->labelStatus[self::STATUS_DRAFT] = $langs->trans('Draft');
			$this->labelStatus[self::STATUS_VALIDATED] = $langs->trans('Enabled');
			$this->labelStatus[self::STATUS_CANCELED] = $langs->trans('Disabled');
			$this->labelStatusShort[self::STATUS_DRAFT] = $langs->trans('Draft');
			$this->labelStatusShort[self::STATUS_VALIDATED] = $langs->trans('Enabled');
			$this->labelStatusShort[self::STATUS_CANCELED] = $langs->trans('Disabled');
		}

		$statusType = 'status'.$status;
		//if ($status == self::STATUS_VALIDATED) $statusType = 'status1';
		if ($status == self::STATUS_CANCELED) $statusType = 'status6';

		return dolGetStatus($this->labelStatus[$status], $this->labelStatusShort[$status], '', $statusType, $mode);
	}

	/**
	 *	Load the info information in the object
	 *
	 *	@param  int		$id       Id of object
	 *	@return	void
	 */
	public function info($id)
	{
		$sql = 'SELECT rowid, date_creation as datec, tms as datem,';
		$sql .= ' fk_user_creat, fk_user_modif';
		$sql .= ' FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
		$sql .= ' WHERE t.rowid = '.$id;
		$result = $this->db->query($sql);
		if ($result)
		{
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);
				$this->id = $obj->rowid;
				if ($obj->fk_user_author)
				{
					$cuser = new User($this->db);
					$cuser->fetch($obj->fk_user_author);
					$this->user_creation = $cuser;
				}

				if ($obj->fk_user_valid)
				{
					$vuser = new User($this->db);
					$vuser->fetch($obj->fk_user_valid);
					$this->user_validation = $vuser;
				}

				if ($obj->fk_user_cloture)
				{
					$cluser = new User($this->db);
					$cluser->fetch($obj->fk_user_cloture);
					$this->user_cloture = $cluser;
				}

				$this->date_creation     = $this->db->jdate($obj->datec);
				$this->date_modification = $this->db->jdate($obj->datem);
				$this->date_validation   = $this->db->jdate($obj->datev);
			}

			$this->db->free($result);
		}
		else
		{
			dol_print_error($this->db);
		}
	}

	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen()
	{
		$this->initAsSpecimenCommon();
	}

	/**
	 * 	Create an array of lines
	 *
	 * 	@return array|int		array of lines if OK, <0 if KO
	 */
	public function getLinesArray()
	{
		$this->lines = array();

		$objectline = new FundingLine($this->db);
		$result = $objectline->fetchAll('ASC', 'position', 0, 0, array('customsql'=>'fk_funding = '.$this->id));

		if (is_numeric($result))
		{
			$this->error = $this->error;
			$this->errors = $this->errors;
			return $result;
		}
		else
		{
			$this->lines = $result;
			return $this->lines;
		}
	}

	/**
	 *  Returns the reference to the following non used object depending on the active numbering module.
	 *
	 *  @return string      		Object free reference
	 */
	public function getNextNumRef()
	{
		global $langs, $conf;
		$langs->load("funding@funding");

		if (empty($conf->global->FUNDING_FUNDING_ADDON)) {
			$conf->global->FUNDING_FUNDING_ADDON = 'mod_funding_standard';
		}

		if (!empty($conf->global->FUNDING_FUNDING_ADDON))
		{
			$mybool = false;

			$file = $conf->global->FUNDING_FUNDING_ADDON.".php";
			$classname = $conf->global->FUNDING_FUNDING_ADDON;

			// Include file with class
			$dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);
			foreach ($dirmodels as $reldir)
			{
				$dir = dol_buildpath($reldir."core/modules/funding/");

				// Load file with numbering class (if found)
				$mybool |= @include_once $dir.$file;
			}

			if ($mybool === false)
			{
				dol_print_error('', "Failed to include file ".$file);
				return '';
			}

			if (class_exists($classname)) {
				$obj = new $classname();
				$numref = $obj->getNextValue($this);

				if ($numref != '' && $numref != '-1')
				{
					return $numref;
				}
				else
				{
					$this->error = $obj->error;
					//dol_print_error($this->db,get_class($this)."::getNextNumRef ".$obj->error);
					return "";
				}
			} else {
				print $langs->trans("Error")." ".$langs->trans("ClassNotFound").' '.$classname;
				return "";
			}
		}
		else
		{
			print $langs->trans("ErrorNumberingModuleNotSetup", $this->element);
			return "";
		}
	}

	/**
	 *  Create a document onto disk according to template module.
	 *
	 *  @param	    string		$modele			Force template to use ('' to not force)
	 *  @param		Translate	$outputlangs	objet lang a utiliser pour traduction
	 *  @param      int			$hidedetails    Hide details of lines
	 *  @param      int			$hidedesc       Hide description
	 *  @param      int			$hideref        Hide ref
	 *  @param      null|array  $moreparams     Array to provide more information
	 *  @return     int         				0 if KO, 1 if OK
	 */
	public function generateDocument($modele, $outputlangs, $hidedetails = 0, $hidedesc = 0, $hideref = 0, $moreparams = null)
	{
		global $conf, $langs;

		$result = 0;
		$includedocgeneration = 1;

		$langs->load("funding@funding");

		if (!dol_strlen($modele)) {
			$modele = 'standard_funding';

			if ($this->modelpdf) {
				$modele = $this->modelpdf;
			} elseif (!empty($conf->global->FUNDING_ADDON_PDF)) {
				$modele = $conf->global->FUNDING_ADDON_PDF;
			}
		}

		$modelpath = "core/modules/funding/doc/";

		if ($includedocgeneration) {
			$result = $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref, $moreparams);
		}

		return $result;
	}

	/**
	 * Action executed by scheduler
	 * CAN BE A CRON TASK. In such a case, parameters come from the schedule job setup field 'Parameters'
	 * Use public function doScheduledJob($param1, $param2, ...) to get parameters
	 *
	 * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
	 */
	public function doScheduledJob()
	{
		global $conf, $langs;

		//$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_mydedicatedlofile.log';

		$error = 0;
		$this->output = '';
		$this->error = '';

		dol_syslog(__METHOD__, LOG_DEBUG);

		$now = dol_now();

		$this->db->begin();

		// ...

		$this->db->commit();

		return $error;
	}
}

/**
 * Class FundingLine. You can also remove this and generate a CRUD class for lines objects.
 */
class FundingLine
{
	// To complete with content of an object FundingLine
	// We should have a field rowid, fk_funding and position
}
