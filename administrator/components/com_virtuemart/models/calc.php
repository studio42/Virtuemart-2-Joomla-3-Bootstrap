<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
/**
*
* Data module for shop calculation rules
*
* @package	VirtueMart
* @subpackage  Calculation tool
* @author Max Milbers
* @author mediaDESIGN> St.Kraft 2013-02-24 manufacturer relation added
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: calc.php 6396 2012-09-05 17:35:36Z Milbo $
*/


if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');

class VirtueMartModelCalc extends VmModel {


    /**
     * Constructor for the calc model.
     *
     * The calc id is read and detmimined if it is an array of ids or just one single id.
     *
     * @author RickG
     */
    public function __construct(){

    	parent::__construct();

			$this->setMainTable('calcs');
			$this->setToggleName('calc_shopper_published');
			$this->setToggleName('calc_vendor_published');
	  	$this->setToggleName('shared');
			$this->addvalidOrderingFieldName(array('virtuemart_category_id','virtuemart_country_id','virtuemart_state_id','virtuemart_shoppergroup_id'
				,'virtuemart_manufacturer_id'
			)); 
    }


    /**
     * Retrieve the detail record for the current $id if the data has not already been loaded.
     *
     * @author Max Milbers
     */
	public function getCalc(){

  	if (empty($this->_data)) {
  		if(empty($this->_db)) $this->_db = JFactory::getDBO();

   		$this->_data = $this->getTable('calcs');
   		$this->_data->load((int)$this->_id);

			$xrefTable = $this->getTable('calc_categories');
			$this->_data->calc_categories = $xrefTable->load($this->_id);
			if ( $xrefTable->getError() ) {
				vmError(get_class( $this ).' calc_categories '.$xrefTable->getError());
			}

			$xrefTable = $this->getTable('calc_shoppergroups');
			$this->_data->virtuemart_shoppergroup_ids = $xrefTable->load($this->_id);
			if ( $xrefTable->getError() ) {
				vmError(get_class( $this ).' calc_shoppergroups '.$xrefTable->getError());
			}

			$xrefTable = $this->getTable('calc_countries');
			$this->_data->calc_countries = $xrefTable->load($this->_id);
			if ( $xrefTable->getError() ) {
				vmError(get_class( $this ).' calc_countries '.$xrefTable->getError());
			}

			$xrefTable = $this->getTable('calc_states');
			$this->_data->virtuemart_state_ids = $xrefTable->load($this->_id);
			if ( $xrefTable->getError() ) {
				vmError(get_class( $this ).' virtuemart_state_ids '.$xrefTable->getError());
			}

			$xrefTable = $this->getTable('calc_manufacturers');
			$this->_data->virtuemart_manufacturers = $xrefTable->load($this->_id);
			if ( $xrefTable->getError() ) {
				vmError(get_class( $this ).' calc_manufacturers '.$xrefTable->getError());
			}

			
			JPluginHelper::importPlugin('vmcalculation');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('plgVmGetPluginInternalDataCalc',array(&$this->_data));

  	}

// 		if($errs = $this->getErrors()){
// 			$app = JFactory::getApplication();
// 			foreach($errs as $err){
// 				$app->enqueueMessage($err);
// 			}
// 		}

// 		vmdebug('my calc',$this->_data);
  		return $this->_data;
	}

	/**
	 * Retrieve a list of calculation rules from the database.
	 *
     * @author Max Milbers
     * @param string $onlyPuiblished True to only retreive the published Calculation rules, false otherwise
     * @param string $noLimit True if no record count limit is used, false otherwise
	 * @return object List of calculation rule objects
	 */
	public function getCalcs($onlyPublished=false, $noLimit=false, $search=false){

		$where = array();
		$this->_noLimit = $noLimit;

		// add filters
		if ($onlyPublished) $where[] = '`published` = 1';

		if($search){
			$db = JFactory::getDBO();
			$search = '"%' . $db->getEscaped( $search, true ) . '%"' ;
			$where[] = ' `calc_name` LIKE '.$search.' OR `calc_descr` LIKE '.$search.' OR `calc_value` LIKE '.$search.' ';
		}

		$whereString= '';
		if (count($where) > 0) $whereString = ' WHERE '.implode(' AND ', $where) ;

		$this->_data = $this->exeSortSearchListQuery(0,'*',' FROM `#__virtuemart_calcs`',$whereString,'',$this->_getOrdering());

		if(!class_exists('shopfunctions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'shopfunctions.php');
		foreach ($this->_data as $data){

			/* Write the first 5 categories in the list */
			$data->calcCategoriesList = shopfunctions::renderGuiList('virtuemart_category_id','#__virtuemart_calc_categories','virtuemart_calc_id',$data->virtuemart_calc_id,'category_name','#__virtuemart_categories','virtuemart_category_id','category');

			/* Write the first 5 shoppergroups in the list */
			$data->calcShoppersList = shopfunctions::renderGuiList('virtuemart_shoppergroup_id','#__virtuemart_calc_shoppergroups','virtuemart_calc_id',$data->virtuemart_calc_id,'shopper_group_name','#__virtuemart_shoppergroups','virtuemart_shoppergroup_id','shoppergroup',4,false);

			/* Write the first 5 countries in the list */
			$data->calcCountriesList = shopfunctions::renderGuiList('virtuemart_country_id','#__virtuemart_calc_countries','virtuemart_calc_id',$data->virtuemart_calc_id,'country_name','#__virtuemart_countries','virtuemart_country_id','country',4,false);

			/* Write the first 5 states in the list */
			$data->calcStatesList = shopfunctions::renderGuiList('virtuemart_state_id','#__virtuemart_calc_states','virtuemart_calc_id',$data->virtuemart_calc_id,'state_name','#__virtuemart_states','virtuemart_state_id','state',4,false);

			/* Write the first 5 manufacturers in the list */
			$data->calcManufacturersList = shopfunctions::renderGuiList('virtuemart_manufacturer_id','#__virtuemart_calc_manufacturers','virtuemart_calc_id',$data->virtuemart_calc_id,'mf_name','#__virtuemart_manufacturers','virtuemart_manufacturer_id','manufacturer');

			$query = 'SELECT `currency_name` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id` = "'.(int)$data->calc_currency.'" ';
			$this->_db->setQuery($query);
			$data->currencyName = $this->_db->loadResult();

			JPluginHelper::importPlugin('vmcalculation');
			$dispatcher = JDispatcher::getInstance();
			$error = $dispatcher->trigger('plgVmGetPluginInternalDataCalcList',array(&$data));
		}

		return $this->_data;
	}

	/**
	 * Bind the post data to the calculation table and save it
     *
     * @author Max Milbers
     * @return boolean True is the save was successful, false otherwise.
	 */
    public function store(&$data) {

		JRequest::checkToken() or jexit( 'Invalid Token, in store calc');

		$table = $this->getTable('calcs');

		// Convert selected dates to MySQL format for storing.
		$startDate = JFactory::getDate($data['publish_up']);
		$data['publish_up'] = $startDate->toMySQL();
//		if ($data['publish_down'] == '' or $data['publish_down']==0){
		if (empty($data['publish_down']) || trim($data['publish_down']) == JText::_('COM_VIRTUEMART_NEVER')){
			if(empty($this->_db)) $this->_db = JFactory::getDBO();
			$data['publish_down']	= $this->_db->getNullDate();
		} else {
			$expireDate = JFactory::getDate($data['publish_down']);
			$data['publish_down']	= $expireDate->toMySQL();
		}

		$table->bindChecknStore($data);
		if($table->getError()){
			vmError('Calculation store '.$table->getError());
			return false;
		}

    	$xrefTable = $this->getTable('calc_categories');
    	$xrefTable->bindChecknStore($data);
    	if($xrefTable->getError()){
			vmError('Calculation store '.$xrefTable->getError());
		}

		$xrefTable = $this->getTable('calc_shoppergroups');
    	$xrefTable->bindChecknStore($data);
    	if($xrefTable->getError()){
			vmError('Calculation store '.$xrefTable->getError());
		}

		$xrefTable = $this->getTable('calc_countries');
    	$xrefTable->bindChecknStore($data);
    	if($xrefTable->getError()){
			vmError('Calculation store '.$xrefTable->getError());
		}

		$xrefTable = $this->getTable('calc_states');
    	$xrefTable->bindChecknStore($data);
    	if($xrefTable->getError()){
			vmError('Calculation store '.$xrefTable->getError());
		}

		$xrefTable = $this->getTable('calc_manufacturers');
    	$xrefTable->bindChecknStore($data);
    	if($xrefTable->getError()){
			vmError('Calculation store '.$xrefTable->getError());
		}

		if (!class_exists('vmCalculationPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmcalculationplugin.php');
		JPluginHelper::importPlugin('vmcalculation');
		$dispatcher = JDispatcher::getInstance();
		$error = $dispatcher->trigger('plgVmStorePluginInternalDataCalc',array(&$data));

    	$errMsg = $this->_db->getErrorMsg();
		$errs = $this->_db->getErrors();

		if(!empty($errMsg)){

			$errNum = $this->_db->getErrorNum();
			vmError('SQL-Error: '.$errNum.' '.$errMsg.' <br /> used query '.$this->_db->getQuery());
		}

		if(!empty($errs)){
			foreach($errs as $err){
				if(!empty($err)) vmError('Calculation store '.$err);
			}
		}

		return $table->virtuemart_calc_id;
	}

	static function getRule($kind){

		if (!is_array($kind)) $kind = array($kind);
		$db = JFactory::getDBO();

		$nullDate		= $db->getNullDate();
		$now			= JFactory::getDate()->toMySQL();

		$q = 'SELECT * FROM `#__virtuemart_calcs` WHERE ';
		foreach ($kind as $field){
			$q .= '`calc_kind`='.$db->Quote($field).' OR ';
		}
		$q=substr($q,0,-3);

		$q .= 'AND ( publish_up = "' . $db->getEscaped($nullDate) . '" OR publish_up <= "' . $db->getEscaped($now) . '" )
				AND ( publish_down = "' . $db->getEscaped($nullDate) . '" OR publish_down >= "' . $db->getEscaped($now) . '" ) ';


		$db->setQuery($q);
		$data = $db->loadObjectList();

		if (!$data) {
   			$data = new stdClass();
  		}
  		return $data;
	}

	/**
	* Delete all calcs selected
	*
	* @author Max Milbers
	* @param  array $cids categories to remove
	* @return boolean if the item remove was successful
	*/
	public function remove($cids) {

		JRequest::checkToken() or jexit( 'Invalid Token, in remove category');

		$table = $this->getTable($this->_maintablename);
		$cat = $this->getTable('calc_categories');
		$sgrp = $this->getTable('calc_shoppergroups');
		$countries = $this->getTable('calc_countries');
		$states = $this->getTable('calc_states');
		$manufacturers = $this->getTable('calc_manufacturers');

		$ok = true;

		foreach($cids as $id) {
			$id = (int)$id;
			vmdebug('remove '.$id);
			if (!$table->delete($id)) {
				vmError(get_class( $this ).'::remove '.$id.' '.$table->getError());
				$ok = false;
			}

			if (!$cat->delete($id)) {
				vmError(get_class( $this ).'::remove '.$id.' '.$cat->getError());
				$ok = false;
			}

			if (!$sgrp->delete($id)) {
				vmError(get_class( $this ).'::remove '.$id.' '.$sgrp->getError());
				$ok = false;
			}

			if (!$countries->delete($id)) {
				vmError(get_class( $this ).'::remove '.$id.' '.$countries->getError());
				$ok = false;
			}

			if (!$states->delete($id)) {
				vmError(get_class( $this ).'::remove '.$id.' '.$states->getError());
				$ok = false;
			}

			// Mod. <mediaDESIGN> St.Kraft 2013-02-24
			if (!$manufacturers->delete($id)) {
				vmError(get_class( $this ).'::remove '.$id.' '.$manufacturers->getError());
				$ok = false;
			}

// 			if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
			JPluginHelper::importPlugin('vmcalculation');
			$dispatcher = JDispatcher::getInstance();
			$returnValues = $dispatcher->trigger('plgVmDeleteCalculationRow', array( $id));

		}

		return $ok;
	}

	static function getTaxes() {

		return self::getRule(array('TAX','VatTax','TaxBill'));
	}

	static function getDiscounts(){
		return  self::getRule(array('DATax','DATaxBill','DBTax','DBTaxBill'));
	}

	static function getDBDiscounts() {

		return self::getRule(array('DBTax','DBTaxBill'));
	}

	static function getDADiscounts() {

		return self::getRule(array('DATax','DATaxBill'));
	}
}