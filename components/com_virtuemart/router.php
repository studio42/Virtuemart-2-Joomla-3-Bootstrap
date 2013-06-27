<?php
if(  !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
 *
 * @package VirtueMart
 * @Author Kohl Patrick
 * @subpackage router
 * @copyright Copyright (C) 2010 Kohl Patrick - Virtuemart Team - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */


function virtuemartBuildRoute(&$query) {

	$segments = array();


	$helper = vmrouterHelper::getInstance($query);
	/* simple route , no work , for very slow server or test purpose */
	if ($helper->router_disabled) {
		foreach ($query as $key => $value){
			if  ($key != 'option')  {
				if ($key != 'Itemid') {
					$segments[]=$key.'/'.$value;
					unset($query[$key]);
				}
			}

		}
		return $segments;
	}

	if ($helper->edit) return $segments;

	/* Full route , heavy work*/
	// $lang = $helper->lang ;
	$view = '';

	$jmenu = $helper->menu ;

	if(isset($query['langswitch'])) unset($query['langswitch']);

	if(isset($query['view'])){
		$view = $query['view'];
		unset($query['view']);
	}
	switch ($view) {
		case 'virtuemart';
			$query['Itemid'] = $jmenu['virtuemart'] ;
			break;
		/* Shop category or virtuemart view
		 All ideas are wellcome to improve this
		 because is the biggest and more used */
		case 'category';
			$start = null;
			$limitstart = null;
			$limit = null;

			if ( isset($query['virtuemart_manufacturer_id'])  ) {
				$segments[] = $helper->lang('manufacturer').'/'.$helper->getManufacturerName($query['virtuemart_manufacturer_id']) ;
				unset($query['virtuemart_manufacturer_id']);

			}
			if ( isset($query['search'])  ) {
				$segments[] = $helper->lang('search') ;
				unset($query['search']);
			}
			if ( isset($query['keyword'] )) {
				$segments[] = $query['keyword'];
				unset($query['keyword']);
			}
			if ( isset($query['virtuemart_category_id']) ) {
				if (isset($jmenu['virtuemart_category_id'][ $query['virtuemart_category_id'] ] ) )
					$query['Itemid'] = $jmenu['virtuemart_category_id'][$query['virtuemart_category_id']];
				else {
					$categoryRoute = $helper->getCategoryRoute($query['virtuemart_category_id']);
					if ($categoryRoute->route) $segments[] = $categoryRoute->route;
					if ($categoryRoute->itemId) $query['Itemid'] = $categoryRoute->itemId;
				}
				unset($query['virtuemart_category_id']);
			}
			if ( isset($jmenu['category']) ) $query['Itemid'] = $jmenu['category'];


			if ( isset($query['order']) ) {
				if ($query['order'] =='DESC') $segments[] = $helper->lang('orderDesc') ;
				unset($query['order']);
			}

			if ( isset($query['orderby']) ) {
				$segments[] = $helper->lang('by').','.$helper->lang( $query['orderby']) ;
				unset($query['orderby']);
			}

			// Joomla replace before route limitstart by start but without SEF this is start !
			if ( isset($query['limitstart'] ) ) {
				$limitstart = $query['limitstart'] ;
				unset($query['limitstart']);
			}
			if ( isset($query['start'] ) ) {
				$start = $query['start'] ;
				unset($query['start']);
			}
			if ( isset($query['limit'] ) ) {
				$limit = $query['limit'] ;
				unset($query['limit']);
			}
			if ($start !== null &&  $limitstart!== null ) {
				//$segments[] = $helper->lang('results') .',1-'.$start ;
			} else if ( $start>0 ) {
				// using general limit if $limit is not set
				if ($limit === null) $limit= vmrouterHelper::$limit ;

				$segments[] = $helper->lang('results') .','. ($start+1).'-'.($start+$limit);
			} else if ($limit !== null && $limit != vmrouterHelper::$limit ) $segments[] = $helper->lang('results') .',1-'.$limit ;//limit change

			return $segments;
			break;
		/* Shop product details view  */
		case 'productdetails';

			$virtuemart_product_id = false;
			if (isset($jmenu['virtuemart_product_id'][ $query['virtuemart_product_id'] ] ) ) {
				$query['Itemid'] = $jmenu['virtuemart_product_id'][$query['virtuemart_product_id']];
				unset($query['virtuemart_product_id']);
				unset($query['virtuemart_category_id']);
			} else {
				if(isset($query['virtuemart_product_id'])) {
					if ($helper->use_id) $segments[] = $query['virtuemart_product_id'];
					$virtuemart_product_id = $query['virtuemart_product_id'];
					unset($query['virtuemart_product_id']);
				}
				if(empty( $query['virtuemart_category_id'])){
					$query['virtuemart_category_id'] = $helper->getParentProductcategory($virtuemart_product_id);
				}
				if(!empty( $query['virtuemart_category_id'])){
					$categoryRoute = $helper->getCategoryRoute($query['virtuemart_category_id']);
					if ($categoryRoute->route) $segments[] = $categoryRoute->route;
					if ($categoryRoute->itemId) $query['Itemid'] = $categoryRoute->itemId;
					else $query['Itemid'] = $jmenu['virtuemart'];
				} else {
					$query['Itemid'] = $jmenu['virtuemart']?$jmenu['virtuemart']:@$jmenu['virtuemart_category_id'][0];
				}
				unset($query['virtuemart_category_id']);

				if($virtuemart_product_id)
					$segments[] = $helper->getProductName($virtuemart_product_id);
			}
			if (!count($query))	return $segments;
			break;
		case 'manufacturer';

			if(isset($query['virtuemart_manufacturer_id'])) {
				if (isset($jmenu['virtuemart_manufacturer_id'][ $query['virtuemart_manufacturer_id'] ] ) ) {
					$query['Itemid'] = $jmenu['virtuemart_manufacturer_id'][$query['virtuemart_manufacturer_id']];
				} else {
					$segments[] = $helper->lang('manufacturers').'/'.$helper->getManufacturerName($query['virtuemart_manufacturer_id']) ;
					if ( isset($jmenu['manufacturer']) ) $query['Itemid'] = $jmenu['manufacturer'];
					else $query['Itemid'] = $jmenu['virtuemart'];
				}
				unset($query['virtuemart_manufacturer_id']);
			} else {
				if ( isset($jmenu['manufacturer']) ) $query['Itemid'] = $jmenu['manufacturer'];
				else $query['Itemid'] = $jmenu['virtuemart'];
			}
			break;
		case 'user';

			if ( isset($jmenu['user']) ) $query['Itemid'] = $jmenu['user'];
			else {
				$segments[] = $helper->lang('user') ;
				$query['Itemid'] = $jmenu['virtuemart'];
			}

			if (isset($query['task'])) {
				//vmdebug('my task in user view',$query['task']);
				if($query['task']=='editaddresscart'){
					if ($query['addrtype'] == 'ST'){
						$segments[] = $helper->lang('editaddresscartST') ;
					} else {
						$segments[] = $helper->lang('editaddresscartBT') ;
					}
				}

				else if($query['task']=='editaddresscheckout'){
					if ($query['addrtype'] == 'ST'){
						$segments[] = $helper->lang('editaddresscheckoutST') ;
					} else {
						$segments[] = $helper->lang('editaddresscheckoutBT') ;
					}
				}

				else if($query['task']=='editaddress'){

					if (isset($query['addrtype']) and $query['addrtype'] == 'ST'){
						$segments[] = $helper->lang('editaddressST') ;
					} else {
						$segments[] = $helper->lang('editaddressBT') ;
					}
				}
				else {
					$segments[] =  $helper->lang($query['task']);
				}
				/*	if ($query['addrtype'] == 'BT' && $query['task']='editaddresscart') $segments[] = $helper->lang('editaddresscartBT') ;
								elseif ($query['addrtype'] == 'ST' && $query['task']='editaddresscart') $segments[] = $helper->lang('editaddresscartST') ;
								elseif ($query['addrtype'] == 'BT') $segments[] = $helper->lang('editaddresscheckoutST') ;
								elseif ($query['addrtype'] == 'ST') $segments[] = $helper->lang('editaddresscheckoutST') ;
								else $segments[] = $query['task'] ;*/

				unset ($query['task'] , $query['addrtype']);
			}
			break;
		case 'vendor';
/* VM208 */
			if(isset($query['virtuemart_vendor_id'])) {
				if (isset($jmenu['virtuemart_vendor_id'][ $query['virtuemart_vendor_id'] ] ) ) {
					$query['Itemid'] = $jmenu['virtuemart_vendor_id'][$query['virtuemart_vendor_id']];
				} else {
					if ( isset($jmenu['vendor']) ) {
						$query['Itemid'] = $jmenu['vendor'];
					} else {
						$segments[] = $helper->lang('vendor') ;
						$query['Itemid'] = $jmenu['virtuemart'];
					}
				}
			} else if ( isset($jmenu['vendor']) ) {
				$query['Itemid'] = $jmenu['vendor'];
			} else {
				$segments[] = $helper->lang('vendor') ;
				$query['Itemid'] = $jmenu['virtuemart'];
			}
			if (isset($query['virtuemart_vendor_id'])) {
				//$segments[] = $helper->lang('vendor').'/'.$helper->getVendorName($query['virtuemart_vendor_id']) ;
				$segments[] =  $helper->getVendorName($query['virtuemart_vendor_id']) ;
				unset ($query['virtuemart_vendor_id'] );
			}


			break;
		case 'cart';
			if ( isset($jmenu['cart']) ) $query['Itemid'] = $jmenu['cart'];
			else {
				$segments[] = $helper->lang('cart') ;
				$query['Itemid'] = $jmenu['virtuemart'];
			}

			break;
		case 'orders';
			if ( isset($jmenu['orders']) ) $query['Itemid'] = $jmenu['orders'];
			else {
				$segments[] = $helper->lang('orders') ;
				$query['Itemid'] = $jmenu['virtuemart'];
			}
			if ( isset($query['order_number']) ) {
				$segments[] = 'number/'.$query['order_number'];
				unset ($query['order_number'],$query['layout']);
			} else if ( isset($query['virtuemart_order_id']) ) {
				$segments[] = 'id/'.$query['virtuemart_order_id'];
				unset ($query['virtuemart_order_id'],$query['layout']);
			}

			//else unset ($query['layout']);
			break;

		// sef only view
		default ;
			$segments[] = $view;


	}

	//	if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
	//	vmdebug("case 'productdetails'",$query);

	if (isset($query['task'])) {
		$segments[] = $helper->lang($query['task']);
		unset($query['task']);
	}
	if (isset($query['layout'])) {
		$segments[] = $helper->lang($query['layout']) ;
		unset($query['layout']);
	}
	// sef the slimbox View
/*	if (isset($query['tmpl'])) {
		//if ( $query['tmpl'] = 'component') $segments[] = 'modal' ;
		$segments[] = $query['tmpl'] ;
		unset($query['tmpl']);
	}*/
	return $segments;
}

/* This function can be slower because is used only one time  to find the real URL*/
function virtuemartParseRoute($segments) {

	$vars = array();
	$helper = vmrouterHelper::getInstance();
	if ($helper->router_disabled) {
		$total = count($segments);
		for ($i = 0; $i < $total; $i=$i+2) {
			$vars[ $segments[$i] ] = $segments[$i+1];
		}
		return $vars;
	}
	if (empty($segments)) {
		return $vars;
	}
	//$lang = $helper->lang ;
	// revert '-' (Joomla change - to :) //
	foreach  ($segments as &$value) {
		$value = str_replace(':', '-', $value);
	}

	// $splitted = explode(',',$segments[0],2);
	$splitted = explode(',',end($segments),2);

	if ( $helper->compareKey($splitted[0] ,'results')){
		// array_shift($segments);
		array_pop($segments);
		$results = explode('-',$splitted[1],2);
		//Pagination has changed, removed the -1 note by Max Milbers NOTE: Works on j1.5, but NOT j1.7
		// limitstart is swapped by joomla to start ! See includes/route.php
		if ($start = $results[0]-1) $vars['limitstart'] = $start;
		else $vars['limitstart'] = 0 ;
		$vars['limit'] = $results[1]-$results[0]+1;

	} else {
		$vars['limitstart'] = 0 ;
		if(vmrouterHelper::$limit === null){
			vmrouterHelper::$limit = VmConfig::get('list_limit', 20);
		}
		$vars['limit'] = vmrouterHelper::$limit;

	}

	if (empty($segments)) {
		$vars['view'] = 'category';
		$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
		return $vars;
	}

	// $orderby = explode(',',$segments[0],2);
	$orderby = explode(',',end($segments),2);
	if (  $helper->compareKey($orderby[0] , 'by') ) {
		$vars['orderby'] =$helper->getOrderingKey($orderby[1]) ;
		// array_shift($segments);
		array_pop($segments);

		if (empty($segments)) {
			$vars['view'] = 'category';
			$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
			return $vars;
		}
	}
	if (  $helper->compareKey(end($segments),'orderDesc') ){
		$vars['order'] ='DESC' ;
		array_pop($segments);
		if (empty($segments)) {
			$vars['view'] = 'category';
			$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
			return $vars;
		}
	}

	if ( $segments[0] == 'product') {
		$vars['view'] = 'product';
		$vars['task'] = $segments[1];
		$vars['tmpl'] = 'component';
		return $vars;
	}

	if (  $helper->compareKey($segments[0] ,'manufacturer') ) {
		array_shift($segments);
		$vars['virtuemart_manufacturer_id'] =  $helper->getManufacturerId($segments[0]);
		array_shift($segments);
		// OSP 2012-02-29 removed search malforms SEF path and search is performed
		// $vars['search'] = 'true';
		if (empty($segments)) {
			$vars['view'] = 'category';
			$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
			return $vars;
		}

	}
/* added in vm208 */
// if no joomla link: vendor/vendorname/layout
// if joomla link joomlalink/vendorname/layout
	if (  $helper->compareKey($segments[0] ,'vendor') ) {
		$vars['virtuemart_vendor_id'] =  $helper->getVendorId($segments[1]);
		// OSP 2012-02-29 removed search malforms SEF path and search is performed
		// $vars['search'] = 'true';
		// this can never happen
		if (empty($segments)) {
			$vars['view'] = 'vendor';
			$vars['virtuemart_vendor_id'] = $helper->activeMenu->virtuemart_vendor_id ;
			return $vars;
		}

	}

	if ( $helper->compareKey($segments[0] ,'search') ) {
		$vars['search'] = 'true';
		array_shift($segments);
		if ( !empty ($segments) ) {
			$vars['keyword'] = array_shift($segments);

		}
		$vars['view'] = 'category';
		$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
		if (empty($segments)) return $vars;
	}
	if (end($segments) == 'modal') {
		$vars['tmpl'] = 'component';
		array_pop($segments);

	}
	if ( $helper->compareKey(end($segments) ,'askquestion') ) {
		$vars = (array)$helper->activeMenu ;
		$vars['task'] = 'askquestion';
		array_pop($segments);

	} elseif ( $helper->compareKey(end($segments) ,'recommend') ) {
		$vars = (array)$helper->activeMenu ;
		$vars['task'] = 'recommend';
		array_pop($segments);

	} elseif ( $helper->compareKey(end($segments) ,'notify') ) {
		$vars = (array)$helper->activeMenu ;
		$vars['layout'] = 'notify';
		array_pop($segments);

	}

	if (empty($segments)) return $vars ;

	// View is first segment now
	$view = $segments[0];
	if ( $helper->compareKey($view,'orders') || $helper->activeMenu->view == 'orders') {
		$vars['view'] = 'orders';
		if ( $helper->compareKey($view,'orders')){
			array_shift($segments);

		}
		if (empty($segments)) {
			$vars['layout'] = 'list';
		}
		else if ($helper->compareKey($segments[0],'list') ) {
			$vars['layout'] = 'list';
			array_shift($segments);
		}
		if ( !empty($segments) ) {
			if ($segments[0] ='number')
				$vars['order_number'] = $segments[1] ;
			else $vars['virtuemart_order_id'] = $segments[1] ;
			$vars['layout'] = 'details';
		}
		return $vars;
	}
	else if ( $helper->compareKey($view,'user') || $helper->activeMenu->view == 'user') {
		$vars['view'] = 'user';
		if ( $helper->compareKey($view,'user') ) {
			array_shift($segments);
		}

		if ( !empty($segments) ) {
			if (  $helper->compareKey($segments[0] ,'editaddresscartBT') ) {
				$vars['addrtype'] = 'BT' ;
				$vars['task'] = 'editaddresscart' ;
			}
			elseif (  $helper->compareKey($segments[0] ,'editaddresscartST') ) {
				$vars['addrtype'] = 'ST' ;
				$vars['task'] = 'editaddresscart' ;
			}
			elseif (  $helper->compareKey($segments[0] ,'editaddresscheckoutBT') ) {
				$vars['addrtype'] = 'BT' ;
				$vars['task'] = 'editaddresscheckout' ;
			}
			elseif (  $helper->compareKey($segments[0] ,'editaddresscheckoutST') ) {
				$vars['addrtype'] = 'ST' ;
				$vars['task'] = 'editaddresscheckout' ;
			}
			elseif (  $helper->compareKey($segments[0] ,'editaddressST') ) {
				$vars['addrtype'] = 'ST' ;
				$vars['task'] = 'editaddressST' ;
			}
			elseif (  $helper->compareKey($segments[0] ,'editaddressBT') ) {
				$vars['addrtype'] = 'BT' ;
				$vars['task'] = 'edit' ;
				$vars['layout'] = 'edit' ;      //I think that should be the layout, not the task
			}
			elseif (  $helper->compareKey($segments[0] ,'edit') ) {
				$vars['layout'] = 'edit' ;      //uncomment and lets test
			}
			else $vars['task'] = $segments[0] ;
		}
		return $vars;
	}
	else if ( $helper->compareKey($view,'vendor') || $helper->activeMenu->view == 'vendor') {
		/* vm208 */
		$vars['view'] = 'vendor';

		if ( $helper->compareKey($view,'vendor') ) {
			array_shift($segments);
			if (empty($segments)) return $vars;
		}
		//$vars['virtuemart_vendor_id'] = array_shift($segments);//// already done
		//array_shift($segments);
		$vars['virtuemart_vendor_id'] =  $helper->getVendorId($segments[0]);
		array_shift($segments);
		if(!empty($segments)) {
			if ( $helper->compareKey($segments[0] ,'contact') ) $vars['layout'] = 'contact' ;
			elseif ( $helper->compareKey($segments[0] ,'tos') ) $vars['layout'] = 'tos' ;
			elseif ( $helper->compareKey($segments[0] ,'details') ) $vars['layout'] = 'details' ;
		} else $vars['layout'] = 'details' ;

		return $vars;

	}
	else if ( $helper->compareKey($view,'cart') || $helper->activeMenu->view == 'cart') {
		$vars['view'] = 'cart';
		if ( $helper->compareKey($view,'cart') ) {
			array_shift($segments);
			if (empty($segments)) return $vars;
		}
		if ( $helper->compareKey($segments[0] ,'edit_shipment') ) $vars['task'] = 'edit_shipment' ;
		elseif ( $helper->compareKey($segments[0] ,'editpayment') ) $vars['task'] = 'editpayment' ;
		elseif ( $helper->compareKey($segments[0] ,'delete') ) $vars['task'] = 'delete' ;
		elseif ( $helper->compareKey($segments[0] ,'checkout') ) $vars['task'] = 'checkout' ;
		else $vars['task'] = $segments[0];
		return $vars;
	}

	else if ( $helper->compareKey($view,'manufacturers') || $helper->activeMenu->view == 'manufacturer') {
		$vars['view'] = 'manufacturer';

		if ( $helper->compareKey($view,'manufacturers') ) {
			array_shift($segments);
		}

		if (!empty($segments) ) {
			$vars['virtuemart_manufacturer_id'] =  $helper->getManufacturerId($segments[0]);
			array_shift($segments);
		}
		if ( isset($segments[0]) && $segments[0] == 'modal') {
			$vars['tmpl'] = 'component';
			array_shift($segments);
		}
		// if (isset($helper->activeMenu->virtuemart_manufacturer_id))
		// $vars['virtuemart_manufacturer_id'] = $helper->activeMenu->virtuemart_manufacturer_id ;

		vmdebug('my parsed URL vars',$vars);
		return $vars;
	}


	/*
	 * seo_sufix must never be used in category or router can't find it
	 * eg. suffix as "-suffix", a category with "name-suffix" get always a false return
	 * Trick : YOu can simply use "-p","-x","-" or ".htm" for better seo result if it's never in the product/category name !
	 */
	/*	if (substr(end($segments ), -(int)$helper->seo_sufix_size ) == $helper->seo_sufix ) {
			vmdebug('$segments productdetail',$segments,end($segments ));*/
	$last_elem = end($segments);
	$slast_elem = prev($segments);
	if ( (substr($last_elem, -(int)$helper->seo_sufix_size ) == $helper->seo_sufix)
		|| ($last_elem=='notify' && substr($slast_elem, -(int)$helper->seo_sufix_size ) == $helper->seo_sufix) ) {

		$vars['view'] = 'productdetails';
		if($last_elem=='notify') {
			$vars['layout'] = 'notify';
			array_pop($segments);
		}

		if (!$helper->use_id ) {
			$product = $helper->getProductId($segments ,$helper->activeMenu->virtuemart_category_id);
			$vars['virtuemart_product_id'] = $product['virtuemart_product_id'];
			$vars['virtuemart_category_id'] = $product['virtuemart_category_id'];
			//vmdebug('View productdetails, using case !$helper->use_id',$vars,$helper->activeMenu);
		}
		elseif (isset($segments[1]) ){
			$vars['virtuemart_product_id'] = $segments[0];
			$vars['virtuemart_category_id'] = $segments[1];
			//vmdebug('View productdetails, using case isset($segments[1]',$vars);
		} else {
			$vars['virtuemart_product_id'] = $segments[0];
			$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
			//vmdebug('View productdetails, using case "else", which uses $helper->activeMenu->virtuemart_category_id ',$vars);
		}



	} elseif (!$helper->use_id && ($helper->activeMenu->view == 'category' ) )  {
		$vars['virtuemart_category_id'] = $helper->getCategoryId (end($segments) ,$helper->activeMenu->virtuemart_category_id);
		$vars['view'] = 'category' ;


	} elseif (isset($segments[0]) && ctype_digit ($segments[0]) || $helper->activeMenu->virtuemart_category_id>0 ) {
		$vars['virtuemart_category_id'] = $segments[0];
		$vars['view'] = 'category';


	} elseif ($helper->activeMenu->virtuemart_category_id >0 && $vars['view'] != 'productdetails') {
		$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
		$vars['view'] = 'category';

	} elseif ($id = $helper->getCategoryId (end($segments) ,$helper->activeMenu->virtuemart_category_id )) {

		// find corresponding category . If not, segment 0 must be a view
		$vars['virtuemart_category_id'] = $id;
		$vars['view'] = 'category' ;
	} else {
		$vars['view'] = $segments[0] ;
		if ( isset($segments[1]) ) {
			$vars['task'] = $segments[1] ;
		}
	}

	//vmdebug('Router vars',$vars);

	return $vars;
}

class vmrouterHelper {

	/* language array */
	public $lang = null ;
	public $langTag = null ;
	public $query = array();
	/* Joomla menus ID object from com_virtuemart */
	public $menu = null ;

	/* Joomla active menu( itemId ) object */
	public $activeMenu = null ;
	public $menuVmitems = null;
	/*
	  * $use_id type boolean
	  * Use the Id's of categorie and product or not
	  */
	public $use_id = false ;

	public $seo_translate = false ;
	private $orderings = null ;
	public static $limit = null ;
	/*
	  * $router_disabled type boolean
	  * true  = don't Use the router
	  */
	public $router_disabled = false ;

	/* instance of class */
	private static $_instances = array ();

	private static $_catRoute = array ();

	public $CategoryName = array();
	private $dbview = array('vendor' =>'vendor','category' =>'category','virtuemart' =>'virtuemart','productdetails' =>'product','cart' => 'cart','manufacturer' => 'manufacturer','user'=>'user');

	private function __construct($instanceKey,$query) {

		if (!$this->router_disabled = VmConfig::get('seo_disabled', false)) {

			$this->seo_translate = VmConfig::get('seo_translate', false);
			$this->setLangs($instanceKey);
			if ( JVM_VERSION===1 ) $this->setMenuItemId();
			else $this->setMenuItemIdJ17();
			$this->setActiveMenu();
			$this->use_id = VmConfig::get('seo_use_id', false);
			$this->seo_sufix = VmConfig::get('seo_sufix', '-detail');
			$this->seo_sufix_size = strlen($this->seo_sufix) ;
			$this->edit = ('edit' == JRequest::getCmd('task') );
			// if language switcher we must know the $query
			$this->query = $query;
		}

	}

	public static function getInstance(&$query = null) {

		if (!class_exists( 'VmConfig' )) {
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		}
		VmConfig::loadConfig();

		if (isset($query['langswitch']) ) {
			if ($query['langswitch'] != VMLANG ) $instanceKey = $query['langswitch'] ;
			unset ($query['langswitch']);

		} else $instanceKey = VMLANG ;
		if (! array_key_exists ($instanceKey, self::$_instances)){
			self::$_instances[$instanceKey] = new vmrouterHelper ($instanceKey,$query);

			if (self::$limit===null){
				$mainframe = Jfactory::getApplication(); ;
				$view = 'virtuemart';
				if(isset($query['view'])) $view = $query['view'];
				self::$limit= $mainframe->getUserStateFromRequest('com_virtuemart.'.$view.'.limit', VmConfig::get('list_limit', 20), 'int');
				// 				self::$limit= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', VmConfig::get('list_limit', 20), 'int');
			}
		}
		return self::$_instances[$instanceKey];
	}

	/* multi language routing ? */
	public function setLangs($instanceKey){
		$langs = VmConfig::get('active_languages',false);
		if(count($langs)> 1) {
			if(!in_array($instanceKey, $langs)) {
				$this->vmlang = VMLANG ;
				$this->langTag = strtr(VMLANG,'_','-');
			} else {
				$this->vmlang = strtolower(strtr($instanceKey,'-','_'));
				$this->langTag= $instanceKey;
			}
		} else $this->vmlang = $this->langTag = VMLANG ;
		$this->setLang($instanceKey);
		$this->Jlang = JFactory::getLanguage();
	}

	public function getCategoryRoute($virtuemart_category_id){

		$cache = JFactory::getCache('_virtuemart','');
		$key = $virtuemart_category_id. $this->vmlang ; // internal cache key
		if (!($CategoryRoute = $cache->get($key))) {
			$CategoryRoute = $this->getCategoryRouteNocache($virtuemart_category_id);
			$cache->store($CategoryRoute, $key);
		}
		return $CategoryRoute ;
	}
	/* Get Joomla menu item and the route for category */
	public function getCategoryRouteNocache($virtuemart_category_id){
		if (! array_key_exists ($virtuemart_category_id . $this->vmlang, self::$_catRoute)){
			$category = new stdClass();
			$category->route = '';
			$category->itemId = 0;
			$menuCatid = 0 ;
			$ismenu = false ;

			// control if category is joomla menu
			if (isset($this->menu['virtuemart_category_id'])) {
				if (isset( $this->menu['virtuemart_category_id'][$virtuemart_category_id])) {
					$ismenu = true;
					$category->itemId = $this->menu['virtuemart_category_id'][$virtuemart_category_id] ;
				} else {
					$CatParentIds = $this->getCategoryRecurse($virtuemart_category_id,0) ;
					/* control if parent categories are joomla menu */
					foreach ($CatParentIds as $CatParentId) {
						// No ? then find the parent menu categorie !
						if (isset( $this->menu['virtuemart_category_id'][$CatParentId]) ) {
							$category->itemId = $this->menu['virtuemart_category_id'][$CatParentId] ;
							$menuCatid = $CatParentId;
							break;
						}
					}
				}
			}
			if ($ismenu==false) {
				if ( $this->use_id ) $category->route = $virtuemart_category_id.'/';
				if (!isset ($this->CategoryName[$virtuemart_category_id])) {
					$this->CategoryName[$virtuemart_category_id] = $this->getCategoryNames($virtuemart_category_id, $menuCatid );
				}
				$category->route .= $this->CategoryName[$virtuemart_category_id] ;
				if ($menuCatid == 0  && $this->menu['virtuemart']) $category->itemId = $this->menu['virtuemart'] ;
			}
			self::$_catRoute[$virtuemart_category_id . $this->vmlang] = $category;
		}
		return self::$_catRoute[$virtuemart_category_id . $this->vmlang] ;
	}

	/*get url safe names of category and parents categories  */
	public function getCategoryNames($virtuemart_category_id,$catMenuId=0){

		static $categoryNamesCache = array();
		$strings = array();
		$db = JFactory::getDBO();
		$parents_id = array_reverse($this->getCategoryRecurse($virtuemart_category_id,$catMenuId)) ;

		foreach ($parents_id as $id ) {
			if(!isset($categoryNamesCache[$id])){
				$q = 'SELECT `slug` as name
					FROM  `#__virtuemart_categories_'.$this->vmlang.'`
					WHERE  `virtuemart_category_id`='.(int)$id;

				$db->setQuery($q);
				$cslug = $db->loadResult();
				$categoryNamesCache[$id] = $cslug;
				$strings[] = $cslug;
			} else {
				$strings[] = $categoryNamesCache[$id];
			}

		}

		if(function_exists('mb_strtolower')){
			return mb_strtolower(implode ('/', $strings ) );
		} else {
			return strtolower(implode ('/', $strings ) );
		}


	}
	/* Get parents of category*/
	public function getCategoryRecurse($virtuemart_category_id,$catMenuId,$first=true ) {
		static $idsArr = array();
		if ($first==true) $idsArr = array();

		$db			= JFactory::getDBO();
		$q = "SELECT `category_child_id` AS `child`, `category_parent_id` AS `parent`
				FROM  #__virtuemart_category_categories AS `xref`
				WHERE `xref`.`category_child_id`= ".(int)$virtuemart_category_id;
		$db->setQuery($q);
		$ids = $db->loadObject();
		if (isset ($ids->child)) {
			$idsArr[] = $ids->child;
			if($ids->parent != 0 and $catMenuId != $virtuemart_category_id and $catMenuId != $ids->parent) {
				$this->getCategoryRecurse($ids->parent,$catMenuId,false);
			}
		}
		return $idsArr ;
	}
	/* return id of categories
	 * $names are segments
	 * $virtuemart_category_ids is joomla menu virtuemart_category_id
	 */
	public function getCategoryId($slug,$virtuemart_category_id ){
		$db = JFactory::getDBO();
		$q = "SELECT `virtuemart_category_id`
				FROM  `#__virtuemart_categories_".$this->vmlang."`
				WHERE `slug` LIKE '".$db->getEscaped($slug)."' ";

		$db->setQuery($q);
		if (!$category_id = $db->loadResult()) {
			$category_id = $virtuemart_category_id;
		}

		return $category_id ;
	}

	/* Get URL safe Product name */
	public function getProductName($id){

		static $productNamesCache = array();

		if(!isset($productNamesCache[$id])){
			$db = JFactory::getDBO();
			$query = 'SELECT `slug` FROM `#__virtuemart_products_'.$this->vmlang.'`  ' .
				' WHERE `virtuemart_product_id` = ' . (int) $id;
			$db->setQuery($query);
			$name = $db->loadResult();
			$productNamesCache[$id] = $name ;
		} else {
			$name = $productNamesCache[$id];
		}

		return $name.$this->seo_sufix;
	}

	var $counter = 0;
	/* Get parent Product first found category ID */
	public function getParentProductcategory($id){

		$virtuemart_category_id = 0;
		$db			= JFactory::getDBO();
		$query = 'SELECT `product_parent_id` FROM `#__virtuemart_products`  ' .
			' WHERE `virtuemart_product_id` = ' . (int) $id;
		$db->setQuery($query);
		/* If product is child then get parent category ID*/
		if ($parent_id = $db->loadResult()) {
			$query = 'SELECT `virtuemart_category_id` FROM `#__virtuemart_product_categories`  ' .
				' WHERE `virtuemart_product_id` = ' . $parent_id;
			$db->setQuery($query);

			//When the child and parent id is the same, this creates a deadlock
			//add $counter, dont allow more then 10 levels
			if (!$virtuemart_category_id = $db->loadResult()){
				$this->counter++;
				if($this->counter<10){
					$this->getParentProductcategory($parent_id) ;
				}
			}

		}
		$this->counter = 0;
		return $virtuemart_category_id ;
	}


	/* get product and category ID */
	public function getProductId($names,$virtuemart_category_id = NULL ){
		$productName = array_pop($names);
		$productName =  substr($productName, 0, -(int)$this->seo_sufix_size );
		$product = array();
		$categoryName = end($names);

		$product['virtuemart_category_id'] = $this->getCategoryId($categoryName,$virtuemart_category_id ) ;
		$db = JFactory::getDBO();
		$q = 'SELECT `p`.`virtuemart_product_id`
			FROM `#__virtuemart_products_'.$this->vmlang.'` AS `p`
			LEFT JOIN `#__virtuemart_product_categories` AS `xref` ON `p`.`virtuemart_product_id` = `xref`.`virtuemart_product_id`
			WHERE `p`.`slug` LIKE "'.$db->getEscaped($productName).'" ';
		//$q .= "	AND `xref`.`virtuemart_category_id` = ".(int)$product['virtuemart_category_id'];
		$db->setQuery($q);
		$product['virtuemart_product_id'] = $db->loadResult();
		/* WARNING product name must be unique or you can't acces the product */

		return $product ;
	}

	/* Get URL safe Manufacturer name */
	public function getManufacturerName($virtuemart_manufacturer_id ){
		$db = JFactory::getDBO();
		$query = 'SELECT `slug` FROM `#__virtuemart_manufacturers_'.$this->vmlang.'` WHERE virtuemart_manufacturer_id='.(int)$virtuemart_manufacturer_id;
		$db->setQuery($query);

		return $db->loadResult();

	}

	/* Get Manufacturer id */
	public function getManufacturerId($slug ){
		$db = JFactory::getDBO();
		$query = "SELECT `virtuemart_manufacturer_id` FROM `#__virtuemart_manufacturers_".$this->vmlang."` WHERE `slug` LIKE '".$db->getEscaped($slug)."' ";
		$db->setQuery($query);

		return $db->loadResult();

	}
	/* Get URL safe Manufacturer name */
	public function getVendorName($virtuemart_vendor_id ){
		$db = JFactory::getDBO();
		$query = 'SELECT `slug` FROM `#__virtuemart_vendors_'.$this->vmlang.'` WHERE virtuemart_vendor_id='.(int)$virtuemart_vendor_id;
		$db->setQuery($query);

		return $db->loadResult();

	}
	/* Get Manufacturer id */
	public function getVendorId($slug ){
		$db = JFactory::getDBO();
		$query = "SELECT `virtuemart_vendor_id` FROM `#__virtuemart_vendors_".$this->vmlang."` WHERE `slug` LIKE '".$db->getEscaped($slug)."' ";
		$db->setQuery($query);

		return $db->loadResult();

	}
	/* Set $this-lang (Translator for language from virtuemart string) to load only once*/
	private function setLang($instanceKey){

		if ( $this->seo_translate ) {
			/* use translator */
			$lang =JFactory::getLanguage();
			$extension = 'com_virtuemart.sef';
			$base_dir = JPATH_SITE;
			$lang->load($extension, $base_dir);

		}
	}

	/* Set $this->menu with the Item ID from Joomla Menus */
	private function setMenuItemIdJ17(){

		$home 	= false ;
		$component	= JComponentHelper::getComponent('com_virtuemart');

		//else $items = $menus->getItems('component_id', $component->id);
		//get all vm menus

		$db			= JFactory::getDBO();
		$query = 'SELECT * FROM `#__menu`  where `link` like "index.php?option=com_virtuemart%" and client_id=0 and published=1 and (language="*" or language="'.$this->langTag.'")'  ;
		$db->setQuery($query);
		// 		vmdebug('setMenuItemIdJ17 q',$query);
		$this->menuVmitems= $db->loadObjectList();
		$homeid =0;
		if(empty($this->menuVmitems)){
			vmWarn(JText::_('COM_VIRTUEMART_ASSIGN_VM_TO_MENU'));
		} else {

			// Search  Virtuemart itemID in joomla menu
			foreach ($this->menuVmitems as $item)	{
				$linkToSplit= explode ('&',$item->link);

				$link =array();
				foreach ($linkToSplit as $tosplit) {
					$splitpos = strpos($tosplit, '=');
					$link[ (substr($tosplit, 0, $splitpos) ) ] = substr($tosplit, $splitpos+1);
				}
				//vmDebug('menu view link',$link);

				//This is fix to prevent entries in the errorlog.
				if(!empty($link['view'])){
					$view = $link['view'] ;
					if (array_key_exists($view,$this->dbview) ){
						$dbKey = $this->dbview[$view];
					}
					else {
						$dbKey = false ;
					}

					if ( isset($link['virtuemart_'.$dbKey.'_id']) && $dbKey ){
						$this->menu['virtuemart_'.$dbKey.'_id'][ $link['virtuemart_'.$dbKey.'_id'] ] = $item->id;
					}
					elseif ($home == $view ) continue;
					else $this->menu[$view]= $item->id ;

					if ($item->home === 1) {
						$home = $view;
						$homeid = $item->id;
					}
				} else {
					vmdebug('my item with empty $link["view"]',$item);
					vmError('$link["view"] is empty');
				}

			}
		}



		// init unsetted views  to defaut front view or nothing(prevent duplicates routes)
		if ( !isset( $this->menu['virtuemart']) ) {
			if (isset ($this->menu['virtuemart_category_id'][0]) ) {
				$this->menu['virtuemart'] = $this->menu['virtuemart_category_id'][0] ;
			}else $this->menu['virtuemart'] = $homeid;
		}
		// if ( !isset( $this->menu['manufacturer']) ) {
		// $this->menu['manufacturer'] = $this->menu['virtuemart'] ;
		// }
		// if ( !isset( $this->menu['vendor']) ) {
		// $this->menu['manufacturer'] = $this->menu['virtuemart'] ;
		// }

	}

	/* Set $this->menu with the Item ID from Joomla Menus */
	private function setMenuItemId(){

		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
		$component	= JComponentHelper::getComponent('com_virtuemart');
		$items = $menus->getItems('componentid', $component->id);

		if(empty($items)){
			vmWarn(JText::_('COM_VIRTUEMART_ASSIGN_VM_TO_MENU'));
		} else {
			// Search  Virtuemart itemID in joomla menu
			foreach ($items as $item)	{
				$view = $item->query['view'] ;
				if ($view=='virtuemart') $this->menu['virtuemart'] = $item->id;
				$dbKey = $this->dbview[$view];
				if ( isset($item->query['virtuemart_'.$dbKey.'_id']) )
					$this->menu['virtuemart_'.$dbKey.'_id'][ $item->query['virtuemart_'.$dbKey.'_id'] ] = $item->id;
				else $this->menu[$view]= $item->id ;
			}
		}

		// init unsetted views  to defaut front view or nothing(prevent duplicates routes)
		if ( !isset( $this->menu['virtuemart'][0]) ) {
			$this->menu['virtuemart'][0] = null;
		}
		if ( !isset( $this->menu['manufacturer']) ) {
			$this->menu['manufacturer'] = $this->menu['virtuemart'][0] ;
		}

	}
	/* Set $this->activeMenu to current Item ID from Joomla Menus */
	private function setActiveMenu(){
		if ($this->activeMenu === null ) {
			//$menu = JSite::getMenu();
			//$menu = JFactory::getApplication()->getMenu();
			$app		= JFactory::getApplication();
			$menu		= $app->getMenu('site');
			if ($Itemid = JRequest::getInt('Itemid',0) ) {
				$menuItem = $menu->getItem($Itemid);
			} else {
				$menuItem = $menu->getActive();
			}

			$this->activeMenu = new stdClass();
			$this->activeMenu->view			= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
			$this->activeMenu->virtuemart_category_id	= (empty($menuItem->query['virtuemart_category_id'])) ? 0 : $menuItem->query['virtuemart_category_id'];
			$this->activeMenu->virtuemart_product_id	= (empty($menuItem->query['virtuemart_product_id'])) ? null : $menuItem->query['virtuemart_product_id'];
			$this->activeMenu->virtuemart_manufacturer_id	= (empty($menuItem->query['virtuemart_manufacturer_id'])) ? null : $menuItem->query['virtuemart_manufacturer_id'];
/* added in 208 */
			$this->activeMenu->virtuemart_vendor_id	= (empty($menuItem->query['virtuemart_vendor_id'])) ? null : $menuItem->query['virtuemart_vendor_id'];

			$this->activeMenu->Component	= (empty($menuItem->component)) ? null : $menuItem->component;
		}

	}

	/*
	 * Get language key or use $key in route
	 */
	public function lang($key) {
		if ($this->seo_translate ) {
			$jtext = (strtoupper( $key ) );
			if ($this->Jlang->hasKey('COM_VIRTUEMART_SEF_'.$jtext) ){
				//vmdebug('router lang translated '.$jtext);
				return JText::_('COM_VIRTUEMART_SEF_'.$jtext);
			}
		}
		//vmdebug('router lang '.$key);
		//falldown
		return $key;
	}

	/*
	 * revert key or use $key in route
	 */
	public function getOrderingKey($key) {
		if ($this->seo_translate ) {
			if ($this->orderings == null) {
				$this->orderings = array(
					'p.virtuemart_product_id'=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_ID'),
					'product_sku'		=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_SKU'),
					'product_price'		=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_PRICE'),
					'category_name'		=> JText::_('COM_VIRTUEMART_SEF_CATEGORY_NAME'),
					'category_description'=> JText::_('COM_VIRTUEMART_SEF_CATEGORY_DESCRIPTION'),
					'mf_name' 			=> JText::_('COM_VIRTUEMART_SEF_MF_NAME'),
					'product_s_desc'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_S_DESC'),
					'product_desc'		=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_DESC'),
					'product_weight'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_WEIGHT'),
					'product_weight_uom'=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_WEIGHT_UOM'),
					'product_length'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_LENGTH'),
					'product_width'		=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_WIDTH'),
					'product_height'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_HEIGHT'),
					'product_lwh_uom'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_LWH_UOM'),
					'product_in_stock'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_IN_STOCK'),
					'low_stock_notification'=> JText::_('COM_VIRTUEMART_SEF_LOW_STOCK_NOTIFICATION'),
					'product_available_date'=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_AVAILABLE_DATE'),
					'product_availability'  => JText::_('COM_VIRTUEMART_SEF_PRODUCT_AVAILABILITY'),
					'product_special'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_SPECIAL'),
					'created_on' 		=> JText::_('COM_VIRTUEMART_SEF_CREATED_ON'),
					// 'p.modified_on' 		=> JText::_('COM_VIRTUEMART_SEF_MDATE'),
					'product_name'		=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_NAME'),
					'product_sales'		=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_SALES'),
					'product_unit'		=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_UNIT'),
					'product_packaging'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_PACKAGING'),
					'p.intnotes'			=> JText::_('COM_VIRTUEMART_SEF_INTNOTES'),
					'ordering' => JText::_('COM_VIRTUEMART_SEF_ORDERING')
				);
			}
			if ($result = array_search($key,$this->orderings )) {
				return $result;
			}
		}
		return $key;
	}
	/*
	 * revert string key or use $key in route
	 */
	public function compareKey($string, $key) {
		if ($this->seo_translate ) {
			if (JText::_('COM_VIRTUEMART_SEF_'.$key) == $string )
			{
				return true;
			}

		}
		if ($string == $key) return true;
		return false;
	}
}

// pure php no closing tag