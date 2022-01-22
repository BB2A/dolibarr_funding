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
    const STATUS_UPDATE = 2;
    //const STATUS_SENDORG = 3;
    const STATUS_ACCEPT = 4;
    const STATUS_DENIED = 5;
    const STATUS_RUNNING = 6;
    const STATUS_END = 7;
    const STATUS_CANCELED = 8;

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
        'ref' => array('type'=>'varchar(128)', 'label'=>'Ref.', 'enabled'=>'1', 'position'=>2, 'notnull'=>1, 'visible'=>4, 'noteditable'=>'1', 'default'=>'(PROV)', 'index'=>1, 'searchall'=>1, 'showoncombobox'=>'1', 'comment'=>"Reference of object"),
        'study_number' => array('type'=>'varchar(128)', 'label'=>'StudyNumber', 'enabled'=>'1', 'position'=>3, 'notnull'=>0, 'visible'=>2, 'index'=>1, 'searchall'=>1, 'help'=>"Help_study_number", 'showoncombobox'=>'1',),
        'folder_number' => array('type'=>'varchar(128)', 'label'=>'FolderNumber', 'enabled'=>'1', 'position'=>4, 'notnull'=>0, 'visible'=>2, 'index'=>1, 'searchall'=>1, 'help'=>"Help_folder_number", 'showoncombobox'=>'1',),
        'amount' => array('type'=>'price', 'label'=>'Amount', 'enabled'=>'1', 'position'=>5, 'notnull'=>0, 'visible'=>5, 'noteditable'=>'1', 'default'=>'null', 'isameasure'=>'1', 'help'=>"Help_amount",),
        'amount_maint' => array('type'=>'price', 'label'=>'AmountMaint', 'enabled'=>'1', 'position'=>6, 'notnull'=>0, 'visible'=>1, 'default'=>'null', 'isameasure'=>'1', 'help'=>"Help_amount_maint",),
        'amount_total' => array('type'=>'price', 'label'=>'AmountTotal', 'enabled'=>'1', 'position'=>7, 'notnull'=>0, 'visible'=>5, 'noteditable'=>'1', 'default'=>'null', 'isameasure'=>'1', 'help'=>"Help_amount_total",),
        'fk_duration' => array('type'=>'integer', 'label'=>'Duration', 'enabled'=>'1', 'position'=>8, 'notnull'=>1, 'visible'=>-1, 'foreignkey'=>'c_funding_duration.rowid', 'help'=>"Help_duration", 'arrayofkeyval'=>array('1'=>'12 Mois', '2'=>'24 Mois', '3'=>'36 Mois', '4'=>'48 Mois', '5'=>'60 Mois'),),
        'coef' => array('type'=>'real', 'label'=>'Coef', 'enabled'=>'1', 'position'=>9, 'notnull'=>0, 'visible'=>-5, 'noteditable'=>'1', 'default'=>'0', 'isameasure'=>'1', 'css'=>'maxwidth75imp', 'help'=>"Help_coef",),
        'fk_scale' => array('type'=>'integer', 'label'=>'Scale', 'enabled'=>'1', 'position'=>10, 'notnull'=>1, 'visible'=>-1, 'foreignkey'=>'c_funding_scale.rowid', 'help'=>"Help_Scale", 'arrayofkeyval'=>array('1'=>'1 - Standard', '2'=>'2 - Création'),),
        'amount_rent' => array('type'=>'price', 'label'=>'Rent', 'enabled'=>'1', 'position'=>11, 'notnull'=>0, 'visible'=>5, 'noteditable'=>'1', 'default'=>'null', 'isameasure'=>'1', 'help'=>"Help_amount_rent",),
        'amount_rent_edit' => array('type'=>'price', 'label'=>'RentEdit', 'enabled'=>'1', 'position'=>12, 'notnull'=>0, 'visible'=>5, 'default'=>'null', 'isameasure'=>'1', 'help'=>"Help_amount_rent_edit",),
        'date_delivery' => array('type'=>'date', 'label'=>'DateDelivery', 'enabled'=>'1', 'position'=>13, 'notnull'=>0, 'visible'=>5, 'noteditable'=>'1', 'searchall'=>1, 'help'=>"Help_date_delivery",),
        'date_signature' => array('type'=>'date', 'label'=>'DateSignature', 'enabled'=>'1', 'position'=>14, 'notnull'=>0, 'visible'=>-4, 'noteditable'=>'0', 'searchall'=>1, 'help'=>"Help_date_signature",),
        'date_end' => array('type'=>'date', 'label'=>'DateEnd', 'enabled'=>'1', 'position'=>15, 'notnull'=>16, 'visible'=>5, 'noteditable'=>'1', 'help'=>"Help_date_end",),
        'fk_funding_type' => array('type'=>'smallint', 'label'=>'TypeFunding', 'enabled'=>'1', 'position'=>17, 'notnull'=>1, 'visible'=>-1, 'foreignkey'=>'c_funding_type.rowid', 'arrayofkeyval'=>array('2'=>'Crédit bail', '1'=>'Location'),),
        'redemption' => array('type'=>'smallint', 'label'=>'Redemption', 'enabled'=>'1', 'position'=>18, 'notnull'=>1, 'visible'=>-1, 'arrayofkeyval'=>array('0'=>'Non', '1'=>'Oui'),),
        'redemption_number' => array('type'=>'varchar(128)', 'label'=>'RedemptionNumber', 'enabled'=>'1', 'position'=>19, 'notnull'=>0, 'visible'=>-1, 'index'=>1, 'searchall'=>1, 'help'=>"Help_redemption_number", 'showoncombobox'=>'1',),
        'retention' => array('type'=>'smallint', 'label'=>'RetentionOfGuarantee', 'enabled'=>'1', 'position'=>20, 'notnull'=>1, 'visible'=>-1, 'default'=>0, 'arrayofkeyval'=>array('0'=>'Non', '1'=>'Oui'),),
        'retention_rate' => array('type'=>'real', 'label'=>'RetentionRate', 'enabled'=>'1', 'position'=>21, 'notnull'=>0, 'visible'=>-5, 'noteditable'=>'1', 'default'=>'0', 'isameasure'=>'1', 'css'=>'maxwidth75imp', 'help'=>"Help_retention_rate",),
        'fk_org' => array('type'=>'integer:Societe:societe/class/societe.class.php::status=1 AND entity IN (__SHARED_ENTITIES__)', 'label'=>'Organization', 'enabled'=>'1', 'position'=>22, 'notnull'=>1, 'visible'=>1, 'index'=>1, 'showoncombobox'=>'1', 'help'=>"LinkToOrganization",),
        'fk_soc' => array('type'=>'integer:Societe:societe/class/societe.class.php::status=1 AND entity IN (__SHARED_ENTITIES__)', 'label'=>'ThirdParty', 'enabled'=>'1', 'position'=>23, 'notnull'=>1, 'visible'=>-2, 'noteditable'=>'1', 'index'=>1, 'showoncombobox'=>'1', 'help'=>"LinkToThirparty",),
        'fk_soc_invoice' => array('type'=>'integer:Societe:societe/class/societe.class.php::status=1 AND entity IN (__SHARED_ENTITIES__)', 'label'=>'ThirdPartyInvoice', 'enabled'=>'1', 'position'=>24, 'notnull'=>0, 'visible'=>-5, 'noteditable'=>'1', 'index'=>1, 'showoncombobox'=>'1', 'help'=>"LinkToThirpartyInvoice",),
        'fk_propal' => array('type'=>'integer:Propal:comm/propal/class/propal.class.php:', 'label'=>'Proposal', 'enabled'=>'1', 'position'=>25, 'notnull'=>3, 'visible'=>0, 'noteditable'=>'1',),
        'fk_order' => array('type'=>'integer:Commande:commande/class/commande.class.php:', 'label'=>'Order', 'enabled'=>'1', 'position'=>26, 'notnull'=>3, 'visible'=>0, 'noteditable'=>'1',),
        'fk_user_comm' => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'SalesRepresentative', 'enabled'=>'1', 'position'=>27, 'notnull'=>0, 'visible'=>-4, 'foreignkey'=>'user.rowid',),
        'description' => array('type'=>'text', 'label'=>'Description', 'enabled'=>'1', 'position'=>100, 'notnull'=>0, 'visible'=>-1,),
        'fundoc1' => array('type'=>'varchar(255)', 'label'=>'fundoc1', 'enabled'=>'1', 'position'=>101, 'notnull'=>0, 'visible'=>0,),
        'fundoc2' => array('type'=>'varchar(255)', 'label'=>'fundoc2', 'enabled'=>'1', 'position'=>102, 'notnull'=>0, 'visible'=>0,),
        'fundoc3' => array('type'=>'varchar(255)', 'label'=>'fundoc3', 'enabled'=>'1', 'position'=>103, 'notnull'=>0, 'visible'=>0,),
        'fundoc4' => array('type'=>'varchar(255)', 'label'=>'fundoc4', 'enabled'=>'1', 'position'=>104, 'notnull'=>0, 'visible'=>0,),
        'fundoc5' => array('type'=>'varchar(255)', 'label'=>'fundoc5', 'enabled'=>'1', 'position'=>104, 'notnull'=>0, 'visible'=>0,),
        'funfoldoc1' => array('type'=>'varchar(255)', 'label'=>'funfoldoc1', 'enabled'=>'1', 'position'=>110, 'notnull'=>0, 'visible'=>0,),
        'funfoldoc2' => array('type'=>'varchar(255)', 'label'=>'funfoldoc2', 'enabled'=>'1', 'position'=>111, 'notnull'=>0, 'visible'=>0,),
        'funfoldoc3' => array('type'=>'varchar(255)', 'label'=>'funfoldoc3', 'enabled'=>'1', 'position'=>112, 'notnull'=>0, 'visible'=>0,),
        'funfoldoc4' => array('type'=>'varchar(255)', 'label'=>'funfoldoc4', 'enabled'=>'1', 'position'=>113, 'notnull'=>0, 'visible'=>0,),
        'funfoldoc5' => array('type'=>'varchar(255)', 'label'=>'funfoldoc5', 'enabled'=>'1', 'position'=>114, 'notnull'=>0, 'visible'=>0,),
        'extension' => array('type'=>'smallint', 'label'=>'extension', 'enabled'=>'1', 'position'=>201, 'default'=>0, 'visible'=>0, 'arrayofkeyval'=>array('0'=>'Non', '1'=>'Oui'),),
        'note_public' => array('type'=>'html', 'label'=>'NotePublic', 'enabled'=>'1', 'position'=>400, 'notnull'=>0, 'visible'=>0,),
        'note_private' => array('type'=>'html', 'label'=>'NotePrivate', 'enabled'=>'1', 'position'=>401, 'notnull'=>0, 'visible'=>0,),
        'date_creation' => array('type'=>'datetime', 'label'=>'DateCreation', 'enabled'=>'1', 'position'=>500, 'notnull'=>1, 'visible'=>-2,),
        'tms' => array('type'=>'timestamp', 'label'=>'DateModification', 'enabled'=>'1', 'position'=>501, 'notnull'=>0, 'visible'=>-2,),
        'fk_user_creat' => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'UserAuthor', 'enabled'=>'1', 'position'=>510, 'notnull'=>1, 'visible'=>-2, 'foreignkey'=>'user.rowid', 'showoncombobox'=>'1',),
        'fk_user_modif' => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'UserModif', 'enabled'=>'1', 'position'=>511, 'notnull'=>-1, 'visible'=>-2, 'showoncombobox'=>'1',),
        'origin' => array('type'=>'varchar(128)', 'label'=>'origin', 'enabled'=>'1', 'position'=>512, 'notnull'=>1, 'visible'=>-2, 'noteditable'=>'1', 'index'=>1, 'searchall'=>1,),
        'origin_id' => array('type'=>'integer', 'label'=>'origin_id', 'enabled'=>'1', 'position'=>513, 'notnull'=>1, 'visible'=>0, 'noteditable'=>'1', 'index'=>1, 'searchall'=>1,),
        'last_main_doc' => array('type'=>'varchar(255)', 'label'=>'last_main_doc', 'enabled'=>'1', 'position'=>10, 'notnull'=>0, 'visible'=>0,),
        'import_key' => array('type'=>'varchar(14)', 'label'=>'ImportId', 'enabled'=>'1', 'position'=>1000, 'notnull'=>-1, 'visible'=>0,),
        'model_pdf' => array('type'=>'varchar(255)', 'label'=>'Model pdf', 'enabled'=>'1', 'position'=>1010, 'notnull'=>-1, 'visible'=>0,),
        'status_folder' => array('type'=>'smallint', 'label'=>'StatusFolder', 'enabled'=>'1', 'position'=>1000, 'notnull'=>0, 'visible'=>0, 'index'=>1,),
        'status' => array('type'=>'smallint', 'label'=>'Status', 'enabled'=>'1', 'position'=>1000, 'notnull'=>1, 'visible'=>2, 'default'=>'0', 'index'=>1, 'showoncombobox'=>'1', 'arrayofkeyval'=>array('0' => 'FundingStatusDraft', '1' => 'FundingStatusValidated', '2' => 'FundingStatusUpdate',/* '3' => 'FundingStatusSendOrg', */'4' => 'FundingStatusAccept', '5' => 'FundingStatusDenied', '6' => 'FundingStatusRunning', '7' => 'FundingStatusEnd', '8' => 'FundingStatusDisabled'),),
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
    public $amount_rent_edit;
    public $date_delivery;
    public $date_signature;
    public $date_end;
    public $fk_funding_type;
    public $redemption;
    public $redemption_number;
    public $retention;
    public $retention_rate;
    public $fk_org;
    public $fk_soc;
    public $fk_soc_invoice;
    public $fk_propal;
    public $fk_order;
    public $fk_user_comm;
    public $description;
    public $fundoc1;
    public $fundoc2;
    public $fundoc3;
    public $fundoc4;
    public $fundoc5;
    public $funfoldoc1;
    public $funfoldoc2;
    public $funfoldoc3;
    public $funfoldoc4;
    public $funfoldoc5;
    public $extension;
    public $note_public;
    public $note_private;
    public $date_creation;
    public $tms;
    public $fk_user_creat;
    public $fk_user_modif;
    public $origin;
    public $origin_id;
    public $last_main_doc;
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
     * @var array   List of child tables. To test if we can delete object.
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

        if (!empty($conf->global->MAIN_SHOW_TECHNICAL_ID) && isset($this->fields['rowid'])) {
            $this->fields['rowid']['visible'] = 1;
        }
        if (empty($conf->multicompany->enabled) && isset($this->fields['entity'])) {
            $this->fields['entity']['enabled'] = 0;
        }
        
        if (!empty($conf->global->FUNDING_DEFAULT_DURATION) && isset($this->fields['fk_duration'])) {
            $this->fields['fk_duration']['default'] = $conf->global->FUNDING_DEFAULT_DURATION;
        }
        if (!empty($conf->global->FUNDING_DEFAULT_SCALE) && isset($this->fields['fk_scale'])) {
            $this->fields['fk_scale']['default'] = $conf->global->FUNDING_DEFAULT_SCALE;
        }
        if (!empty($conf->global->FUNDING_DEFAULT_REDEMPTION) && isset($this->fields['redemption'])) {
            $this->fields['redemption']['default'] = $conf->global->FUNDING_DEFAULT_REDEMPTION;
        }
        
        if (!empty($conf->global->FUNDING_FILTRE_ORGANIZATION) && isset($this->fields['fk_org'])) {
            $this->fields['fk_org']['type'] = $this->fields['fk_org']['type']. "AND fk_typent=".$conf->global->FUNDING_FILTRE_ORGANIZATION;
        }
        if (!empty($conf->global->FUNDING_DEFAULT_ORGANIZATION) && isset($this->fields['fk_org'])) {
            $this->fields['fk_org']['default'] = $this->fields['fk_org']['default'] = $conf->global->FUNDING_DEFAULT_ORGANIZATION;
        }

        if (GETPOST('action', 'alpha') == 'edit') {
            $this->fields['amount_rent_edit']['visible'] = 1 & $this->fields['amount_rent']['visible'] = 1;
        }


        // Unset fields that are disabled
        foreach ($this->fields as $key => $val) {
            if (isset($val['enabled']) && empty($val['enabled'])) {
                unset($this->fields[$key]);
            }
        }

        // Translate some data of arrayofkeyval
        if (is_object($langs)) {
            foreach ($this->fields as $key => $val) {
                if (is_array($val['arrayofkeyval'])) {
                    foreach ($val['arrayofkeyval'] as $key2 => $val2) {
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
        if ($resql) {
            $num = $db->num_rows($resql);
            if ($num > 0) {
                $arrayofkeyval = array();
                $i = 0;
                while ($i < $num) {
                    $obj = $db->fetch_object($resql);
                    $arrayofkeyval[$obj->rowid] = $obj->label;
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
                    $arrayofkeyval[$obj->rowid] = $obj->label;
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

		//Chagement du dictionnaire type
		$sql = 'SELECT c.rowid, c.code, c.label, c.active';
        $sql.= ' FROM '.MAIN_DB_PREFIX.'c_funding_type as c';
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
                    $arrayofkeyval[$obj->rowid] = $obj->label;
                    $i = $i +1;
                }
                $this->fields['fk_funding_type']['arrayofkeyval'] = $arrayofkeyval;
            }
            $db->free($resql);
        } else {
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
        if ($resql) {
            $row = $db->fetch_row($resql);
            $idcommercial = $row[2];
            return $idcommercial;
        } else {
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

        $document = '';

        if ($typedoc == 'propal') {
            $document = new propal($db);
        }
        if ($typedoc == 'order') {
            $document = new commande($db);
        }

        $document->fetch($iddoc);

        if ($document) {
            return $document;
        } else {
            $this->errors[] = 'Error '.$this->db->lasterror();
            dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);
            return -1;
        }
    }

    /**
     * Récupére le coef corespondant
     *
     * @param  Total à fiancer
     * @param  La durée du fiancement
     * @param  Le béreme
     * @param  Organisme de financement
     * @return $coef = ok or -1 = nok
     */
    public function coef($total, $duration, $scale, $org)
    {
        global $conf, $db;

        //Récupére la durée
        //$duration = $this->fetch_duration($duration);
        //Récupére scale
        //$scale = $this->fetch_scale($scale);
        $sql = "SELECT * FROM ".MAIN_DB_PREFIX.'funding_coefficient as c';
        $sql.= ' WHERE c.status = 1';
        $sql.= ' AND c.fk_org = '.$org;
        $sql.= ' AND c.fk_duration = '.$duration;
        $sql.= ' AND c.fk_scale = '.$scale;
        $sql.= ' AND c.amount_of <= '.$total;
        $sql.= ' AND c.amount_to >= '.$total;
        $resql = $db->query($sql);

        if ($resql) {
            $obj = $db->fetch_object($resql);
            return $obj->coef;
        } else {
            $this->errors[] = 'Error '.$this->db->lasterror();
            dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);
            return -1;
        }
    }
    
    /**
     * Récupére le taux retenue de grantie
     *
     * @param  organisme de financement
     * @return $rate = ok or -1 = nok
     */
    public function retention_rate($org)
    {
        global $conf, $db;

        $sql = "SELECT * FROM ".MAIN_DB_PREFIX.'funding_retention as c';
        $sql.= ' WHERE c.status = 1';
        $sql.= ' AND c.fk_soc = '.$org;
        $resql = $db->query($sql);

        if ($resql) {
            $obj = $db->fetch_object($resql);
            return $obj->rate;
        } else {
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
        global $langs;
        $now        = dol_now();
        $typedoc    = GETPOST('typedoc', 'alpha');
        $iddoc      = GETPOST('iddoc', 'int');

        $coef = -1;
        $document = -1;
        $idcomm = -1;
        $duration = -1;

        $this->ref = $this->getNextNumRef();

        //Initialise les information obligatoire non editable
        //Document
        if ($iddoc && $typedoc) {
            $this->origin = $typedoc;
            $this->origin_id = $iddoc;
            $document = $this->info_doc($iddoc, $typedoc);
            if ($document > 0) {
                //Création imposible si document non validé ou déja livré facturé
                if (is_object($document) && $document->statut >= 1 && $document->statut != 3) {
                    //Récupére si une adresse facturation différente
                    $socpeopleinvoice   = $document->getIdContact('external', 'BILLING');
                    if ($socpeopleinvoice) {
                        $socinvoice         = $this->fetch_socinvoice($socpeopleinvoice[0]);
                        if ($socinvoice > -1) {
                            $this->fk_soc_invoice = $socinvoice;
                        } else {
                            setEventMessages($langs->trans("socinvoicenok".'-'.$socpeopleinvoice[0]), null, 'errors');
                        }
                    } else {
                        $this->fk_soc_invoice = '';
                    }
                    
                    $this->fk_soc       = $document->socid;
                    $this->amount       = $document->total_ht;
                    $this->amount_total = empty($this->amount_maint) ? $document->total_ht : $document->total_ht + $this->amount_maint;
                    if ($this->retention == 1) {
                        $this->retention_rate = $this->retention_rate($this->fk_org);
                        $this->amount_total = price2num($this->amount_total / (1-($this->retention_rate/100)), 'MT');
                    }
                    $coef               = $this->coef($this->amount_total, $this->fk_duration, $this->fk_scale, $this->fk_org);
                    if ($coef > 0) {
                        $this->coef         = $coef;
                        $this->amount_rent  = price2num($this->amount_total * $coef / 100, 'MT');
                        $this->amount_rent_edit = $this->amount_rent;
                        
                        //Information sur date de livraison date de fin
                        $this->date_delivery = $document->date_livraison;

                        // Voir si delete
                        if ($typedoc == 'propal') {
                            $this->fk_propal  = $iddoc;
                        }
                        if ($typedoc == 'order') {
                            $this->fk_order    = $iddoc;
                        }
                        // Commercial
                        $idcomm             = $this->comm_tiers($document->socid);
                        if ($idcomm > 0) {
                            $this->fk_user_comm = $idcomm;
                            $this->status = self::STATUS_DRAFT;
                            $create = $this->createCommon($user, $notrigger);
                            if ($create > 0) {
                                // Add object linked
                                $ret = $this->add_object_linked($typedoc, $iddoc);
                                if (!$ret) {
                                    $this->error = $this->db->lasterror();
                                    $error++;
                                }
                                // Créate ref
                                // Probléme récupére pas les info du document avec le update a la suite
                                //$this->update($user, true); //No trigger
                                //$this->ref = "(PROV".$this->id.")";
                                $this->date_creation = $now;
                                //$this->validate($user);
                            }
                            return $create;
                        } else {
                            setEventMessages($langs->trans("commnok"), null, 'errors');
                        }
                    } else {
                        setEventMessages($langs->trans("coefnok"), null, 'errors');
                    }
                } else {
                    setEventMessages($langs->trans("docnotvalidateorclosed"), null, 'errors');
                }
            } else {
                setEventMessages($langs->trans("nodoc"), null, 'errors');
            }
        } else {
            setEventMessages($langs->trans("paramnok"), null, 'errors');
        }
    }

    /**
     * Clone an object into another one
     *
     * @param   User    $user       User that creates
     * @param   int     $fromid     Id of object to clone
     * @return  mixed               New object created, <0 if KO
     */
    public function createFromClone(User $user, $fromid, $origin, $origin_id)
    {
        global $conf, $db, $langs, $extrafields;
        $error = 0;

        dol_syslog(__METHOD__, LOG_DEBUG);

        $object = new self($this->db);

        $this->db->begin();

        // Load source object
        $result = $object->fetchCommon($fromid);
        $oldref = $object->ref;
        if ($result > 0 && !empty($object->table_element_line)) {
            $object->fetchLines();
        }

        // get lines so they will be clone
        //foreach($this->lines as $line)
        //  $line->fetch_optionals();

        // Reset some properties
        unset($object->id);
        unset($object->fk_user_creat);
        unset($object->import_key);

        // Clear fields
        if (property_exists($object, 'ref')) {
            $object->ref = empty($this->fields['ref']['default']) ? "Copy_Of_".$object->ref : $this->fields['ref']['default'];
        }
        //if (property_exists($object, 'label')) $object->label = empty($this->fields['label']['default']) ? $langs->trans("CopyOf")." ".$object->label : $this->fields['label']['default'];
        if (property_exists($object, 'origin')) {
            $object->origin = $origin;
        }
        if (property_exists($object, 'origin_id')) {
            $object->origin_id = $origin_id;
        }
        if (property_exists($object, 'fk_order')) {
            $object->fk_order = $origin_id;
        }
        if (property_exists($object, 'status')) {
            $object->status = self::STATUS_DRAFT;
        }
        if (property_exists($object, 'date_creation')) {
            $object->date_creation = dol_now();
        }
        if (property_exists($object, 'date_modification')) {
            $object->date_modification = null;
        }
        // ...
        // Clear extrafields that are unique
        if (is_array($object->array_options) && count($object->array_options) > 0) {
            $extrafields->fetch_name_optionals_label($this->table_element);
            foreach ($object->array_options as $key => $option) {
                $shortkey = preg_replace('/options_/', '', $key);
                if (!empty($extrafields->attributes[$this->table_element]['unique'][$shortkey])) {
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
        $this->fetch($result);
        if (!$error) {
            // Add object linked
            if ($this->add_object_linked($origin, $origin_id) < 0) {
                $error++;
            }
        }

        if (!$error) {
            // copy internal contacts
            if ($this->copy_linked_contact($object, 'internal') < 0) {
                $error++;
            }
        }

        if (!$error) {
            // copy external contacts if same company
            if (property_exists($this, 'socid') && $this->socid == $object->socid) {
                if ($this->copy_linked_contact($object, 'external') < 0) {
                    $error++;
                }
            }
        }
        if (!$error) {
            // Validate
            $this->date_creation = $now;
            if ($object->validate($user, $notrigger) < 0) {
                $error++;
            }
        }
        unset($object->context['createfromclone']);

        if (!$error) {
            // Copy documents
            $oldref = dol_sanitizeFileName($oldref);
            $newref = dol_sanitizeFileName($object->ref);

            $dirsource = $conf->funding->multidir_output[$object->entity ? $object->entity : $conf->entity]."/".dol_sanitizeFileName($oldref).'/';
            $dirdest = $conf->funding->multidir_output[$object->entity ? $object->entity : $conf->entity]."/".dol_sanitizeFileName($newref).'/';

            $filesmove = array(
            'fundoc1'=>$object->fundoc1,
            'fundoc2'=>$object->fundoc2,
            'fundoc3'=>$object->fundoc3,
            'fundoc4'=>$object->fundoc4,
            'funfoldoc1'=>$object->funfoldoc1,
            'funfoldoc2'=>$object->funfoldoc2,
            'funfoldoc3'=>$object->funfoldoc3,
            'funfoldoc4'=>$object->funfoldoc4,
            'funfoldoc5'=>$object->funfoldoc5
            );

            if (!(is_dir($dirdest))) {
                mkdir($dirdest);
            }

            foreach ($filesmove as $key => $file) {
                if (!empty($file)) {
                    if (!copy($dirsource.$file, $dirdest.$file)) {
                        setEventMessages($langs->trans("filesmovenok").' '.$file, null, 'errors');
                    }

                    $newnamefile = str_replace($oldref, $newref, $file);

                    if (file_exists($dirdest.$file)) {
                        $rename = rename($dirdest.$file, $dirdest.$newnamefile);
                    }

                    if ($rename) {
                        $sql = "UPDATE ".MAIN_DB_PREFIX.$object->table_element." SET ".$key." = '".$newnamefile."' WHERE rowid = ".$object->id;
                        $resql = $db->query($sql);
                        dol_syslog(__METHOD__." $object->id=".$object->id.", '".$doc."'=''", LOG_DEBUG);
                    }
                }
            }
        }


        // End
        if (!$error) {
            $this->db->commit();
            return $object;
        } else {
            $this->db->rollback();
            return -1;
        }
    }
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
        //if ($result > 0 && !empty($this->table_element_line)) $this->fetchLines();

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
        if (isset($this->ismultientitymanaged) && $this->ismultientitymanaged == 1) {
            $sql .= ' WHERE t.entity IN ('.getEntity($this->table_element).')';
        } else {
            $sql .= ' WHERE 1 = 1';
        }
        // Manage filter
        $sqlwhere = array();
        if (count($filter) > 0) {
            foreach ($filter as $key => $value) {
                if ($key == 't.rowid') {
                    $sqlwhere[] = $key.'='.$value;
                } elseif (strpos($key, 'date') !== false) {
                    $sqlwhere[] = $key.' = \''.$this->db->idate($value).'\'';
                } elseif ($key == 'customsql') {
                    $sqlwhere[] = $value;
                } else {
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
            while ($i < ($limit ? min($limit, $num) : $num)) {
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
     * Récupére la durée
     *
     * @param  id durée
     * @param
     * @return $duration = ok or -1 = nok
     */
    public function fetch_duration($duration)
    {
        global $conf, $db;

        $sql = "SELECT * FROM ".MAIN_DB_PREFIX."c_funding_duration";
        $sql.= " WHERE rowid = ".$duration;
        $resql = $db->query($sql);
        if ($resql) {
            return $db->fetch_object($resql);
        } else {
            $this->errors[] = 'Error '.$this->db->lasterror();
            dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);
            return -1;
        }
    }

    /**
     * Récupére scale
     *
     * @param  id scale
     * @param
     * @return $scale = ok or -1 = nok
     */
    public function fetch_scale($scale)
    {
        global $conf, $db;

        $sql = "SELECT * FROM ".MAIN_DB_PREFIX."c_funding_scale";
        $sql.= " WHERE rowid = ".$scale;
        $resql = $db->query($sql);
        if ($resql) {
            return $db->fetch_object($resql);
        } else {
            $this->errors[] = 'Error '.$this->db->lasterror();
            dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);
            return -1;
        }
    }
    
    /**
     * Récupére type funding
     *
     * @param  id type
     * @return $idsocinvoic = ok or -1 = nok
     */
    public function fetch_type($type)
    {
        global $conf, $db;

        $sql = "SELECT * FROM ".MAIN_DB_PREFIX."c_funding_type";
        $sql.= " WHERE rowid = ".$type;
        $resql = $db->query($sql);
        if ($resql) {
            return $db->fetch_object($resql);
        } else {
            $this->errors[] = 'Error '.$this->db->lasterror();
            dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);
            return -1;
        }
    }
    
    /**
     * Récupére soc invoice
     *
     * @param  id contact invoice
     * @param
     * @return $idsocinvoic = ok or -1 = nok
     */
    public function fetch_socinvoice($socpeopleinvoice)
    {
        global $conf, $db;

        $sql = "SELECT fk_soc FROM ".MAIN_DB_PREFIX."socpeople";
        $sql.= " WHERE rowid = ".$socpeopleinvoice;
        $resql = $db->query($sql);
        if ($resql) {
            $row = $db->fetch_object($resql);
            $socinvoice = $row->fk_soc;
            return $socinvoice;
        } else {
            $this->errors[] = 'Error '.$this->db->lasterror();
            dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);
            return -1;
        }
    }

    /**
     * Récupére org
     *
     * @param  id contact invoice
     * @param
     * @return $idsocinvoic = ok or -1 = nok
    */
    public function fetch_soc($soc)
    {
        global $conf, $db;

        $sql = "SELECT * FROM ".MAIN_DB_PREFIX."societe";
        $sql.= " WHERE rowid = ".$soc;
        $resql = $db->query($sql);
        if ($resql) {
            return $db->fetch_object($resql);
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
        global $langs;
        
        $coef = -1;
        $document = '';
        $idcomm = -1;
        $duration = -1;
        $typedoc = $this->origin;
        $iddoc = $this->origin_id;
        
        //Document
        if ($iddoc && $typedoc) {
            $document = $this->info_doc($iddoc, $typedoc);
            
            if (is_object($document) && $document->statut > 0) {
                //Récupére si une adresse facturation différente
                $socpeopleinvoice   = $document->getIdContact('external', 'BILLING');
                if ($socpeopleinvoice) {
                    $socinvoice         = $this->fetch_socinvoice($socpeopleinvoice[0]);
                    if ($socinvoice > -1) {
                        $this->fk_soc_invoice = $socinvoice;
                    } else {
                        setEventMessages($langs->trans("socinvoicenok".'-'.$socpeopleinvoice[0]), null, 'errors');
                    }
                } else {
                    $this->fk_soc_invoice = '';
                }

                $this->amount       = $document->total_ht;
                $this->amount_total = empty($this->amount_maint) ? $document->total_ht : $document->total_ht + $this->amount_maint;
                if ($this->retention == 1) {
                    $this->retention_rate = $this->retention_rate($this->fk_org);
                    $this->amount_total = price2num($this->amount_total / (1-($this->retention_rate/100)), 'MT');
                }
                $coef = $this->coef($this->amount_total, $this->fk_duration, $this->fk_scale, $this->fk_org);
                if ($coef > 0) {
                    $this->coef         = $coef;
                    $this->amount_rent  = price2num($this->amount_total * $coef / 100, 'MT');
                    
                    if (!empty($this->amount_rent_edit) && $this->amount_rent_edit < $this->amount_rent) {
                        $this->amount_rent_edit = $this->amount_rent;
                        setEventMessages($langs->trans("amount_rent_edit<amount_rent"), null, 'errors');
                    }
                    $this->date_delivery = $document->date_livraison;
                    
                    // Date de signature renseigné si commande livré
                    if ($document->status == 3) {
                        if (empty($this->date_signature)) {
                            $this->date_signature = $this->date_delivery;
                        }
                    }

                    // Si date de signature calcul date de fin
                    if ($this->date_signature) {
                        //Ajoute la durée à la date de livraison pour avoir la date de fin
                        $duration = $this->fetch_duration($this->fk_duration);
                        if ($duration->code > 0) {
                            $this->date_end = date('Y-m-d', strtotime('+'.$duration->code.' month', strtotime(date('Y-m-d', $this->date_signature))));
                        }
                    } else {
                        $this->date_end = '';
                    }
                    // Changement du status si le montant du document change et que le financement est accept
                    if ($this->status >= self::STATUS_ACCEPT && $this->amount <> $document->total_ht) {
                            $this->status = self::STATUS_UPDATE;
                    }
                    if (!$error && !$notrigger) {
                        // Call trigger
                        $result = $this->call_trigger('FUNDING_UPDATE', $user);
                        if ($result < 0) {
                            $error++;
                        }
                        // End call triggers
                    }
                    if (!$error) {
                        return $this->updateCommon($user, $notrigger);
                    }
                } else {
                    setEventMessages($langs->trans("coefnok"), null, 'errors');
                }
            } else {
                setEventMessages($langs->trans("docnotvalidate"), null, 'errors');
            }
        } else {
            setEventMessages($langs->trans("paramnok"), null, 'errors');
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
        global $langs, $conf;

        if ($this->status == self::STATUS_RUNNING) {
            setEventMessages($langs->trans("deletnok").' - '.$this->ref, null, 'errors');
            return -1;
        }
        
        // Removed extrafields of object
        if (!$error) {
            $result = $this->deleteExtraFields();
            if ($result < 0) {
                $error++;
                dol_syslog(get_class($this)."::delete error ".$this->error, LOG_ERR);
            }
        }
        
        if (!$error) {
            // Delete linked object
            $res = $this->deleteObjectLinked();
            if ($res < 0) {
                $error++;
            }
        }
        
        // Delete record into ECM index and physically
        if (!$error) {
            $res = $this->deleteEcmFiles(0); // Deleting files physically is done later with the dol_delete_dir_recursive
            if (!$res) {
                $error++;
            }
        }

        if (!$error) {
            // We remove directory
            $ref = dol_sanitizeFileName($this->ref);
            if (!empty($this->ref)) {
                $dir = $conf->funding->multidir_output[$this->entity ? $this->entity : $conf->entity]."/".$ref;
                $file = $dir."/".$ref.".pdf";
                if (file_exists($file)) {
                    dol_delete_preview($this);

                    if (!dol_delete_file($file, 0, 0, 0, $this)) {
                        $this->error = 'ErrorFailToDeleteFile';
                        $this->errors[] = $this->error;
                        $this->db->rollback();
                        return 0;
                    }
                }

                if (file_exists($dir)) {
                    $res = @dol_delete_dir_recursive($dir);
                    if (!$res) {
                        $this->error = 'ErrorFailToDeleteDir';
                        $this->errors[] = $this->error;
                        $this->db->rollback();
                        return 0;
                    }
                }
            }
        }
        
        
        if (!$error) {
            // Delete main
            $res = $this->deleteCommon($user, $notrigger);
            if ($res < 0) {
                $error++;
            }
        }

        
        
        if (!$error) {
            dol_syslog(get_class($this)."::delete ".$this->id." by ".$user->id, LOG_DEBUG);
            $this->db->commit();
            return 1;
        } else {
            $this->db->rollback();
            return -1;
        }
        //return $this->deleteCommon($user, $notrigger, 1);
    }

    /**
     *  Delete a line of object in database
     *
     *  @param  User    $user       User that delete
     *  @param  int     $idline     Id of line to delete
     *  @param  bool    $notrigger  false=launch triggers after, true=disable triggers
     *  @return int                 >0 if OK, <0 if KO
     */
    public function deleteLine(User $user, $idline, $notrigger = false)
    {
        if ($this->status < 0) {
            $this->error = 'ErrorDeleteLineNotAllowedByObjectStatus';
            return -2;
        }

        return $this->deleteLineCommon($user, $idline, $notrigger);
    }


    /**
     *  Validate object
     *
     *  @param      User    $user           User making status change
     *  @param      int     $notrigger      1=Does not execute triggers, 0= execute triggers
     *  @return     int                     <=0 if OK, 0=Nothing done, >0 if KO
     */
    public function validate($user, $notrigger = 0)
    {
        global $conf, $langs;

        require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

        $error = 0;

        // Protection
        if ($this->status == self::STATUS_VALIDATED) {
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
        if (!$error && (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref))) { // empty should not happened, but when it occurs, the test save life
            $num = $this->getNextNumRef();
        } else {
            $num = $this->ref;
        }
        $this->newref = $num;

        if (!empty($num)) {
            // Validate
            $sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
            $sql .= " SET ref = '".$this->db->escape($num)."',";
            $sql .= " status = ".self::STATUS_VALIDATED;
            if (!empty($this->fields['date_validation'])) {
                $sql .= ", date_validation = '".$this->db->idate($now)."',";
            }
            if (!empty($this->fields['fk_user_valid'])) {
                $sql .= ", fk_user_valid = ".$user->id;
            }
            $sql .= " WHERE rowid = ".$this->id;

            dol_syslog(get_class($this)."::validate()", LOG_DEBUG);
            $resql = $this->db->query($sql);
            if (!$resql) {
                dol_print_error($this->db);
                $this->error = $this->db->lasterror();
                $error++;
            }

            if (!$error && !$notrigger) {
                // Call trigger
                $result = $this->call_trigger('FUNDING_VALIDATE', $user);
                if ($result < 0) {
                    $error++;
                }
                // End call triggers
            }
        }

        if (!$error) {
            $this->oldref = $this->ref;

            // Rename directory if dir was a temporary ref
            if (preg_match('/^[\(]?PROV/i', $this->ref)) {
                // Now we rename also files into index
                $sql = 'UPDATE '.MAIN_DB_PREFIX."ecm_files set filename = CONCAT('".$this->db->escape($this->newref)."', SUBSTR(filename, ".(strlen($this->ref) + 1).")), filepath = 'funding/".$this->db->escape($this->newref)."'";
                $sql .= " WHERE filename LIKE '".$this->db->escape($this->ref)."%' AND filepath = 'funding/".$this->db->escape($this->ref)."' and entity = ".$conf->entity;
                $resql = $this->db->query($sql);
                if (!$resql) {
                    $error++;
                    $this->error = $this->db->lasterror();
                }

                // We rename directory ($this->ref = old ref, $num = new ref) in order not to lose the attachments
                $oldref = dol_sanitizeFileName($this->ref);
                $newref = dol_sanitizeFileName($num);
                $dirsource = $conf->funding->dir_output.'/'.$oldref;
                $dirdest = $conf->funding->dir_output.'/'.$newref;
                if (!$error && file_exists($dirsource)) {
                    dol_syslog(get_class($this)."::validate() rename dir ".$dirsource." into ".$dirdest);

                    if (@rename($dirsource, $dirdest)) {
                        dol_syslog("Rename ok");
                        // Rename docs starting with $oldref with $newref
                        $listoffiles = dol_dir_list($conf->funding->dir_output.'/'.$newref, 'files', 1, '^'.preg_quote($oldref, '/'));
                        foreach ($listoffiles as $fileentry) {
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
        if (!$error) {
            $this->ref = $num;
            $this->status = self::STATUS_VALIDATED;
        }

        if (!$error) {
            $this->db->commit();
            return 1;
        } else {
            $this->db->rollback();
            return -1;
        }
    }


    /**
     *  Set draft status
     *
     *  @param  User    $user           Object user that modify
     *  @param  int     $notrigger      1=Does not execute triggers, 0=Execute triggers
     *  @return int                     <0 if KO, >0 if OK
     */
    public function setDraft($user, $notrigger = 0)
    {
        // Protection
        if ($this->status <= self::STATUS_DRAFT) {
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
     *  Set cancel status
     *
     *  @param  User    $user           Object user that modify
     *  @param  int     $notrigger      1=Does not execute triggers, 0=Execute triggers
     *  @return int                     <0 if KO, 0=Nothing done, >0 if OK
     */
    public function cancel($user, $notrigger = 0)
    {
        // Protection
        if ($this->status != self::STATUS_VALIDATED) {
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
     *  Set Accepted Refused  status
     *
     *  @param  User    $user           Object user that modify
     *  @param  int     $status         value status
     *  @param  alpha   $note           note de validation
     *  @param  int     $notrigger      1=Does not execute triggers, 0=Execute triggers
     *  @return int                     <0 if KO, 0=Nothing done, >0 if OK
     */
    public function Set_AcceptedRefused($user, $status, $note = '', $notrigger = 0)
    {
        // Protection
        if ($this->status == self::STATUS_CANCELED) {
            return 0;
        }
        if ($status == self::STATUS_ACCEPT) {
            $triger = 'FUNDING_ACCEPT';
        }
        if ($status == self::STATUS_DENIED) {
            $triger = 'FUNDING_DENIED';
        }
        
        return $this->setStatusCommon($user, $status, $notrigger, $triger);
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
        if (!$resql) {
            $this->errors[] = $this->db->error();
            $error++;
        }

        if (!$error) {
            $this->oldcopy = clone $this;
            $this->study_number = $study_number;
        }

        if (!$notrigger && empty($error)) {
            // Call trigger
            $result = $this->call_trigger('FUNDING_MODIFY', $user);
            if ($result < 0) {
                $error++;
            }
            // End call triggers
        }

        if (!$error) {
            $this->db->commit();
            return 1;
        } else {
            foreach ($this->errors as $errmsg) {
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
        if (!$resql) {
            $this->errors[] = $this->db->error();
            $error++;
        }

        if (!$error) {
            $this->oldcopy = clone $this;
            $this->folder_number = $folder_number;
        }

        if (!$notrigger && empty($error)) {
            // Call trigger
            $result = $this->call_trigger('FUNDING_MODIFY', $user);
            if ($result < 0) {
                $error++;
            }
            // End call triggers
        }

        if (!$error) {
            $this->db->commit();
            return 1;
        } else {
            foreach ($this->errors as $errmsg) {
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
    public function set_Extension($user)
    {
            $error = 0;

            $this->db->begin();

            $sql = "UPDATE ".MAIN_DB_PREFIX."funding_funding";
        if (empty($this->extension)) {
            $sql .= " SET extension = 1";
        } else {
            $sql .= " SET extension = 0";
        }
            
            $sql .= " WHERE rowid = ".$this->id;
            
            dol_syslog(__METHOD__.' $this->id='.$this->id.', extension', LOG_DEBUG);
            
            $resql = $this->db->query($sql);
        if (!$resql) {
            $this->errors[] = $this->db->error();
            $error++;
        }

        if (!$error) {
            $this->db->commit();
            return 1;
        } else {
            foreach ($this->errors as $errmsg) {
                dol_syslog(__METHOD__.' Error: '.$errmsg, LOG_ERR);
                $this->error .= ($this->error ? ', '.$errmsg : $errmsg);
            }
            $this->db->rollback();
            return -1 * $error;
        }
    }

    /**
     *  Return a link to the object card (with optionaly the picto)
     *
     *  @param  int     $withpicto                  Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
     *  @param  string  $option                     On what the link point to ('nolink', ...)
     *  @param  int     $notooltip                  1=Disable tooltip
     *  @param  string  $morecss                    Add more css on link
     *  @param  int     $save_lastsearch_value      -1=Auto, 0=No save of lastsearch_values when clicking, 1=Save lastsearch_values whenclicking
     *  @return string                              String with URL
     */
    public function getNomUrl($withpicto = 0, $option = '', $notooltip = 0, $morecss = '', $save_lastsearch_value = -1)
    {
        global $conf, $langs, $hookmanager;

        if (!empty($conf->dol_no_mouse_hover)) {
            $notooltip = 1; // Force disable tooltips
        }

        $result = '';
        //$label = img_picto('', $this->picto).' <u class="paddingrightonly">'.$langs->trans("Proposal").'</u>';
        $label = '<u>'.$langs->trans("Funding").'</u>';
        $label .= '';
        $label .= '<br><b>'.$langs->trans('Ref').':</b> '.$this->ref;
        $label .= '<br><b>'.$langs->trans('Amount').':</b> '.price($this->amount_total, 0, $langs, 0, -1, -1, $conf->currency);
        $label .= '<br><b>'.$langs->trans('Rent').':</b> '.price($this->amount_rent, 0, $langs, 0, -1, -1, $conf->currency);
        $label .= '<br><b>'.$langs->trans('Duration').':</b> '.$this->fetch_duration($this->fk_duration)->label;
        if (isset($this->status)) {
            $label .= '<br><b>'.$langs->trans("Status").":</b> ".$this->getLibStatut(5);
        }

        $url = dol_buildpath('/funding/funding_card.php', 1).'?id='.$this->id;

        if ($option != 'nolink') {
            // Add param to save lastsearch_values or not
            $add_save_lastsearch_values = ($save_lastsearch_value == 1 ? 1 : 0);
            if ($save_lastsearch_value == -1 && preg_match('/list\.php/', $_SERVER["PHP_SELF"])) {
                $add_save_lastsearch_values = 1;
            }
            if ($add_save_lastsearch_values) {
                $url .= '&save_lastsearch_values=1';
            }
        }

        $linkclose = '';
        if (empty($notooltip)) {
            if (!empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER)) {
                $label = $langs->trans("ShowFunding");
                $linkclose .= ' alt="'.dol_escape_htmltag($label, 1).'"';
            }
            $linkclose .= ' title="'.dol_escape_htmltag($label, 1).'"';
            $linkclose .= ' class="classfortooltip'.($morecss ? ' '.$morecss : '').'"';
        } else {
            $linkclose = ($morecss ? ' class="'.$morecss.'"' : '');
        }

        $linkstart = '<a href="'.$url.'"';
        $linkstart .= $linkclose.'>';
        $linkend = '</a>';

        $result .= $linkstart;

        if (empty($this->showphoto_on_popup)) {
            if ($withpicto) {
                $result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), ($notooltip ? (($withpicto != 2) ? 'class="paddingright"' : '') : 'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip ? 0 : 1);
            }
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
                    } else {
                        $result .= '<div class="floatleft inline-block valignmiddle divphotoref"><img class="photouserphoto userphoto" alt="No photo" border="0" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$module.'&entity='.$conf->entity.'&file='.urlencode($pathtophoto).'"></div>';
                    }

                    $result .= '</div>';
                } else {
                    $result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), ($notooltip ? (($withpicto != 2) ? 'class="paddingright"' : '') : 'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip ? 0 : 1);
                }
            }
        }

        if ($withpicto != 2) {
            $result .= $this->ref;
        }

        $result .= $linkend;
        //if ($withpicto != 2) $result.=(($addlabel && $this->label) ? $sep . dol_trunc($this->label, ($addlabel > 1 ? $addlabel : 0)) : '');

        global $action, $hookmanager;
        $hookmanager->initHooks(array('fundingdao'));
        $parameters = array('id'=>$this->id, 'getnomurl'=>$result);
        $reshook = $hookmanager->executeHooks('getNomUrl', $parameters, $this, $action); // Note that $action and $object may have been modified by some hooks
        if ($reshook > 0) {
            $result = $hookmanager->resPrint;
        } else {
            $result .= $hookmanager->resPrint;
        }

        return $result;
    }

    /**
     *  Return label of the status
     *
     *  @param  int     $mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
     *  @return string                 Label of status
     */
    public function getLibStatut($mode = 0)
    {
        return $this->LibStatut($this->status, $mode);
    }

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
    /**
     *  Return the status
     *
     *  @param  int     $status        Id status
     *  @param  int     $mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
     *  @return string                 Label of status
     */
    public function LibStatut($status, $mode = 0)
    {
		// phpcs:enable
        if (empty($this->labelStatus) || empty($this->labelStatusShort)) {
            global $langs;
            //$langs->load("funding");
            $this->labelStatus[self::STATUS_DRAFT] = $langs->trans('FundingStatusDraft');
            $this->labelStatus[self::STATUS_VALIDATED] = $langs->trans('FundingStatusValidated');
            $this->labelStatus[self::STATUS_UPDATE] = $langs->trans('FundingStatusUpdate');
            $this->labelStatus[self::STATUS_ACCEPT] = $langs->trans('FundingStatusAccept');
            $this->labelStatus[self::STATUS_DENIED] = $langs->trans('FundingStatusDenied');
            $this->labelStatus[self::STATUS_RUNNING] = $langs->trans('FundingStatusRunning');
            $this->labelStatus[self::STATUS_END] = $langs->trans('FundingStatusEnd');
            $this->labelStatus[self::STATUS_CANCELED] = $langs->trans('FundingStatusDisabled');
            $this->labelStatusShort[self::STATUS_DRAFT] = $langs->trans('FundingStatusDraftShort');
            $this->labelStatusShort[self::STATUS_VALIDATED] = $langs->trans('FundingStatusEnabledShort');
            $this->labelStatusShort[self::STATUS_UPDATE] = $langs->trans('FundingStatusUpdateShort');
            $this->labelStatusShort[self::STATUS_ACCEPT] = $langs->trans('FundingStatusAcceptShort');
            $this->labelStatusShort[self::STATUS_DENIED] = $langs->trans('FundingStatusDeniedShort');
            $this->labelStatusShort[self::STATUS_RUNNING] = $langs->trans('FundingStatusRunningShort');
            $this->labelStatusShort[self::STATUS_END] = $langs->trans('FundingStatusEndShort');
            $this->labelStatusShort[self::STATUS_CANCELED] = $langs->trans('FundingStatusDisabledShort');
        }

        // BB2A Status Correspodanse avec les format d'affichage
        $statusType = 'status'.$status;
        //if ($status == self::STATUS_VALIDATED) $statusType = 'status1';
        if ($status == self::STATUS_CANCELED) {
            $statusType = 'status6';
        }

        return dolGetStatus($this->labelStatus[$status], $this->labelStatusShort[$status], '', $statusType, $mode);
    }

    /**
     *  Load the info information in the object
     *
     *  @param  int     $id       Id of object
     *  @return void
     */
    public function info($id)
    {
        $sql = 'SELECT rowid, date_creation as datec, tms as datem,';
        $sql .= ' fk_user_creat, fk_user_modif';
        $sql .= ' FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
        $sql .= ' WHERE t.rowid = '.$id;
        $result = $this->db->query($sql);
        if ($result) {
            if ($this->db->num_rows($result)) {
                $obj = $this->db->fetch_object($result);
                $this->id = $obj->rowid;
                if ($obj->fk_user_author) {
                    $cuser = new User($this->db);
                    $cuser->fetch($obj->fk_user_author);
                    $this->user_creation = $cuser;
                }

                if ($obj->fk_user_valid) {
                    $vuser = new User($this->db);
                    $vuser->fetch($obj->fk_user_valid);
                    $this->user_validation = $vuser;
                }

                if ($obj->fk_user_cloture) {
                    $cluser = new User($this->db);
                    $cluser->fetch($obj->fk_user_cloture);
                    $this->user_cloture = $cluser;
                }

                $this->date_creation     = $this->db->jdate($obj->datec);
                $this->date_modification = $this->db->jdate($obj->datem);
                $this->date_validation   = $this->db->jdate($obj->datev);
            }

            $this->db->free($result);
        } else {
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
     *  Create an array of lines
     *
     *  @return array|int       array of lines if OK, <0 if KO
     */
    public function getLinesArray()
    {
        $this->lines = array();

        $objectline = new FundingLine($this->db);
        $result = $objectline->fetchAll('ASC', 'position', 0, 0, array('customsql'=>'fk_funding = '.$this->id));

        if (is_numeric($result)) {
            $this->error = $this->error;
            $this->errors = $this->errors;
            return $result;
        } else {
            $this->lines = $result;
            return $this->lines;
        }
    }

    /**
     *  Returns the reference to the following non used object depending on the active numbering module.
     *
     *  @return string              Object free reference
     */
    public function getNextNumRef()
    {
        global $langs, $conf;
        $langs->load("funding@funding");

        if (empty($conf->global->FUNDING_FUNDING_ADDON)) {
            $conf->global->FUNDING_FUNDING_ADDON = 'mod_funding_standard';
        }

        if (!empty($conf->global->FUNDING_FUNDING_ADDON)) {
            $mybool = false;

            $file = $conf->global->FUNDING_FUNDING_ADDON.".php";
            $classname = $conf->global->FUNDING_FUNDING_ADDON;

            // Include file with class
            $dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);
            foreach ($dirmodels as $reldir) {
                $dir = dol_buildpath($reldir."core/modules/funding/");

                // Load file with numbering class (if found)
                $mybool |= @include_once $dir.$file;
            }

            if ($mybool === false) {
                dol_print_error('', "Failed to include file ".$file);
                return '';
            }

            if (class_exists($classname)) {
                $obj = new $classname();
                $numref = $obj->getNextValue($this);

                if ($numref != '' && $numref != '-1') {
                    return $numref;
                } else {
                    $this->error = $obj->error;
                    //dol_print_error($this->db,get_class($this)."::getNextNumRef ".$obj->error);
                    return "";
                }
            } else {
                print $langs->trans("Error")." ".$langs->trans("ClassNotFound").' '.$classname;
                return "";
            }
        } else {
            print $langs->trans("ErrorNumberingModuleNotSetup", $this->element);
            return "";
        }
    }

    /**
     *  Create a document onto disk according to template module.
     *
     *  @param      string      $modele         Force template to use ('' to not force)
     *  @param      Translate   $outputlangs    objet lang a utiliser pour traduction
     *  @param      int         $hidedetails    Hide details of lines
     *  @param      int         $hidedesc       Hide description
     *  @param      int         $hideref        Hide ref
     *  @param      null|array  $moreparams     Array to provide more information
     *  @return     int                         0 if KO, 1 if OK
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
     * @return  int         0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
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

/* Notification
	if (!empty($conf->notification->enabled)) {
			require_once DOL_DOCUMENT_ROOT.'/core/class/notify.class.php';
			$notify = new Notify($db);
			$formquestion = array_merge($formquestion, array(
				array('type' => 'onecolumn', 'value' => $notify->confirmMessage('PROPAL_CLOSE_SIGNED', $object->socid, $object)),
			));
		}
*/

