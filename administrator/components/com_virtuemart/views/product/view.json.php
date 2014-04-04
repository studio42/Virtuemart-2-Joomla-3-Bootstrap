<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2012 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.json.php 6543 2012-10-16 06:41:27Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
jimport( 'joomla.application.component.view');
		// Load some common models
if(!class_exists('VirtueMartModelCustomfields')) require(JPATH_VM_ADMINISTRATOR.'/models'.DS.'customfields.php');

/**
 * HTML View class for the VirtueMart Component
 *
 * @package		VirtueMart
 * @author
 */
class VirtuemartViewProduct extends JViewLegacy {

	var $json = array();

	function __construct( ){

		$this->type = JRequest::getWord('type', false);
		$this->row = JRequest::getInt('row', false);
		$this->db = JFactory::getDBO();
		$this->model = VmModel::getModel('Customfields') ;

	}
	function display($tpl = null) {

		// Get the task
		$this->task = JRequest::getWord('task',$this->getLayout());
		if ($this->task == 'massxref_cats_exe') {
			return;
		
		}
		
		$filter = JRequest::getVar('q', JRequest::getVar('term', false) );
		if ($filter) $filter = $this->db->escape( $filter, true ) ;
		$id = JRequest::getInt('id', false);
		$product_id = JRequest::getInt('virtuemart_product_id', 0);
		//$customfield = $this->model->getcustomfield();
		/* Get the task */
		if ($this->type=='relatedproducts') {
			$query = "SELECT virtuemart_product_id AS id, CONCAT(product_name, '::', product_sku) AS value
				FROM #__virtuemart_products_".VMLANG."
				 JOIN `#__virtuemart_products` AS p using (`virtuemart_product_id`)";
			if ($filter) $query .= " WHERE product_name LIKE '%". $filter ."%' or product_sku LIKE '%". $filter ."%'";
			$query .= " limit 0,10";
			self::setRelatedHtml($query,'R');
		}
		else if ($this->type=='relatedcategories')
		{
			$query = "SELECT virtuemart_category_id AS id, CONCAT(category_name, '::', virtuemart_category_id) AS value
				FROM #__virtuemart_categories_".VMLANG;
			if ($filter) $query .= " WHERE category_name LIKE '%". $filter ."%'";
			$query .= " limit 0,10";
			self::setRelatedHtml($query,'Z');
		}
		else if ($this->type=='custom')
		{
			$query = "SELECT CONCAT(virtuemart_custom_id, '|', custom_value, '|', field_type) AS id, CONCAT(custom_title, '::', custom_tip) AS value
				FROM #__virtuemart_customs";
			if ($filter) $query .= " WHERE custom_title LIKE '%".$filter."%' limit 0,50";
			$this->db->setQuery($query);
			$this->json['value'] = $this->db->loadObjectList();
			$this->json['ok'] = 1 ;
		}
		else if ($this->type=='fields')
		{
			$fieldTypes= $this->model->getField_types() ;

			$query = "SELECT *,custom_value as value FROM #__virtuemart_customs
			WHERE (`virtuemart_custom_id`=".$id." or `custom_parent_id`=".$id.")";
			$query .=" order by custom_parent_id asc";
			$this->db->setQuery($query);
			$rows = $this->db->loadObjectlist();
			$html = array ('cart_attributes' => '','custom_fields' =>'','childs' => '');
			foreach ($rows as $field) {
				if ($field->field_type =='C' ){
					$this->json['table'] = 'childs';
					$q='SELECT `virtuemart_product_id` FROM `#__virtuemart_products` WHERE `published`=1
					AND `product_parent_id`= '.JRequest::getInt('virtuemart_product_id');
					//$this->db->setQuery(' SELECT virtuemart_product_id, product_name FROM `#__virtuemart_products` WHERE `product_parent_id` ='.(int)$product_id);
					$this->db->setQuery($q);
					if ($childIds = $this->db->loadColumn()) {
					// Get childs
						foreach ($childIds as $childId) {
							$field->custom_value = $childId;
							$display = $this->model->displayProductCustomfieldBE($field,$childId,$this->row);
							 if ($field->is_cart_attribute) $cartIcone=  'default';
							 else  $cartIcone= 'default-off';
							 $html[] = '<div class="removable">
								<td>'.$field->custom_title.'</td>
								 <td>'.$display.$field->custom_tip.'</td>
								 <td>'.JText::_($fieldTypes[$field->field_type]).'
								'.$this->model->setEditCustomHidden($field, $this->row).'
								 </td>
								 <td><span class="vmicon vmicon-16-'.$cartIcone.'"></span></td>
								 <td></td>
								</div>';
							$this->row++;
						}
					}
				} else {
					$this->json['table'] = 'fields';
					$display = $this->model->displayProductCustomfieldBE($field,$product_id,$this->row);
					if ($field->custom_tip) $tip = ' class="hasTooltip" title="'.$field->custom_tip.'"';
					else $tip ='';
					if ($field->layout_pos)  $field->layout_pos = '<div><small>'.$field->layout_pos.'</small></div>';
					$tbName = $field->is_cart_attribute ? 'cart_attributes' : 'custom_fields' ;
					$html[$tbName] .= '<tr class="removable">
						<td><div '.$tip.'>'.JText::_($field->custom_title).'<div>'.
						($field->custom_field_desc ? '<small>'.$field->custom_field_desc.'</small>' :'' ). '
						 <td>'.$display.'</td>
						 <td>
							'.JText::_($fieldTypes[$field->field_type]).'
							'.$field->layout_pos.'
							'.$this->model->setEditCustomHidden($field, $this->row).'
						</td>
						 <td><span class="icon-remove"></span><input class="ordering" type="hidden" value="'.$this->row.'" name="field['.$this->row .'][ordering]" /></td>
						 <td><span class="icon-move"></span></td>
						</tr>';
					$this->row++;
				}
			}
			foreach ($html as $key => $item) {
				if (empty($item)) unset($html[$key]) ;
			}
			$this->json['value'] = $html;
			$this->json['ok'] = 1 ;
		} else if ($this->type=='userlist')
		{
			$status = JRequest::getvar('status');
			$productShoppers=0;
			if ($status) {
				$productModel = VmModel::getModel('product');
				$productShoppers = $productModel->getProductShoppersByStatus($product_id ,$status);
			}
			if(!class_exists('ShopFunctions'))require(JPATH_VM_ADMINISTRATOR.'/helpers'.DS.'shopfunctions.php');
			$html = ShopFunctions::renderProductShopperList($productShoppers);
			$this->json['value'] = $html;

		} else $this->json['ok'] = 0 ;

		if ( empty($this->json)) {
			$this->json['value'] = null;
			$this->json['ok'] = 1 ;
		}

		echo json_encode($this->json);

	}

	function setRelatedHtml($query,$fieldType) {

		$this->db->setQuery($query);
		$this->json = $this->db->loadObjectList();

		$query = 'SELECT * FROM `#__virtuemart_customs` WHERE field_type ="'.$fieldType.'" ';
		$this->db->setQuery($query);
		$customs = $this->db->loadObject();
		foreach ($this->json as &$related) {

			$customs->custom_value = $related->id;
			$display = $this->model->displayProductCustomfieldBE($customs,$related->id,$this->row);
			$html = '<div class="vm_thumb_image">
				<span>'.$display.'</span>
				'.$this->model->setEditCustomHidden($customs, $this->row).'
				<div class="icon-remove"></div></div>';

			$related->label = $html;

		}
	}

}
// pure php no closing tag
