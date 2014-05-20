<?php

if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
VmConfig::loadConfig();
$attentionText = JText::_('COM_VIRTUEMART_MIGRATION_WARN_VM1_EXTENSIONS');
vmWarn($attentionText);
echo $attentionText;

vmTrace('Called by',TRUE);