<?php
if( !defined( '_JEXEC' ) ) die();

/**
*
* @version $Id: default.php 6489 2012-10-01 23:17:36Z Milbo $
* @package VirtueMart
* @subpackage Report
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
/* Load some variables */
$rows = count( $this->report );
$intervalTitle = JRequest::getVar('intervals','day');
if ( ($intervalTitle =='week') or ($intervalTitle =='month') ) $addDateInfo = true ;
else $addDateInfo = false;
// JHtml::_('behavior.framework', true);
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php AdminUIHelper::startAdminArea(); ?>

 	<div id="filter-bar" class="btn-toolbar">
		<div class="btn-group pull-left">
			<?php echo JText::_('COM_VIRTUEMART_ORDERSTATUS')?> <?php echo $this->lists['state_list']; ?>
		</div>
		<div class="btn-group pull-left">
			<button type="submit" id="searchsubmit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT') ?>"><i class="icon-search"></i></button>
		</div>
		<div class="btn-group pull-right">
			<?php echo JText::_('COM_VIRTUEMART_REPORT_INTERVAL') . $this->lists['intervals']; ?>
		</div>
		<?php
		if(VmConfig::get('multix','none')!='none'){ ?>
			<div class="btn-group pull-right">
				<?php
				$vendorId = JRequest::getInt('virtuemart_vendor_id',1);
				echo ShopFunctions::renderVendorList($vendorId,false); ?>
			</div>
			<?php
		} ?>
		<div class="btn-group pull-right" style="clear:right;">
		<?php echo vmJsApi::jDate($this->until_period, 'until_period', '', true , '', JText::_('COM_VIRTUEMART_REPORT_UNTIL_PERIOD') ); ?>
		</div>
		<div class="btn-group pull-right">
		<?php echo vmJsApi::jDate($this->from_period, 'from_period' , '', true, '', JText::_('COM_VIRTUEMART_REPORT_FROM_PERIOD')); ?> 
		</div>
		<div class="btn-group pull-right">
		<?php echo $this->lists['select_date']; ?>
		</div>

	</div>
	<div class="clearfix"> </div>
	<div id="results">
		<?php 
		// split to use ajax search
		echo $this->loadTemplate('results'); ?>
	</div>
	<?php AdminUIHelper::endAdminArea(true); ?>
</form>


