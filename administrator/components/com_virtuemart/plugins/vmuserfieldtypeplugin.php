<?php

/**
 * Abstract plugin class to extend the userfields
 *
 * @version $Id: vmuserfieldplugin.php 4634 2011-11-09 21:07:44Z Milbo $
 * @package VirtueMart
 * @subpackage vmplugins
 * @copyright Copyright (C) 2011-2011 VirtueMart Team - All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL 2,
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 *
 * @author Max Milbers
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!class_exists('vmPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmplugin.php');

abstract class vmUserfieldPlugin extends vmPlugin {

	function __construct(& $subject, $config) {

		parent::__construct($subject, $config);

		// $this->_tablename = '#__virtuemart_userfield_' . $this->_name;
		// $this->_createTable();
		// $this->_tableChecked = true;
	}

	// add params fields in object 
	
	function AddUserfieldParameter($params){

		$plgParams = explode('|', $params);
		foreach($plgParams as $item){
			if (empty($item)) continue;
			$param = explode('=',$item);
			$this->$param[0] = json_decode($param[1]);
			//unset($item[0]);
		}

	}
	// add params fields in object by name
	
	function AddUserfieldParameterByPlgName($plgName){
		if(empty($this->_db)) $this->_db = JFactory::getDBO();
		$q = 'SELECT `params` FROM `#__virtuemart_userfields` WHERE `type` = "plugin' . $plgName.'"';
		$this->_db->setQuery($q);
		$params = $this->_db->loadResult();
		$this->AddUserfieldParameter($params);
	}	


}
