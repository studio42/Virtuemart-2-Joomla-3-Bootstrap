<?php defined ( '_JEXEC' ) or die ();

// add messgae in bootstrap style
$app   = JFactory::getApplication();
$messages = $app->getMessageQueue();
// html code for front-end administration
$view=jrequest::getWord('view');
$task =jrequest::getWord('task');
if ($pid = jrequest::getInt('virtuemart_product_id'))
	$link = 'productdetails&virtuemart_product_id='.$pid;
elseif ($cid = jrequest::getInt('virtuemart_category_id'))
	$link = 'category&virtuemart_category_id='.$cid;
else $link ='virtuemart';
JHtml::_('script', 'system/core.js', false, true);
$document = JFactory::getDocument();
JHtml::_('jquery.ui');
vmJsApi::js ('jquery.ui.autocomplete.html');
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
	});
" ;
$document->addScriptDeclaration ( $j);
$document->addStyleDeclaration('
 body { padding-top: 30px; }
 body,.vmadmin{width:100%;background-color:#fff !important;background-image:none !important;margin:0px;}
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
$treemenu= array(
    'catalog' => array(
        'product&task=add' => jtext::_('COM_VIRTUEMART_PRODUCT').' '.jtext::_('JNEW'),
        'product' => 'COM_VIRTUEMART_PRODUCT_S',
        'category' => 'COM_VIRTUEMART_CATEGORY_S',
        'manufacturer' => 'COM_VIRTUEMART_MANUFACTURER_S',
        'custom' => 'COM_VIRTUEMART_CUSTOM',
        'ratings' => 'COM_VIRTUEMART_LISTREVIEWS',
    ),
    'sales' => array(
        'orders' => 'COM_VIRTUEMART_ORDER_S',
        'shoppers' => 'COM_VIRTUEMART_USER_S',
        'coupons' => 'COM_VIRTUEMART_COUPON_S',
        'report' => 'COM_VIRTUEMART_REPORT'
    )
);

?>

<div class="vm2admin row-fluid">
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a class="btn btn-navbar" data-toggle="collapse" data-target="#mainvmnav">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a target="_blank" title="Your Shop" href="<?php echo jRoute::_('index.php?option=com_virtuemart') ?>" class="brand">Your shop<i class="icon-out-2 small"></i></a>
				<div class="nav-collapse" id="mainvmnav">
					<ul class="nav" id="menu">
					<?php foreach ($treemenu as $topname => $menus) { ?>
					<li class="dropdown"><a href="#" data-toggle="dropdown" class="dropdown-toggle"><?php echo $topname ?><span class="caret"></span></a>
						<ul class="dropdown-menu">
							<?php foreach ($menus as $link => $name) { ?>
							<li>
								<a href="<?php echo jRoute::_('index.php?option=com_virtuemart&tmpl=component&view='.$link) ?>" class="menu-cpanel"><?php echo jtext::_($name) ?></a>
							</li>
							<?php } ?>
						</ul>
					</li>
					<?php } ?>
						<li class="dropdown"><a href="<?php echo jRoute::_('index.php?option=com_virtuemart&tmpl=component') ?>"><?php echo jtext::_('COM_VIRTUEMART_ADMIN') ?><span class="caret"></span></a>
					<ul/>
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
	<div class="header">
		<div class="container-fluid">
			<h1 class="page-title"><?php echo $document->getTitle(); ?>
				<div class="nav pull-right">
					<a class="btn" href="<?php echo jRoute::_('index.php?option=com_virtuemart&view='.$link ) ?>"><?php echo jText::_('COM_VIRTUEMART_CLOSE') ?></a>
				</div>
				<div class="clearfix"></div>
			</h1>

		</div>
	</div>
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

	<div  class="row-fluid">