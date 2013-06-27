<?php
defined ('_JEXEC') or die('Direct Access to ' . basename (__FILE__) . ' is not allowed.');

/**
 * General helper class
 *
 * This class provides some shop functions that are used throughout the VirtueMart shop.
 *
 * @package	VirtueMart
 * @subpackage Helpers
 * @author RolandD
 * @author Max Milbers
 * @author Patrick Kohl
 * @copyright Copyright (c) 2004-2008 Soeren Eberhardt-Biermann, 2009 VirtueMart Team. All rights reserved.
 * @version $Id: shopfunctions.php 6559 2012-10-18 13:22:30Z Milbo $
 */
class ShopFunctions {

	/**
	 * Contructor
	 */
	public function __construct () {

	}

	/*
	 * Add simple search to form
	* @param $searchLabel text to display before searchbox
	* @param $name 		 lists and id name
	* ??JText::_('COM_VIRTUEMART_NAME')
	*/

	static public function displayDefaultViewSearch ($searchLabel, $value, $name = 'search') {

		return JText::_ ('COM_VIRTUEMART_FILTER') . ' ' . JText::_ ($searchLabel) . ':
		<input type="text" name="' . $name . '" id="' . $name . '" value="' . $value . '" class="text_area" />
		<button onclick="this.form.submit();">' . JText::_ ('COM_VIRTUEMART_GO') . '</button>
		<button onclick="document.getElementById(\'' . $name . '\').value=\'\';this.form.submit();">' . JText::_ ('COM_VIRTUEMART_RESET') . '</button>';
	}

	/**
	 * Builds an enlist for information (not chooseable)
	 *
	 * //TODO check for misuse by code injection
	 *
	 * @author Max Milbers
	 *
	 * @param $fieldnameXref datafield for the xreftable, where the name is stored
	 * @param $tableXref xref table
	 * @param $fieldIdXref datafield for the xreftable, where the id is stored
	 * @param $idXref The id to query in the xref table
	 * @param $fieldname the name of the datafield in the main table
	 * @param $table main table
	 * @param $fieldId the name of the field where the id is stored
	 * @param $quantity The number of items in the list
	 * @return List as String
	 */
	static public function renderGuiList ($fieldnameXref, $tableXref, $fieldIdXref, $idXref, $fieldname, $table, $fieldId, $view, $quantity = 4, $translate = 1) {

		if (!class_exists( 'VmConfig' )) require(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		VmConfig::loadJLang('com_virtuemart_countries');

		//Sanitize input
		$quantity = (int)$quantity;

		$db = JFactory::getDBO ();
		$q = 'SELECT ' . $db->getEscaped ($fieldnameXref) . ' FROM ' . $db->getEscaped ($tableXref) . ' WHERE ' . $db->getEscaped ($fieldIdXref) . ' = "' . (int)$idXref . '"';
		$db->setQuery ($q);
		$tempArray = $db->loadResultArray ();
		//echo $db->_sql;
		if (isset($tempArray)) {
			$links = '';
			$ttip = '';
			$i = 0;
			foreach ($tempArray as $value) {
				if ($translate) {
					$mainTable = $table . '_' . VMLANG;
					$q = 'SELECT ' . $db->getEscaped ($fieldname) . ' FROM ' . $db->getEscaped ($mainTable) . ' JOIN ' . $table . ' using (`' . $fieldnameXref . '`) WHERE ' . $db->getEscaped ($fieldId) . ' = "' . (int)$value . '"';
				} else {
					$q = 'SELECT ' . $db->getEscaped ($fieldname) . ' FROM ' . $db->getEscaped ($table) . ' WHERE ' . $db->getEscaped ($fieldId) . ' = "' . (int)$value . '"';
				}
				$db->setQuery ($q);
				$tmp = $db->loadResult ();
				if ($i < $quantity) {
					if ($view != 'user') {
						$cid = 'cid';
					} else {
						$cid = 'virtuemart_user_id';
					}
					$links .= JHTML::_ ('link', JRoute::_ ('index.php?option=com_virtuemart&view=' . $view . '&task=edit&' . $cid . '[]=' . $value), JText::_($tmp)) . ', ';
				}
				$ttip .= $tmp . ', ';

				//				$list .= $tmp. ', ';
				$i++;
				//if($i==$quantity) break;
			}
			$links = substr ($links, 0, -2);
			$ttip = substr ($ttip, 0, -2);

			$list = '<span class="hasTip" title="' . $ttip . '" >' . $links . '</span>';

			return $list;
		} else {
			return '';
		}
	}

	/**
	 * Creates a Drop Down list of available Creditcards
	 *
	 * @author Max Milbers
	 * @deprecated
	 */
	static public function renderCreditCardList ($ccId, $multiple = FALSE) {

		$model = VmModel::getModel ('creditcard');
		$creditcards = $model->getCreditCards ();

		$attrs = '';
		$name = 'creditcard_name';
		$idA = $id = 'virtuemart_creditcard_id';

		if ($multiple) {
			$attrs = 'multiple="multiple"';
			$idA .= '[]';
		} else {
			$emptyOption = JHTML::_ ('select.option', '', JText::_ ('COM_VIRTUEMART_LIST_EMPTY_OPTION'), $id, $name);
			array_unshift ($creditcards, $emptyOption);
		}
		$listHTML = JHTML::_ ('select.genericlist', $creditcards, $idA, $attrs, $id, $name, $ccId);
		return $listHTML;
	}

	/**
	 * Creates a Drop Down list of available Vendors
	 *
	 * @author Max Milbers, RolandD
	 * @access public
	 * @param int $virtuemart_shoppergroup_id the shopper group to pre-select
	 * @param bool $multiple if the select list should allow multiple selections
	 * @return string HTML select option list
	 */
	static public function renderVendorList ($vendorId, $multiple = FALSE) {

		$db = JFactory::getDBO ();

		if (Vmconfig::get ('multix', 'none') == 'none') {

			$vendorId = 1;

			$q = 'SELECT `vendor_name` FROM #__virtuemart_vendors WHERE `virtuemart_vendor_id` = "' . (int)$vendorId . '" ';
			$db->setQuery ($q);
			$vendor = $db->loadResult ();
			$html = '<input type="text" size="14" name="vendor_name" class="inputbox" value="' . $vendor . '" readonly="">';
		} else {
			if (!class_exists ('Permissions')) {
						require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
					}
			if (!Permissions::getInstance ()->check ('admin')) {
				if (empty($vendorId)) {
					$vendorId = 1;
					//Dont delete this message, we need it later for multivendor
					//JError::raiseWarning(1,'renderVendorList $vendorId is empty, please correct your used model to automatically set the virtuemart_vendor_id to the logged Vendor');
				}
				$q = 'SELECT `vendor_name` FROM #__virtuemart_vendors WHERE `virtuemart_vendor_id` = "' . (int)$vendorId . '" ';
				$db->setQuery ($q);
				$vendor = $db->loadResult ();
				$html = '<input type="text" size="14" name="vendor_name" class="inputbox" value="' . $vendor . '" readonly="">';
				//			$html .='<input type="hidden" value="'.$vendorId.'" name="virtuemart_vendor_id">';
				return $html;
			} else {

				$q = 'SELECT `virtuemart_vendor_id`,`vendor_name` FROM #__virtuemart_vendors';
				$db->setQuery ($q);
				$vendors = $db->loadAssocList ();

				$attrs = '';
				$name = 'vendor_name';
				$idA = $id = 'virtuemart_vendor_id';

				if ($multiple) {
					$attrs = 'multiple="multiple"';
					$idA .= '[]';
				} else {
					$emptyOption = JHTML::_ ('select.option', '', JText::_ ('COM_VIRTUEMART_LIST_EMPTY_OPTION'), $id, $name);
					array_unshift ($vendors, $emptyOption);
				}
				$listHTML = JHTML::_ ('select.genericlist', $vendors, $idA, $attrs, $id, $name, $vendorId);
				return $listHTML;
			}
		}

	}

	/**
	 * Creates a Drop Down list of available Shopper Groups
	 *
	 * @author Max Milbers, RolandD
	 * @access public
	 * @param int $shopperGroupId the shopper group to pre-select
	 * @param bool $multiple if the select list should allow multiple selections
	 * @return string HTML select option list
	 */
	static public function renderShopperGroupList ($shopperGroupId = 0, $multiple = TRUE,$name='virtuemart_shoppergroup_id') {

		$shopperModel = VmModel::getModel ('shoppergroup');
		$shoppergrps = $shopperModel->getShopperGroups (FALSE, TRUE);
		$attrs = '';
		//$name = 'shopper_group_name';
		//$idA = $id = 'virtuemart_shoppergroup_id';

		if ($multiple) {
			$attrs = 'multiple="multiple"';
			if($name=='virtuemart_shoppergroup_id'){
				$name.= '[]';
			}
		} else {
			$emptyOption = JHTML::_ ('select.option', '', JText::_ ('COM_VIRTUEMART_LIST_EMPTY_OPTION'), 'virtuemart_shoppergroup_id', 'shopper_group_name');
			array_unshift ($shoppergrps, $emptyOption);
		}
		//vmdebug('renderShopperGroupList',$name,$shoppergrps);
		$listHTML = JHTML::_ ('select.genericlist', $shoppergrps, $name, $attrs, 'virtuemart_shoppergroup_id', 'shopper_group_name', $shopperGroupId);
		return $listHTML;
	}

	/**
	 * Renders the list of Manufacturers
	 *
	 * @author St. Kraft
	 * Mod. <mediaDESIGN> St.Kraft 2013-02-24 Herstellerrabatt
	 */
	static public function renderManufacturerList ($manufacturerId = 0, $multiple = FALSE, $name = 'virtuemart_manufacturer_id') {

		$manufacturerModel = VmModel::getModel ('manufacturer');
		$manufacturers = $manufacturerModel->getManufacturers (FALSE, TRUE);
		$attrs = '';

		if ($multiple) {
			$attrs = 'multiple="multiple"';
			if($name=='virtuemart_manufacturer_id')	$name.= '[]';
		} else {
			$emptyOption = JHTML::_ ('select.option', '', JText::_ ('COM_VIRTUEMART_LIST_EMPTY_OPTION'), 'virtuemart_manufacturer_id', 'mf_name');
			array_unshift ($manufacturers, $emptyOption);
		}
		// vmdebug('renderManufacturerList',$name,$manufacturers);
		$listHTML = JHTML::_ ('select.genericlist', $manufacturers, $name, $attrs, 'virtuemart_manufacturer_id', 'mf_name', $manufacturerId);
		return $listHTML;
	}

	/**
	 * Render a simple country list
	 *
	 * @author jseros, Max Milbers, Valérie Isaksen
	 *
	 * @param int $countryId Selected country id
	 * @param boolean $multiple True if multiple selections are allowed (default: false)
	 * @param mixed $_attrib string or array with additional attributes,
	 * e.g. 'onchange=somefunction()' or array('onchange'=>'somefunction()')
	 * @param string $_prefix Optional prefix for the formtag name attribute
	 * @return string HTML containing the <select />
	 */
	static public function renderCountryList ($countryId = 0, $multiple = FALSE, $_attrib = array(), $_prefix = '', $required = 0) {

		$countryModel = VmModel::getModel ('country');
		$countries = $countryModel->getCountries (TRUE, TRUE, FALSE);
		$attrs = array();
		$name = 'country_name';
		$id = 'virtuemart_country_id';
		$idA = $_prefix . 'virtuemart_country_id';
		$attrs['class'] = 'virtuemart_country_id';
		// Load helpers and  languages files
		if (!class_exists( 'VmConfig' )) require(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		if(VmConfig::get('enableEnglish', 1)){
		    $jlang =JFactory::getLanguage();
		    $jlang->load('com_virtuemart_countries', JPATH_ADMINISTRATOR, 'en-GB', TRUE);
		    $jlang->load('com_virtuemart_countries', JPATH_ADMINISTRATOR, $jlang->getDefault(), TRUE);
		    $jlang->load('com_virtuemart_countries', JPATH_ADMINISTRATOR, NULL, TRUE);
		}

        $sorted_countries = array();
		$lang = JFactory::getLanguage();
		$prefix="COM_VIRTUEMART_COUNTRY_";
        foreach ($countries as  $country) {
	        $country_string = $lang->hasKey($prefix.$country->country_3_code) ?   JText::_($prefix.$country->country_3_code)  : $country->country_name;
            $sorted_countries[$country->virtuemart_country_id] = $country_string;
        }

		asort($sorted_countries);

		$countries_list=array();
		$i=0;
	    foreach ($sorted_countries as  $key=>$value) {
		    $countries_list[$i] = new stdClass();
	        $countries_list[$i]->$id = $key;
			$countries_list[$i]->$name = $value;
		    $i++;
	    }

		if ($required != 0) {
			$attrs['class'] .= ' required';
		}

		if ($multiple) {
			$attrs['multiple'] = 'multiple';
			$attrs['size'] = '12';
			$idA .= '[]';
		} else {
			$emptyOption = JHTML::_ ('select.option', '', JText::_ ('COM_VIRTUEMART_LIST_EMPTY_OPTION'), $id, $name);
			array_unshift ($countries_list, $emptyOption);
		}

		if (is_array ($_attrib)) {
			$attrs = array_merge ($attrs, $_attrib);
		} else {
			$_a = explode ('=', $_attrib, 2);
			$attrs[$_a[0]] = $_a[1];
		}

		return JHTML::_ ('select.genericlist', $countries_list, $idA, $attrs, $id, $name, $countryId);
	}

	/**
	 * Render a simple state list
	 *
	 * @author jseros, Patrick Kohl
	 *
	 * @param int $stateID Selected state id
	 * @param int $countryID Selected country id
	 * @param string $dependentField Parent <select /> ID attribute
	 * @param string $_prefix Optional prefix for the formtag name attribute
	 * @return string HTML containing the <select />
	 */
	static public function renderStateList ($stateId = '0', $_prefix = '', $multiple = FALSE, $required = 0) {

		if (is_array ($stateId)) {
					$stateId = implode (",", $stateId);
				}
		vmJsApi::JcountryStateList ($stateId);

		if ($multiple) {
			$attrs = 'multiple="multiple" size="12" name="' . $_prefix . 'virtuemart_state_id[]" ';
			//$class = 'class="inputbox multiple"';
		} else {
			/*$app = JFactory::getApplication();
			if($app->isSite()) {
				$class = 'class="chzn-select"';
			} else {
				$class = 'class="inputbox multiple"';
			}*/
			$attrs = 'size="1"  name="' . $_prefix . 'virtuemart_state_id" ';
		}

		if ($required != 0) {
			$attrs .= ' required';
		}

		$class = 'class="inputbox multiple"';

		$listHTML = '<select '.$class.' id="virtuemart_state_id" ' . $attrs . '>
						<option value="">' . JText::_ ('COM_VIRTUEMART_LIST_EMPTY_OPTION') . '</option>
						</select>';

		return $listHTML;
	}

	/**
	 * Renders the list for the tax rules
	 *
	 * @author Max Milbers
	 */
	static function renderTaxList ($selected, $name = 'product_tax_id', $class = '') {

		if (!class_exists ('VirtueMartModelCalc')) {
					require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'calc.php');
				}
		$taxes = VirtueMartModelCalc::getTaxes ();

		$taxrates = array();
		$taxrates[] = JHTML::_ ('select.option', '-1', JText::_ ('COM_VIRTUEMART_PRODUCT_TAX_NONE'), $name);
		$taxrates[] = JHTML::_ ('select.option', '0', JText::_ ('COM_VIRTUEMART_PRODUCT_TAX_NO_SPECIAL'), $name);
		foreach ($taxes as $tax) {
			$taxrates[] = JHTML::_ ('select.option', $tax->virtuemart_calc_id, $tax->calc_name, $name);
		}
		$listHTML = JHTML::_ ('Select.genericlist', $taxrates, $name, $class, $name, 'text', $selected);
		return $listHTML;
	}

	/**
	 * Creates the chooseable template list
	 *
	 * @author Max Milbers, impleri
	 *
	 * @param string defaultText Text for the empty option
	 * @param boolean defaultOption you can supress the empty otion setting this to false
	 * return array of Template objects
	 */
	static public function renderTemplateList ($defaultText = 0, $defaultOption = TRUE) {

		if (empty($defaultText)) {
					$defaultText = JText::_ ('COM_VIRTUEMART_TEMPLATE_DEFAULT');
				}

		$defaulttemplate = array();
		if ($defaultOption) {
			$defaulttemplate[0] = new stdClass;
			$defaulttemplate[0]->name = $defaultText;
			$defaulttemplate[0]->directory = 0;
			$defaulttemplate[0]->value = 'default';
		}

		if (JVM_VERSION === 1) {
			if (!class_exists ('TemplatesHelper')) {
						require (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_templates' . DS . 'helpers' . DS . 'template.php');
					}
			$jtemplates = TemplatesHelper::parseXMLTemplateFiles (JPATH_SITE . DS . 'templates');
		} else {
			require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_templates' . DS . 'helpers' . DS . 'templates.php');
			require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_templates' . DS . 'models' . DS . 'templates.php');
			$templatesModel = new TemplatesModelTemplates();
			$jtemplates = $templatesModel->getItems ();
		}

		foreach ($jtemplates as $key => $template) {
			$template->value = $template->name;
			if (JVM_VERSION === 2) {
				if ($template->client_id == '0') {
					$template->directory = $template->element;
				} else {
					unset($jtemplates[$key]);
				}
			}
		}

		return array_merge ($defaulttemplate, $jtemplates);
	}

	/**
	 * Returns all the weight unit
	 *
	 * @author Valérie Isaksen
	 */
	static function getWeightUnit () {

		static $weigth_unit;
		if ($weigth_unit) {
			return $weigth_unit;
		}
		return $weigth_unit = array(
			'KG' => JText::_ ('COM_VIRTUEMART_UNIT_NAME_KG')
		, 'G'   => JText::_ ('COM_VIRTUEMART_UNIT_NAME_G')
		, 'MG'   => JText::_ ('COM_VIRTUEMART_UNIT_NAME_MG')
		, 'LB'   => JText::_ ('COM_VIRTUEMART_UNIT_NAME_LB')
		, 'OZ'   => JText::_ ('COM_VIRTUEMART_UNIT_NAME_ONCE')
		);
	}

	/**
	 * Renders the string for the
	 *
	 * @author Valérie Isaksen
	 */
	static function renderWeightUnit ($name) {

		$weigth_unit = self::getWeightUnit ();
		if (isset($weigth_unit[$name])) {
					return $weigth_unit[$name];
		} else {
			return '';
		}
	}

	/**
	 * Renders the list for the Weight Unit
	 *
	 * @author Valérie Isaksen
	 */
	static function renderWeightUnitList ($name, $selected) {

		$weight_unit_default = self::getWeightUnit ();
		foreach ($weight_unit_default as  $key => $value) {
			$wu_list[] = JHTML::_ ('select.option', $key, $value, $name);
		}
		$listHTML = JHTML::_ ('Select.genericlist', $wu_list, $name, '', $name, 'text', $selected);
		return $listHTML;
		/*
		if (!class_exists('VmHTML')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'html.php');
		return VmHTML::selectList($name, $selected, $weight_unit_default);
		 *
		 */
	}

	static function renderUnitIsoList($name, $selected){

		$weight_unit_default = array(
			'KG' => JText::_ ('COM_VIRTUEMART_UNIT_SYMBOL_KG')
		, 'DMG' => JText::_ ('COM_VIRTUEMART_UNIT_SYMBOL_100MG')
		, 'M'   => JText::_ ('COM_VIRTUEMART_UNIT_SYMBOL_M')
		, 'SM'   => JText::_ ('COM_VIRTUEMART_UNIT_SYMBOL_SM')
		, 'CUBM'   => JText::_ ('COM_VIRTUEMART_UNIT_SYMBOL_CUBM')
		, 'L'   => JText::_ ('COM_VIRTUEMART_UNIT_SYMBOL_L')
		, 'DML'   => JText::_ ('COM_VIRTUEMART_UNIT_SYMBOL_100ML')
		);
		foreach ($weight_unit_default as  $key => $value) {
			$wu_list[] = JHTML::_ ('select.option', $key, $value, $name);
		}
		$listHTML = JHTML::_ ('Select.genericlist', $wu_list, $name, '', $name, 'text', $selected);
		return $listHTML;
	}

	/**
	 * Convert Weigth Unit
	 *
	 * @author Valérie Isaksen
	 */
	static function convertWeigthUnit ($value, $from, $to) {

		$from = strtoupper($from);
		$to = strtoupper($to);
		$value = str_replace (',', '.', $value);
		if ($from === $to) {
			return $value;
		}

		$g = 1;

		switch ($from) {
			case 'KG':
				$g = (float)(1000 * $value);
			break;
			case 'G':
				$g = (float)$value;
			break;
			case 'MG':
				$g = (float)($value / 1000);
			break;
			case 'LB':
				$g = (float)(453.59237 * $value);
			break;
			case 'OZ':
				$g = (float)(28.3495 * $value);
			break;
		}
		switch ($to) {
			case 'KG' :
				$value = (float)($g / 1000);
				break;
			case 'G' :
				$value = $g;
				break;
			case 'MG' :
				$value = (float)(1000 * $g);
				break;
			case 'LB' :
				$value = (float)($g / 453.59237);
				break;
			case 'OZ' :
				$value = (float)($g / 28.3495);
				break;
		}
		return $value;
	}

	/**
	 * Convert Metric Unit
	 *
	 * @author Florian Voutzinos
	 */
	static function convertDimensionUnit ($value, $from, $to) {

		$from = strtoupper($from);
		$to = strtoupper($to);
		$value = (float)str_replace (',', '.', $value);
		if ($from === $to) {
			return $value;
		}
		$meter = 1 * $value;

		// transform $value in meters
		switch ($from) {
			case 'CM':
				$meter = (float)(0.01 * $value);
				break;
			case 'MM':
				$meter = (float)(0.001 * $value);
				break;
			case 'YD' :
				$meter =(float) (0.9144 * $value);
				break;
			case 'FT' :
				$meter = (float)(0.3048 * $value);
				break;
			case 'IN' :
				$meter = (float)(0.0254 * $value);
				break;
		}
		switch ($to) {
			case 'CM':
				$value = (float)($meter / 0.01);
				break;
			case 'MM':
				$value = (float)($meter / 0.001);
				break;
			case 'YD' :
				$value =(float) ($meter / 0.9144);
				break;
			case 'FT' :
				$value = (float)($meter / 0.3048);
				break;
			case 'IN' :
				$value = (float)($meter / 0.0254);
				break;
		}
		return $value;
	}

	/**
	 * Renders the list for the Length, Width, Height Unit
	 *
	 * @author Valérie Isaksen
	 */
	static function renderLWHUnitList ($name, $selected) {

		if (!class_exists ('VmHTML')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
		}

		$lwh_unit_default = array('M' => JText::_ ('COM_VIRTUEMART_UNIT_NAME_M')
		, 'CM'                        => JText::_ ('COM_VIRTUEMART_UNIT_NAME_CM')
		, 'MM'                        => JText::_ ('COM_VIRTUEMART_UNIT_NAME_MM')
		, 'YD'                        => JText::_ ('COM_VIRTUEMART_UNIT_NAME_YARD')
		, 'FT'                        => JText::_ ('COM_VIRTUEMART_UNIT_NAME_FOOT')
		, 'IN'                        => JText::_ ('COM_VIRTUEMART_UNIT_NAME_INCH')
		);
		return VmHTML::selectList ($name, $selected, $lwh_unit_default);

	}


	/**
	 * Writes a line  for the price configuration
	 *
	 * @author Max Milberes
	 * @param string $name
	 * @param string $langkey
	 */
	static function writePriceConfigLine ($obj, $name, $langkey) {

		if (!class_exists ('VmHTML')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
		}
		$html =
			'<tr>
				<td class="key">
					<span class="editlinktip hasTip" title="' . JText::_ ($langkey . '_EXPLAIN') . '">
						<label>' . JText::_ ($langkey) .
						'</label>
					</span>
				</td>

				<td>' .
				VmHTML::checkbox ($name, $obj->get ($name)) . '
				</td>
				<td align="center">' .
				VmHTML::checkbox ($name . 'Text', $obj->get ($name . 'Text', 1)) . '
				</td>
				<td align="center">
				<input type="text" value="' . $obj->get ($name . 'Rounding', 2) . '" class="inputbox" size="4" name="' . $name . 'Rounding">
				</td>
			</tr>';
		return $html;
	}

	/**
	 * This generates the list when the user have different ST addresses saved
	 *
	 * @author Oscar van Eijk
	 */
	static function generateStAddressList ($view, $userModel, $task) {

		// Shipment address(es)
		$_addressList = $userModel->getUserAddressList ($userModel->getId (), 'ST');
		if (count ($_addressList) == 1 && empty($_addressList[0]->address_type_name)) {
			return JText::_ ('COM_VIRTUEMART_USER_NOSHIPPINGADDR');
		} else {
			$_shipTo = array();
			$useXHTTML = empty($view->useXHTML) ? TRUE : $view->useXHTML;
			$useSSL = empty($view->useSSL) ? FALSE : $view->useSSL;

			for ($_i = 0; $_i < count ($_addressList); $_i++) {
				if (empty($_addressList[$_i]->virtuemart_user_id)) {
					$_addressList[$_i]->virtuemart_user_id = JFactory::getUser ()->id;
				}
				if (empty($_addressList[$_i]->virtuemart_userinfo_id)) {
					$_addressList[$_i]->virtuemart_userinfo_id = 0;
				}
				if (empty($_addressList[$_i]->address_type_name)) {
					$_addressList[$_i]->address_type_name = 0;
				}

				$_shipTo[] = '<li>' . '<a href="index.php'
					. '?option=com_virtuemart'
					. '&view=user'
					. '&task=' . $task
					. '&addrtype=ST'
					. '&virtuemart_user_id[]=' . $_addressList[$_i]->virtuemart_user_id
					. '&virtuemart_userinfo_id=' . $_addressList[$_i]->virtuemart_userinfo_id
					. '">' . $_addressList[$_i]->address_type_name . '</a> ' ;

				$_shipTo[] = '&nbsp;&nbsp;<a href="'.JRoute::_ ('index.php?option=com_virtuemart&view=user&task=removeAddressST&virtuemart_user_id[]=' . $_addressList[$_i]->virtuemart_user_id . '&virtuemart_userinfo_id=' . $_addressList[$_i]->virtuemart_userinfo_id, $useXHTTML, $useSSL ). '" class="icon_delete">'.JText::_('COM_VIRTUEMART_USER_DELETE_ST').'</a></li>';

			}


			$addLink = '<a href="' . JRoute::_ ('index.php?option=com_virtuemart&view=user&task=' . $task . '&new=1&addrtype=ST&virtuemart_user_id[]=' . $userModel->getId (), $useXHTTML, $useSSL) . '"><span class="vmicon vmicon-16-editadd"></span> ';
			$addLink .= JText::_ ('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL') . ' </a>';

			return $addLink . '<ul>' . join ('', $_shipTo) . '</ul>';
		}
	}

	/**
	 * used mostly in the email, to display the vendor address
	 * Attention, this function will be removed from any view.html.php
	 *
	 * @static
	 * @param        $vendorId
	 * @param string $lineSeparator
	 * @param array  $skips
	 * @return string
	 */
	static public function renderVendorAddress ($vendorId,$lineSeparator="<br />", $skips = array('name','username','email','agreed')) {

		$vendorModel = VmModel::getModel('vendor');
		$vendorModel->setId($vendorId);
		$vendorFields = $vendorModel->getVendorAddressFields($vendorId);

		$vendorAddress = '';
		foreach ($vendorFields['fields'] as $field) {
			if(in_array($field['name'],$skips)) continue;
			if (!empty($field['value'])) {
				$vendorAddress .= $field['value'];
				if ($field['name'] != 'title' and $field['name'] != 'first_name' and $field['name'] != 'middle_name' and $field['name'] != 'zip') {
					$vendorAddress .= $lineSeparator;
				} else {
					$vendorAddress .= ' ';
				}
			}
		}
		return $vendorAddress;
	}



	public static $counter = 0;
	public static $categoryTree = 0;

	static public function categoryListTree ($selectedCategories = array(), $cid = 0, $level = 0, $disabledFields = array()) {

		if (empty(self::$categoryTree)) {
// 			vmTime('Start with categoryListTree');
			$cache = JFactory::getCache ('_virtuemart');
			$cache->setCaching (1);
			self::$categoryTree = $cache->call (array('ShopFunctions', 'categoryListTreeLoop'), $selectedCategories, $cid, $level, $disabledFields);
			// self::$categoryTree = self::categoryListTreeLoop($selectedCategories, $cid, $level, $disabledFields);
// 			vmTime('end loop categoryListTree '.self::$counter);
		}

		return self::$categoryTree;
	}

	/**
	 * Creates structured option fields for all categories
	 *
	 * @todo: Connect to vendor data
	 * @author RolandD, Max Milbers, jseros
	 * @param array 	$selectedCategories All category IDs that will be pre-selected
	 * @param int 		$cid 		Internally used for recursion
	 * @param int 		$level 		Internally used for recursion
	 * @return string 	$category_tree HTML: Category tree list
	 */
	static public function categoryListTreeLoop ($selectedCategories = array(), $cid = 0, $level = 0, $disabledFields = array()) {

		self::$counter++;

		static $categoryTree = '';

		$virtuemart_vendor_id = 1;

// 		vmSetStartTime('getCategories');
		$categoryModel = VmModel::getModel ('category');
		$level++;

		$categoryModel->_noLimit = TRUE;
		$app = JFactory::getApplication ();
		$records = $categoryModel->getCategories ($app->isSite (), $cid);
// 		vmTime('getCategories','getCategories');
		$selected = "";
		if (!empty($records)) {
			foreach ($records as $key => $category) {

				$childId = $category->category_child_id;

				if ($childId != $cid) {
					if (in_array ($childId, $selectedCategories)) {
						$selected = 'selected=\"selected\"';
					} else {
						$selected = '';
					}

					$disabled = '';
					if (in_array ($childId, $disabledFields)) {
						$disabled = 'disabled="disabled"';
					}

					if ($disabled != '' && stristr ($_SERVER['HTTP_USER_AGENT'], 'msie')) {
						//IE7 suffers from a bug, which makes disabled option fields selectable
					} else {
						$categoryTree .= '<option ' . $selected . ' ' . $disabled . ' value="' . $childId . '">';
						$categoryTree .= str_repeat (' - ', ($level - 1));

						$categoryTree .= $category->category_name . '</option>';
					}
				}

				if ($categoryModel->hasChildren ($childId)) {
					self::categoryListTreeLoop ($selectedCategories, $childId, $level, $disabledFields);
				}

			}
		}

		return $categoryTree;
	}

	/**
	 * Gets the total number of product for category
	 *
	 * @author jseros
	 * @param int $categoryId Own category id
	 * @return int Total number of products
	 */
	static public function countProductsByCategory ($categoryId = 0) {

		$categoryModel = VmModel::getModel ('category');
		return $categoryModel->countProducts ($categoryId);
	}

	/**
	 * Return the countryname or code of a given countryID
	 *
	 * @author Oscar van Eijk
	 * @access public
	 * @param int $_id Country ID
	 * @param char $_fld Field to return: country_name (default), country_2_code or country_3_code.
	 * @return string Country name or code
	 */
	static public function getCountryByID ($id, $fld = 'country_name') {

		if (empty($id)) {
			return '';
		}

		$id = (int)$id;
		$db = JFactory::getDBO ();

		$q = 'SELECT ' . $db->getEscaped ($fld) . ' AS fld FROM `#__virtuemart_countries` WHERE virtuemart_country_id = ' . (int)$id;
		$db->setQuery ($q);
		return $db->loadResult ();
	}

	/**
	 * Return the countryID of a given country name
	 *
	 * @author Oscar van Eijk
	 * @author Max Milbers
	 * @access public
	 * @param string $_name Country name
	 * @return int Country ID
	 */
	static public function getCountryIDByName ($name) {

		if (empty($name)) {
			return 0;
		}
		$db = JFactory::getDBO ();

		if (strlen ($name) === 2) {
			$fieldname = 'country_2_code';
		} else {
			if (strlen ($name) === 3) {
				$fieldname = 'country_3_code';
			} else {
				$fieldname = 'country_name';
			}
		}
		$q = 'SELECT `virtuemart_country_id` FROM `#__virtuemart_countries` WHERE `' . $fieldname . '` = "' . $db->getEscaped ($name) . '"';
		$db->setQuery ($q);
		$r = $db->loadResult ();
		return $r;
	}

	/**
	 * Return the statename or code of a given countryID
	 *
	 * @author Oscar van Eijk
	 * @access public
	 * @param int $_id State ID
	 * @param char $_fld Field to return: state_name (default), state_2_code or state_3_code.
	 * @return string state name or code
	 */
	static public function getStateByID ($id, $fld = 'state_name') {

		if (empty($id)) {
			return '';
		}
		$db = JFactory::getDBO ();
		$q = 'SELECT ' . $db->getEscaped ($fld) . ' AS fld FROM `#__virtuemart_states` WHERE virtuemart_state_id = "' . (int)$id . '"';
		$db->setQuery ($q);
		$r = $db->loadObject ();
		return $r->fld;
	}

	/**
	 * Return the stateID of a given state name
	 *
	 * @author Max Milbers
	 * @access public
	 * @param string $_name Country name
	 * @return int Country ID
	 */
	static public function getStateIDByName ($name) {

		if (empty($name)) {
			return 0;
		}
		$db = JFactory::getDBO ();
		if (strlen ($name) === 2) {
			$fieldname = 'state_2_code';
		} else {
			if (strlen ($name) === 3) {
				$fieldname = 'state_3_code';
			} else {
				$fieldname = 'state_name';
			}
		}
		$q = 'SELECT `virtuemart_state_id` FROM `#__virtuemart_states` WHERE `' . $fieldname . '` = "' . $db->getEscaped ($name) . '"';
		$db->setQuery ($q);
		$r = $db->loadResult ();
		return $r;
	}

	/*
	 * Return the Tax or code of a given taxID
	*
	* @author Valérie Isaksen
	* @access public
	* @param int $_d TAx ID
	* @return string Country name or code
	*/
	static public function getTaxByID ($id) {

		if (empty($id)) {
			return '';
		}

		$id = (int)$id;
		$db = JFactory::getDBO ();
		$q = 'SELECT  *   FROM `#__virtuemart_calcs` WHERE virtuemart_calc_id = ' . (int)$id;
		$db->setQuery ($q);
		return $db->loadAssoc ();

	}

	/**
	 * Return the currencyname or code of a given currencyID
	 *
	 * @author Valérie Isaksen
	 * @access public
	 * @param int $_id Currency ID
	 * @param char $_fld Field to return: currency_name (default), currency_2_code or currency_3_code.
	 * @return string Currency name or code
	 */
	static public function getCurrencyByID ($id, $fld = 'currency_name') {

		if (empty($id)) {
			return '';
		}

		$id = (int)$id;
		$db = JFactory::getDBO ();

		$q = 'SELECT ' . $db->getEscaped ($fld) . ' AS fld FROM `#__virtuemart_currencies` WHERE virtuemart_currency_id = ' . (int)$id;
		$db->setQuery ($q);
		return $db->loadResult ();
	}

	/**
	 * Return the countryID of a given Currency name
	 *
	 * @author Valerie Isaksen
	 * @access public
	 * @param string $_name Currency name
	 * @return int Currency ID
	 */
	static public function getCurrencyIDByName ($name) {

		if (empty($name)) {
			return 0;
		}
		$db = JFactory::getDBO ();

		if (strlen ($name) === 2) {
			$fieldname = 'currency_code_2';
		} else {
			if (strlen ($name) === 3) {
				$fieldname = 'currency_code_3';
			} else {
				$fieldname = 'currency_name';
			}
		}
		$q = 'SELECT `virtuemart_currency_id` FROM `#__virtuemart_currencies` WHERE `' . $fieldname . '` = "' . $db->getEscaped ($name) . '"';
		$db->setQuery ($q);
		$r = $db->loadResult ();
		return $r;
	}

	/**
	 * Print a select-list with enumerated categories
	 *
	 * @author jseros
	 *
	 * @param boolean $onlyPublished Show only published categories?
	 * @param boolean $withParentId Keep in mind $parentId param?
	 * @param integer $parentId Show only its childs
	 * @param string $attribs HTML attributes for the list
	 * @return string <Select /> HTML
	 */
	static public function getEnumeratedCategories ($onlyPublished = TRUE, $withParentId = FALSE, $parentId = 0, $name = '', $attribs = '', $key = '', $text = '', $selected = NULL) {

		$categoryModel = VmModel::getModel ('category');

		$categories = $categoryModel->getCategories ($onlyPublished, $parentId);

		foreach ($categories as $index => $cat) {
			$cat->category_name = $cat->ordering . '. ' . $cat->category_name;
			$categories[$index] = $cat;
		}
		return JHTML::_ ('Select.genericlist', $categories, $name, $attribs, $key, $text, $selected, $name);
	}

	/**
	 * Return the order status name for a given code
	 *
	 * @author Oscar van Eijk
	 * @access public
	 *
	 * @param char $_code Order status code
	 * @return string The name of the order status
	 */
	static public function getOrderStatusName ($_code) {

		$db = JFactory::getDBO ();

		$_q = 'SELECT `order_status_name` FROM `#__virtuemart_orderstates` WHERE `order_status_code` = "' . $db->getEscaped ($_code) . '"';
		$db->setQuery ($_q);
		$_r = $db->loadObject ();
		if (empty($_r->order_status_name)) {
			vmError ('getOrderStatusName: couldnt find order_status_name for ' . $_code);
			return 'current order status broken';
		} else {
			return JText::_($_r->order_status_name);
		}

	}

	/*
	 * @author Valerie
	 */
	static function InvoiceNumberReserved ($invoice_number) {

		if (($pos = strpos ($invoice_number, 'reservedByPayment_')) === FALSE) {
	       return FALSE;
	   } else {
	        return TRUE;
	   }
	}

	/**
	 * Creates an drop-down list with numbers from 1 to 31 or of the selected range,
	 * dont use within virtuemart. It is just meant for paymentmethods
	 *
	 * @param string $list_name The name of the select element
	 * @param string $selected_item The pre-selected value
	 */
	static function listDays ($list_name, $selected = FALSE, $start = NULL, $end = NULL) {

		$options = array();
		if (!$selected) {
			$selected = date ('d');
		}
		$start = $start ? $start : 1;
		$end = $end ? $end : $start + 30;
		$options[] = JHTML::_ ('select.option', 0, JText::_ ('DAY'));
		for ($i = $start; $i <= $end; $i++) {
			$options[] = JHTML::_ ('select.option', $i, $i);
		}
		return JHTML::_ ('select.genericlist', $options, $list_name, '', 'value', 'text', $selected);
	}


	/**
	 * Creates a Drop-Down List for the 12 months in a year
	 *
	 * @param string $list_name The name for the select element
	 * @param string $selected_item The pre-selected value
	 *
	 */
	static function listMonths ($list_name, $selected = FALSE, $class = '') {

		$options = array();
		if (!$selected) {
			$selected = date ('m');
		}

		$options[] = JHTML::_ ('select.option', 0, JText::_ ('MONTH'));
		$options[] = JHTML::_ ('select.option', "01", JText::_ ('JANUARY'));
		$options[] = JHTML::_ ('select.option', "02", JText::_ ('FEBRUARY'));
		$options[] = JHTML::_ ('select.option', "03", JText::_ ('MARCH'));
		$options[] = JHTML::_ ('select.option', "04", JText::_ ('APRIL'));
		$options[] = JHTML::_ ('select.option', "05", JText::_ ('MAY'));
		$options[] = JHTML::_ ('select.option', "06", JText::_ ('JUNE'));
		$options[] = JHTML::_ ('select.option', "07", JText::_ ('JULY'));
		$options[] = JHTML::_ ('select.option', "08", JText::_ ('AUGUST'));
		$options[] = JHTML::_ ('select.option', "09", JText::_ ('SEPTEMBER'));
		$options[] = JHTML::_ ('select.option', "10", JText::_ ('OCTOBER'));
		$options[] = JHTML::_ ('select.option', "11", JText::_ ('NOVEMBER'));
		$options[] = JHTML::_ ('select.option', "12", JText::_ ('DECEMBER'));
		return JHTML::_ ('select.genericlist', $options, $list_name, '', 'value', 'text', $selected);

	}

	/**
	 * Creates an drop-down list with years of the selected range or of the next 7 years
	 *
	 * @param string $list_name The name of the select element
	 * @param string $selected_item The pre-selected value
	 */
	static function listYears ($list_name, $selected = FALSE, $start = NULL, $end = NULL, $attr = '') {

		$options = array();
		if (!$selected) {
			$selected = date ('Y');
		}
		$start = $start ? $start : date ('Y');
		$end = $end ? $end : $start + 7;
		$options[] = JHTML::_ ('select.option', 0, JText::_ ('YEAR'));
		for ($i = $start; $i <= $end; $i++) {
			$options[] = JHTML::_ ('select.option', $i, $i);
		}
		return JHTML::_ ('select.genericlist', $options, $list_name, $attr, 'value', 'text', $selected);
	}

	static function checkboxListArr ($arr, $tag_name, $tag_attribs, $key = 'value', $text = 'text', $selected = NULL, $required = 0) {

		reset ($arr);
		$html = array();
		$n = count ($arr);
		for ($i = 0; $i < $n; $i++) {
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			$id = isset($arr[$i]->id) ? $arr[$i]->id : NULL;

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array ($selected)) {
				foreach ($selected as $obj) {
					$k2 = $obj->$key;
					if ($k == $k2) {
						$extra .= " checked=\"checked\"";
						break;
					}
				}
			} else {
				$extra .= ($k == $selected ? " checked=\"checked\"" : '');
			}
			$tmp = "<input type=\"checkbox\" name=\"$tag_name\" id=\"" . str_replace ('[]', '', $tag_name) . "_field$i\" value=\"" . $k . "\"$extra $tag_attribs />" . "<label for=\"" . str_replace ('[]', '', $tag_name) . "_field$i\">";
			$tmp .= JText::_ ($t);
			$tmp .= "</label>";
			$html[] = $tmp;
		}
		return $html;
	}

	function checkboxList ($arr, $tag_name, $tag_attribs, $key = 'value', $text = 'text', $selected = NULL, $required = 0) {

		return "\n\t" . implode ("\n\t", vmCommonHTML::checkboxListArr ($arr, $tag_name, $tag_attribs, $key, $text, $selected, $required)) . "\n";
	}

	function checkboxListTable ($arr, $tag_name, $tag_attribs, $key = 'value', $text = 'text', $selected = NULL, $cols = 0, $rows = 0, $size = 0, $required = 0) {

		$cellsHtml = self::checkboxListArr ($arr, $tag_name, $tag_attribs, $key, $text, $selected, $required);
		return self::list2Table ($cellsHtml, $cols, $rows, $size);
	}

	// private methods:
	private function list2Table ($cellsHtml, $cols, $rows, $size) {

		$cells = count ($cellsHtml);
		if ($size == 0) {
			$localstyle = ""; //" style='width:100%'";
		} else {
			$size = (($size - ($size % 3)) / 3) * 2; // int div  3 * 2 width/heigh ratio
			$localstyle = " style='width:" . $size . "em;'";
		}
		$return = "";
		if ($cells) {
			if ($rows) {
				$return = "\n\t<table class='vmMulti'" . $localstyle . ">";
				$cols = ($cells - ($cells % $rows)) / $rows; // int div
				if ($cells % $rows) {
					$cols++;
				}
				$lineIdx = 0;
				for ($lineIdx = 0; $lineIdx < min ($rows, $cells); $lineIdx++) {
					$return .= "\n\t\t<tr>";
					for ($i = $lineIdx; $i < $cells; $i += $rows) {
						$return .= "<td>" . $cellsHtml[$i] . "</td>";
					}
					$return .= "</tr>\n";
				}
				$return .= "\t</table>\n";
			} else {
				if ($cols) {
					$return = "\n\t<table class='vmMulti'" . $localstyle . ">";
					$idx = 0;
					while ($cells) {
						$return .= "\n\t\t<tr>";
						for ($i = 0, $n = min ($cells, $cols); $i < $n; $i++, $cells--) {
							$return .= "<td>" . $cellsHtml[$idx++] . "</td>";
						}
						$return .= "</tr>\n";
					}
					$return .= "\t</table>\n";
				} else {
					$return = "\n\t" . implode ("\n\t ", $cellsHtml) . "\n";
				}
			}
		}
		return $return;
	}

	/**
	 * Validates an email address by using regular expressions
	 * Does not resolve the domain name!
	 * ATM NOT USED
	 * Joomla has it's own e-mail checker but is no good JMailHelper::isEmailAddress()
	 * maybe in the future it will be better
	 *
	 * @param string $email
	 * @return boolean The result of the validation
	 */
	function validateEmail ($email) {

		$valid = preg_match ('/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}$/', $email);
		return $valid;
	}

	/**
	 * Return $str with all but $display_length at the end as asterisks.
	 *
	 * @author gday
	 *
	 * @access public
	 * @param string $str The string to mask
	 * @param int $display_length The length at the end of the string that is NOT masked
	 * @param boolean $reversed When true, masks the end. Masks from the beginning at default
	 * @return string The string masked by asteriks
	 */
	public function asteriskPad ($str, $display_length, $reversed = FALSE) {

		$total_length = strlen ($str);

		if ($total_length > $display_length) {
			if (!$reversed) {
				for ($i = 0; $i < $total_length - $display_length; $i++) {
					$str[$i] = "*";
				}
			} else {
				for ($i = $total_length - 1; $i >= $total_length - $display_length; $i--) {
					$str[$i] = "*";
				}
			}
		}

		return ($str);
	}

	/**
	 * Return the icon to move an item UP
	 *
	 * @access	public
	 * @param	int		$i The row index
	 * @param	boolean	$condition True to show the icon
	 * @param	string	$task The task to fire
	 * @param	string	$alt The image alternate text string
	 * @return	string	Either the icon to move an item up or a space
	 * @since	1.0
	 */
	function orderUpIcon ($i, $condition = TRUE, $task = 'orderup', $alt = 'COM_VIRTUEMART_MOVE_UP', $enabled = TRUE) {

		$alt = JText::_ ($alt);

		$html = '&nbsp;';
		if ($i > 0) {
			if ($enabled) {
				$html = '<a href="#reorder"  class="orderUp" title="' . $alt . '">';
				$html .= '   <img src="images/uparrow.png" width="16" height="16" border="0" alt="' . $alt . '" />';
				$html	.= '</a>';
			} else {
				$html = '<img src="images/uparrow0.png" width="16" height="16" border="0" alt="' . $alt . '" />';
			}
		}

		return $html;
	}

	/**
	 * Return the icon to move an item DOWN
	 *
	 * @access	public
	 * @param	int		$i The row index
	 * @param	int		$n The number of items in the list
	 * @param	boolean	$condition True to show the icon
	 * @param	string	$task The task to fire
	 * @param	string	$alt The image alternate text string
	 * @return	string	Either the icon to move an item down or a space
	 * @since	1.0
	 */
	function orderDownIcon ($i, $n, $condition = TRUE, $task = 'orderdown', $alt = 'Move Down', $enabled = TRUE) {

		$alt = JText::_ ($alt);

		$html = '&nbsp;';
		if ($i < $n - 1) {
			if ($enabled) {
				$html = '<a href="#reorder" class="orderDown" title="' . $alt . '">';
				$html .= '  <img src="images/downarrow.png" width="16" height="16" border="0" alt="' . $alt . '" />';
				$html	.= '</a>';
			} else {
				$html = '<img src="images/downarrow0.png" width="16" height="16" border="0" alt="' . $alt . '" />';
			}
		}

		return $html;
	}

	static function getValidProductFilterArray () {

		static $filterArray;

		if (!isset($filterArray)) {
		/*
		$filterArray = array('p.virtuemart_product_id', 'p.product_sku','pp.product_price','c.category_name','c.category_description',
		'm.mf_name', 'l.product_s_desc', 'p.product_desc', 'p.product_weight', 'p.product_weight_uom', 'p.product_length', 'p.product_width',
		'p.product_height', 'p.product_lwh_uom', 'p.product_in_stock', 'p.low_stock_notification', 'p.product_available_date',
		'p.product_availability', 'p.product_special', 'p.created_on', 'p.modified_on', 'l.product_name', 'p.product_sales',
		'p.product_unit', 'p.product_packaging', 'p.intnotes', 'l.metadesc', 'l.metakey', 'p.metarobot', 'p.metaauthor');
		}
        */
		$filterArray = array('product_name', '`p`.created_on', '`p`.product_sku',
			'product_s_desc', 'product_desc',
				'category_name', 'category_description', 'mf_name',
			'product_price', 'product_special', 'product_sales', 'product_availability', '`p`.product_available_date',
			'product_height', 'product_width', 'product_length', 'product_lwh_uom',
			'product_weight', 'product_weight_uom', 'product_in_stock', 'low_stock_notification',
			 '`p`.modified_on',
				'product_unit', 'product_packaging', '`p`.virtuemart_product_id', 'pc.ordering');
		//other possible fields
		//'p.intnotes',		this is maybe interesting, but then only for admins or special shoppergroups

		// this fields leads to trouble, because we have this fields in product, category and manufacturer,
		// they are anyway making not a lot sense for orderby or search.
		//'l.metadesc', 'l.metakey', 'l.metarobot', 'l.metaauthor'
		}

		return $filterArray;
	}

	/**
	 * Returns developer information for a plugin
	 * Returns a 2 link with background image, should look like a button to open contact page or manual
	 *
	 * @static
	 * @param $title string Title of the plugin
	 * @param $intro string Intro text
	 * @param $logolink url Url to logo images, use here the path and then as image names contact.png and manual.png
	 * @param $developer string Name of the developer/company
	 * @param $contactlink url Url to the contact form of the developer for support
	 * @param $manlink url URL to the manual for this specific plugin
	 * @return string
	 */
	static function display3rdInfo($title,$intro,$developer,$logolink,$contactlink,$manlink,$width='96px',$height='66px',$linesHeight='33px'){

		$html = $intro;

		$html .= self::displayLinkButton(JText::sprintf('COM_VIRTUEMART_THRD_PARTY_CONTACT',$developer),$contactlink, $logolink.'/contact.png',$width,$height,$linesHeight);
		$html .='<br />';
		$html .= self::displayLinkButton(JText::sprintf('COM_VIRTUEMART_THRD_PARTY_MANUAL',$title),$manlink, $logolink.'/manual.png',$width,$height,$linesHeight);

		return $html;
	}


	static function displayLinkButton($title, $link, $bgrndImage,$width,$height,$linesHeight,$additionalStyles=''){

		//$lineHeight = ((int)$height)/$lines;
		//vmdebug('displayLinkButton '.$height.' '.$lineHeight);
		$html = '<div style="line-height:'.$linesHeight.';background-image:url('.$bgrndImage.');width:'.$width.';height:'.$height.';'.$additionalStyles.'">'
				.'<a  title="'.$title.'" href="'.$link.'" target="_blank" >'.$title .'</a></div>';

		return $html;
	}

	static $tested = False;
	static function checkSafePath($safePath=0){


		if($safePath==0) {
			$safePath = VmConfig::get('forSale_path',0);
			if(self::$tested) return $safePath;
		}

		$warn = FALSE;
		$uri = JFactory::getURI();
		$configlink = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=config';

		if(empty($safePath)){
			$warn = 'COM_VIRTUEMART_WARN_NO_SAFE_PATH_SET';
		} else {
			$exists = JFolder::exists($safePath);
			if(!$exists){
				$warn = 'COM_VIRTUEMART_WARN_SAFE_PATH_WRONG';
			} else{
				if(!is_writable( $safePath )){
					VmWarn('COM_VIRTUEMART_WARN_SAFE_PATH_NOT_WRITEABLE',JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_FORSALE_PATH'),$safePath,$configlink);
				} else {
					if(!is_writable( $safePath.'invoices' )){
						VmWarn('COM_VIRTUEMART_WARN_SAFE_PATH_INV_NOT_WRITEABLE',JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_FORSALE_PATH'),$safePath,$configlink);
					}
				}
			}
		}

		if($warn){
			$suggestedPath=shopFunctions::getSuggestedSafePath();

			VmWarn($warn,JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_FORSALE_PATH'),$suggestedPath,$configlink);
			return FALSE;
		}

		return $safePath;
	}

	/*
	 * Returns the suggested safe Path, used to store the invoices
	 * @static
	 * @return string: suggested safe path
	 */
	static public function getSuggestedSafePath() {
		$lastIndex= strrpos(JPATH_ROOT,DS);
		return substr(JPATH_ROOT,0,$lastIndex).DS.'vmfiles';
	}
	/*
	 * @author Valerie Isaksen
	 */
	static public function renderProductShopperList ($productShoppers) {

		$html = '';
		$i=0;
		if(empty($productShoppers)) return '';
		foreach ($productShoppers as $email => $productShopper) {
			$html .= '<tr  class="customer row'.$i.'" data-cid="' . $productShopper['email'] . '">
			<td rowspan ="'.$productShopper['nb_orders'] .'">' . $productShopper['name'] . '</td>
			<td rowspan ="'.$productShopper['nb_orders'] .'><a class="mailto" href="' . $productShopper['mail_to'] . '"><span class="mail">' . $productShopper['email'] . '</span></a></td>
			<td rowspan ="'.$productShopper['nb_orders'] .'class="shopper_phone">' . $productShopper['phone'] . '</td>';
            $first=TRUE;
			foreach ($productShopper['order_info'] as $order_info) {
				if (!$first)
				$html .= '<tr class="row'.$i.'">';
			$html .= '<td class="quantity">';
			$html .= $order_info['quantity'];
			$html .= '</td>';
			$html .= '<td class="order_status">';
			$html .= $order_info['order_item_status_name'];
			$html .= '</td>
			<td class="order_number">';
				$link = 'index.php?option=com_virtuemart&view=orders&task=edit&virtuemart_order_id=' . $order_info['order_id'];
				$html .= JHTML::_ ('link', JRoute::_ ($link), $order_info['order_number'], array('title' => JText::_ ('COM_VIRTUEMART_ORDER_EDIT_ORDER_NUMBER') . ' ' . $order_info['order_number']));
			$first=FALSE;
			$html .= '
					</td>
				</tr>
				';
			}
			$i = 1 - $i;
		}
		if (empty($html)) {
			$html = '
				<tr class="customer">
					<td colspan="4">
						' . JText::_ ('COM_VIRTUEMART_NO_SEARCH_RESULT') . '
					</td>
				</tr>
				';
		}

		return $html;
	}
}

//pure php no tag
