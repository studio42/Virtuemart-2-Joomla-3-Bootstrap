<?php
defined ('_JEXEC') or die();
/**
 *
 * @package    VirtueMart
 * @subpackage Plugins  - Elements
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: $
 */
class JElementVMFiles extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'Files';


	function fetchElement ($name, $value, &$node, $control_name) {

		jimport ('joomla.filesystem.folder');
		jimport ('joomla.filesystem.file');
		$lang = JFactory::getLanguage ();
		$lang->load ('com_virtuemart', JPATH_ADMINISTRATOR);
		// path to images directory
		$folder = $node->attributes ('directory');
		$rel_path = str_replace ('/', DS, $folder);
		$path = JPATH_ROOT . DS . $rel_path;
		$filter = $node->attributes ('filter');
		$exclude = array($node->attributes ('exclude'), '.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html');
		$stripExt = $node->attributes ('stripext');
		if (!JFolder::exists ($path)) {
			return JText::sprintf ('COM_VIRTUEMART_FOLDER_NOT_EXIST', $node->attributes ('directory'));
		}

		$files = JFolder::files ($path, $filter, FALSE, FALSE, $exclude);

		$options = array();

		if (!$node->attributes ('hide_none')) {
			$options[] = JHTML::_ ('select.option', '-1', '- ' . JText::_ ('Do not use') . ' -');
		}

		if (!$node->attributes ('hide_default')) {
			$options[] = JHTML::_ ('select.option', '', '- ' . JText::_ ('Use default') . ' -');
		}

		if (is_array ($files)) {
			foreach ($files as $file) {
				if ($exclude) {
					if (preg_match (chr (1) . $exclude . chr (1), $file)) {
						continue;
					}
				}
				if ($stripExt) {
					$file = JFile::stripExt ($file);
				}
				$options[] = JHTML::_ ('select.option', $file, $file);
			}
		}
		$class = 'multiple="true" size="5"';
		return JHTML::_ ('select.genericlist', $options, '' . $control_name . '[' . $name . '][]', $class, 'value', 'text', $value, $control_name . $name);
	}

}