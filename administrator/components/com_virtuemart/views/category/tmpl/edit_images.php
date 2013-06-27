<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Category
* @author RickG, jseros
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: edit.php 3466 2011-06-08 22:37:28Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


?>
<div class="col50">
	<div class="selectimage">
			<?php
				//echo $this->category->images[0]->displayFilesHandler($this->category->virtuemart_media_id);
				if(empty($this->category->images[0]->virtuemart_media_id)) $this->category->images[0]->addHidden('file_is_category_image','1');
				if ($this->category->virtuemart_media_id) echo $this->category->images[0]->displayFilesHandler($this->category->virtuemart_media_id,'category');
				else echo $this->category->images[0]->displayFilesHandler(null,'category');
			?>
	</div>
</div>