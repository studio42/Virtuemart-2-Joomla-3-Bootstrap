<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_modules
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// TODO squeezbox hide the tooltip container !!!
defined('_JEXEC') or die;

?>
<fieldset>
	<legend>
		<?php echo JText::_('COM_VIRTUEMART_ADMIN_PAYMENT_CONFIGURATION') ?> <?php echo $this->payment->payment_name ? $this->payment->payment_name : $this->payment->payment_element ?>
	</legend>
<?php
	echo JHtml::_('bootstrap.startAccordion', 'moduleOptions', array('active' => 'collapse0'));
	if ($this->payment->form) {
	$fieldSets = $this->payment->form->getFieldsets('params');
	$i = 0;
	$collapse = 'collapse' ;
	foreach ($fieldSets as $name => $fieldSet) :
		$label = !empty($fieldSet->label) ? $fieldSet->label : 'JGLOBAL_FIELDSET_'.$name;
		$class = isset($fieldSet->class) && !empty($fieldSet->class) ? $fieldSet->class : '';

		echo JHtml::_('bootstrap.addSlide', 'moduleOptions', JText::_($label), 'collapse' . $i++, $class);
			if (isset($fieldSet->description) && trim($fieldSet->description)) :
				echo '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
			endif;
		?>
		<div class="form-horizontal">
			<?php foreach ($this->payment->form->getFieldset($name) as $field) : ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $field->label; ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
					</div>
				</div>
			<?php endforeach;?>
		</div>
		<?php
		echo JHtml::_('bootstrap.endSlide');
	endforeach;
	} else {
		echo JText::_('COM_VIRTUEMART_SELECT_PAYMENT_METHOD' );
	}
echo JHtml::_('bootstrap.endAccordion'); ?>
</fieldset>