<?php
/**
*
* user_shoppergroup__xref table
*
* @package	VirtueMart
* @subpackage User
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: user_shoppergroup.php 2420 2010-06-01 21:12:57Z oscar $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTableXarray'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtablexarray.php');

/**
 * user_shoppergroup_xref table class
 * The class is used to link users to shoppergroups.
 *
 * @package	VirtueMart
 * @author Max Milbers
 */

 class TableVmuser_shoppergroups extends VmTableXarray {


	/**
	 * @param $db Class constructor; connect to the database
	 */
	function __construct(&$db)
	{
		parent::__construct('#__virtuemart_vmuser_shoppergroups', 'id', $db);
		$this->setPrimaryKey('virtuemart_user_id');
		$this->setSecondaryKey('virtuemart_shoppergroup_id');
	}

 }
