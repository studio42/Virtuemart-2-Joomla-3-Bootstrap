<?php
/**
 *
 * Description
 *
 * @package    VirtueMart
 * @subpackage
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved by the author.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id:$
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');

if (!class_exists ('VmModel')) {
	require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmmodel.php');
}

/**
 * Model for VirtueMart Customs Fields
 *
 * @package        VirtueMart
 */
class VirtueMartModelCustomfields extends VmModel {

	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 *
	 * @author Max Milbers
	 */
// 	function __construct($modelName ='product') {
	function __construct ($modelName = 'product') {

		parent::__construct ('virtuemart_customfield_id');
		$this->setMainTable ('product_customfields');
	}


	/**
	 * Gets a single custom by virtuemart_customfield_id
	 *
	 * @param string $type
	 * @param string $mime mime type of custom, use for exampel image
	 * @return customobject
	 */
	function getCustomfield () {

		$this->data = $this->getTable ('product_customfields');
		$this->data->load ($this->_id);

		return $this;
	}

	// **************************************************
	// Custom FIELDS
	//

	function getProductCustomsChilds ($childs) {

		$data = array();
		foreach ($childs as $child) {
			$query = 'SELECT C.* , field.*
					FROM `#__virtuemart_product_customfields` AS field
					LEFT JOIN `#__virtuemart_customs` AS C ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
					WHERE `virtuemart_product_id` =' . (int)$child->virtuemart_product_id;
			$query .= ' and C.field_type = "C" ';

			$this->_db->setQuery ($query);
			$child->field = $this->_db->loadObject ();
			$customfield = new stdClass();
			$customfield->custom_value = $child->virtuemart_product_id;
			$customfield->field_type = 'C';
			$child->display = $this->displayProductCustomfieldFE ($child, $customfield);
			if ($child->field) {
				$data[] = $child;
			}
		}
		return $data;

	}

	public function getCustomParentTitle ($custom_parent_id) {

		$q = 'SELECT custom_title FROM `#__virtuemart_customs` WHERE virtuemart_custom_id =' . (int)$custom_parent_id;
		$this->_db->setQuery ($q);
		return $this->_db->loadResult ();
	}

	/** @return autorized Types of data **/
	function getField_types () {

		return array('S' => 'COM_VIRTUEMART_CUSTOM_STRING',
		             'I' => 'COM_VIRTUEMART_CUSTOM_INT',
		             'P' => 'COM_VIRTUEMART_CUSTOM_PARENT',
		             'B' => 'COM_VIRTUEMART_CUSTOM_BOOL',
		             'D' => 'COM_VIRTUEMART_DATE',
		             'T' => 'COM_VIRTUEMART_TIME',
		             'M' => 'COM_VIRTUEMART_IMAGE',
		             'V' => 'COM_VIRTUEMART_CUSTOM_CART_VARIANT',
		             'A' => 'COM_VIRTUEMART_CHILD_GENERIC_VARIANT',
		             'X' => 'COM_VIRTUEMART_CUSTOM_EDITOR',
		             'Y' => 'COM_VIRTUEMART_CUSTOM_TEXTAREA',
		             'E' => 'COM_VIRTUEMART_CUSTOM_EXTENSION'
		);

		// 'U'=>'COM_VIRTUEMART_CUSTOM_CART_USER_VARIANT',
		// 'C'=>'COM_VIRTUEMART_CUSTOM_PRODUCT_CHILD',
		// 'G'=>'COM_VIRTUEMART_CUSTOM_PRODUCT_CHILD_GROUP',
		//			'R'=>'COM_VIRTUEMART_RELATED_PRODUCT',
		//			'Z'=>'COM_VIRTUEMART_RELATED_CATEGORY',
	}

	static function setParameterableByFieldType(&$table,$type=0){

		if($type===0) $type = $table->field_type;

		$varsToPush = self::getVarsToPush($type);

		if(!empty($varsToPush)){
			$table->setParameterable('custom_param',$varsToPush,TRUE);
		}

	}

	static function bindParameterableByFieldType(&$table,$type=0){

		if($type===0) $type = $table->field_type;

		$varsToPush = self::getVarsToPush($type);

		if(!empty($varsToPush)){
			VmTable::bindParameterable($table,'custom_param',$varsToPush);
		}

	}


	static function getVarsToPush($type){

		$varsToPush = 0;
		if($type=='A'){
			$varsToPush = array(
				'withParent'        => array(0, 'int'),
				'parentOrderable'   => array(0, 'int')
			);
		}
		return $varsToPush;
	}

	private $_hidden = array();

	/**
	 * Use this to adjust the hidden fields of the displaycustomHandler to your form
	 *
	 * @author Max Milbers
	 * @param string $name for exampel view
	 * @param string $value for exampel custom
	 */
	public function addHidden ($name, $value = '') {

		$this->_hidden[$name] = $value;
	}

	/**
	 * Adds the hidden fields which are needed for the form in every case
	 *
	 * @author Max Milbers
	 * OBSELTE ?
	 */
	private function addHiddenByType ($datas) {

		$this->addHidden ('virtuemart_custom_id', $datas->virtuemart_custom_id);
		$this->addHidden ('option', 'com_virtuemart');

	}

	/**
	 * Displays a possibility to select created custom
	 *
	 * @author Max Milbers
	 * @author Patrick Kohl
	 */
	public function displayCustomSelection () {

		$customslist = $this->getCustomsList ();
		if (isset($this->virtuemart_custom_id)) {
			$value = $this->virtuemart_custom_id;
		}
		else {
			$value = JRequest::getInt ('custom_parent_id', 0);
		}
		return VmHTML::row ('select', 'COM_VIRTUEMART_CUSTOM_PARENT', 'custom_parent_id', $customslist, $value);
	}

	/**
	 * Retrieve a list of layouts from the default and chosen templates directory.
	 *
	 * We may use here the getCustoms function of the custom model or write something simular
	 *
	 * @author Max Milbers
	 * @param name of the view
	 * @return object List of flypage objects
	 */
	function getCustomsList ($publishedOnly = FALSE) {

		$vendorId = 1;
		// get custom parents
		$q = 'SELECT virtuemart_custom_id as value ,custom_title as text FROM `#__virtuemart_customs` where custom_parent_id=0
			AND field_type <> "R" AND field_type <> "Z" ';
		if ($publishedOnly) {
			$q .= 'AND `published`=1';
		}
		if ($ID = JRequest::getInt ('virtuemart_custom_id', 0)) {
			$q .= ' and `virtuemart_custom_id`!=' . (int)$ID;
		}
		//if (isset($this->virtuemart_custom_id)) $q.=' and virtuemart_custom_id !='.$this->virtuemart_custom_id;
		$this->_db->setQuery ($q);
		//		$result = $this->_db->loadAssocList();
		$result = $this->_db->loadObjectList ();

		$errMsg = $this->_db->getErrorMsg ();
		$errs = $this->_db->getErrors ();

		if (!empty($errMsg)) {
			$app = JFactory::getApplication ();
			$errNum = $this->_db->getErrorNum ();
			$app->enqueueMessage ('SQL-Error: ' . $errNum . ' ' . $errMsg);
		}

		if ($errs) {
			$app = JFactory::getApplication ();
			foreach ($errs as $err) {
				$app->enqueueMessage ($err);
			}
		}

		return $result;
	}

	/**
	 * This displays a custom handler.
	 *
	 * @param string $html atttributes, Just for displaying the fullsized image
	 */
	public function displayCustomFields ($datas) {

		$identify = ''; // ':'.$this->virtuemart_custom_id;
		if (!class_exists ('VmHTML')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
		}
		if ($datas->field_type) {
			$this->addHidden ('field_type', $datas->field_type);
		}
		$this->addHiddenByType ($datas);

		//$html = '<div id="custom_title">'.$datas->custom_title.'</div>';
		$html = "";
		//$html = ' <table class="admintable"> ';

		if (!class_exists ('Permissions')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
		}
		if (!Permissions::getInstance ()->check ('admin')) {
			$readonly = 'readonly';
		}
		else {
			$readonly = '';
		}
		// only input when not set else display
		if ($datas->field_type) {
			$html .= VmHTML::row ('value', 'COM_VIRTUEMART_CUSTOM_FIELD_TYPE', $datas->field_types[$datas->field_type]);
		}
		else {
			$html .= VmHTML::row ('select', 'COM_VIRTUEMART_CUSTOM_FIELD_TYPE', 'field_type', $this->getOptions ($datas->field_types), $datas->field_type, VmHTML::validate ('R'));
		}
		$html .= VmHTML::row ('input', 'COM_VIRTUEMART_TITLE', 'custom_title', $datas->custom_title, VmHTML::validate ('S'));
		$html .= VmHTML::row ('booleanlist', 'COM_VIRTUEMART_PUBLISHED', 'published', $datas->published);
		$html .= VmHTML::row ('select', 'COM_VIRTUEMART_CUSTOM_PARENT', 'custom_parent_id', $this->getParentList ($datas->virtuemart_custom_id), $datas->custom_parent_id, '');
		$html .= VmHTML::row ('booleanlist', 'COM_VIRTUEMART_CUSTOM_IS_CART_ATTRIBUTE', 'is_cart_attribute', $datas->is_cart_attribute);
		$html .= VmHTML::row ('input', 'COM_VIRTUEMART_DESCRIPTION', 'custom_field_desc', $datas->custom_field_desc);
		// change input by type
		$html .= VmHTML::row ('input', 'COM_VIRTUEMART_DEFAULT', 'custom_value', $datas->custom_value);
		$html .= VmHTML::row ('input', 'COM_VIRTUEMART_CUSTOM_TIP', 'custom_tip', $datas->custom_tip);
		$html .= VmHTML::row ('input', 'COM_VIRTUEMART_CUSTOM_LAYOUT_POS', 'layout_pos', $datas->layout_pos);
		//$html .= VmHTML::row('booleanlist','COM_VIRTUEMART_CUSTOM_PARENT','custom_parent_id',$this->getCustomsList(),  $datas->custom_parent_id,'');
		$html .= VmHTML::row ('booleanlist', 'COM_VIRTUEMART_CUSTOM_ADMIN_ONLY', 'admin_only', $datas->admin_only);
		$html .= VmHTML::row ('booleanlist', 'COM_VIRTUEMART_CUSTOM_IS_LIST', 'is_list', $datas->is_list);
		$html .= VmHTML::row ('booleanlist', 'COM_VIRTUEMART_CUSTOM_IS_HIDDEN', 'is_hidden', $datas->is_hidden);

		// $html .= '</table>';  removed
		$html .= VmHTML::inputHidden ($this->_hidden);

		return $html;
	}

	/**
	 * child classes can add their own options and you can get them with this function
	 *
	 * @param array $optionsarray
	 */
	private function getOptions ($field_types) {

		$options = array();
		foreach ($field_types as $optionName=> $langkey) {
			$options[] = JHTML::_ ('select.option', $optionName, JText::_ ($langkey));
		}
		return $options;
	}

	/**
	 * Just for creating simpel rows
	 *
	 * @author Max Milbers
	 * @param string $descr
	 * @param string $name
	 */
	private function displayRow ($descr, $name, $readonly = '') {

		$html = '<tr>
		<td class="labelcell">' . JText::_ ($descr) . '</td>
		<td> <input type="text" ' . $readonly . 'class="inputbox ' . $readonly . '" name="' . $name . '" size="70" value="' . $this->$name . '" /></td>
	</tr>';
		return $html;
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $excludedId
	 * @return unknown|multitype:
	 */
	function getParentList ($excludedId = 0) {

		$this->_db->setQuery (' SELECT virtuemart_custom_id as value,custom_title as text FROM `#__virtuemart_customs` WHERE `field_type` ="P" and virtuemart_custom_id!=' . $excludedId);
		if ($results = $this->_db->loadObjectList ()) {
			return $results;
		}
		else {
			return array();
		}
	}

	/**
	 *
	 * Enter description here ...
	 */
	function getProductChildCustomRelation () {

		$this->_db->setQuery (' SELECT virtuemart_custom_id as value,custom_title as text FROM `#__virtuemart_customs` WHERE `field_type` ="C"');
		if ($results = $this->_db->loadObjectList ()) {
			return $results;
		}
		else {
			return array();
		}
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $product_id
	 * @return unknown
	 */
	function getProductChildCustom ($product_id) {

		$this->_db->setQuery (' SELECT `virtuemart_custom_id`,`custom_value` FROM `#__virtuemart_product_customfields` WHERE  `virtuemart_product_id` =' . (int)$product_id);
		if ($childcustom = $this->_db->loadObject ()) {
			return $childcustom;
		}
		else {
			$childcustom->virtuemart_custom_id = 0;
			$childcustom->custom_value = '';
			return $childcustom;
		}
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $product_id
	 * @return string|Ambigous <string, mixed, multitype:>
	 */
	function getProductParentRelation ($product_id) {

		$this->_db->setQuery (' SELECT `custom_value` FROM `#__virtuemart_product_customfields` WHERE  `virtuemart_product_id` =' . (int)$product_id);
		if ($childcustom = $this->_db->loadResult ()) {
			return '(' . $childcustom . ')';
		}
		else {
			return JText::_ ('COM_VIRTUEMART_CUSTOM_NO_PARENT_RELATION');
		}
	}

	/**
	 * AUthor Kohl Patrick
	 * Load all custom fields for a Single product
	 * return custom fields value and definition
	 */
	public function getproductCustomslist ($virtuemart_product_id, $parent_id = NULL) {

		$query = 'SELECT C.`virtuemart_custom_id` , `custom_element`, `custom_jplugin_id`, `custom_params`, `custom_parent_id` , `admin_only` , `custom_title` , `custom_tip` , C.`custom_value` AS value, `custom_field_desc` , `field_type` , `is_list` , `is_cart_attribute` , `is_hidden` , C.`published` , field.`virtuemart_customfield_id` , field.`custom_value`,field.`custom_param`,field.`custom_price`,field.`ordering`
			FROM `#__virtuemart_customs` AS C
			LEFT JOIN `#__virtuemart_product_customfields` AS field ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
			Where `virtuemart_product_id` =' . $virtuemart_product_id . ' order by field.`ordering` ASC';
		$this->_db->setQuery ($query);
		$productCustoms = $this->_db->loadObjectList ();
		//if (!$productCustoms ) return array();
		if (!$productCustoms) {
			return;
		}
		$row = 0;
		foreach ($productCustoms as $field) {
			if ($parent_id) {
				$field->custom_value = "";
				$field->virtuemart_customfield_id = "";
				$field->custom_param = NULL;
				$virtuemart_product_id = $parent_id;
			}
			if ($field->field_type == 'E') {

				JPluginHelper::importPlugin ('vmcustom');
				$dispatcher = JDispatcher::getInstance ();
				$retValue = $dispatcher->trigger ('plgVmDeclarePluginParams', array('custom', $field->custom_element, $field->custom_jplugin_id, $field));

			}else {
				VirtueMartModelCustomfields::bindParameterableByFieldType($field);
			}
			//vmdebug('fields',$field);
			$field->display = $this->displayProductCustomfieldBE ($field, $virtuemart_product_id, $row); //custom_param without S !!!
			$row++;
		}
		return $productCustoms;
	}

	/* Save and delete from database
	* all product custom_fields and xref
	@ var   $table	: the xref table(eg. product,category ...)
	@array $data	: array of customfields
	@int     $id		: The concerned id (eg. product_id)
	*/
	public function storeProductCustomfields($table,$datas, $id) {

		//vmdebug('storeProductCustomfields',$datas);
		JRequest::checkToken() or jexit( 'Invalid Token, in store customfields');
		//Sanitize id
		$id = (int)$id;

		//Table whitelist
		$tableWhiteList = array('product','category','manufacturer');
		if(!in_array($table,$tableWhiteList)) return false;


		// Get old IDS
		$this->_db->setQuery( 'SELECT `virtuemart_customfield_id` FROM `#__virtuemart_'.$table.'_customfields` as `PC` WHERE `PC`.virtuemart_'.$table.'_id ='.$id );
		$old_customfield_ids = $this->_db->loadResultArray();


		if (isset ( $datas['custom_param'] )) $params = true ;
		else $params = false ;
		if (array_key_exists('field', $datas)) {
			//vmdebug('datas save',$datas);
			$customfieldIds = array();


			foreach($datas['field'] as $key => $fields){
				$fields['virtuemart_'.$table.'_id'] =$id;
				$tableCustomfields = $this->getTable($table.'_customfields');
				$tableCustomfields->setPrimaryKey('virtuemart_product_id');

				if (!empty($datas['custom_param'][$key]) and !isset($datas['clone']) ) {
					if (array_key_exists( $key,$datas['custom_param'])) {
						$fields['custom_param'] = json_encode($datas['custom_param'][$key]);
					}
				}

				VirtueMartModelCustomfields::setParameterableByFieldType($tableCustomfields,$fields['field_type']);
				if(!isset($datas['clone'])){
					VirtueMartModelCustomfields::bindParameterableByFieldType($tableCustomfields,$fields['field_type']);
				}

				$tableCustomfields->bindChecknStore($fields);
				$errors = $tableCustomfields->getErrors();

				foreach($errors as $error){
					vmError($error);
				}
				$key = array_search($fields['virtuemart_customfield_id'], $old_customfield_ids );
				if ($key !== false ) unset( $old_customfield_ids[ $key ] );
// 				vmdebug('datas clone',$old_customfield_ids,$fields);
			}

		}

		if ( count($old_customfield_ids) ) {
			// delete old unused Customfields
			$this->_db->setQuery( 'DELETE FROM `#__virtuemart_'.$table.'_customfields` WHERE `virtuemart_customfield_id` in ("'.implode('","', $old_customfield_ids ).'") ');
			$this->_db->query();
		}


		JPluginHelper::importPlugin('vmcustom');
		$dispatcher = JDispatcher::getInstance();
		if (isset($datas['plugin_param']) and is_array($datas['plugin_param'])) {
			foreach ($datas['plugin_param'] as $key => $plugin_param ) {
				$dispatcher->trigger('plgVmOnStoreProduct', array($datas, $plugin_param ));
			}
		}

	}


	/**
	 * Formatting admin display by roles
	 * input Types for product only !
	 * $field->is_cart_attribute if can have a price
	 */
	public function displayProductCustomfieldBE ($field, $product_id, $row) {

		$field->custom_value = empty($field->custom_value) ? $field->value : $field->custom_value;

		if ($field->is_cart_attribute) {
			if(!class_exists('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'vendor.php');
			if(!class_exists('VirtueMartModelCurrency')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'currency.php');
			$vendor_model = VmModel::getModel('vendor');
			$vendor_model->setId(1);
			$vendor = $vendor_model->getVendor();
			$currency_model = VmModel::getModel('currency');
			$vendor_currency = $currency_model->getCurrency($vendor->vendor_currency);
			$priceInput = '<span style="white-space: nowrap;"><input type="text" size="12" style="text-align:right;" value="' . (isset($field->custom_price) ?  $field->custom_price : '0') . '" name="field[' . $row . '][custom_price]" /> '.$vendor_currency->currency_symbol."</span>";
		}
		else {
			$priceInput = ' ';
		}

		if ($field->is_list) {
			$options = array();
			$values = explode (';', $field->value);

			foreach ($values as $key => $val) {
				$options[] = array('value' => $val, 'text' => $val);
			}

		        $currentValue = $field->custom_value;
			return JHTML::_ ('select.genericlist', $options, 'field[' . $row . '][custom_value]', null, 'value', 'text', $currentValue) . '</td><td>' . $priceInput;
		}
		else {

			switch ($field->field_type) {

				case 'A':
					//vmdebug('displayProductCustomfieldBE $field',$field);
					if(!isset($field->withParent)) $field->withParent = 0;
					if(!isset($field->parentOrderable)) $field->parentOrderable = 0;
					//vmdebug('displayProductCustomfieldFE',$field);
					if (!class_exists('VmHTML')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'html.php');
					$html = JText::_('COM_VIRTUEMART_CUSTOM_WP').VmHTML::checkbox('field[' . $row . '][withParent]',$field->withParent,1,0,'').'<br />';
					$html .= JText::_('COM_VIRTUEMART_CUSTOM_PO').VmHTML::checkbox('field[' . $row . '][parentOrderable]',$field->parentOrderable,1,0,'');

					$options = array();
// 					$options[] = array( 'value' => 'product_name' ,'text' =>JText::_('COM_VIRTUEMART_PRODUCT_FORM_NAME')); Is anyway displayed there
					$options[] = array('value' => 'product_sku', 'text' => JText::_ ('COM_VIRTUEMART_PRODUCT_SKU'));
					$options[] = array('value' => 'slug', 'text' => JText::_ ('COM_VIRTUEMART_PRODUCT_ALIAS'));
					$options[] = array('value' => 'product_length', 'text' => JText::_ ('COM_VIRTUEMART_PRODUCT_LENGTH'));
					$options[] = array('value' => 'product_width', 'text' => JText::_ ('COM_VIRTUEMART_PRODUCT_WIDTH'));
					$options[] = array('value' => 'product_height', 'text' => JText::_ ('COM_VIRTUEMART_PRODUCT_HEIGHT'));
					$options[] = array('value' => 'product_weight', 'text' => JText::_ ('COM_VIRTUEMART_PRODUCT_WEIGHT'));

					$html .= JHTML::_ ('select.genericlist', $options, 'field[' . $row . '][custom_value]', '', 'value', 'text', $field->custom_value) . '</td><td>' . $priceInput;
					return $html;
					// 					return 'Automatic Childvariant creation (later you can choose here attributes to show, now product name) </td><td>';
					break;
				// variants
				case 'V':
					return '<input type="text" value="' . $field->custom_value . '" name="field[' . $row . '][custom_value]" /></td><td>' . $priceInput;
					break;
				/*
									 * Stockable (group of) child variants
								 * Special type setted by the plugin
								 */
				case 'G':
					return;
					break;
				/*Extended by plugin*/
				case 'E':

					$html = '<input type="hidden" value="' . $field->value . '" name="field[' . $row . '][custom_value]" />';
					if (!class_exists ('vmCustomPlugin')) {
						require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');
					}
					JPluginHelper::importPlugin ('vmcustom', $field->custom_element);
					$dispatcher = JDispatcher::getInstance ();
					$retValue = '';
					$dispatcher->trigger ('plgVmOnProductEdit', array($field, $product_id, &$row, &$retValue));

					return $html . $retValue  . '</td><td>'. $priceInput;
					break;
				case 'D':
					return vmJsApi::jDate ($field->custom_value, 'field[' . $row . '][custom_value]', 'field_' . $row . '_customvalue') .'</td><td>'. $priceInput;
					break;
				case 'T':
					//TODO Patrick
					return '<input type="text" value="' . $field->custom_value . '" name="field[' . $row . '][custom_value]" /></td><td>' . $priceInput;
					break;
				/* string or integer */
				case 'S':
				case 'I':
					return '<input type="text" value="' . $field->custom_value . '" name="field[' . $row . '][custom_value]" /></td><td>' . $priceInput;
					break;
				//'X'=>'COM_VIRTUEMART_CUSTOM_EDITOR',
				case 'X':
					return '<textarea class="mceInsertContentNew" name="field[' . $row . '][custom_value]" id="field-' . $row . '-custom_value">' . $field->custom_value . '</textarea>
						<script type="text/javascript">// Creates a new editor instance
							tinymce.execCommand("mceAddControl",true,"field-' . $row . '-custom_value")
						</script></td><td>' . $priceInput;
					//return '<input type="text" value="'.$field->custom_value.'" name="field['.$row.'][custom_value]" /></td><td>'.$priceInput;
					break;
				//'Y'=>'COM_VIRTUEMART_CUSTOM_TEXTAREA'
				case 'Y':
					return '<textarea id="field[' . $row . '][custom_value]" name="field[' . $row . '][custom_value]" class="inputbox" cols=80 rows=50 >' . $field->custom_value . '</textarea></td><td>' . $priceInput;
					//return '<input type="text" value="'.$field->custom_value.'" name="field['.$row.'][custom_value]" /></td><td>'.$priceInput;
					break;

				case 'editorta':
					jimport ('joomla.html.editor');
					$editor = JFactory::getEditor ();
					//TODO This is wrong!
					$_return['fields'][$_fld->name]['formcode'] = $editor->display ($_prefix . $_fld->name, $_return['fields'][$_fld->name]['value'], 300, 150, $_fld->cols, $_fld->rows);
					break;
				/* bool */
				case 'B':
					return JHTML::_ ('select.booleanlist', 'field[' . $row . '][custom_value]', 'class="inputbox"', $field->custom_value) . '</td><td>' . $priceInput;
					break;
				/* parent */
				case 'P':
					return $field->custom_value . '<input type="hidden" value="' . $field->custom_value . '" name="field[' . $row . '][custom_value]" /></td><td>';
					break;
				/* related category*/
				case 'Z':
					if (!$field->custom_value) {
						return '';
					} // special case it's category ID !
					$q = 'SELECT * FROM `#__virtuemart_categories_' . VMLANG . '` JOIN `#__virtuemart_categories` AS p using (`virtuemart_category_id`) WHERE `published`=1 AND `virtuemart_category_id`= "' . (int)$field->custom_value . '" ';
					$this->_db->setQuery ($q);
					//echo $this->_db->_sql;
					if ($category = $this->_db->loadObject ()) {
						$q = 'SELECT `virtuemart_media_id` FROM `#__virtuemart_category_medias` WHERE `virtuemart_category_id`= "' . (int)$field->custom_value . '" ';
						$this->_db->setQuery ($q);
						$thumb = '';
						if ($media_id = $this->_db->loadResult ()) {
							$thumb = $this->displayCustomMedia ($media_id,'category');
						}
						$display = '<input type="hidden" value="' . $field->custom_value . '" name="field[' . $row . '][custom_value]" />';
						return $display . JHTML::link (JRoute::_ ('index.php?option=com_virtuemart&view=category&task=edit&virtuemart_category_id=' . (int)$field->custom_value), $thumb . ' ' . $category->category_name, array('title' => $category->category_name)) . $display;
					}
					else {
						return 'no result';
					}
				/* related product*/
				case 'R':
					if (!$field->custom_value) {
						return '';
					}
					$q = 'SELECT `product_name`,`product_sku`,`product_s_desc` FROM `#__virtuemart_products_' . VMLANG . '` as l JOIN `#__virtuemart_products` AS p using (`virtuemart_product_id`) WHERE `virtuemart_product_id`=' . (int)$field->custom_value;
					$this->_db->setQuery ($q);
					$related = $this->_db->loadObject ();
					$display = $related->product_name . '(' . $related->product_sku . ')';
					$display = '<input type="hidden" value="' . $field->custom_value . '" name="field[' . $row . '][custom_value]" />';

					$q = 'SELECT `virtuemart_media_id` FROM `#__virtuemart_product_medias`WHERE `virtuemart_product_id`= "' . (int)$field->custom_value . '" AND (`ordering` = 0 OR `ordering` = 1)';
					$this->_db->setQuery ($q);
					$thumb = '';
					if ($media_id = $this->_db->loadResult ()) {
						$thumb = $this->displayCustomMedia ($media_id);
					}
					$title= $related->product_s_desc?  $related->product_s_desc :'';
					return $display . JHTML::link (JRoute::_ ('index.php?option=com_virtuemart&view=product&task=edit&virtuemart_product_id=' . $field->custom_value), $thumb . '<br /> ' . $related->product_name, array('title' => $title));
					break;
				/* image */
				case 'M':
					if (empty($product)) {
						$vendorId = 1;
					}
					else {
						$vendorId = $product->virtuemart_vendor_id;
					}
					$q = 'SELECT `virtuemart_media_id` as value,`file_title` as text FROM `#__virtuemart_medias` WHERE `published`=1
					AND (`virtuemart_vendor_id`= "' . $vendorId . '" OR `shared` = "1")';
					$this->_db->setQuery ($q);
					$options = $this->_db->loadObjectList ();
					return JHTML::_ ('select.genericlist', $options, 'field[' . $row . '][custom_value]', '', 'value', 'text', $field->custom_value) . '</td><td>' . $priceInput;
					break;
				/* Child product Group */
				case 'G':
					break;
				/* Child product */
				/*				case 'C':
					if (empty($product)){
				   $virtuemart_product_id = JRequest::getInt('virtuemart_product_id', 0);
				   } else {
				   $virtuemart_product_id = $product->virtuemart_product_id;
				   }
				   $html = '';
				   $q='SELECT concat(`product_sku`,":",`product_name`) as text ,`virtuemart_product_id`,`product_in_stock` FROM `#__virtuemart_products` WHERE `published`=1
				   AND `virtuemart_product_id`= "'.$field->custom_value.'"';
				   //$db->setQuery(' SELECT virtuemart_product_id, product_name FROM `#__virtuemart_products` WHERE `product_parent_id` ='.(int)$product_id);
				   $this->_db->setQuery($q);
				   if ($child = $this->_db->loadObject()) {
				   $html .= JHTML::link ( JRoute::_ ( 'index.php?option=com_virtuemart&view=product&task=edit&virtuemart_product_id='.$field->custom_value), $child->text.' ('.$field->custom_value.')', array ('title' => $child->text ));
				   $html .= ' '.JText::_('COM_VIRTUEMART_PRODUCT_FORM_IN_STOCK').':'.$child->product_in_stock ;
				   $html .= '<input type="hidden" value="'.$child->virtuemart_product_id.'" name="field['.$row.'][custom_value]" /></div><div>'.$priceInput;
				   return $html;
				   //					return '<input type="text" value="'.$field->custom_value.'" name="field['.$row.'][custom_value]" />';
				   }
				   else return JText::_('COM_VIRTUEMART_CUSTOM_NO_CHILD_PRODUCT');
				   break;*/
			}

		}
	}

	public function getProductCustomsField ($product) {

		$query = 'SELECT C.`virtuemart_custom_id` , `custom_element`, `custom_params`, `custom_parent_id` , `admin_only` , `custom_title` , `custom_tip` , C.`custom_value` AS value, `custom_field_desc` , `field_type` , `is_list` , `is_hidden`, `layout_pos`, C.`published` , field.`virtuemart_customfield_id` , field.`custom_value`, field.`custom_param`, field.`custom_price`, field.`ordering`
			FROM `#__virtuemart_customs` AS C
			LEFT JOIN `#__virtuemart_product_customfields` AS field ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
			Where `virtuemart_product_id` =' . (int)$product->virtuemart_product_id . ' and `field_type` != "G" and `field_type` != "R" and `field_type` != "Z"';
		$query .= ' and is_cart_attribute = 0 order by field.`ordering`,virtuemart_custom_id';
		$this->_db->setQuery ($query);
		if ($productCustoms = $this->_db->loadObjectList ()) {

			$row = 0;
			if (!class_exists ('vmCustomPlugin')) {
				require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');
			}
			foreach ($productCustoms as $field) {
				if ($field->field_type == "E") {
					$field->display = '';
					JPluginHelper::importPlugin ('vmcustom');
					$dispatcher = JDispatcher::getInstance ();
					$ret = $dispatcher->trigger ('plgVmOnDisplayProductFE', array($product, &$row, &$field));

				}
				else {
					$field->display = $this->displayProductCustomfieldFE ($product, $field, $row);
				}
				$row++;
			}
			return $productCustoms;
		}
		else {
			return array();
		}
	}

	public function getProductCustomsFieldRelatedCategories ($product) {

		$query = 'SELECT C.`virtuemart_custom_id` , `custom_parent_id` , `admin_only` , `custom_title` , `custom_tip` , C.`custom_value` AS value, `custom_field_desc` , `field_type` , `is_list` , `is_hidden` , C.`published` , field.`virtuemart_customfield_id` , field.`custom_value`, field.`custom_param`, field.`custom_price`, field.`ordering`
			FROM `#__virtuemart_customs` AS C
			LEFT JOIN `#__virtuemart_product_customfields` AS field ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
			Where `virtuemart_product_id` =' . (int)$product->virtuemart_product_id . ' and `field_type` = "Z"';
		$query .= ' and is_cart_attribute = 0 order by virtuemart_custom_id';
		$this->_db->setQuery ($query);
		if ($productCustoms = $this->_db->loadObjectList ()) {
			$row = 0;
			foreach ($productCustoms as & $field) {
				$field->display = $this->displayProductCustomfieldFE ($product, $field, $row);
				$row++;
			}
			return $productCustoms;
		}
		else {
			return array();
		}
	}

	public function getProductCustomsFieldRelatedProducts ($product) {

		$query = 'SELECT C.`virtuemart_custom_id` , `custom_parent_id` , `admin_only` , `custom_title` , `custom_tip` , C.`custom_value` AS value, `custom_field_desc` , `field_type` , `is_list` , `is_hidden` , C.`published` , field.`virtuemart_customfield_id` , field.`custom_value`, field.`custom_param`, field.`custom_price`, field.`ordering`
			FROM `#__virtuemart_customs` AS C
			LEFT JOIN `#__virtuemart_product_customfields` AS field ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
			Where `virtuemart_product_id` =' . (int)$product->virtuemart_product_id . ' and `field_type` = "R"';
		$query .= ' and is_cart_attribute = 0 order by virtuemart_customfield_id';
		$this->_db->setQuery ($query);
		if ($productCustoms = $this->_db->loadObjectList ()) {
			$row = 0;
			foreach ($productCustoms as & $field) {
				$field->display = $this->displayProductCustomfieldFE ($product, $field, $row);
				$row++;
			}
			return $productCustoms;
		}
		else {
			return array();
		}
	}

	/**
	 * Display for the cart
	 *
	 * @author Patrick Kohl
	 * @param obj $product product object
	 * @return html code
	 */
	public function getProductCustomsFieldCart ($product) {

		// group by virtuemart_custom_id
		$query = 'SELECT C.`virtuemart_custom_id`, `custom_title`, C.`custom_value`,`custom_field_desc` ,`custom_tip`,`field_type`,field.`virtuemart_customfield_id`,`is_hidden`
				FROM `#__virtuemart_customs` AS C
				LEFT JOIN `#__virtuemart_product_customfields` AS field ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
				Where `virtuemart_product_id` =' . (int)$product->virtuemart_product_id . ' and `field_type` != "G" and `field_type` != "R" and `field_type` != "Z"';
		$query .= ' and is_cart_attribute = 1 group by virtuemart_custom_id ORDER BY field.`ordering`';

		$this->_db->setQuery ($query);
		$groups = $this->_db->loadObjectList ();

		if (!class_exists ('VmHTML')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
		}
		$row = 0;
		if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		}
		$currency = CurrencyDisplay::getInstance ();

		if (!class_exists ('calculationHelper')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
		}
		$calculator = calculationHelper::getInstance ();
		$calculator ->_product = $product;
		$calculator->_cats = $product->categories;
		$calculator->product_tax_id = isset($product->product_tax_id)? $product->product_tax_id:0;
		$calculator->product_discount_id = isset($product->product_discount_id)? $product->product_discount_id:0;

		if (!class_exists ('vmCustomPlugin')) {
			require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');
		}

		//$free = JText::_ ('COM_VIRTUEMART_CART_PRICE_FREE');
		// render select list
		foreach ($groups as $group) {

			//				$query='SELECT  field.`virtuemart_customfield_id` as value ,concat(field.`custom_value`," :bu ", field.`custom_price`) AS text
			$query = 'SELECT field.`virtuemart_product_id`, `custom_params`,`custom_element`, field.`virtuemart_custom_id`,
							field.`virtuemart_customfield_id`,field.`custom_value`, field.`custom_price`, field.`custom_param`
					FROM `#__virtuemart_customs` AS C
					LEFT JOIN `#__virtuemart_product_customfields` AS field ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
					Where `virtuemart_product_id` =' . (int)$product->virtuemart_product_id;
			$query .= ' and is_cart_attribute = 1 and C.`virtuemart_custom_id`=' . (int)$group->virtuemart_custom_id;

			// We want the field to be ordered as the user defined
			$query .= ' ORDER BY field.`ordering`';

			$this->_db->setQuery ($query);
			$options = $this->_db->loadObjectList ();
			//vmdebug('getProductCustomsFieldCart options',$options);
			$group->options = array();
			foreach ($options as $option) {
				$group->options[$option->virtuemart_customfield_id] = $option;
			}

			if ($group->field_type == 'V') {
				$default = current ($group->options);
				foreach ($group->options as $productCustom) {
					$price = self::_getCustomPrice($productCustom->custom_price, $currency, $calculator);
					$productCustom->text = $productCustom->custom_value . ' ' . $price;
				}
				$group->display = VmHTML::select ('customPrice[' . $row . '][' . $group->virtuemart_custom_id . ']', $group->options, $default->custom_value, '', 'virtuemart_customfield_id', 'text', FALSE);
			}
			else {
				if ($group->field_type == 'G') {
					$group->display .= ''; // no direct display done by plugin;
				}
				else {
					if ($group->field_type == 'E') {
						$group->display = '';

						foreach ($group->options as $k=> $productCustom) {
							$price = self::_getCustomPrice($productCustom->custom_price, $currency, $calculator);
							$productCustom->text = $productCustom->custom_value . ' ' . $price;
							$productCustom->virtuemart_customfield_id = $k;
							if (!class_exists ('vmCustomPlugin')) {
								require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');
							}

							//legacy, it will be removed 2.2
							$productCustom->value = $productCustom->virtuemart_customfield_id;
							JPluginHelper::importPlugin ('vmcustom');
							$dispatcher = JDispatcher::getInstance ();
							$fieldsToShow = $dispatcher->trigger ('plgVmOnDisplayProductVariantFE', array($productCustom, &$row, &$group));

						//	$group->display .= '<input type="hidden" value="' . $k . '" name="customPrice[' . $row . '][' . $group->virtuemart_custom_id . ']" /> ';
							$group->display .= '<input type="hidden" value="' . $productCustom->virtuemart_customfield_id . '" name="customPrice[' . $row . '][' . $productCustom->virtuemart_custom_id . ']" /> ';
							if (!empty($currency->_priceConfig['variantModification'][0]) and $price !== '') {
								$group->display .= '<div class="price-plugin">' . JText::_ ('COM_VIRTUEMART_CART_PRICE') . '<span class="price-plugin">' . $price . '</span></div>';
							}
							$row++;
						}
						$row--;
					}
					else {
						if ($group->field_type == 'U') {
							foreach ($group->options as $productCustom) {
								$price = self::_getCustomPrice($productCustom->custom_price, $currency, $calculator);
								$productCustom->text = $productCustom->custom_value . ' ' . $price;

								$group->display .= '<input type="text" value="' . JText::_ ($productCustom->custom_value) . '" name="customPrice[' . $row . '][' . $group->virtuemart_custom_id . '][' . $productCustom->value . ']" /> ';
								if (!empty($currency->_priceConfig['variantModification'][0]) and $price !== '') {
									$group->display .= '<div class="price-plugin">' . JText::_ ('COM_VIRTUEMART_CART_PRICE') . '<span class="price-plugin">' . $price . '</span></div>';
								}
							}
						}
						else {
							if ($group->field_type == 'A') {
								$group->display = '';
								foreach ($group->options as $productCustom) {
								/*	if ((float)$productCustom->custom_price) {
										$price = $currency->priceDisplay ($calculator->calculateCustomPriceWithTax ($productCustom->custom_price));
									}
									else {
										$price = ($productCustom->custom_price === '') ? '' : $free;
									}*/
									$productCustom->field_type = $group->field_type;
									$productCustom->is_cart = 1;
									$group->display .= $this->displayProductCustomfieldFE ($product, $productCustom, $row);
									$checked = '';
								}
							}
							else {

								$group->display = '';
								$checked = 'checked="checked"';
								foreach ($group->options as $productCustom) {
									//vmdebug('getProductCustomsFieldCart',$productCustom);
									$price = self::_getCustomPrice($productCustom->custom_price, $currency, $calculator);
									$productCustom->field_type = $group->field_type;
									$productCustom->is_cart = 1;
								//	$group->display .= '<input id="' . $productCustom->virtuemart_custom_id . '" ' . $checked . ' type="radio" value="' .
								//		$productCustom->virtuemart_custom_id . '" name="customPrice[' . $row . '][' . $productCustom->virtuemart_customfield_id . ']" /><label
								//		for="' . $productCustom->virtuemart_custom_id . '">' . $this->displayProductCustomfieldFE ($productCustom, $row) . ' ' . $price . '</label>';
						//MarkerVarMods
									$group->display .= '<input id="' . $productCustom->virtuemart_custom_id .$row. '" ' . $checked . ' type="radio" value="' .
										$productCustom->virtuemart_customfield_id . '" name="customPrice[' . $row . '][' . $productCustom->virtuemart_custom_id . ']" /><label
										for="' . $productCustom->virtuemart_custom_id . '" class="other-customfield">' . $this->displayProductCustomfieldFE ($product, $productCustom, $row) . ' ' . $price . '</label>';

									$checked = '';
								}
							}
						}
					}
				}
			}
			$row++;
		}

		return $groups;

	}
	static function _getCustomPrice($customPrice, $currency, $calculator) {
		if ((float)$customPrice) {
			$price = strip_tags ($currency->priceDisplay ($calculator->calculateCustomPriceWithTax ($customPrice)));
			if ($customPrice >0) {
				$price ="+".$price;
			}
		}
		else {
			$price = ($customPrice === '') ? '' :  JText::_ ('COM_VIRTUEMART_CART_PRICE_FREE');
		}
		return $price;
	}
	/**
	 * Formating front display by roles
	 *  for product only !
	 */
	public function displayProductCustomfieldFE (&$product, $customfield, $row = '') {

		$virtuemart_custom_id = isset($customfield->virtuemart_custom_id)? $customfield->virtuemart_custom_id:0;
		$value = $customfield->custom_value;
		$type = $customfield->field_type;
		$is_list = isset($customfield->is_list)? $customfield->is_list:0;
		$price = isset($customfield->custom_price)? $customfield->custom_price:0;
		$is_cart = isset($customfield->is_cart)? $customfield->is_cart:0;


		//vmdebug('displayProductCustomfieldFE and here is something wrong ',$customfield);

		if (!class_exists ('CurrencyDisplay'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		$currency = CurrencyDisplay::getInstance ();

		if ($is_list > 0) {
			$values = explode (';', $value);
			if ($is_cart != 0) {

				$options = array();

				foreach ($values as $key => $val) {
					$options[] = array('value' => $val, 'text' => $val);
				}
				vmdebug('displayProductCustomfieldFE is a list ',$options);
				return JHTML::_ ('select.genericlist', $options, 'field[' . $row . '][custom_value]', NULL, 'value', 'text', FALSE, TRUE);
			}
			else {
				$html = '';
				// 				if($type=='M'){
				// 					foreach ($values as $key => $val){
				// 						$html .= '<div id="custom_'.$virtuemart_custom_id.'_'.$val.'" >'.$this->displayCustomMedia($val).'</div>';
				// 					}

				// 				} else {
				// 					foreach ($values as $key => $val){
				$html .= '<div id="custom_' . $virtuemart_custom_id . '_' . $value . '" >' . $value . '</div>';
				// 					}
				// 				}

				return $html;
			}

		}
		else {
			if ($price > 0) {

				$price = $currency->priceDisplay ((float)$price);
			}
			switch ($type) {

				case 'A':

					$options = array();

					$session = JFactory::getSession ();
					$virtuemart_category_id = $session->get ('vmlastvisitedcategoryid', 0, 'vm');

					$productModel = VmModel::getModel ('product');

					//parseCustomParams
					VirtueMartModelCustomfields::bindParameterableByFieldType($customfield);
					//Todo preselection as dropdown of children
					//Note by Max Milbers: This is not necessary, in this case it is better to unpublish the parent and to give the child which should be preselected a category
					//Or it is withParent, in that case there exists the case, that a parent should be used as a kind of mini category and not be orderable.
					//There exists already other customs and in special plugins which wanna disable or change the add to cart button.
					//I suggest that we manipulate the button with a message "choose a variant first"
					//if(!isset($customfield->pre_selected)) $customfield->pre_selected = 0;
					$selected = JRequest::getInt ('virtuemart_product_id',0);

					$html = '';
					$uncatChildren = $productModel->getUncategorizedChildren ($customfield->withParent);

					if(empty($uncatChildren)){
						return $html;
						break;
					}

					foreach ($uncatChildren as $k => $child) {
						$options[] = array('value' => JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_category_id=' . $virtuemart_category_id . '&virtuemart_product_id=' . $child['virtuemart_product_id']), 'text' => $child['product_name']);
					}

					$html .= JHTML::_ ('select.genericlist', $options, 'field[' . $row . '][custom_value]', 'onchange="window.top.location.href=this.options[this.selectedIndex].value" size="1" class="inputbox"', "value", "text",
						JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_category_id=' . $virtuemart_category_id . '&virtuemart_product_id=' . $selected));
					//vmdebug('$customfield',$customfield);

					if($customfield->parentOrderable==0 and $product->product_parent_id==0){
						$product->orderable = FALSE;
					}

					return $html;
					break;

				/* variants*/
				case 'V':
					if ($price == 0)
						$price = JText::_ ('COM_VIRTUEMART_CART_PRICE_FREE');

					/* Loads the product price details */
					return '<input type="text" value="' . JText::_ ($value) . '" name="field[' . $row . '][custom_value]" /> ' . JText::_ ('COM_VIRTUEMART_CART_PRICE') . $price . ' ';
					break;
				/*Date variant*/
				case 'D':
					return '<span class="product_custom_date">' . vmJsApi::date ($value, 'LC1', TRUE) . '</span>'; //vmJsApi::jDate($field->custom_value, 'field['.$row.'][custom_value]','field_'.$row.'_customvalue').$priceInput;
					break;
				/* text area or editor No JText, only displayed in BE */
				case 'X':
				case 'Y':
					return $value;
					break;
				/* string or integer */
				case 'S':
				case 'I':
					return JText::_ ($value);
					break;
				/* bool */
				case 'B':
					if ($value == 0)
						return JText::_ ('COM_VIRTUEMART_NO');
					return JText::_ ('COM_VIRTUEMART_YES');
					break;
				/* parent */
				case 'P':
					return '<span class="product_custom_parent">' . JText::_ ($value) . '</span>';
					break;
				/* related */
				case 'R':
					$q = 'SELECT l.`product_name`, p.`product_parent_id` , l.`product_name`, x.`virtuemart_category_id` FROM `#__virtuemart_products_' . VMLANG . '` as l
					 JOIN `#__virtuemart_products` AS p using (`virtuemart_product_id`)
					 LEFT JOIN `#__virtuemart_product_categories` as x on x.`virtuemart_product_id` = p.`virtuemart_product_id`
					 WHERE p.`published`=1 AND  p.`virtuemart_product_id`= "' . (int)$value . '" ';
					$this->_db->setQuery ($q);
					$related = $this->_db->loadObject ();
					if (empty ($related))
						return '';
					$thumb = '';
					$q = 'SELECT `virtuemart_media_id` FROM `#__virtuemart_product_medias`WHERE `virtuemart_product_id`= "' . (int)$value . '" AND (`ordering` = 0 OR `ordering` = 1)';
					$this->_db->setQuery ($q);
					$thumb="";
					if ($media_id = $this->_db->loadResult ()) {
						$thumb = $this->displayCustomMedia ($media_id).' ';
					}
					return JHTML::link (JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $value . '&virtuemart_category_id=' . $related->virtuemart_category_id), $thumb   . $related->product_name, array('title' => $related->product_name));
					break;
				/* image */
				case 'M':
					return $this->displayCustomMedia ($value);
					break;
				/* categorie */
				case 'Z':
					$q = 'SELECT * FROM `#__virtuemart_categories_' . VMLANG . '` as l JOIN `#__virtuemart_categories` AS c using (`virtuemart_category_id`) WHERE `published`=1 AND l.`virtuemart_category_id`= "' . (int)$value . '" ';
					$this->_db->setQuery ($q);
					if ($category = $this->_db->loadObject ()) {
						$q = 'SELECT `virtuemart_media_id` FROM `#__virtuemart_category_medias`WHERE `virtuemart_category_id`= "' . $category->virtuemart_category_id . '" ';
						$this->_db->setQuery ($q);
						$thumb = '';
						if ($media_id = $this->_db->loadResult ()) {
							$thumb = $this->displayCustomMedia ($media_id,'category');
						}
						return JHTML::link (JRoute::_ ('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id), $thumb . ' ' . $category->category_name, array('title' => $category->category_name));
					}
					else return '';
				/* Child Group list
						  * this have no direct display , used for stockable product
						 */
				case 'G':
					return ''; //'<input type="text" value="'.JText::_($value).'" name="field['.$row.'][custom_value]" /> '.JText::_('COM_VIRTUEMART_CART_PRICE').' : '.$price .' ';
					break;
					break;
			}
		}
	}

	function displayCustomMedia ($media_id, $table = 'product', $absUrl = FALSE) {

		if (!class_exists ('TableMedias'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'medias.php');
		//$data = $this->getTable('medias');
		$db = JFactory::getDBO ();
		$data = new TableMedias($db);
		$data->load ((int)$media_id);

		if (!class_exists ('VmMediaHandler'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'mediahandler.php');
		$media = VmMediaHandler::createMedia ($data, $table);

		return $media->displayMediaThumb ('', FALSE, '', TRUE, TRUE, $absUrl);

	}

	/**
	 * There are too many functions doing almost the same for my taste
	 * the results are sometimes slighty different and makes it hard to work with it, therefore here the function for future proxy use
	 *
	 */
	public function customFieldDisplay ($product, $variantmods, $html, $trigger) {

		//vmdebug('customFieldDisplay $variantmods',$variantmods);
		$row = 0;
		if (!class_exists ('shopFunctionsF'))
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
		//MarkerVarMods
		foreach ($variantmods as $selected => $variant) {
		//foreach ($variantmods as $variant=> $selected) {
			//vmdebug('customFieldDisplay '.$variant.' '.$selected);
			if ($selected) {

				$productCustom = self::getProductCustomField ($selected);
				//vmdebug('customFieldDisplay',$selected,$productCustom);
				if (!empty($productCustom)) {
					$html .= ' <span class="product-field-type-' . $productCustom->field_type . '">';
					if ($productCustom->field_type == "E") {

						$product = self::addParam ($product);
						$product->productCustom = $productCustom;
						//vmdebug('CustomsFieldCartDisplay $productCustom',$productCustom);
// 								vmdebug('customFieldDisplay $product->param selected '.$selected,$product->param);
						if (!class_exists ('vmCustomPlugin'))
							require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');
						JPluginHelper::importPlugin ('vmcustom');
						$dispatcher = JDispatcher::getInstance ();
						$dispatcher->trigger ($trigger, array($product, $row, &$html));

					}
					else {
						//vmdebug('customFieldDisplay $productCustom by self::getProductCustomField $variant: '.$variant.' $selected: '.$selected,$productCustom);
						$value = '';
						if (($productCustom->field_type == "G")) {

							$child = self::getChild ($productCustom->custom_value);
							// 						$html .= $productCustom->custom_title.' '.$child->product_name;
							$value = $child->product_name;
						}
						elseif (($productCustom->field_type == "M")) {
							// 						$html .= $productCustom->custom_title.' '.self::displayCustomMedia($productCustom->custom_value);
							$value = self::displayCustomMedia ($productCustom->custom_value);
						}
						elseif (($productCustom->field_type == "S")) {
							// 					q	$html .= $productCustom->custom_title.' '.JText::_($productCustom->custom_value);
							$value = $productCustom->custom_value;
						}
						else {
							// 						$html .= $productCustom->custom_title.' '.$productCustom->custom_value;
							//vmdebug('customFieldDisplay',$productCustom);
							$value = $productCustom->custom_value;
						}
						$html .= ShopFunctionsF::translateTwoLangKeys ($productCustom->custom_title, $value);
					}
					$html .= '</span><br />';
				}
				else {
					// falldown method if customfield are deleted
					foreach ((array)$selected as $key => $value) {
						$html .= '<br/ >Couldnt find customfield' . ($key ? '<span>' . $key . ' </span>' : '') . $value;
					}
					vmdebug ('CustomsFieldOrderDisplay, $item->productCustom empty? ' . $variant);
					vmdebug ('customFieldDisplay, $productCustom is EMPTY ');
				}

			}
			$row++;
		}

	//	vmdebug ('customFieldDisplay html begin: ' . $html . ' end');
		return $html . '</div>';
	}

	/**
	 * TODO This is html and view stuff and MUST NOT be in the model, notice by Max
	 * render custom fields display cart module FE
	 */
	public function CustomsFieldCartModDisplay ($priceKey, $product) {

		if (empty($calculator)) {
			if (!class_exists ('calculationHelper'))
				require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
			$calculator = calculationHelper::getInstance ();
		}

		$variantmods = $calculator->parseModifier ($priceKey);

		return self::customFieldDisplay ($product, $variantmods, '<div class="vm-customfield-mod">', 'plgVmOnViewCartModule');

	}

	/**
	 *  TODO This is html and view stuff and MUST NOT be in the model, notice by Max
	 * render custom fields display cart FE
	 */
	public function CustomsFieldCartDisplay ($priceKey, $product) {

		if (empty($calculator)) {
			if (!class_exists ('calculationHelper'))
				require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
			$calculator = calculationHelper::getInstance ();
		}

		vmdebug('CustomsFieldCartDisplay ',$priceKey);
		$variantmods = $calculator->parseModifier ($priceKey);

		return self::customFieldDisplay ($product, $variantmods, '<div class="vm-customfield-cart">', 'plgVmOnViewCart');

	}

	/*
		   * render custom fields display order BE/FE
		  */
	public function CustomsFieldOrderDisplay ($item, $view = 'FE', $absUrl = FALSE) {

		$row = 0;
		// 		$item=(array)$item;
		if (!empty($item->product_attribute)) {
			$item->param = json_decode ($item->product_attribute, TRUE);
// 					$html = '<div class="vm-customfield-cart">';
			if (!empty($item->param)) {
				return self::customFieldDisplay ($item, $item->param, '<div class="vm-customfield-cart">', 'plgVmDisplayInOrder' . $view);

			}
			else {
				vmdebug ('CustomsFieldOrderDisplay $item->param empty? ');
			}
		}
		else {
			// 			vmTrace('$item->product_attribut is empty');
		}
		return FALSE;
	}

	/**
	 *
	 * custom fields for cart and cart module
	 */
	public function getProductCustomField ($selected) {

		$db = JFactory::getDBO ();
		$query = 'SELECT C.`virtuemart_custom_id` , `custom_element` , `custom_parent_id` , `admin_only` , `custom_title` , `custom_tip` ,
		C.`custom_value` AS value, `custom_field_desc` , `field_type` , `is_list` , `is_cart_attribute` , `is_hidden` , C.`published` ,
		field.`virtuemart_customfield_id` , field.`custom_value`,field.`custom_param`,field.`custom_price`
			FROM `#__virtuemart_customs` AS C
			LEFT JOIN `#__virtuemart_product_customfields` AS field ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
			WHERE `virtuemart_customfield_id` ="' . (int)$selected . '"';
		// 		if($product_parent_id!=0){
		// 			$query .= ' AND (`virtuemart_product_id` ="' . $product_id.'" XOR `virtuemart_product_id` ="' . $product_parent_id.'")';
		// 		} else {
		// 			$query .= ' AND (`virtuemart_product_id` ="' . $product_id.'"';
		// 		}
		$db->setQuery ($query);
		return $db->loadObject ();
	}

	/*
		   * add parameter to product definition
		  */
	public function addParam ($product) {

// 				vmdebug('addParam? ',$product->custom_param,$product->customPlugin);
		$custom_param = empty($product->custom_param) ? array() : json_decode ($product->custom_param, TRUE);
		$product_param = empty($product->customPlugin) ? array() : json_decode ($product->customPlugin, TRUE);
		$params = (array)$product_param + (array)$custom_param;
		foreach ($params as $key => $param) {
			$product->param[$key] = $param;
		}
		return $product;
	}

	public function getChild ($child) {

		$db = JFactory::getDBO ();
		$db->setQuery ('SELECT  `product_sku`, `product_name` FROM `#__virtuemart_products_' . VMLANG . '` WHERE virtuemart_product_id=' . $child);
		return $db->loadObject ();
	}

	static public function setEditCustomHidden ($customfield, $i) {

		if (!isset($customfield->virtuemart_customfield_id))
			$customfield->virtuemart_customfield_id = '0';
		$html = '
			<input type="hidden" value="' . $customfield->field_type . '" name="field[' . $i . '][field_type]" />
			<input type="hidden" value="' . $customfield->virtuemart_custom_id . '" name="field[' . $i . '][virtuemart_custom_id]" />
			<input type="hidden" value="' . $customfield->virtuemart_customfield_id . '" name="field[' . $i . '][virtuemart_customfield_id]" />
			<input type="hidden" value="' . $customfield->admin_only . '" checked="checked" name="field[' . $i . '][admin_only]" />';
		return $html;

	}
}
// pure php no closing tag
