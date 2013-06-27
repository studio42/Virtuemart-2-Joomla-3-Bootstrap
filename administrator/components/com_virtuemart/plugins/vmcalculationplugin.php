<?php

/**
 * Abstract plugin class to extend the calculation
 *
 * @version $Id: vmcalculationplugin.php 4634 2011-11-09 21:07:44Z Milbo $
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

abstract class vmCalculationPlugin extends vmPlugin {

	function __construct(& $subject, $config) {

		parent::__construct($subject, $config);

		$this->_tablepkey = 'virtuemart_calc_id';
		$this->_tablename = '#__virtuemart_calc_plg_'. $this->_name;
	}


	protected function getPluginInternalDataCalc(&$calcData){

	 	$datas = $this->getPluginInternalData($calcData->virtuemart_calc_id,'virtuemart_calc_id');

		if($datas){
			$attribsCalc = get_object_vars($datas);

			unset($attribsCalc['virtuemart_calc_id']);
			foreach($attribsCalc as $k=>$v){
				$calcData->$k = $v;
			}
		}

	}


}