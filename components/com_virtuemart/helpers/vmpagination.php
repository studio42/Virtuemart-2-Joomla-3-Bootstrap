<?php
/**
 * front pagination helper
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
	
	public function __construct($total, $limitstart, $limit, $perRow=5){
		if((int)$perRow>1){
			$this->_perRow = $perRow;
		}
		parent::__construct($total, $limitstart, $limit);
		// studio42 fix for limit=int
		$this->setAdditionalUrlParam('limit', $limit);
	}

	/** Creates a dropdown box for selecting how many records to show per page.
	 * Modification of Joomla Core libraries/html/pagination.php getLimitBox function
	 * The function uses as sequence a generic function or a sequence configured in the vmconfig
	 *
	 * use in a view.html.php $vmModel->setPerRow($perRow); to activate it
	 *
	 * @author Joe Motacek (Cleanshooter)
	 * @author Max Milbers
	 * rewrite by Patrick Kohl, Studio42
	 * @return  string   The HTML for the limit # input box.
	 * @since   11.1
	 */

	public function setSequence($sequence){
		$this->_sequence = $sequence;
	}

	public function getLimitBox($sequence=0)
	{
		$app = JFactory::getApplication();

		// Initialize variables
		$limits = array ();
		//_viewall removed in j3
		$viewall = isset($this->viewall) ? $this->viewall : $this->_viewall ;
		$selected = $viewall ? 0 : $this->limit;
		// Build the select list
		$getArray = JRequest::get( 'get' );
		$link ='';

		//FIX BY STUDIO 42
		unset ($getArray['limit'], $getArray['language']);
		if (!isset($getArray['virtuemart_vendor_id']) ) {
			$vendor_id= JRequest::getInt( 'virtuemart_vendor_id',null );
			if ($vendor_id) $getArray['virtuemart_vendor_id'] = $vendor_id ;
		}
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
		$link .= '&limitstart=0';

		if(empty($sequence)){
			$sequence = VmConfig::get('pagseq_'.$this->_perRow);
		}
		if(!empty($sequence)){
			$sequences = explode(',', $sequence);
			if(count($sequences<2)){
				$sequences = array();
			}
		}
		// generic limits
		if (empty($sequences)) {
			if($this->_perRow===1) $this->_perRow = 5;
			$basePerRow = $this->_perRow * 2 ;
			$sequences[] = $basePerRow;
			$sequences[] = $basePerRow *2;
			$sequences[] = $basePerRow *4;
			$sequences[] = $basePerRow *10;
		}

		$app = JFactory::getApplication();
		$chromePath = JPATH_THEMES . '/' . $app->getTemplate() . '/html/pagination.php';

		foreach($sequences as $option){
			$limits[$option]=JHtml::_('select.option', JRoute::_( $link.'&limit='. $option), $option);
		}

		if(!isset($limits[$this->limit]) ) {
			$limits[$this->limit] = JHTML::_('select.option', JRoute::_( $link.'&limit='.$this->limit,false),$this->limit);
			ksort($limits);
		}
		if (file_exists($chromePath))
		{
			include_once $chromePath;

			if (function_exists('pagination_limit_box'))
			{
				return pagination_limit_box($limits,$selected);
			}
		}
		$selected= JRoute::_( $link.'&limit='. $selected) ;
		$js = 'onchange="window.top.location.href=this.options[this.selectedIndex].value"';
		$html = JHTML::_('select.genericlist',  $limits, '', 'class="pagination-limit inputbox input-mini" '.$js, 'value', 'text', $selected);
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
		if (JVM_VERSION === 3) return parent:: getListFooter();

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
		return parent:: getPagesLinks();

		$app = JFactory::getApplication();
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
	public function __pagination_item_active(&$item)
	{
		if ($item->base>0)
			return "<a href=\"#\" title=\"".$item->text."\" onclick=\"document.adminForm." . $item->prefix . "limitstart.value=".$item->base."; Joomla.submitform();return false;\">".$item->text."</a>";
		else
			return "<a href=\"#\" title=\"".$item->text."\" onclick=\"document.adminForm." . $item->prefix . "limitstart.value=0; Joomla.submitform();return false;\">".$item->text."</a>";
	}
	public function __pagination_item_inactive(&$item)
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
	public function pagination_item_active(&$item)
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
	public function pagination_item_inactive(&$item)
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
	public function pagination_list_render($list)
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
