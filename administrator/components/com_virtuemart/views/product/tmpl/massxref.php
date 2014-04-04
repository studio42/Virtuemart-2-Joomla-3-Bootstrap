<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2012 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: product.php 3304 2011-05-20 06:57:27Z alatak $
*/
// reset the layout to default and change path
$this->setLayout('default');
if($this->task=='massxref_cats' or $this->task=='massxref_cats_exe'){
	$this->addTemplatePath(JPATH_VM_ADMINISTRATOR.'/views'.DS.'category'.DS.'tmpl');
	echo $this->loadTemplate();
	// $this->include(JPATH_VM_ADMINISTRATOR.'/views'.DS.'category'.DS.'tmpl'.DS.'default.php');
}

if($this->task=='massxref_sgrps' or $this->task=='massxref_sgrps_exe'){
	$this->addTemplatePath(JPATH_VM_ADMINISTRATOR.'/views'.DS.'shoppergroup'.DS.'tmpl');
	echo $this->loadTemplate();
	// include(JPATH_VM_ADMINISTRATOR.'/views'.DS.'shoppergroup'.DS.'tmpl'.DS.'default.php');
}