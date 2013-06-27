<?php
/**
*
* Layout for the login
*
* @package	VirtueMart
* @subpackage User
* @author Max Milbers, George Kostopoulos
*
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: cart.php 4431 2011-10-17 grtrustme $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//set variables, usually set by shopfunctionsf::getLoginForm in case this layout is differently used
if (!isset( $this->show )) $this->show = TRUE;
if (!isset( $this->from_cart )) $this->from_cart = FALSE;
if (!isset( $this->order )) $this->order = FALSE ;


if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');
$comUserOption=shopfunctionsF::getComUserOption();
if (empty($this->url)){
	$uri = JFactory::getURI();
	$url = $uri->toString(array('path', 'query', 'fragment'));
} else{
	$url = $this->url;
}

$user = JFactory::getUser();

if ($this->show and $user->id == 0  ) {
JHtml::_('behavior.formvalidation');
JHTML::_ ( 'behavior.modal' );


//$uri = JFactory::getURI();
//$url = $uri->toString(array('path', 'query', 'fragment'));


	//Extra login stuff, systems like openId and plugins HERE
    if (JPluginHelper::isEnabled('authentication', 'openid')) {
        $lang = JFactory::getLanguage();
        $lang->load('plg_authentication_openid', JPATH_ADMINISTRATOR);
        $langScript = '
//<![CDATA[
'.'var JLanguage = {};' .
                ' JLanguage.WHAT_IS_OPENID = \'' . JText::_('WHAT_IS_OPENID') . '\';' .
                ' JLanguage.LOGIN_WITH_OPENID = \'' . JText::_('LOGIN_WITH_OPENID') . '\';' .
                ' JLanguage.NORMAL_LOGIN = \'' . JText::_('NORMAL_LOGIN') . '\';' .
                ' var comlogin = 1;
//]]>
                ';
        $document = JFactory::getDocument();
        $document->addScriptDeclaration($langScript);
        JHTML::_('script', 'openid.js');
    }

    $html = '';
    JPluginHelper::importPlugin('vmpayment');
    $dispatcher = JDispatcher::getInstance();
    $returnValues = $dispatcher->trigger('plgVmDisplayLogin', array($this, &$html, $this->from_cart));

    if (is_array($html)) {
		foreach ($html as $login) {
		    echo $login.'<br />';
		}
    }
    else {
		echo $html;
    }

    //end plugins section

    //anonymous order section
    if ($this->order  ) {
    	?>

	    <div class="order-view">

	    <h1><?php echo JText::_('COM_VIRTUEMART_ORDER_ANONYMOUS') ?></h1>

	    <form action="<?php echo JRoute::_( 'index.php', 1, $this->useSSL); ?>" method="post" name="com-login" >

	    	<div class="width30 floatleft" id="com-form-order-number">
	    		<label for="order_number"><?php echo JText::_('COM_VIRTUEMART_ORDER_NUMBER') ?></label><br />
	    		<input type="text" id="order_number" name="order_number" class="inputbox" size="18" alt="order_number" />
	    	</div>
	    	<div class="width30 floatleft" id="com-form-order-pass">
	    		<label for="order_pass"><?php echo JText::_('COM_VIRTUEMART_ORDER_PASS') ?></label><br />
	    		<input type="text" id="order_pass" name="order_pass" class="inputbox" size="18" alt="order_pass" value="p_"/>
	    	</div>
	    	<div class="width30 floatleft" id="com-form-order-submit">
	    		<input type="submit" name="Submitbuton" class="button" value="<?php echo JText::_('COM_VIRTUEMART_ORDER_BUTTON_VIEW') ?>" />
	    	</div>
	    	<div class="clr"></div>
	    	<input type="hidden" name="option" value="com_virtuemart" />
	    	<input type="hidden" name="view" value="orders" />
	    	<input type="hidden" name="layout" value="details" />
	    	<input type="hidden" name="return" value="" />

	    </form>

	    </div>

<?php   }


    // XXX style CSS id com-form-login ?>
    <form id="com-form-login" action="<?php echo JRoute::_('index.php', $this->useXHTML, $this->useSSL); ?>" method="post" name="com-login" >
    <fieldset class="userdata">
	<?php if (!$this->from_cart ) { ?>
	<div>
		<h2><?php echo JText::_('COM_VIRTUEMART_ORDER_CONNECT_FORM'); ?></h2>
	</div>
<div class="clear"></div>
<?php } else { ?>
        <p><?php echo JText::_('COM_VIRTUEMART_ORDER_CONNECT_FORM'); ?></p>
<?php }   ?>
        <p class="width30 floatleft" id="com-form-login-username">
            <input type="text" name="username" class="inputbox" size="18" alt="<?php echo JText::_('COM_VIRTUEMART_USERNAME'); ?>" value="<?php echo JText::_('COM_VIRTUEMART_USERNAME'); ?>" onblur="if(this.value=='') this.value='<?php echo addslashes(JText::_('COM_VIRTUEMART_USERNAME')); ?>';" onfocus="if(this.value=='<?php echo addslashes(JText::_('COM_VIRTUEMART_USERNAME')); ?>') this.value='';" />
	</p>

        <p class="width30 floatleft" id="com-form-login-password">
            <?php if ( JVM_VERSION===1 ) { ?>
            <input type="password" id="passwd" name="passwd" class="inputbox" size="18" alt="<?php echo JText::_('COM_VIRTUEMART_PASSWORD'); ?>" value="<?php echo JText::_('COM_VIRTUEMART_PASSWORD'); ?>" onblur="if(this.value=='') this.value='<?php echo addslashes(JText::_('COM_VIRTUEMART_PASSWORD')); ?>';" onfocus="if(this.value=='<?php echo addslashes(JText::_('COM_VIRTUEMART_PASSWORD')); ?>') this.value='';" />
            <?php } else { ?>
            <input id="modlgn-passwd" type="password" name="password" class="inputbox" size="18" alt="<?php echo JText::_('COM_VIRTUEMART_PASSWORD'); ?>" value="<?php echo JText::_('COM_VIRTUEMART_PASSWORD'); ?>" onblur="if(this.value=='') this.value='<?php echo addslashes(JText::_('COM_VIRTUEMART_PASSWORD')); ?>';" onfocus="if(this.value=='<?php echo addslashes(JText::_('COM_VIRTUEMART_PASSWORD')); ?>') this.value='';" />
            <?php } ?>
		</p>

        <p class="width30 floatleft" id="com-form-login-remember">
            <input type="submit" name="Submit" class="default" value="<?php echo JText::_('COM_VIRTUEMART_LOGIN') ?>" />
            <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
            <label for="remember"><?php echo $remember_me = JVM_VERSION===1? JText::_('Remember me') : JText::_('JGLOBAL_REMEMBER_ME') ?></label>
            <input type="checkbox" id="remember" name="remember" class="inputbox" value="yes" alt="Remember Me" />
            <?php endif; ?>
        </p>
        </fieldset>
        <div class="clr"></div>

        <div class="width30 floatleft">
            <a href="<?php echo JRoute::_('index.php?option='.$comUserOption.'&view=remind'); ?>">
            <?php echo JText::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_USERNAME'); ?></a>
        </div>
        <div class="width30 floatleft">
            <a href="<?php echo JRoute::_('index.php?option='.$comUserOption.'&view=reset'); ?>">
            <?php echo JText::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD'); ?></a>
        </div>



        <?php /*
          $usersConfig = &JComponentHelper::getParams( 'com_users' );
          if ($usersConfig->get('allowUserRegistration')) { ?>
          <div class="width30 floatleft">
          <a  class="details" href="<?php echo JRoute::_( 'index.php?option=com_virtuemart&view=user' ); ?>">
          <?php echo JText::_('COM_VIRTUEMART_ORDER_REGISTER'); ?></a>
          </div>
          <?php }
         */ ?>

        <div class="clr"></div>


        <?php if ( JVM_VERSION===1 ) { ?>
        <input type="hidden" name="task" value="login" />
        <?php } else { ?>
	<input type="hidden" name="task" value="user.login" />
        <?php } ?>
        <input type="hidden" name="option" value="<?php echo $comUserOption ?>" />
        <input type="hidden" name="return" value="<?php echo base64_encode($url) ?>" />
        <?php echo JHTML::_('form.token'); ?>
    </form>

<?php  } else if ( $user->id ) { ?>

   <form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="login" id="form-login">
        <?php echo JText::sprintf( 'COM_VIRTUEMART_HINAME', $user->name ); ?>
	<input type="submit" name="Submit" class="button" value="<?php echo JText::_( 'COM_VIRTUEMART_BUTTON_LOGOUT'); ?>" />
        <input type="hidden" name="option" value="<?php echo $comUserOption ?>" />
        <?php if ( JVM_VERSION===1 ) { ?>
            <input type="hidden" name="task" value="logout" />
        <?php } else { ?>
            <input type="hidden" name="task" value="user.logout" />
        <?php } ?>
        <?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="return" value="<?php echo base64_encode($url) ?>" />
    </form>

<?php }

?>

