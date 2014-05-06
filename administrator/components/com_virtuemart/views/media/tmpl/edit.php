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
?>
<form name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
	<?php
	$this->media->addHidden('file_type',$this->media->file_type);
	$virtuemart_product_id = JRequest::getInt('virtuemart_product_id', '');
	if(!empty($virtuemart_product_id)) {
		$this->media->addHidden('virtuemart_product_id',$virtuemart_product_id);
	} elseif($virtuemart_category_id = JRequest::getInt('virtuemart_category_id', '')) {
		$this->media->addHidden('virtuemart_category_id',$virtuemart_category_id);
	}

	echo $this->media->displayFileHandler();
	echo $this->addStandardHiddenToForm();
	
	// check max file for standard vendors
	if ($this->adminVendor >1){
		$params = JComponentHelper::getParams('com_virtuemart', true);
		$max_uploads = $params->get('max_uploads',1);
		$checkVendor = "if(this.files.length>".$max_uploads.")
				alert('Only the first ".$max_uploads." file(s) will be uploaded');";
	} else $checkVendor = "";
	?>
</form>
<script type="text/javascript">
	jQuery(function($){
		$('#uploads').change( function (){
			if ($('#media_action0').is(':checked') ) $('#media_actionupload').trigger('click');
			<?php echo $checkVendor ?>
		});
		
		$('#uploads').fileinput({
			browseClass: 'btn btn-success',
			msgSelected: '{n} <?php echo JText::_('COM_VIRTUEMART_E_IMAGES') ?>',
			browseLabel: ' <?php echo JText::_('COM_VIRTUEMART_FILES_LIST') ?> <i class=\'icon icon-upload\'></i>',
			browseIcon: '<i class=\'icon icon-folder\'></i>',
			removeClass: 'btn btn-danger',
			removeLabel: '<?php echo JText::_('JTOOLBAR_DELETE') ?>',
			removeIcon: '<i class=\"icon icon-trash\"></i>',
			uploadClass: 'btn btn-info',
			uploadIcon: '<i class=\"icon icon-upload\"></i>',
		});
		$('#image_desc_accordion').collapse('show');
		$('input[name=media_attributes]').click(function(e) {
			var value = $(this).val();
			if (value === 'product') 
				$('#media_rolesfile_is_displayable').parent().siblings().show();
			else $('#media_rolesfile_is_displayable').trigger('click').parent().siblings().hide();
			// console.log(value,$(this));
		});
	});
</script>
<?php
AdminUIHelper::endAdminArea();
