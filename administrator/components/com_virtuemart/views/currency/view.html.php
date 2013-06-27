<?php
/**
*
* Currency View
*
* @package	VirtueMart
* @subpackage Currency
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 6043 2012-05-21 21:40:56Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmview.php');

/**
 * HTML View class for maintaining the list of currencies
 *
 * @package	VirtueMart
 * @subpackage Currency
 * @author RickG, Max Milbers
 */
class VirtuemartViewCurrency extends VmView {

	function display($tpl = null) {

		// Load the helper(s)


		$this->loadHelper('html');

		$model = VmModel::getModel();


		$config = JFactory::getConfig();
		$layoutName = JRequest::getWord('layout', 'default');
		if ($layoutName == 'edit') {
			$cid	= JRequest::getVar( 'cid' );

			$task = JRequest::getWord('task', 'add');
			//JArrayHelper::toInteger($cid);
			if($task!='add' && !empty($cid) && !empty($cid[0])){
				$cid = (int)$cid[0];
			} else {
				$cid = 0;
			}

			$model->setId($cid);
			$currency = $model->getCurrency();
			$this->SetViewTitle('',$currency->currency_name);
			$this->assignRef('currency',	$currency);

			$this->addStandardEditViewCommands();

		} else {

			$this->SetViewTitle();
			$this->addStandardDefaultViewCommands();

			$this->addStandardDefaultViewLists($model,0,'ASC');

			$currencies = $model->getCurrenciesList(JRequest::getWord('search', false));
			$this->assignRef('currencies',	$currencies);

			$pagination = $model->getPagination();
			$this->assignRef('pagination', $pagination);


		}

		parent::display($tpl);
	}

}
// pure php no closing tag
