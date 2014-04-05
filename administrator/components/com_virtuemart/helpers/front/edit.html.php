<?php defined ( '_JEXEC' ) or die ();

// add messgae in bootstrap style
$app   = JFactory::getApplication();
$messages = $app->getMessageQueue();
$user = JFactory::getUser();
if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
$admin = Permissions::getInstance()->check('admin');
$vendor = VmModel::getModel('vendor');
// $vendor->setId($this->vendorId);
$vendor->setId(Permissions::getInstance()->isSuperVendor());
$store = $vendor->getVendor();

// html code for front-end administration
$view=jrequest::getWord('view');
$task =jrequest::getWord('task');

JHtml::_('script', 'system/core.js', false, true);
$document = JFactory::getDocument();
JHtml::_('jquery.ui');
vmJsApi::js ('jquery.ui.autocomplete.html');
vmJsApi::js ('template','administrator/components/com_virtuemart/assets/js');
$document->addStyleSheet(JURI::root(true).'/administrator/components/com_virtuemart/assets/css/admin.styles.css');
$j = "
	jQuery(function() {
		jQuery( '#virtuemartSave').click(function(e){
			e.preventDefault();
			jQuery( '#media-dialog' ).remove();
			document.adminForm.task.value='apply';
			document.adminForm.submit();
			return false;
		});
		jQuery('link[rel=stylesheet][href*=\"template\"]').remove();
		jQuery('.btn-micro').addClass('btn-mini');
		jQuery('#menu li a').click(function(e)
		{
			$('#menu li.open').removeClass('open');
			// console.log('toggle');
			// $('#menu li a')
			// e.stopPropagation();
		});
	});
" ;
$document->addScriptDeclaration ( $j);
$document->addStyleDeclaration('
@media (max-width: 767px) {
 body { padding-top: 0px;}
 .vm2admin .navbar-fixed-top,.vm2admin .header{ margin:0px;max-width:100%}
 .vm2admin .subhead {margin-left:0px;margin-right:0px}
}
 body,.vmadmin{width:100%;padding:0}
 .vm2admin .subhead,.vmadmin{margin:0px;}
body {margin:0px}
.page-title{font-size:150%;}
.navbar-fixed-top {
 margin-bottom: 0px;
}
#menu i, #nav-user .icon-cog, .desktop { width:80px }
 #system-message-container { display: none; }
/*#toolbar {padding-left: 10px;}
.vm2admin #adminForm input[type="text"] {width: auto;}
.vm2admin .navbar{margin-bottom:0px}
#system-message .message > ul {list-style: none outside none;}
#system-message dt { display: none; }
#system-message-container dl, #system-message-container dd{margin:0px} */
');
/* simplified front admin menu
	view => language key
 */

$params = JComponentHelper::getParams('com_virtuemart', true);
$addTask = true ;

$treemenu= array(
    'COM_VIRTUEMART_STORE' => array(
        'product' => '<i class="icon-camera"></i> '.jtext::_('COM_VIRTUEMART_PRODUCT_S'),
        'category' => '<i class="icon-folder"></i> '.jtext::_('COM_VIRTUEMART_CATEGORY_S'),
        'manufacturer' => '<i class="icon-briefcase"></i> '.jtext::_('COM_VIRTUEMART_MANUFACTURER_S'),
        'custom' => '<i class="icon-equalizer"></i> '.jtext::_('COM_VIRTUEMART_CUSTOM'),
        'media' => '<i class="icon-images"></i> '.jtext::_('COM_VIRTUEMART_MEDIA_S'),
        'ratings' => '<i class="icon-folder"></i> '.jtext::_('COM_VIRTUEMART_LISTREVIEWS'),
    ),
    'COM_VIRTUEMART_USER_S' => array(
        'orders' => '<i class="icon-stack"></i> '.jtext::_('COM_VIRTUEMART_ORDER_S'),
        'user' => '<i class="icon-users"></i> '.jtext::_('COM_VIRTUEMART_USER_S'),
        'coupon' => '<i class="icon-scissors"></i> '.jtext::_('COM_VIRTUEMART_COUPON_S'),
        'report' => '<i class="icon-folder"></i> '.jtext::_('COM_VIRTUEMART_REPORT'),
        'inventory' => '<i class="icon-health"></i> '.jtext::_('COM_VIRTUEMART_PRODUCT_INVENTORY')
    ),
    'COM_VIRTUEMART_ADD' => array(
        'product&task=add' => '<i class="icon-stack"></i> '.jtext::_('COM_VIRTUEMART_PRODUCT'),
        'category&task=add' => '<i class="icon-folder"></i> '.jtext::_('COM_VIRTUEMART_CATEGORY'),
        'manufacturer&task=add' => '<i class="icon-briefcase"></i> '.jtext::_('COM_VIRTUEMART_MANUFACTURER'),
        'coupon&task=add' => '<i class="icon-scissors"></i> '.jtext::_('COM_VIRTUEMART_COUPON')
    )
);
$mainIcons = array('COM_VIRTUEMART_STORE' => 'mobile','COM_VIRTUEMART_USER_S' => 'users','COM_VIRTUEMART_ADD' => 'new');
$task = 'edit';
?>

	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a class="btn btn-navbar" data-toggle="collapse" data-target="#mainvmnav">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a target="_blank" title="<?php echo jtext::_('COM_VIRTUEMART_INSTALL_GO_SHOP') ?>" href="<?php echo jRoute::_('index.php?option=com_virtuemart&view=virtuemart') ?>" class="brand"><?php echo $store->vendor_store_name ?> <i class="icon-out-2 small"></i></a>
				<div class="nav-collapse collapse" id="mainvmnav">
					<ul class="nav navbar-nav" id="menu">
						<li><a href="<?php echo jRoute::_('index.php?option=com_virtuemart&view=virtuemart&tmpl=component') ?>"><i class="icon icon-dashboard"></i><span class="desktop"><?php echo jtext::_('COM_VIRTUEMART_SHOP_HOME') ?></span></a></li>
						<?php foreach ($treemenu as $topname => $menus) {
							if ($topname == 'COM_VIRTUEMART_ADD') $task='add';
							$hasLink = false; ?>
							<li class="dropdown"><a href="#" data-toggle="dropdown" class="dropdown-toggle"><i class="icon icon-<?php echo $mainIcons[$topname] ?>"></i><span class="desktop"><?php echo jtext::_($topname) ?></span><span class="caret"></span></a>
								<ul class="dropdown-menu">
									<?php foreach ($menus as $link => $name) {
											if ( !ShopFunctions::can($task,$link) ) continue;
											$hasLink = true;
										?>
										<li>
											<a href="<?php echo jRoute::_('index.php?option=com_virtuemart&tmpl=component&view='.$link) ?>" class="menu-cpanel"><?php echo jText::_($name) ?></a>
										</li>
									<?php } 
									if (!$hasLink) { ?>
										<li class="nav-header">
											<?php echo jText::_('JDISABLED') ?></a>
										</li>
									<?php } ?>
								</ul>
							</li>
						<?php } ?>
					</ul>
					<ul class="nav pull-right">
						<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $user->name ?><b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li class=""><a href="<?php echo jRoute::_('index.php?option=com_virtuemart&view=user') ?>"><i class="icon icon-user"></i> <?php echo jText::_('COM_VIRTUEMART_YOUR_ACCOUNT_DETAILS') ?></a></li>
								<li class="divider"></li>
								<li class=""><a href="<?php echo jRoute::_('index.php?option=com_users&task=user.logout&'.jSession::getFormToken().'=1&return='.base64_encode('index.php?option=com_virtuemart')) ?>"><i class="icon icon-exit"></i> <?php echo jText::_('COM_VIRTUEMART_BUTTON_LOGOUT') ?></a></li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</nav>
	<?php if (count($messages) ) {
		foreach ($messages as $message ) {
			
		
			?>
			<div class="alert alert-<?php echo $message['type'] ?>">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<?php echo $message['message'] ?>
			</div>
			<?php 
		}
	} ?>
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php 
			echo $document->getTitle(); 
		?>
		</h1>
	</div>
</header>
	<div class="subhead-collapse">
		<div class="subhead">
			<div class="container-fluid">
				<div id="container-collapse" class="container-collapse"></div>
				<div class="row-fluid">
					<div class="span12">
						<?php $toolbar = JToolbar::getInstance('toolbar')->render('toolbar'); 
							echo $toolbar;
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
<div id="ajax-tmpl-component" class="dark">
	<div  class="row-fluid light">