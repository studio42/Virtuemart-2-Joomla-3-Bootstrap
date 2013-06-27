<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Manufacturer
* @author Patrick Kohl
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: edit.php 3617 2011-07-05 12:55:12Z enytheme $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access'); ?>

<div class="col50">
	<div class="selectimage">
		<?php 
		$this->manufacturer->images[0]->addHidden('virtuemart_vendor_id',$this->virtuemart_vendor_id);

		echo $this->manufacturer->images[0]->displayFilesHandler($this->manufacturer->virtuemart_media_id,'manufacturer'); ?>
	</div>
</div>