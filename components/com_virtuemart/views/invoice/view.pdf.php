<?php
/**
 * Generate invoice in PDF format
 *
 * @package	VirtueMart
 * @subpackage invoice
 * @author Patrick Kohl
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.raw.php 5522 2012-02-21 14:40:10Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Load the main HTML view 
jexit();
$vmview = dirname(__FILE__);
require($vmview.'/view.html.php');
