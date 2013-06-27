<?php
/**
*
* Extensions View
*
* @package	VirtueMart
* @subpackage Extensions
* @author StephanieS
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 5601 2012-03-04 18:22:24Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmview.php');

/**
 * HTML View class for maintaining the list of extensions
 *
 * @package	VirtueMart
 * @subpackage Extensions
 * @author Max Milbers
 */
class VirtuemartViewUsergroups extends VmView {

	function display( $tpl = null ){



		$this->loadHelper('html');

		$model = VmModel::getModel();
		// TODO icon for this view
		$this->SetViewTitle();


		$layoutName = JRequest::getWord('layout', 'default');
		if ($layoutName == 'edit') {

			$usergroup = $model->getUsergroup();
			$this->assignRef('usergroup',	$usergroup);

			$this->addStandardEditViewCommands();

		} else {
			$this->addStandardDefaultViewCommands();
			$this->addStandardDefaultViewLists($model);

			$ugroups = $model->getUsergroups(false,true);
			$this->assignRef('usergroups',	$ugroups);

			$pagination = $model->getPagination();
			$this->assignRef('pagination', $pagination);

		}

		parent::display($tpl);
	}

}
// pure php no closing tag
