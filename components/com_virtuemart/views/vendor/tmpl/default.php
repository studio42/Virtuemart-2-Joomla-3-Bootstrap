<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage vendor
* @author Kohl Patrick, Eugen Stranz
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 2701 2011-02-11 15:16:49Z impleri $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Category and Columns Counter
$iColumn = 1;
$ivendor = 1;

// Calculating Categories Per Row
$vendorPerRow = 3;
if ($vendorPerRow != 1) {
	$vendorCellWidth = ' width'.floor ( 100 / $vendorPerRow );
} else {
	$vendorCellWidth = '';
}

// Separator
$verticalSeparator = " vertical-separator";
$horizontalSeparator = '<div class="horizontal-separator"></div>';

// Lets output the categories, if there are some
if (!empty($this->vendors)) { ?>

<div class="vendor-view-default">

	<?php // Start the Output
	foreach ( $this->vendors as $vendor ) {

		// Show the horizontal seperator
		if ($iColumn == 1 && $ivendor > $vendorPerRow) {
			echo $horizontalSeparator;
		}

		// this is an indicator wether a row needs to be opened or not
		if ($iColumn == 1) { ?>
		<div class="row">
		<?php }

		// Show the vertical seperator
		if ($ivendor == $vendorPerRow or $ivendor % $vendorPerRow == 0) {
			$showVerticalSeparator = ' ';
		} else {
			$showVerticalSeparator = $verticalSeparator;
		}

		// vendor Elements
		$vendorsLink = JRoute::_('index.php?option=com_virtuemart&view=vendor&virtuemart_vendor_id=' . $vendor->virtuemart_vendor_id);
		$vendorIncludedProductsURL = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_vendor_id=' . $vendor->virtuemart_vendor_id);
		//$vendorImage = $vendor->images[0]->displayMediaThumb("",false);

		// Show Category ?>
		<div class="manufacturer floatleft<?php echo $vendorCellWidth . $showVerticalSeparator ?>">
			<div class="spacer">
				<h2>
					<a title="<?php echo $vendor->vendor_store_name; ?>" href="<?php echo $vendorsLink; ?>"><?php echo $vendor->vendor_store_name; ?></a>
				</h2>
				<a title="<?php echo $vendor->vendor_store_name; ?>" href="<?php echo $vendorsLink; ?>"><?php //echo $vendorImage;?></a>
			</div>
			<div><?php echo $vendor->vendor_name; ?></div>
		</div>
		<?php
		$ivendor ++;

		// Do we need to close the current row now?
		if ($iColumn == $vendorPerRow) {
			echo '<div class="clear"></div></div>';
			$iColumn = 1;
		} else {
			$iColumn ++;
		}
	}

	// Do we need a final closing row tag?
	if ($iColumn != 1) { ?>
		<div class="clear"></div>
	</div>
	<?php } ?>

</div>
<?php
} else {
	echo 'Serious configuration problem, no vendor found.';
}
?>