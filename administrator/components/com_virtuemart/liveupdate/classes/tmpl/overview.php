<?php
/**
 * @package LiveUpdate
 * @copyright Copyright ©2011 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();
?>

<div class="liveupdate vm2admin">

	<?php if(!$this->updateInfo->supported): ?>
	<div class="alert alert-error">
		<h3><i class="icon-cancel"></i><?php echo JText::_('LIVEUPDATE_NOTSUPPORTED_HEAD') ?></h3>

		<p><?php echo JText::_('LIVEUPDATE_NOTSUPPORTED_INFO'); ?></p>
		<p class="liveupdate-url">
			<?php echo $this->escape($this->updateInfo->extInfo->updateurl) ?>
		</p>
		<p><?php echo JText::sprintf('LIVEUPDATE_NOTSUPPORTED_ALTMETHOD', $this->escape($this->updateInfo->extInfo->title)); ?></p>
		<p class="liveupdate-buttons">
			<button class="btn" onclick="window.location='<?php echo $this->requeryURL ?>'" ><?php echo JText::_('LIVEUPDATE_REFRESH_INFO') ?></button>
		</p>
	</div>

	<?php elseif($this->updateInfo->stuck):?>
	<div class="alert alert-error">
		<h3><i class="icon-cancel"></i><?php echo JText::_('LIVEUPDATE_STUCK_HEAD') ?></h3>

		<p><?php echo JText::_('LIVEUPDATE_STUCK_INFO'); ?></p>
		<p><?php echo JText::sprintf('LIVEUPDATE_NOTSUPPORTED_ALTMETHOD', $this->escape($this->updateInfo->extInfo->title)); ?></p>

		<p class="liveupdate-buttons">
			<button class="btn" onclick="window.location='<?php echo $this->requeryURL ?>'" ><?php echo JText::_('LIVEUPDATE_REFRESH_INFO') ?></button>
		</p>
	</div>
	<?php else: 
		$class = $this->updateInfo->hasUpdates ? 'hasupdates' : 'noupdates';
		$ico = $this->updateInfo->hasUpdates ? 'info' : 'notice';
		$auth = $this->config->getAuthorization();
		$auth = empty($auth) ? '' : '?'.$auth;
	?>
	<?php if($this->needsAuth): ?>
	<p class="liveupdate-error-needsauth">
		<?php echo JText::_('LIVEUPDATE_ERROR_NEEDSAUTH'); ?>
	</p>
	<?php endif; ?>
	<div >
		<h3  class="alert alert-<?php echo $ico?>"><?php echo JText::_('LIVEUPDATE_'.strtoupper($class).'_HEAD') ?></h3>
		<div class="liveupdate-infotable row-striped">
			<div class="liveupdate-row row-fluid">
				<span ><?php echo JText::_('LIVEUPDATE_CONSIDER_COMPATIBILITY') ?></span>
			</div>
			<div class="liveupdate-row row-fluid">
				<span ><?php echo JText::_('LIVEUPDATE_REMEMBER_TO_UPDATE_AIO') ?></span>
			</div>
			<div class="liveupdate-row row-fluid">
				<span class="liveupdate-label span4"><?php echo JText::_('LIVEUPDATE_CURRENTVERSION') ?></span>
				<span class="liveupdate-data span8"><?php echo $this->updateInfo->extInfo->version ?></span>
			</div>
			<div class="liveupdate-row row-fluid">
				<span class="liveupdate-label span4"><?php echo JText::_('LIVEUPDATE_LATESTVERSION') ?></span>
				<span class="liveupdate-data span8"><?php echo $this->updateInfo->version ?></span>
			</div>
			<div class="liveupdate-row row-fluid">
				<span class="liveupdate-label span4"><?php echo JText::_('LIVEUPDATE_LATESTRELEASED') ?></span>
				<span class="liveupdate-data span8"><?php echo $this->updateInfo->date ?></span>
			</div>
			<div class="liveupdate-row row-fluid">
				<span class="liveupdate-label span4"><?php echo JText::_('LIVEUPDATE_DOWNLOADURL') ?></span>
				<span class="liveupdate-data span8"><a href="<?php echo $this->updateInfo->downloadURL.$auth?>"><?php echo $this->escape($this->updateInfo->downloadURL)?></a></span>
			</div>
			<div class="liveupdate-row row-fluid">
			<?php require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'version.php'); ?>
				<span ><?php echo $myVersion ?></span>
			</div>
			<div class="liveupdate-row row-fluid">
			<iframe src="http://virtuemart.net/index.php?option=com_content&id=416&tmpl=component" width="100%" height="400" name="Live update information">
  <p>Your browser blocks to display iFrames, please use the following link instead: <a href="http://virtuemart.net/index.php?option=com_content&id=416&tmpl=component">Live update information</a></p>
</iframe>
			</div>
		</div>

		<p class="liveupdate-buttons">
			<?php if($this->updateInfo->hasUpdates):?>
			<?php $disabled = $this->needsAuth ? 'disabled="disabled"' : ''?>
			<button class="btn" <?php echo $disabled?> onclick="window.location='<?php echo $this->runUpdateURL ?>'" ><i class="icon-box-add"></i> <?php echo JText::_('LIVEUPDATE_DO_UPDATE') ?></button>
			<?php endif;?>
			<button class="btn" onclick="window.location='<?php echo $this->requeryURL ?>'" ><?php echo JText::_('LIVEUPDATE_REFRESH_INFO') ?> <i class="icon-loop"></i></button>
		</p>
	</div>

	<?php endif; ?>

	<p class="liveupdate-poweredby">
		Powered by <a href="https://www.akeebabackup.com/software/akeeba-live-update.html">Akeeba Live Update</a>
	</p>

</div>