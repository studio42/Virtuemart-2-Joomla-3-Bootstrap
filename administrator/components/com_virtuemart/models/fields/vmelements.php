<?php
defined('_JEXEC') or die();

/**
 * Derivated from http://docs.joomla.org/Making_single_installation_packages_for_Joomla!_1.5,_1.6_and_1.7
 * @package	VirtueMart
 * @subpackage Plugins  - Elements
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: $
 */
/*
 * This trick allows us to extend the correct class, based on whether it's Joomla! 1.5 or 1.6
 */
// if(!class_exists('JFakeElementBase')) {
jimport('joomla.form.formfield');
if (JVM_VERSION === 2) {

    class VmElements extends JFormField {

        // This line is required to keep Joomla! 1.6/1.7 from complaining
        public function getInput() {

        }

    }

} else {

    class VmElements extends JElement {

    }

}
