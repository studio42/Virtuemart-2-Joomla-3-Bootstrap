<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved by the author.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: custom.php 3057 2011-04-19 12:59:22Z Electrocity $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');

/**
 * Model for VirtueMart Customs Fields
 *
 * @package		VirtueMart
 */
class VirtueMartModelCustom extends VmModel {

	private $plugin=null ;
	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct();
		$this->setMainTable('customs');
		$this->setToggleName('admin_only');
		$this->setToggleName('is_hidden');
	}

    /**
     * Gets a single custom by virtuemart_custom_id
     * .
     * @param string $type
     * @param string $mime mime type of custom, use for exampel image
     * @return customobject
     */
    function getCustom(){

    	if(empty($this->_data)){

//     		JTable::addIncludePath(JPATH_VM_ADMINISTRATOR.DS.'tables');
    		$this->_data = $this->getTable('customs');
    		$this->_data->load($this->_id);
			if (!$this->_id) {
				
				$field_type = JRequest::getWord('field_type', 0) ;
				if (!empty( $field_type ) ) {
				$this->_data->field_type = $field_type[0];
				} else {
					var_dump($this->_data);
					var_dump(jrequest::getVar('params'));
					jexit('program faillure');
				}

				if ( $this->_data->custom_jplugin_id = JRequest::getInt('custom_jplugin_id', 0) ) {
					$q = 'SELECT `element` FROM `#__extensions` WHERE `folder` = "vmcustom" AND `enabled`=1 ';
					$q .= ' AND `extension_id` = '.$this->_data->custom_jplugin_id ;
					$this->_db->setQuery($q);
					$this->_data->custom_element = $this->_db->loadResult();
				}
			}
		    $customfields = VmModel::getModel('Customfields');
		    $this->_data->field_types = $customfields->getField_types() ;

    		//    		vmdebug('getCustom $data',$this->_data);
    		if(!empty($this->_data->custom_element)){
				// set params
				$registry = new JRegistry;
				$registry->loadString($this->_data->custom_params);
				$this->_data->params = $registry->toArray();
    			JPluginHelper::importPlugin('vmcustom');
    			$dispatcher = JDispatcher::getInstance();
    			//    			$varsToPushParam = $dispatcher->trigger('plgVmDeclarePluginParams',array('custom',$this->_data->custom_element,$this->_data->custom_jplugin_id));
    			$retValue = $dispatcher->trigger('plgVmDeclarePluginParamsCustom',array('custom',$this->_data->custom_element,$this->_data->custom_jplugin_id,&$this->_data));
				// Get the payment XML.
				$path	= JPath::clean( JPATH_PLUGINS.'/vmcustom/' . $this->_data->custom_element . '/' . $this->_data->custom_element . '.xml');
				if (file_exists($path))
				{

					$this->_data->form = JForm::getInstance('plgForm', $path, array(),true, '//config');
					// $this->_data->xml = simplexml_load_file($path);
					$this->_data->form->bind($this->_data);
				}
				else
				{
					$this->_data->form = null;
				}
				
    		} else {
				$this->_data->form = null;
			    //Todo this is not working, because the custom is using custom_params, while the customfield is using custom_param !
			    //VirtueMartModelCustomfields::bindParameterableByFieldType($this->_data);
		    }


    	}

  		return $this->_data;

    }


    /**
	 * Retireve a list of customs from the database. This is meant only for backend use
	 *
	 * @author Kohl Patrick
	 * @return object List of custom objects
	 */
    function getCustoms($custom_parent_id,$search = false){

    	// vmdebug('for model');
		$query='* FROM `#__virtuemart_customs` WHERE field_type <> "R" AND field_type <> "Z" AND field_type <> "G"';
		if($custom_parent_id){
			$query .= ' AND `custom_parent_id` ='.(int)$custom_parent_id;
		}

		if($search){
			$search = '"%' . $this->_db->escape( $search, true ) . '%"' ;
			$query .= ' AND `custom_title` LIKE '.$search;
		}
		$published = JRequest::getvar('filter_published');
		if ($published === '1') {
			$query .= " AND `published` = 1 ";
		} else if ($published === '0') {
			$query .= " AND `published` = 0 ";
		}
	    $datas = new stdClass();
		$datas->items = $this->exeSortSearchListQuery(0, $query, '', $this->_getOrdering());

		$customfields = VmModel::getModel('Customfields');

		if (!class_exists('VmHTML')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'html.php');
		$datas->field_types = $customfields->getField_types() ;
		if ($datas->items) {
			foreach ($datas->items as $key => & $data) {
				if (!empty($data->custom_parent_id)) $data->custom_parent_title = $customfields->getCustomParentTitle($data->custom_parent_id);
				else {
					$data->custom_parent_title =  '-' ;
				}
				if(!empty($datas->field_types[$data->field_type ])){
					$data->field_type_display = JText::_( $datas->field_types[$data->field_type ] );
				} else {
					$data->field_type_display = 'not valid, delete this line';
					vmError('The field with id '.$data->virtuemart_custom_id.' and title '.$data->custom_title.' is not longer valid, please delete it from the list');
				}

			}
		}
		// this display all. Here parent only needed
		//$datas->customsSelect=$customfields->displayCustomSelection();

		// only parent.
		$datas->customsSelect= $this->parentsList();
		
		return $datas;
    }
	function parentsList() {
		$vendorId = 1;
		$value = JRequest::getInt ('custom_parent_id', 0);
		// get custom parents
		$q = 'SELECT c.`virtuemart_custom_id` as value ,c.`custom_title` as text FROM `#__virtuemart_customs` as c';
		$q .=' JOIN `#__virtuemart_customs` as cc on c.`virtuemart_custom_id` = cc.`custom_parent_id`';
		$q .=' WHERE c.`custom_parent_id`=0 group by value';
		//if (isset($this->virtuemart_custom_id)) $q.=' and virtuemart_custom_id !='.$this->virtuemart_custom_id;
		$this->_db->setQuery ($q);
		$result = $this->_db->loadObjectList();
		$emptyOption = JHTML::_ ('select.option', '', '- '.JText::_ ('COM_VIRTUEMART_CUSTOM_PARENT').' -', 'value', 'text');
		array_unshift ($result, $emptyOption);
		return VmHTML::select('custom_parent_id', $result, $value,'onchange="Joomla.ajaxSearch(this); return false;"','value' ,'text',false);
	}

	/**
	 * Creates a clone of a given custom id
	 *
	 * @author Max Milbers
	 * @param int $virtuemart_product_id
	 */

	public function createClone($id){
		$this->virtuemart_custom_id = $id;
		$row = $this->getTable('customs');
		$row->load( $id );
		$row->virtuemart_custom_id = 0;
		$row->custom_title = $row->custom_title.' Copy';

		if (!$clone = $row->store()) {
			JError::raiseError(500, 'createClone '. $row->getError() );
		}
		return $clone;
	}


	/* Save and delete from database
	 *  all Child product custom_fields relation
	 * 	@ var   $table	: the xref table(eg. product,category ...)
	 * 	@array $data	: array of customfields
	 * 	@int     $id		: The concerned id (eg. product_id)
	 **/
	public function saveChildCustomRelation($table,$datas) {

		JSession::checkToken() or jexit( 'Invalid Token, in store customfields');
		//Table whitelist
		$tableWhiteList = array('product','category','manufacturer');
		if(!in_array($table,$tableWhiteList)) return false;

		$customfieldIds = array();
		// delete existings from modelXref and table customfields
		foreach ($datas as $child_id =>$fields) {
			$fields['virtuemart_'.$table.'_id']=$child_id;
			$this->_db->setQuery( 'DELETE PC FROM `#__virtuemart_'.$table.'_customfields` as `PC`, `#__virtuemart_customs` as `C` WHERE `PC`.`virtuemart_custom_id` = `C`.`virtuemart_custom_id` AND field_type="C" and virtuemart_'.$table.'_id ='.$child_id );
			if(!$this->_db->execute()){
				vmError('Error in deleting child relation '); //.$this->_db->getQuery()); Dont give hackers too much info
			}

			$tableCustomfields = $this->getTable($table.'_customfields');
			$tableCustomfields->bindChecknStore($fields);
    		$errors = $tableCustomfields->getErrors();
			foreach($errors as $error){
				vmError($error);
			}
		}

	}



	public function store(&$data){

		if(!empty($data['params'])){
			foreach($data['params'] as $k=>$v){
				$data[$k] = $v;
			}
			$data['custom_params'] = $data['params'];
		}

		if(empty($data['virtuemart_vendor_id'])){
			if(!class_exists('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'vendor.php');
			$data['virtuemart_vendor_id'] = VirtueMartModelVendor::getLoggedVendor();
		} else {
			$data['virtuemart_vendor_id'] = (int) $data['virtuemart_vendor_id'];
		}

		// missing string FIX, Bad way ?
		$tb = '#__extensions';
		$ext_id = 'extension_id';

		$q = 'SELECT `element` FROM `' . $tb . '` WHERE `' . $ext_id . '` = "'.$data['custom_jplugin_id'].'"';
		$this->_db->setQuery($q);
		$data['custom_element'] = $this->_db->loadResult();
// 		vmdebug('store custom',$data);
		$table = $this->getTable('customs');

		if(isset($data['custom_jplugin_id'])){
			vmdebug('$data store custom',$data);
			JPluginHelper::importPlugin('vmcustom');
			$dispatcher = JDispatcher::getInstance();
// 			$retValue = $dispatcher->trigger('plgVmSetOnTablePluginParamsCustom',array($data['custom_value'],$data['custom_jplugin_id'],&$table));
			$retValue = $dispatcher->trigger('plgVmSetOnTablePluginParamsCustom',array($data['custom_element'],$data['custom_jplugin_id'],&$table));
		}

		$table->bindChecknStore($data);
		$errors = $table->getErrors();
		if(!empty($errors)){
			foreach($errors as $error){
				vmError($error);
			}
		}

		JPluginHelper::importPlugin('vmcustom');
		$dispatcher = JDispatcher::getInstance();
		$error = $dispatcher->trigger('plgVmOnStoreInstallPluginTable', array('custom' , $data['custom_element']));

		return $table->virtuemart_custom_id ;
	}


}
// pure php no closing tag
