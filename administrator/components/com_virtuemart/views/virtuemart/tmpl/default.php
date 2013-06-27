<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 6053 2012-06-05 12:36:21Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


AdminUIHelper::startAdminArea (); 

JToolBarHelper::title(JText::_('COM_VIRTUEMART')." ".JText::_('COM_VIRTUEMART_CONTROL_PANEL'), 'head vm_store_48');


// Loading Templates in Tabs
AdminUIHelper::buildTabs ( $this, array (	'controlpanel' 	=> 	'COM_VIRTUEMART_CONTROL_PANEL',
									'statisticspage'=> 	'COM_VIRTUEMART_STATISTIC_STATISTICS'
									 ) );

AdminUIHelper::endAdminArea ();
