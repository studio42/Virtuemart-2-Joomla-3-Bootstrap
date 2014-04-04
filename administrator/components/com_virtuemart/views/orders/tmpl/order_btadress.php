<?php
/**
 * Print orderdetails
 *
 * @package	VirtueMart
 * @subpackage Orders
 * @author Oscar van Eijk
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: order_print.php 6043 2012-05-21 21:40:56Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access'); ?>
		<table width="100%">

			<?php
			foreach ($this->userfields['fields'] as $_field ) {

				echo '		<tr>'."\n";
				echo '			<td class="key">'."\n";
				echo '				<label for="'.$_field['name'].'_field">'."\n";
				echo '					'.$_field['title'] . ($_field['required']?' *': '')."\n";
				echo '				</label>'."\n";
				echo '			</td>'."\n";
				echo '			<td>'."\n";
				echo '				'.$_field['formcode']."\n";
				echo '			</td>'."\n";
				echo '		</tr>'."\n"; //*/
			/*	$fn = $_field['name'];
				$fv = $_field['value'];
				$ft = $_field['title'];
				echo '		<tr>'."\n";
				echo '			<td class="key">'."\n";
				echo '				'.$ft."\n";
				echo '			</td>'."\n";
				echo '			<td>'."\n";
				echo "				<input name='BT_$fn' id='$fn' value='$fv' size='50'>\n";
				echo '			</td>'."\n";
				echo '		</tr>'."\n";*/
			}
			?>

		</table>
