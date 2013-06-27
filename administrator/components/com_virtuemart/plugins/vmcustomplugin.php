<?php
defined ('_JEXEC') or die('Direct Access to ' . basename (__FILE__) . ' is not allowed.');

/**
 * Abstract class for shipment plugins
 *
 * @package    VirtueMart
 * @subpackage Plugins
 * @author Oscar van Eijk
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: vmpsplugin.php 4007 2011-08-31 07:31:35Z alatak $
 */
// Load the helper functions that are needed by all plugins
if (!class_exists ('VmHTML')) {
	require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
}

// Get the plugin library
jimport ('joomla.plugin.plugin');

if (!class_exists ('vmPlugin')) {
	require(JPATH_VM_PLUGINS . DS . 'vmplugin.php');
}

/**
 * @package    VirtueMart
 * @subpackage Plugins
 * @author Oscar van Eijk
 * @author Patrick Kohl
 * @author Max Milbers
 */
abstract class vmCustomPlugin extends vmPlugin {


	/**
	 * @var array List with all carriers the have been implemented with the plugin in the format
	 * id => name
	 */
	protected $customs;

	function __construct (& $subject, $config) {

		parent::__construct ($subject, $config);

		$this->_tablepkey = 'virtuemart_product_id';
		$this->_tablename = '#__virtuemart_product_' . $this->_psType . '_plg_' . $this->_name;
		$this->_idName = 'virtuemart_custom_id';
		$this->_configTableFileName = $this->_psType . 's';
		$this->_configTableClassName = 'Table' . ucfirst ($this->_psType) . 's'; //TablePaymentmethods
		$this->_configTable = '#__virtuemart_customs';

	}

	function onDisplayEditBECustom ($virtuemart_custom_id, &$customPlugin) {

		//if($this->plugin = $this->selectedThisByMethodId($this->_psType,$virtuemart_custom_id)){
		if ($this->plugin = $this->selectedThisByMethodId ($virtuemart_custom_id)) {

			if (empty($this->plugin)) {
				$this->plugin->custom_jplugin_id = NULL;
				return $this->plugin;
			}

			//Must use here the table to get valid params
			$this->plugin = $this->getVmPluginMethod ($this->plugin->virtuemart_custom_id);

			if (empty($this->plugin->virtuemart_vendor_id)) {
				if (!class_exists ('VirtueMartModelVendor')) {
					require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'vendor.php');
				}
				$this->plugin->virtuemart_vendor_id = VirtueMartModelVendor::getLoggedVendor ();
			}
			$customPlugin = $this->plugin;
			//   		return $this->plugin;
			return TRUE;
		}
	}

	/*
	 * helper to parse plugin parameters as object
	 *
	 */
	public function parseCustomParams (&$field, $xParams = 'custom_params') {

		VmTable::bindParameterable ($field, $xParams, $this->_varsToPushParam);

		if (empty($field->custom_element)) {
			return 0;
		}
		if (!empty($field->custom_param) && is_string ($field->custom_param)) {
			$custom_param = json_decode ($field->custom_param, TRUE);
		}
		else {
			return;
		}
		//$field->custom_param = $custom_param;
		foreach ($custom_param as $k => $v) {
			if (!empty($v)) {
				$field->$k = $v;
			}
		}
	}

	/*
		 * helper to get plugin parameters as object
		 * All params are added to $this->params plugin
		 */
	public function getCustomParams (&$field) {

		VmTable::bindParameterable ($field, 'custom_params', $this->_varsToPushParam);

		//Why do we have this?
		if (empty($field->custom_element)) {
			return 0;
		}

		//Why do we have this, when bindParameterable could already doing it
		//And why we do it here, when we do it later again?
		foreach ($this->_varsToPushParam as $k => $v) {
			if (!isset($this->params->$k)) {
				$this->params->$k = $field->$k;
			}
			// vmdebug('fields org '.$this->_name,$this->params);
		}
		$this->virtuemart_custom_id = $field->virtuemart_custom_id;
		if (!empty($field->custom_param) && is_string ($field->custom_param)) {
			$this->params = json_decode ($field->custom_param);
		}
		else {
			return;
		}

		//$field->custom_param = $custom_param;
		//vmdebug('$this->_varsToPushParam '.$this->_name,$this->_varsToPushParam );
		foreach ($this->_varsToPushParam as $k => $v) {
			if (!isset($this->params->$k)) {
				$this->params->$k = $field->$k;
			}
		}

	}

	/**
	 * Helper to add all params of specific product of this custom to an object
	 *
	 * @param object $field
	 * @param int    $product_id
	 */
	protected function getPluginProductDataCustom (&$field, $product_id) {

		$id = $this->getIdForCustomIdProduct ($product_id, $field->virtuemart_custom_id);

		$datas = $this->getPluginInternalData ($id);
		if ($datas) {
			//$fields = get_object_vars($datas);
			// vmdebug('datas',$datas);
			foreach ($datas as $k=> $v) {
				if (!is_string ($v)) {
					continue;
				} // Only get real Table variable
				if (isset($field->$k) && $v === 0) {
					continue;
				}
				$field->$k = $v;
			}
		}

	}

	/**
	 * helper to get plugin table as object
	 * All params are added to $this->params plugin
	 *
	 * @param unknown_type $field
	 * @param unknown_type $product_id
	 */
	protected function getPluginCustomData (&$field, $product_id) {

		$id = $this->getIdForCustomIdProduct ($product_id, $field->virtuemart_custom_id);

		$datas = $this->getPluginInternalData ($id);
		if ($datas) {
			foreach ($this->_varsToPushParam as $k => $v) {
				if (!isset($datas->$k)) {
					continue;
				}
				if (isset($this->params->$k) && $datas->$k == 0) {
					continue;
				}
				$this->params->$k = $datas->$k;
			}
		}
	}


	/**
	 * This is the actions which take place, when a product gets stored
	 *
	 * @param string $type atm valid 'product'
	 * @param array  $data form data
	 * @param int    $id virtuemart_product_id
	 */
	function OnStoreProduct ($data, $plugin_param) {

		if (key ($plugin_param) !== $this->_name) {
			vmdebug('OnStoreProduct return because key '.key ($plugin_param).'!== '. $this->_name);
			return;
		}

		$key = key ($plugin_param);
		$plugin_param[$key]['virtuemart_product_id'] = $data['virtuemart_product_id'];
		//vmdebug ('plgData', $plugin_param[$key]);
		// $this->id = $this->getIdForCustomIdProduct($data['virtuemart_product_id'],$plugin_param[$key]['virtuemart_custom_id']);
		$this->storePluginInternalDataProduct ($plugin_param[$key], 'id', $data['virtuemart_product_id']);
	}

	/**
	 * This stores the data of the plugin, attention NOT the configuration of the pluginmethod,
	 * this function should never be triggered only called from triggered functions.
	 *
	 * @author Max Milbers
	 * @param array  $values array or object with the data to store
	 * @param string $tableName When different then the default of the plugin, provid it here
	 * @param string $tableKey an additionally unique key
	 */
	protected function storePluginInternalDataProduct (&$values, $primaryKey = 0, $product_id = 0) {
		$custom_id = $values['virtuemart_custom_id'];
		$db = JFactory::getDBO ();
		if (!empty($custom_id) && !empty($product_id)) {
			$_qry = 'SELECT `id` FROM `#__virtuemart_product_custom_plg_' . $this->_name . '` WHERE `virtuemart_product_id`=' . (int)$product_id . ' and `virtuemart_custom_id`=' . (int)$custom_id;
			$db->setQuery ($_qry);
			$id = $db->loadResult ();
		}
		$values['id'] = $id ? $id : 0;
		// vmdebug('$value',$values, $id);
		$this->storePluginInternalData ($values);

		return $values;

	}

	/**
	 * Calculate the variant price by The plugin
	 * override calculateModificators() in calculatorh.
	 * Eg. recalculate price by a quantity set in the plugin
	 * You must reimplement modifyPrice() in your plugin
	 * or price is returned defaut custom_price
	 */
	// 	 public function plgVmCalculatePluginVariant( $product, $field,$selected,$row){
	public function getCustomVariant ($product, &$productCustomsPrice, $selected) {
		if ($productCustomsPrice->custom_element !== $this->_name) {
			return FALSE;
		}

		vmPlugin::declarePluginParams ('custom', $productCustomsPrice->custom_element, $productCustomsPrice->custom_jplugin_id, $productCustomsPrice);
// 		VmTable::bindParameterable($productCustomsPrice,'custom_params',$this->_varsToPushParam);

		$pluginFields = JRequest::getVar ('customPlugin', NULL);
		if ($pluginFields == NULL and isset($product->customPlugin)) {
			$pluginFields = json_decode ($product->customPlugin, TRUE);
		}

		return $pluginFields[$productCustomsPrice->virtuemart_customfield_id][$this->_name];

	}

	/**
	 * convert param for render and
	 * display The plugin in cart
	 * return null if not $this->_name
	 */
	public function GetPluginInCart ($product) {
		//$plgName = $productCustom->value;

		if (!empty($product->param)) {
			if (!is_array ($product->param)) {
				return FALSE;
			}
			$param = array();

// 			vmdebug('$product->param',$product->param);
			foreach ($product->param as $k => $plg) {

				if (is_array ($plg) and key ($plg) == $this->_name) {
					$param[$k] = $plg[$this->_name];
				}
			}
			if ($param) {
				return $param;
			}
		}

		return NULL;

	}


	/**
	 * render the plugin with param  to display on product edit
	 * called by customfields inputTypePlugin
	 *
	 */
	public function selectSearchableCustom (&$selectList) {
		return NULL;
	}

	/**
	 * render the plugin with param  to display on product edit
	 * called by customfields inputTypePlugin
	 *
	 */
/*	public function plgVmAddToSearch (&$where, $searchplugin) {

	}*/

	/**
	 * render the plugin with param  to display on product edit
	 * called by customfields inputTypePlugin
	 *
	 */
	public function GetNameByCustomId ($custom_id) {
		static $custom_element;
		if (isset($custom_element)) {
			return $custom_element;
		}
		$db = JFactory::getDBO ();
		$q = 'SELECT `custom_element` FROM `#__virtuemart_customs` WHERE `virtuemart_custom_id`=' . (int)$custom_id;
		$db->setQuery ($q);
		$custom_element = $db->loadResult ();
		return $custom_element;

	}

	/**
	 * render the plugin with param  to display on product edit
	 * called by customfields inputTypePlugin
	 *
	 */
	public function getIdForCustomIdProduct ($product_id, $custom_id) {
		$db = JFactory::getDBO ();
		$q = 'SELECT `id` FROM `#__virtuemart_product_custom_plg_' . $this->_name . '` WHERE `virtuemart_product_id`=' . (int)$product_id . ' and `virtuemart_custom_id`=' . (int)$custom_id;
		$db->setQuery ($q);
		return $db->loadResult ();
	}

}
