<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: edit.php 6043 2012-05-21 21:40:56Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
AdminUIHelper::startAdminArea();
AdminUIHelper::imitateTabs('start','COM_VIRTUEMART_PRODUCT_MEDIA');

echo'<form name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">';
echo '<fieldset>';



$this->media->addHidden('view','media');
$this->media->addHidden('task','');
$this->media->addHidden(JUtility::getToken(),1);
$this->media->addHidden('file_type',$this->media->file_type);


$virtuemart_product_id = JRequest::getInt('virtuemart_product_id', '');
if(!empty($virtuemart_product_id)) $this->media->addHidden('virtuemart_product_id',$virtuemart_product_id);

$virtuemart_category_id = JRequest::getInt('virtuemart_category_id', '');
if(!empty($virtuemart_category_id)) $this->media->addHidden('virtuemart_category_id',$virtuemart_category_id);

echo $this->media->displayFileHandler();
echo '</fieldset>';
echo '</form>';

AdminUIHelper::imitateTabs('end');
AdminUIHelper::endAdminArea();
