<?php
if( !defined( '_JEXEC' ) ) die('Restricted access');

/**
*
* @version $Id: view.raw.php 6489 2012-10-01 23:17:36Z Milbo $
* @package VirtueMart
* @subpackage Report
* @author Patrick Kohl/Studio 42
* @copyright Copyright (C) VirtueMart Team - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.org
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Load the main HTML view 

$vmview = dirname(__FILE__);
require($vmview.'/view.html.php');
