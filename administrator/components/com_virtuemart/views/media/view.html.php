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
* @version $Id: view.html.php 6068 2012-06-06 14:59:42Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');

/**
 * HTML View class for the VirtueMart Component
 *
 * @package		VirtueMart
 * @author
 */
class VirtuemartViewMedia extends VmView {

	function display($tpl = null) {

		$this->loadHelper('html');
		$this->loadHelper('permissions');
		//@todo should be depended by loggedVendor
		$this->vendorId=1;

		$titleMsg ='';
		$model = VmModel::getModel();
		$this->perms = Permissions::getInstance();

		$layoutName = JRequest::getWord('layout', 'default');
		if ($layoutName == 'edit') {

			$this->media = $model->getFile();

			$this->addStandardEditViewCommands();

        }
        else {
			$this->cat_id = 0 ;
        	if ($this->product_id = JRequest::getInt('virtuemart_product_id',0) ) {
				$product = VmModel::getModel('product')->getProductSingle ($this->product_id, false, false );
				$this->link =  $this->editLink(	$this->product_id, '<i class="icon-edit"></i> '.$product->product_name, 'virtuemart_product_id',
					array('class'=> 'hasTooltip btn btn-inverse', 'title' => JText::_('COM_VIRTUEMART_EDIT').' '.$product->product_name), 'product') ;
				$titleMsg =  $product->product_name ;

			} else if ($this->cat_id = JRequest::getInt('virtuemart_category_id',0) ) {
				$category = VmModel::getModel('category')->getCategory($this->cat_id,false);
				$this->link =  $this->editLink(	$this->cat_id, '<i class="icon-edit"></i> '.$category->category_name, 'virtuemart_category_id',
					array('class'=> 'hasTooltip btn btn-inverse', 'title' => JText::_('COM_VIRTUEMART_EDIT').' '. $category->category_name), 'category') ;
				$titleMsg = $category->category_name ;
			}

			JToolBarHelper::custom('synchronizeMedia', 'new', 'new', JText::_('COM_VIRTUEMART_TOOLS_SYNC_MEDIA_FILES'),false);
			$this->addStandardDefaultViewCommands();
			$this->addStandardDefaultViewLists($model,null,null,'searchMedia');
			$options = array( '' => '- '.JText::_('COM_VIRTUEMART_TYPE').' -',
				'product' => JText::_('COM_VIRTUEMART_PRODUCT'),
				'category' => JText::_('COM_VIRTUEMART_CATEGORY'),
				'manufacturer' => JText::_('COM_VIRTUEMART_MANUFACTURER'),
				'vendor' => JText::_('COM_VIRTUEMART_VENDOR')
				);
			$this->lists['search_type'] = VmHTML::selectList('search_type', JRequest::getVar('search_type'),$options,1,'','onchange="Joomla.ajaxSearch(this); return false;"');

			$options = array( '' => JText::_('COM_VIRTUEMART_LIST_ALL_ROLES'),
				'file_is_displayable' => JText::_('COM_VIRTUEMART_FORM_MEDIA_DISPLAYABLE'),
				'file_is_downloadable' => JText::_('COM_VIRTUEMART_FORM_MEDIA_DOWNLOADABLE'),
				'file_is_forSale' => JText::_('COM_VIRTUEMART_FORM_MEDIA_SET_FORSALE'),
				);
			$this->lists['search_role'] = VmHTML::selectList('search_role', JRequest::getVar('search_role'),$options,1,'','onchange="this.form.submit();"');

			$this->files = $model->getFiles(false,false,$this->product_id,$this->cat_id);

			$this->pagination = $model->getPagination();

		}
		// TODO add icon for media view
		$this->SetViewTitle('',$titleMsg);
		parent::display($tpl);
	}

}
// pure php no closing tag