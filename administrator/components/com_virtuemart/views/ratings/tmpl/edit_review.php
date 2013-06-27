<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage 	ratings
* @author
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: ratings_edit.php 2233 2010-01-21 21:21:29Z SimonHodgkiss $
*
* @todo decide to allow or not a JEditor here instead of a textarea
* @todo comment length check should also occur on the server side (model?)
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
vmJsApi::cssSite();
AdminUIHelper::startAdminArea();
AdminUIHelper::imitateTabs('start','COM_VIRTUEMART_REVIEW_DETAILS');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<div class="col50">
<fieldset>
<legend><?php echo JText::_('COM_VIRTUEMART_REVIEW_DETAILS'); ?></legend>
<table class="admintable" summary="<?php echo JText::_('COM_VIRTUEMART_RATING_EDIT_TITLE');?>">
	<tr>
		<td width="24%" align="left" valign="top">
			<?php echo JText::_('COM_VIRTUEMART_RATING_TITLE'); ?>
		</td>
		<td valign="top"><fieldset class="radio">
		<!-- Rating stars -->
		<?php
		$rating_options = array();
		for ($i=0;$i<=$this->max_rating;$i++) {

            $title = (JText::_("COM_VIRTUEMART_RATING_TITLE").' : '. $i . '/' . $this->max_rating) ;
			$stars  = '<span class="floatleft vmiconFE vm2-stars'.$i.'" title="'.$title.'"></span>';
			$rating_options[] = JHTML::_('select.option',$i,$stars);

		}
		echo JHTML::_('select.radiolist', $rating_options, 'vote', 'id="vote" class="inputbox"', 'value', 'text', $this->rating->vote);
		?>
		</fieldset></td>
	</tr>
		<!-- Review comment -->
	<tr>
		<td width="24%" align="left" valign="top">
			<?php echo JTEXT::_('COM_VIRTUEMART_REVIEW'); ?>
        	</td>
		<td width="76%" align="left">
			<textarea onblur="refresh_counter();" onfocus="refresh_counter();" onkeypress="refresh_counter();" rows="20" cols="60" name="comment"><?php echo $this->rating->comment; ?></textarea>
		</td>
	</tr>
	<tr>
		<!-- Show number of typed in characters -->
		<td width="24%" align="left" valign="top"> &nbsp; </td>
		<td width="76%" align="left">
	        <div align="left"><i><?php echo JText::_('COM_VIRTUEMART_REVIEW_COUNT') ?></i>
                	<input type="text" value="150" size="4" class="inputbox readonly" name="counter" maxlength="4" readonly="readonly" />
            	</div>
		</td>
	</tr>
        <?php if (false) { ?>
<!-- todo?? To be used with HTML editor (with some more restrictions)
	<tr>
		<td width="24%" align="left" valign="top">
			<?php echo JTEXT::_('COM_VIRTUEMART_REVIEW'); ?>
        	</td>
		<td width="76%" align="left">
	<?php
	$editor = JFactory::getEditor();
	echo $editor->display('comment', $this->rating->comment, '100%', '100', '60', '20',false);?>
	</td>
	</tr>
-->
  <?php } ?>
	<tr>
		<!-- published status -->
		<td>
			<?php echo JText::_('COM_VIRTUEMART_PUBLISHED'); ?>
		</td>
		<td><fieldset class="radio">
			<?php echo JHTML::_('select.booleanlist', 'published', '', $this->rating->published); ?>
		</fieldset></td>
	</tr>
</table>
</fieldset>
</div>
<!-- Hidden Fields -->
	<?php echo $this->addStandardHiddenToForm(); ?>
<input type="hidden" name="virtuemart_rating_review_id" value="<?php echo $this->rating->virtuemart_rating_review_id; ?>" />
<input type="hidden" name="virtuemart_product_id" value="<?php echo $this->rating->virtuemart_product_id; ?>" />
<input type="hidden" name="created_by" value="<?php echo $this->rating->created_by; ?>" />

</form>

<?php
AdminUIHelper::imitateTabs('end');
AdminUIHelper::endAdminArea(); ?>
<script type="text/javascript">
function refresh_counter() {
    var form = document.adminForm;
    form.counter.value = form.comment.value.length;
}
refresh_counter();

function submitbutton(pressbutton) {

	 if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	else {
		if (document.adminForm.counter.value > <?php echo VmConfig::get('reviews_maximum_comment_length'); ?>) alert('<?php echo addslashes( JText::sprintf('COM_VIRTUEMART_REVIEW_ERR_COMMENT2_JS',VmConfig::get('reviews_maximum_comment_length')) ); ?>');
		else if (document.adminForm.counter.value < <?php echo VmConfig::get('reviews_minimum_comment_length'); ?>) alert('<?php echo addslashes( JText::sprintf('COM_VIRTUEMART_REVIEW_ERR_COMMENT1_JS',VmConfig::get('reviews_minimum_comment_length')) ); ?>');
		else submitform( pressbutton );
		return;
	}
}
</script>