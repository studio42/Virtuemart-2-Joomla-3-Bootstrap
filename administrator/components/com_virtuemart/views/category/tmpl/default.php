<?php
/**
*
* Lists all the categories in the shop
*
* @package	VirtueMart
* @subpackage Category
* @author RickG, jseros, RolandD, Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 6477 2012-09-24 14:33:54Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if (!class_exists ('shopFunctionsF'))
	require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');

AdminUIHelper::startAdminArea();

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div id="header">
<div id="filterbox">
	<table class="">
		<tr>
			<td align="left">
			<?php echo $this->displayDefaultViewSearch() ?>
			</td>

		</tr>
	</table>
	</div>
	<div id="resultscounter"><?php echo $this->catpagination->getResultsCounter(); ?></div>

</div>


	<div id="editcell">
		<table class="adminlist" cellspacing="0" cellpadding="0">
		<thead>
		<tr>

			<th width="20px">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->categories); ?>);" />
			</th>
			<th align="left" width="12%">
				<?php echo $this->sort('category_name') ?>
			</th>
			<th align="left">
				<?php echo $this->sort('category_description', 'COM_VIRTUEMART_DESCRIPTION') ; ?>
			</th>
			<th align="left" width="11%">
				<?php echo JText::_('COM_VIRTUEMART_PRODUCT_S'); ?>
			</th>

			<th align="left" width="13%">
				<?php echo $this->sort( 'c.ordering' , 'COM_VIRTUEMART_ORDERING') ?>
				<?php echo JHTML::_('grid.order', $this->categories, 'filesave.png', 'saveOrder' ); ?>
			</th>
			<th align="center" width="20px">
				<?php echo $this->sort('c.published' , 'COM_VIRTUEMART_PUBLISHED') ?>
			</th>
			<?php if(Vmconfig::get('multix','none')!=='none' and $this->perms->check('admin') ){ ?>
            <th width="20px">

				<?php echo $this->sort( 'cx.category_shared' , 'COM_VIRTUEMART_SHARED') ?>
            </th>
			<?php } ?>

			<th><?php echo $this->sort('virtuemart_category_id', 'COM_VIRTUEMART_ID')  ?></th>

		</tr>
		</thead>
		<tbody>
		<?php
		$k = 0;
		$repeat = 0;

 		$nrows = count( $this->categories );

		if( $this->catpagination->limit < $nrows ){
			if( ($this->catpagination->limitstart + $this->catpagination->limit) < $nrows ) {
				$nrows = $this->catpagination->limitstart + $this->catpagination->limit;
			}
		}

// 		for ($i = $this->pagination->limitstart; $i < $nrows; $i++) {

		foreach($this->categories as $i=>$cat){

// 			if( !isset($this->rowList[$i])) $this->rowList[$i] = $i;
// 			if( !isset($this->depthList[$i])) $this->depthList[$i] = 0;

// 			$row = $this->categories[$this->rowList[$i]];

			$checked = JHTML::_('grid.id', $i, $cat->virtuemart_category_id);
			$published = JHTML::_('grid.published', $cat, $i);
			$editlink = JRoute::_('index.php?option=com_virtuemart&view=category&task=edit&cid=' . $cat->virtuemart_category_id);
// 			$statelink	= JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $cat->virtuemart_category_id);
			$showProductsLink = JRoute::_('index.php?option=com_virtuemart&view=product&virtuemart_category_id=' . $cat->virtuemart_category_id);
			$shared = $this->toggle($cat->shared, $i, 'toggle.shared');

			$categoryLevel = '';
			if(!isset($cat->level)){
				if($cat->category_parent_id){
					$cat->level = 1;
				} else {
					$cat->level = 0;
				}

			}
			$repeat = $cat->level;

			if($repeat > 1){
				$categoryLevel = str_repeat(".&nbsp;&nbsp;&nbsp;", $repeat - 1);
				$categoryLevel .= "<sup>|_</sup>&nbsp;";
			}
		?>
			<tr class="<?php echo "row".$k;?>">

				<td><?php echo $checked;?></td>
				<td align="left">
					<span class="categoryLevel"><?php echo $categoryLevel;?></span>
					<a href="<?php echo $editlink;?>"><?php echo $this->escape($cat->category_name);?></a>
				</td>
				<td align="left">

					<?php

					echo shopFunctionsF::limitStringByWord(JFilterOutput::cleanText($cat->category_description),200); ?>
				</td>
				<td>
					<?php echo  $this->catmodel->countProducts($cat->virtuemart_category_id);//ShopFunctions::countProductsByCategory($row->virtuemart_category_id);?>
					&nbsp;<a href="<?php echo $showProductsLink; ?>">[ <?php echo JText::_('COM_VIRTUEMART_SHOW');?> ]</a>
				</td>
				<td align="center" class="order">
					<span><?php 
					
					
					$cond = (($cat->category_parent_id == 0 || $cat->category_parent_id == @$this->categories[$i - 1]->category_parent_id));
					$cond2= ($cat->category_parent_id == 0 || $cat->category_parent_id == @$this->categories[$i + 1]->category_parent_id);
					echo $this->catpagination->orderUpIcon( $i, $cond, 'orderUp', JText::_('COM_VIRTUEMART_MOVE_UP')); ?></span>
					<span><?php echo $this->catpagination->orderDownIcon( $i, $nrows, $cond2, 'orderDown', JText::_('COM_VIRTUEMART_MOVE_DOWN')); ?></span>
					<input class="ordering" type="text" name="order[<?php echo $i?>]" id="order[<?php echo $i?>]" size="5" value="<?php echo $cat->ordering; ?>" style="text-align: center" />
				</td>
				<td align="center">
					<?php echo $published;?>
				</td>
				<?php
				if((Vmconfig::get('multix','none')!='none')) {
					?><td align="center">
						<?php echo $shared; ?>
                    </td>
					<?php
				}
				?>
				<td><?php echo $cat->virtuemart_category_id; // echo $product->vendor_name; ?></td>
			</tr>
		<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->catpagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
</div>

	<?php
	vmdebug('my name here is '.$this->_name);
	echo $this->addStandardHiddenToForm($this->_name,$this->task);

	  ?>
</form>


<?php AdminUIHelper::endAdminArea(); ?>
