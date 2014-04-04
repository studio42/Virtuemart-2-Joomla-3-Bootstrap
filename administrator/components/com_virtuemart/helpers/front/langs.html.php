<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Toolbar
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;
$view = jRequest::getWord('view','virtuemart');
$langCurrent = jRequest::getWord('lang',null);
$langs = VmConfig::get('active_languages',false);
$flagPath = JURI::root( true ).'/administrator/components/com_virtuemart/assets/images/flag/' ; ?>
<footer class="navbar navbar-default navbar-fixed-bottom dark" role="navigation">
  <div class="container"><span style="padding: 0px 32px"><?php echo jText::_('JFIELD_LANGUAGE_LABEL').' ['.$langCurrent.'] </span> &nbsp; ';
	foreach ($langs as $lang) { 
		$tag = (substr($lang,0,2) );
		$url = JRoute::_('index.php?option=com_virtuemart&view='.$view.'&lang='.$tag.'&tmpl=component');
		if ($langCurrent == $tag) $btn = 'primary' ;
		else $btn = 'default' ;
		$flagImage = '<img style="vertical-align: middle;" alt="'.$lang.'" src="'.$flagPath.$tag.'.png"> ';
			?>
			<a class="btn btn-<?php echo $btn?>" href="<?php echo $url?>"><?php echo $lang.' '.$flagImage?></a> 
		<?php
	} ?>
	</div>
</footer>
