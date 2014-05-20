<?php
/**
 * pagination helper
 *
 * @package	VirtueMart
 * @subpackage Helpers
 * @author Max Milbers
 * @copyright Copyright (c) 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */

defined('_JEXEC') or die();

jimport('joomla.html.pagination');

class VmPagination extends JPagination {

	private $_perRow = 5;
	var $_viewall 		= null;
	
	function __construct($total, $limitstart, $limit, $perRow=5){
		if($perRow!==0){
			$this->_perRow = $perRow;
		}
		parent::__construct($total, $limitstart, $limit);
	}

	/** Creates a dropdown box for selecting how many records to show per page.
	 * Modification of Joomla Core libraries/html/pagination.php getLimitBox function
	 * The function uses as sequence a generic function or a sequence configured in the vmconfig
	 *
	 * use in a view.html.php $vmModel->setPerRow($perRow); to activate it
	 *
	 * @author Joe Motacek (Cleanshooter)
	 * @author Max Milbers
	 * @return  string   The HTML for the limit # input box.
	 * @since   11.1
	 */

	function setSequence($sequence){
		$this->_sequence = $sequence;
	}

	function getLimitBox($sequence=0)
	{
		$app = JFactory::getApplication();

		// Initialize variables
		$limits = array ();
		//_viewall removed in j3
		$viewall = isset($this->viewall) ? $this->viewall : $this->_viewall ;
		$selected = $viewall ? 0 : $this->limit;
		// Build the select list
		if ($app->isAdmin() || jRequest::getWord('tmpl') =="component") {

			if(empty($sequence)){
				$sequence = VmConfig::get('pagseq',0);
			}

			if(!empty($sequence)){
				$sequenceArray = explode(',', $sequence);
				if(count($sequenceArray>1)){
				foreach($sequenceArray as $items){
						$limits[$items]=JHtml::_('select.option', $items);
				}
				}
			}

			if(empty($limits)){
				// $limits[15] = JHTML::_('select.option', 15);
				$limits[20] = JHTML::_('select.option', 20);
				$limits[50] = JHTML::_('select.option', 50);
				$limits[100] = JHTML::_('select.option', 100);
				$limits[200] = JHTML::_('select.option', 200);
				$limits[500] = JHTML::_('select.option', 500);
			}

			if(!array_key_exists($this->limit,$limits)){
				$limits[$this->limit] = JHTML::_('select.option', $this->limit);
				ksort($limits);
				
			}

			$html = JHTML::_('select.genericlist',  $limits, 'limit', 'class="inputbox input-mini" onchange="Joomla.submitform();"', 'value', 'text', $selected);
		} else {

			$getArray = (JRequest::get( 'get' ));
			$link ='';
			//FIX BY STUDIO 42
			unset ($getArray['limit'], $getArray['language']);

			// foreach ($getArray as $key => $value ) $link .= '&'.$key.'='.$value;
			foreach ($getArray as $key => $value ){
				if (is_array($value)){
					foreach ($value as $k => $v ){
						$link .= '&'.$key.'['.$k.']'.'='.$v;
					}
				} else {
					$link .= '&'.$key.'='.$value;
				}
			}
			$link[0] = "?";
			$link = 'index.php'.$link ;
			if(empty($sequence)){
				$sequence = VmConfig::get('pagseq_'.$this->_perRow);
			}
			if(!empty($sequence)){
				$sequenceArray = explode(',', $sequence);
				if(count($sequenceArray>1)){
					foreach($sequenceArray as $items){
						$limits[$items]=JHtml::_('select.option', JRoute::_( $link.'&limit='. $items, false), $items);
					}
				}
			}

			if(empty($limits) or !is_array($limits)){
				if($this->_perRow===1) $this->_perRow = 5;
				$limits[$this->_perRow * 5] = JHtml::_('select.option',JRoute::_( $link.'&limit='. $this->_perRow * 5, false) ,$this->_perRow * 5);
				$limits[$this->_perRow * 10] = JHTML::_('select.option',JRoute::_( $link.'&limit='. $this->_perRow * 10, false) , $this->_perRow * 10 );
				$limits[$this->_perRow * 20] = JHTML::_('select.option',JRoute::_( $link.'&limit='. $this->_perRow * 20, false) , $this->_perRow * 20 );
				$limits[$this->_perRow * 50] = JHTML::_('select.option',JRoute::_( $link.'&limit='. $this->_perRow * 50, false) , $this->_perRow * 50 );
			}
			if(!array_key_exists($this->limit,$limits)){
				$limits[$this->limit] = JHTML::_('select.option', JRoute::_( $link.'&limit='.$this->limit,false),$this->limit);
				ksort($limits);
			}
			// fix studio42 false missing
			$selected= JRoute::_( $link.'&limit='. $selected, false) ;
			$js = 'onchange="window.top.location.href=this.options[this.selectedIndex].value"';

			$html = JHTML::_('select.genericlist',  $limits, '', 'class="inputbox input-mini" size="1" '.$js , 'value', 'text', $selected);
		}
		return $html;
	}

	/**
	 * Return the pagination footer.
	 *
	 * @return  string   Pagination footer.
	 *
	 * @since   11.1
	 */
	public function getListFooter()
	{
		$app = JFactory::getApplication();
		if (JVM_VERSION === 3 || ($app->isSite() && jrequest::getVar('tmpl') !=='component') ) return parent:: getListFooter();

		$list = array();
		$list['prefix'] = $this->prefix;
		$list['limit'] = $this->limit;
		$list['limitstart'] = $this->limitstart;
		$list['total'] = $this->total;
		$list['limitfield'] = $this->getLimitBox();
		$list['pagescounter'] = $this->getPagesCounter();
		$list['pageslinks'] = $this->getPagesLinks();

		// Initialise variables.
		$lang = JFactory::getLanguage();
		$html = "<div class='pagination'>\n";

		// TODO $html .= "\n<div class=\"limit\">".JText::_('JGLOBAL_DISPLAY_NUM').$list['limitfield']."</div>";
		$html .= $list['pageslinks'];
		// TODO $html .= "\n<div class=\"limit\">".$list['pagescounter']."</div>";

		$html .= "\n<input type='hidden' name=\"" . $list['prefix'] . "limitstart\" value=\"".$list['limitstart']."\" />";
		$html .= "\n</div>";

		return $html;
	}
	/**
	 * Create and return the pagination page list string, ie. Previous, Next, 1 2 3 ... x.
	 *
	 * @return  string  Pagination page list string.
	 *
	 * @since   11.1
	 * overide to use bootstrap in j2.5
	 */
	public function getPagesLinks()
	{
		$app = JFactory::getApplication();
		if (JVM_VERSION === 3 || ( $app->isSite() && jrequest::getVar('tmpl') !=='component' )  ) return parent:: getPagesLinks();

		// Build the page navigation list.
		$data = $this->_buildDataObject();

		$list = array();
		$list['prefix'] = $this->prefix;

		$itemOverride = false;

		// Build the select list
		if ($data->all->base !== null)
		{
			$list['all']['active'] = true;
			$list['all']['data'] = $this->pagination_item_active($data->all);
		}
		else
		{
			$list['all']['active'] = false;
			$list['all']['data'] = $this->pagination_item_inactive($data->all);
		}

		if ($data->start->base !== null)
		{
			$list['start']['active'] = true;
			$list['start']['data'] = $this->pagination_item_active($data->start);
		}
		else
		{
			$list['start']['active'] = false;
			$list['start']['data'] = $this->pagination_item_inactive($data->start) ;
		}
		if ($data->previous->base !== null)
		{
			$list['previous']['active'] = true;
			$list['previous']['data'] = $this->pagination_item_active($data->previous) ;
		}
		else
		{
			$list['previous']['active'] = false;
			$list['previous']['data'] = $this->pagination_item_inactive($data->previous) ;
		}

		$list['pages'] = array(); //make sure it exists
		foreach ($data->pages as $i => $page)
		{
			if ($page->base !== null)
			{
				$list['pages'][$i]['active'] = true;
				$list['pages'][$i]['data'] = $this->pagination_item_active($page);
			}
			else
			{
				$list['pages'][$i]['active'] = false;
				$list['pages'][$i]['data'] = $this->pagination_item_inactive($page) ;
			}
		}

		if ($data->next->base !== null)
		{
			$list['next']['active'] = true;
			$list['next']['data'] = $this->pagination_item_active($data->next) ;
		}
		else
		{
			$list['next']['active'] = false;
			$list['next']['data'] = $this->pagination_item_inactive($data->next) ;
		}

		if ($data->end->base !== null)
		{
			$list['end']['active'] = true;
			$list['end']['data'] = $this->pagination_item_active($data->end) ;
		}
		else
		{
			$list['end']['active'] = false;
			$list['end']['data'] = $this->pagination_item_inactive($data->end) ;
		}

		if ($this->total > $this->limit)
		{
			return $this->pagination_list_render($list);
		}
		else
		{
			return '';
		}
	}
	/*
	 * item active replacement using bootstrap
	 */
	function __pagination_item_active(&$item)
	{
		if ($item->base>0)
			return "<a href=\"#\" title=\"".$item->text."\" onclick=\"document.adminForm." . $item->prefix . "limitstart.value=".$item->base."; Joomla.submitform();return false;\">".$item->text."</a>";
		else
			return "<a href=\"#\" title=\"".$item->text."\" onclick=\"document.adminForm." . $item->prefix . "limitstart.value=0; Joomla.submitform();return false;\">".$item->text."</a>";
	}
	function __pagination_item_inactive(&$item)
	{
		return "<span>".$item->text."</span>";
	}
	/**
	 * Renders an active item in the pagination block
	 *
	 * @param   JPaginationObject  $item  The current pagination object
	 *
	 * @return  string  HTML markup for active item
	 *
	 * @since   3.0
	 */
	function pagination_item_active(&$item)
	{
		// Check for "Start" item
		if ($item->text == JText::_('JLIB_HTML_START'))
		{
			$display = '<i class="icon-first"></i>&nbsp;';
		}

		// Check for "Prev" item
		if ($item->text == JText::_('JPREV'))
		{
			$display = '<i class="icon-previous"></i>&nbsp;';
		}

		// Check for "Next" item
		if ($item->text == JText::_('JNEXT'))
		{
			$display = '&nbsp;<i class="icon-next"></i>';
		}

		// Check for "End" item
		if ($item->text == JText::_('JLIB_HTML_END'))
		{
			$display = '&nbsp;<i class="icon-last"></i>';
		}

		// If the display object isn't set already, just render the item with its text
		if (!isset($display))
		{
			$display = $item->text;
		}

		if ($item->base > 0)
		{
			$limit = 'limitstart.value=' . $item->base;
		}
		else
		{
			$limit = 'limitstart.value=0';
		}

		return '<li><a href="#" title="' . $item->text . '" onclick="document.adminForm.' . $item->prefix . $limit . '; Joomla.submitform();return false;">' . $display . '</a></li>';
	}
	/**
	 * Renders an inactive item in the pagination block
	 *
	 * @param   JPaginationObject  $item  The current pagination object
	 *
	 * @return  string  HTML markup for inactive item
	 *
	 * @since   3.0
	 */
	function pagination_item_inactive(&$item)
	{
		// Check for "Start" item
		if ($item->text == JText::_('JLIB_HTML_START'))
		{
			return '<li class="disabled"><a><i class="icon-first"></i>&nbsp;</a></li>';
		}

		// Check for "Prev" item
		if ($item->text == JText::_('JPREV'))
		{
			return '<li class="disabled"><a><i class="icon-previous"></i>&nbsp;</a></li>';
		}

		// Check for "Next" item
		if ($item->text == JText::_('JNEXT'))
		{
			return '<li class="disabled"><a>&nbsp;<i class="icon-next"></i>&nbsp;</a></li>';
		}

		// Check for "End" item
		if ($item->text == JText::_('JLIB_HTML_END'))
		{
			return '<li class="disabled"><a>&nbsp;<i class="icon-last"></i>&nbsp;</a></li>';
		}

		// Check if the item is the active page
		if (isset($item->active) && ($item->active))
		{
			return '<li class="active"><a>' . $item->text . '</a></li>';
		}

		// Doesn't match any other condition, render a normal item
		return '<li class="disabled"><a>' . $item->text . '</a></li>';
	}
	/**
	 * Renders the pagination list
	 *
	 * @param   array  $list  Array containing pagination information
	 *
	 * @return  string  HTML markup for the full pagination object
	 *
	 * @since   3.0
	 */
	function pagination_list_render($list)
	{
		// Calculate to display range of pages
		$currentPage = 1;
		$range = 1;
		$step = 5;
		foreach ($list['pages'] as $k => $page)
		{
			if (!$page['active'])
			{
				$currentPage = $k;
			}
		}
		if ($currentPage >= $step)
		{
			if ($currentPage % $step == 0)
			{
				$range = ceil($currentPage / $step) + 1;
			}
			else
			{
				$range = ceil($currentPage / $step);
			}
		}

		$html = '<ul class="pagination-list">';
		$html .= $list['start']['data'];
		$html .= $list['previous']['data'];

		foreach ($list['pages'] as $k => $page)
		{
			if (in_array($k, range($range * $step - ($step + 1), $range * $step)))
			{
				if (($k % $step == 0 || $k == $range * $step - ($step + 1)) && $k != $currentPage && $k != $range * $step - $step)
				{
					$page['data'] = preg_replace('#(<a.*?>).*?(</a>)#', '$1...$2', $page['data']);
				}
			}

			$html .= $page['data'];
		}

		$html .= $list['next']['data'];
		$html .= $list['end']['data'];

		$html .= '</ul>';

		return $html;
	}
}
