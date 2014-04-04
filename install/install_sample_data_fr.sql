-- VirtueMart table data SQL script
-- This will insert all required data into the VirtueMart tables


--
--  Dumping data for `#__virtuemart_calcs`
--

INSERT IGNORE INTO `#__virtuemart_calcs` (`virtuemart_calc_id`, `virtuemart_vendor_id`, `calc_name`, `calc_descr`, `calc_kind`, `calc_value_mathop`, `calc_value`, `calc_currency`, `ordering`, `calc_shopper_published`, `calc_vendor_published`, `publish_up`, `publish_down`, `created_on`, `modified_on`, `published`, `shared`) VALUES
(1, 1, 'Tax 19.6%', 'A simple tax for all products regardless the category', 'VatTax', '+%', 19.60, '47', 0, 1, 1, '2010-02-21 00:00:00', NULL, NULL, NULL,  1, 1),
(2, 1, 'Discount for all Hand Tools', 'Discount for all Hand Tools 2 euro', 'DATax', '-', 2, '47', 1, 1, 1, '2010-02-21 00:00:00', NULL, NULL, NULL, 1, 0),
(3, 1, 'Duty for Powertools', 'Ah tax that only effects a certain category, Power Tools, and Shoppergroup', 'Tax', '+%', 20, '47', 0, 1, 1, '2010-02-21 00:00:00', NULL, NULL, NULL, 1, 0),
(4, 1, 'Tax 5.5%', 'Reduced VAT rates for all products regardless the category', 'VatTax', '+%', 5.50, '47', 0, 1, 1, '2010-02-21 00:00:00', NULL, NULL, NULL,  1, 1),
(5, 1, 'Tax 7.0%', 'A simple tax for all products regardless the category', 'VatTax', '+%', 7.0, '47', 0, 1, 1, '2010-02-21 00:00:00', NULL, NULL, NULL,  1, 1);


--
-- Dumping data for table `#__virtuemart_calc_categories`
--
INSERT INTO `#__virtuemart_calc_categories` (`id`, `virtuemart_calc_id`, `virtuemart_category_id`) VALUES
(1, 3, 2),
(2, 2, 1);

--
-- Dumping data for table `#__virtuemart_calc_shoppergroups`
--

INSERT IGNORE INTO `#__virtuemart_calc_shoppergroups` (`id`, `virtuemart_calc_id`, `virtuemart_shoppergroup_id`) VALUES
(NULL, 2, 2);


--
-- Dumping data for table `#__virtuemart_categories`
--

INSERT INTO `#__virtuemart_categories` (`virtuemart_category_id`, `virtuemart_vendor_id`,`published`, `created_on`, `modified_on`, `category_template`, `category_layout`, `category_product_layout`, `products_per_row`, `ordering`, `limit_list_start`, `limit_list_step`, `limit_list_max`, `limit_list_initial`, `metarobot`, `metaauthor`) VALUES
(1, 1, 1, NULL, NULL, '0', 'default', 'default', 3, 1, 0, 10, 0, 10, '', ''),
(2, 1, 1, NULL, NULL, '', '', '', 4, 2, NULL, NULL, NULL, NULL, '', ''),
(3, 1, 1, NULL, NULL, '', '', '', 2, 3, NULL, NULL, NULL, NULL, '', ''),
(4, 1, 1, NULL, NULL, '', '', '', 1, 4, NULL, NULL, NULL, NULL, '', ''),
(5, 1, 1, NULL, NULL, '', '', '', 1, 5, NULL, NULL, NULL, NULL, '', '');

-- --------------------------------------------------------

--
-- Structure de la table `#__virtuemart_categories_en_gb`
--

CREATE TABLE IF NOT EXISTS `#__virtuemart_categories_en_gb` (
  `virtuemart_category_id` int(1) unsigned NOT NULL,
  `category_name` char(180) NOT NULL DEFAULT '',
  `category_description` varchar(19000) NOT NULL DEFAULT '',
  `metadesc` varchar(400) NOT NULL DEFAULT '',
  `metakey` varchar(400) NOT NULL DEFAULT '',
  `customtitle` char(255) NOT NULL DEFAULT '',
  `slug` char(192) NOT NULL DEFAULT '',
  PRIMARY KEY (`virtuemart_category_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__virtuemart_categories_en_gb` (`virtuemart_category_id`, `category_name`, `category_description`, `metadesc`, `metakey`, `slug`) VALUES
(1, 'Hand Tools', 'Hand Tools', '', '', 'handtools'),
(2, 'Power Tools', 'Power Tools', '', '', 'powertools'),
(3, 'Garden Tools', 'Garden Tools', '', '', 'gardentools'),
(4, 'Outdoor Tools', 'Outdoor Tools', '', '', 'outdoortools'),
(5, 'Indoor Tools', 'Indoor Tools', '', '', 'indoortools');

-- --------------------------------------------------------

--
-- Structure de la table `#__virtuemart_categories_fr_fr`
--

CREATE TABLE IF NOT EXISTS `#__virtuemart_categories_fr_fr` (
  `virtuemart_category_id` int(1) unsigned NOT NULL,
  `category_name` char(180) NOT NULL DEFAULT '',
  `category_description` varchar(19000) NOT NULL DEFAULT '',
  `metadesc` varchar(400) NOT NULL DEFAULT '',
  `metakey` varchar(400) NOT NULL DEFAULT '',
  `customtitle` char(255) NOT NULL DEFAULT '',
  `slug` char(192) NOT NULL DEFAULT '',
  PRIMARY KEY (`virtuemart_category_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__virtuemart_categories_fr_fr` (`virtuemart_category_id`, `category_name`, `category_description`, `metadesc`, `metakey`, `slug`) VALUES
(1, 'Outillages manuel', 'Outillage manuel', '', '', 'outil-manuel'),
(2, 'Outillages électriques', 'Outillages électriques', '', '', 'outillage-electrique'),
(3, 'Outils de jardinage', 'Outils de jardinage', '', '', 'outillage-jardin'),
(4, 'Outils pour extérieur', 'Outillages à utiliser en extérieur', '', '', 'outillage-exterieur'),
(5, 'Outils pour la maison', 'Outils pour la maison', '', '', 'outillage-interieur');

--
-- Dumping data for table `#__virtuemart_category_categories`
--

INSERT IGNORE INTO `#__virtuemart_category_categories` (`category_parent_id`, `category_child_id`) VALUES
( 0, 1),
( 0, 2),
( 0, 3),
( 2, 4),
( 2, 5);

--
-- Dumping data for table `#__virtuemart_category_medias`
--

INSERT IGNORE INTO `#__virtuemart_category_medias` (`id`,`virtuemart_category_id`, `virtuemart_media_id`) VALUES
(NULL, 1, 8),
(NULL, 2, 11),
(NULL, 3, 7),
(NULL, 4, 10),
(NULL, 5, 9);

--
-- Dumping data for table `#__virtuemart_customs`
--
INSERT INTO `#__virtuemart_customs` (`virtuemart_custom_id`, `custom_parent_id`, `virtuemart_vendor_id`, `custom_jplugin_id`, `custom_element`, `admin_only`, `custom_title`, `custom_tip`, `custom_value`, `custom_field_desc`, `field_type`, `is_list`, `is_hidden`, `is_cart_attribute`, `layout_pos`, `custom_params`, `shared`, `published`, `created_on`, `created_by`, `ordering`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
(3, 11, 1, 0, '0', 0, 'Handle length (cm)', '', '100', '', 'I', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 0, 0, '2012-10-26 10:14:35', 627, '0000-00-00 00:00:00', 0),
(4, 11, 1, 0, '0', 0, 'Replaceable Head', '', '0', '', 'B', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 0, 0, '2012-10-26 10:14:41', 627, '0000-00-00 00:00:00', 0),
(7, 0, 1, 0, '', 0, 'Photo', 'Give a media ID as defaut', '1', 'Add a photo', 'M', 0, 0, 0, NULL, NULL, 0, 1, '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
(9, 0, 1, 0, '0', 0, 'Chain size', 'Select the chain size', '30', '(cm)', 'V', 0, 0, 1, '', '0', 0, 1, '0000-00-00 00:00:00', 0, 0, '2012-10-26 10:21:07', 627, '0000-00-00 00:00:00', 0),
(11, 0, 1, 0, '0', 0, 'Hammer Specifications', '', '', '', 'P', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 0, 0, '2012-10-26 10:12:15', 627, '0000-00-00 00:00:00', 0),
(12, 11, 1, 0, '0', 0, 'Manufacturer Warranty', '', 'Lifetime against manufacturers defect', '', 'S', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 0, 0, '2012-10-26 10:13:48', 627, '0000-00-00 00:00:00', 0),
(13, 0, 1, 0, '', 0, 'Color', '', 'Choose a color', 'Be important on your construction site, buy a red one', 'S', 0, 0, 1, NULL, NULL, 0, 1, '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
(17, 0, 1, 0, '0', 0, 'Diameter', 'Select the Diameter', '', '', 'V', 0, 0, 1, '', '0', 0, 1, '0000-00-00 00:00:00', 0, 0, '2012-10-26 12:26:20', 627, '0000-00-00 00:00:00', 0),
(15, 0, 1, 0, '0', 0, 'Select the Hand Shovel type', '', '', '', 'A', 0, 0, 0, 'ontop', '0', 0, 1, '0000-00-00 00:00:00', 0, 0, '2012-10-26 10:45:53', 627, '0000-00-00 00:00:00', 0),
(16, 11, 1, 0, '0', 0, 'Handle color', '', 'Blue;Pink;Gold;Platine', '', 'S', 1, 0, 0, '', '0', 0, 1, '2012-10-26 10:16:24', 627, 0, '2012-10-26 10:16:24', 627, '0000-00-00 00:00:00', 0),
(18, 0, 1, 0, '0', 0, 'Ladder Specifications', '', '', '', 'P', 0, 0, 0, '', '0', 0, 1, '2012-10-26 12:27:52', 627, 0, '2012-10-26 12:27:52', 627, '0000-00-00 00:00:00', 0),
(19, 18, 1, 0, '0', 0, 'Height', '', '2,60', '', 'I', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 0, 0, '2012-10-26 12:38:57', 627, '0000-00-00 00:00:00', 0),
(20, 18, 1, 0, '0', 0, 'Type of ladder', '', 'Extensible', '', 'S', 0, 0, 0, '', '0', 0, 1, '0000-00-00 00:00:00', 0, 0, '2012-10-26 12:29:48', 627, '0000-00-00 00:00:00', 0)
;

--
-- Dumping data for table  `#__virtuemart_product_customfields`
--

INSERT INTO `#__virtuemart_product_customfields` (`virtuemart_customfield_id`, `virtuemart_product_id`, `virtuemart_custom_id`, `custom_value`, `custom_price`, `custom_param`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`, `ordering`) VALUES
(24, 5, 2, '5', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 10:25:23', 627, '0000-00-00 00:00:00', 0, 0),
(22, 5, 1, '7', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 10:25:23', 627, '0000-00-00 00:00:00', 0, 0),
(23, 5, 2, '2', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 10:25:23', 627, '0000-00-00 00:00:00', 0, 0),
(45, 15, 18, '', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:40:49', 627, '0000-00-00 00:00:00', 0, 0),
(46, 15, 19, '1,50', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:40:49', 627, '0000-00-00 00:00:00', 0, 1),
(47, 15, 20, 'Step Ladder', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:40:49', 627, '0000-00-00 00:00:00', 0, 2),
(41, 2, 20, 'Extensible', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:34:43', 627, '0000-00-00 00:00:00', 0, 2),
(40, 2, 19, '2,10', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:34:43', 627, '0000-00-00 00:00:00', 0, 1),
(42, 14, 18, '', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:39:17', 627, '0000-00-00 00:00:00', 0, 0),
(43, 14, 19, '2,60', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:39:17', 627, '0000-00-00 00:00:00', 0, 1),
(12, 1, 15, 'product_sku', 0.00000, 'withParent="1"|parentOrderable="0"|', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:43:38', 627, '0000-00-00 00:00:00', 0, 0),
(13, 7, 9, '30', 0.00000, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 10:19:12', 627, '0000-00-00 00:00:00', 0, 0),
(14, 7, 9, '40', 15.00000, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 10:19:12', 627, '0000-00-00 00:00:00', 0, 0),
(15, 7, 9, '50', 35.00000, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 10:19:12', 627, '0000-00-00 00:00:00', 0, 0),
(16, 6, 11, '', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:43:57', 627, '0000-00-00 00:00:00', 0, 0),
(17, 6, 3, '100', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:43:57', 627, '0000-00-00 00:00:00', 0, 1),
(18, 6, 4, '0', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:43:57', 627, '0000-00-00 00:00:00', 0, 2),
(19, 6, 12, 'Lifetime against manufacturers defect', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:43:57', 627, '0000-00-00 00:00:00', 0, 3),
(20, 6, 16, 'Pink', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:43:57', 627, '0000-00-00 00:00:00', 0, 4),
(39, 2, 18, '', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:34:43', 627, '0000-00-00 00:00:00', 0, 0),
(35, 8, 17, '15', 5.00000, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:27:05', 627, '0000-00-00 00:00:00', 0, 1),
(34, 8, 17, '10', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:27:05', 627, '0000-00-00 00:00:00', 0, 0),
(36, 8, 17, '20', 10.00000, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:27:05', 627, '0000-00-00 00:00:00', 0, 2),
(44, 14, 20, 'Extensible', NULL, '', 0, '0000-00-00 00:00:00', 0, '2012-10-26 12:39:17', 627, '0000-00-00 00:00:00', 0, 2);

--
-- Dumping data for table `#__virtuemart_manufacturers`
--

INSERT INTO `#__virtuemart_manufacturers` (`virtuemart_manufacturer_id`, `virtuemart_manufacturercategories_id`, `published`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `#__virtuemart_manufacturers_en_gb`
--

CREATE TABLE IF NOT EXISTS `#__virtuemart_manufacturers_en_gb` (
  `virtuemart_manufacturer_id` int(1) unsigned NOT NULL,
  `mf_name` char(180) NOT NULL DEFAULT '',
  `mf_email` char(255) NOT NULL DEFAULT '',
  `mf_desc` varchar(19000) NOT NULL DEFAULT '',
  `mf_url` char(255) NOT NULL DEFAULT '',
  `slug` char(192) NOT NULL DEFAULT '',
  PRIMARY KEY (`virtuemart_manufacturer_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `#__virtuemart_manufacturers_en_gb` (`virtuemart_manufacturer_id`, `mf_name`, `mf_email`, `mf_desc`, `mf_url`, `slug`) VALUES
	(1, 'Manufacturer', ' manufacturer@example.org', 'An example for a manufacturer', 'http://www.example.org', 'manufacturer-example');

-- --------------------------------------------------------

--
-- Structure de la table `#__virtuemart_manufacturers_fr_fr`
--

CREATE TABLE IF NOT EXISTS `#__virtuemart_manufacturers_fr_fr` (
  `virtuemart_manufacturer_id` int(1) unsigned NOT NULL,
  `mf_name` char(180) NOT NULL DEFAULT '',
  `mf_email` char(255) NOT NULL DEFAULT '',
  `mf_desc` varchar(19000) NOT NULL DEFAULT '',
  `mf_url` char(255) NOT NULL DEFAULT '',
  `slug` char(192) NOT NULL DEFAULT '',
  PRIMARY KEY (`virtuemart_manufacturer_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__virtuemart_manufacturers_fr_fr` (`virtuemart_manufacturer_id`, `mf_name`, `mf_email`, `mf_desc`, `mf_url`, `slug`) VALUES
	(1, 'fabricant', ' fabircant@example.org', 'Un exemple de fabricant', 'http://www.example.fr', 'fabricant-exemple');


--
-- Dumping data for table `#__virtuemart_manufacturercategories`
--

INSERT INTO `#__virtuemart_manufacturercategories` (`virtuemart_manufacturercategories_id`, `published`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `#__virtuemart_manufacturercategories_en_gb`
--

CREATE TABLE IF NOT EXISTS `#__virtuemart_manufacturercategories_en_gb` (
  `virtuemart_manufacturercategories_id` int(1) unsigned NOT NULL,
  `mf_category_name` char(180) NOT NULL DEFAULT '',
  `mf_category_desc` varchar(19000) NOT NULL DEFAULT '',
  `slug` char(192) NOT NULL DEFAULT '',
  PRIMARY KEY (`virtuemart_manufacturercategories_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__virtuemart_manufacturercategories_en_gb` (`virtuemart_manufacturercategories_id`, `mf_category_name`, `mf_category_desc`, `slug`) VALUES
	(1, '-default-', 'This is the default manufacturer category', '-default-');

-- --------------------------------------------------------

--
-- Structure de la table `#__virtuemart_manufacturercategories_fr_fr`
--

CREATE TABLE IF NOT EXISTS `#__virtuemart_manufacturercategories_fr_fr` (
  `virtuemart_manufacturercategories_id` int(1) unsigned NOT NULL,
  `mf_category_name` char(180) NOT NULL DEFAULT '',
  `mf_category_desc` varchar(19000) NOT NULL DEFAULT '',
  `slug` char(192) NOT NULL DEFAULT '',
  PRIMARY KEY (`virtuemart_manufacturercategories_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__virtuemart_manufacturercategories_fr_fr` (`virtuemart_manufacturercategories_id`, `mf_category_name`, `mf_category_desc`, `slug`) VALUES
	(1, '-défault-', 'Catégories de fabircant par défault', '-default-');

--
-- Dumping data for table `#__virtuemart_manufacturer_medias`
--

INSERT IGNORE INTO `#__virtuemart_manufacturer_medias` (`id`,`virtuemart_manufacturer_id`, `virtuemart_media_id`) VALUES
(NULL, 1, 14);

--
-- Dumping data for table `#__virtuemart_medias`
--
INSERT INTO `#__virtuemart_medias` (`virtuemart_media_id`, `virtuemart_vendor_id`, `file_title`, `file_description`, `file_meta`, `file_mimetype`, `file_type`, `file_url`, `file_url_thumb`, `file_is_product_image`, `file_is_downloadable`, `file_is_forSale`, `file_params`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
(1, 1, 'hand_saw.jpg', '', 'hand saw', 'image/jpeg', 'product', 'images/stories/virtuemart/product/hand_saw.jpg', 'images/stories/virtuemart/product/resized/hand_saw_90x90.jpg', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '2012-10-26 10:25:23', 627, '0000-00-00 00:00:00', 0),
(2, 1, 'hand_shovel.jpg', '', 'hand shovel', 'image/jpeg', 'product', 'images/stories/virtuemart/product/hand_shovel.jpg', 'images/stories/virtuemart/product/resized/hand_shovel_90x90.jpg', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '2012-10-26 10:42:44', 627, '0000-00-00 00:00:00', 0),
(3, 1, 'ladder.jpg', '', 'ladder', 'image/jpeg', 'product', 'images/stories/virtuemart/product/ladder.jpg', 'images/stories/virtuemart/product/resized/ladder_90x90.jpg', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
(4, 1, 'power_drill.jpg', '', 'power drill', 'image/jpeg', 'product', 'images/stories/virtuemart/product/power_drill.jpg', 'images/stories/virtuemart/product/resized/power_drill_90x90.jpg', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
(5, 1, 'power_sander.jpg', '', 'power sander', 'image/jpeg', 'product', 'images/stories/virtuemart/product/power_sander.jpg', 'images/stories/virtuemart/product/resized/power_sander_90x90.jpg', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
(6, 1, 'shovel.jpg', '', 'shovel', 'image/jpeg', 'product', 'images/stories/virtuemart/product/shovel.jpg', 'images/stories/virtuemart/product/resized/shovel_90x90.jpg', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
(7, 1, 'garden_tools.jpg', '', 'garden tools', 'image/jpeg', 'category', 'images/stories/virtuemart/category/garden_tools.jpg', 'images/stories/virtuemart/category/resized/garden_tools_90x90.jpg', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
(8, 1, 'hand_tools.jpg', '', 'hand tools', 'image/jpeg', 'category', 'images/stories/virtuemart/category/hand_tools.jpg', 'images/stories/virtuemart/category/resized/hand_tools_90x90.jpg', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
(9, 1, 'power_indoor_tool.jpg', '', 'power indoor tool', 'image/jpeg', 'category', 'images/stories/virtuemart/category/power_indoor_tool.jpg', 'images/stories/virtuemart/category/resized/power_indoor_tool_90x90.jpg', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
(10, 1, 'power_outdoor_tool.jpg', '', 'Power outdoor tool', 'image/jpeg', 'category', 'images/stories/virtuemart/category/power_outdoor_tool.jpg', 'images/stories/virtuemart/category/resized/power_outdoor_tool_90x90.jpg', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
(11, 1, 'power_tools.jpg', '', 'power tools', 'image/jpeg', 'category', 'images/stories/virtuemart/category/power_tools.jpg', 'images/stories/virtuemart/category/resized/power_tools_90x90.jpg', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '2012-10-26 10:24:06', 627, '0000-00-00 00:00:00', 0),
(12, 1, 'manufacturersample.jpg', '', 'manufacturer sample', 'image/jpeg', 'manufacturer', 'images/stories/virtuemart/manufacturer/manufacturersample.jpg', 'images/stories/virtuemart/manufacturer/resized/manufacturersample_90x90.jpg', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
(13, 1, 'washupito.gif', '', 'washupito', 'image/gif', 'vendor', 'images/stories/virtuemart/vendor/washupito.gif', 'images/stories/virtuemart/vendor/resized/washupito_90x90.gif', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
(14, 1, 'hammer.jpg', '', 'hammer', 'image/jpeg', 'product', 'images/stories/virtuemart/product/hammer.jpg', 'images/stories/virtuemart/product/resized/hammer_90x90.jpg', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '2012-10-26 10:16:59', 627, '0000-00-00 00:00:00', 0),
(15, 1, 'chain_saw.jpg', '', 'chain saw', 'image/jpeg', 'product', 'images/stories/virtuemart/product/chain_saw.jpg', 'images/stories/virtuemart/product/resized/chain_saw_90x90.jpg', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '2012-10-26 10:19:12', 627, '0000-00-00 00:00:00', 0),
(16, 1, 'circular_saw.jpg', '', 'circular saw', 'image/jpeg', 'product', 'images/stories/virtuemart/product/circular_saw.jpg', 'images/stories/virtuemart/product/resized/circular_saw_90x90.jpg', 0, 0, 0, '', 0, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0);




INSERT INTO `#__virtuemart_products` (`virtuemart_product_id`, `virtuemart_vendor_id`, `product_parent_id`, `product_sku`, `product_weight`, `product_weight_uom`, `product_length`, `product_width`, `product_height`, `product_lwh_uom`, `product_url`, `product_in_stock`, `product_ordered`, `low_stock_notification`, `product_available_date`, `product_availability`, `product_special`, `product_sales`, `product_unit`, `product_packaging`, `product_params`, `hits`, `intnotes`, `metarobot`, `metaauthor`, `layout`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
(1, 1, 0, 'G01', 1.2000, 'KG', 0.0000, 0.0000, 0.0000, 'M', '', 10, 0, 5, '2010-02-21 00:00:00', '48h.gif', 0, 0, 'KG', 0.0000, 'min_order_level=""|max_order_level=""|product_box=""|', 0, '', '', '', '0', 1, '2012-10-26 10:36:01', 0, '2012-10-28 17:55:17', 627, '0000-00-00 00:00:00', 0),
(2, 1, 0, 'G02', 10.0000, 'KG', 0.0000, 0.0000, 0.0000, 'M', '', 76, 0, 5, '2010-02-21 00:00:00', '3-5d.gif', 0, 0, 'KG', 0.0000, 'min_order_level=""|max_order_level=""|product_box=""|', 0, '', '', '', '0', 0, '2012-10-25 10:36:01', 0, '2012-10-28 17:52:31', 627, '0000-00-00 00:00:00', 0),
(3, 1, 0, 'G03', 0.0000, 'KG', 0.0000, 0.0000, 0.0000, 'M', '', 32, 0, 5, '2010-02-21 00:00:00', '7d.gif', 0, 0, 'KG', 0.0000, 'min_order_level=""|max_order_level=""|product_box=""|', 0, '', '', '', '0', 1, '2012-10-23 10:36:01', 0, '2012-10-28 17:56:54', 627, '0000-00-00 00:00:00', 0),
(4, 1, 0, 'G04', 10.0000, 'KG', 0.0000, 0.0000, 0.0000, 'M', '', 98, 0, 5, '2010-02-21 00:00:00', 'on-order.gif', 0, 0, 'KG', 0.0000, 'min_order_level=""|max_order_level=""|product_box=""|', 0, '', '', '', '0', 1, '2012-10-25 10:36:01', 0, '2012-10-28 17:55:32', 627, '0000-00-00 00:00:00', 0),
(5, 1, 0, 'H01', 10.0000, 'KG', 0.0000, 0.0000, 0.0000, 'M', '', 32, 0, 5, '2010-02-21 00:00:00', '1-4w.gif', 1, 0, 'KG', 0.0000, 'min_order_level=""|max_order_level=""|product_box=""|', 0, '', '', '', '0', 1, '2012-10-22 10:36:01', 0, '2012-10-28 17:51:47', 627, '0000-00-00 00:00:00', 0),
(6, 1, 0, 'H02', 0.9000, 'KG', 0.0000, 0.0000, 0.0000, 'M', '', 500, 0, 5, '2011-12-21 00:00:00', '24h.gif', 1, 0, 'KG', 0.0000, 'min_order_level=""|max_order_level=""|product_box=""|', 0, '', '', '', '0', 1, '2012-10-26 10:36:01', 0, '2012-10-28 17:52:17', 627, '0000-00-00 00:00:00', 0),
(7, 1, 0, 'P01', 10.0000, 'KG', 0.0000, 0.0000, 0.0000, 'M', '', 45, 0, 5, '2011-12-21 00:00:00', '48h.gif', 1, 0, 'KG', 0.0000, 'min_order_level=""|max_order_level=""|product_box=""|', 0, '', '', '', '0', 1, '2012-10-22 10:36:01', 0, '2012-10-28 17:53:13', 627, '0000-00-00 00:00:00', 0),
(8, 1, 0, 'P02', 10.0000, 'KG', 0.0000, 0.0000, 0.0000, 'M', '', 33, 0, 5, '2010-12-21 00:00:00', '3-5d.gif', 0, 0, 'KG', 0.0000, 'min_order_level=""|max_order_level=""|product_box=""|', 0, '', '', '', '0', 1, '2012-10-23 10:36:01', 0, '2012-10-28 17:53:51', 627, '0000-00-00 00:00:00', 0),
(9, 1, 0, 'P03', 10.0000, 'KG', 0.0000, 0.0000, 0.0000, 'M', '', 3, 0, 5, '2011-07-21 00:00:00', '2-3d.gif', 0, 0, 'KG', 0.0000, 'min_order_level=""|max_order_level=""|product_box=""|', 0, '', '', '', '0', 1, '2012-10-24 10:36:01', 0, '2012-10-28 17:53:00', 627, '0000-00-00 00:00:00', 0),
(10, 1, 0, 'P04', 10.0000, 'KG', 0.0000, 0.0000, 0.0000, 'M', '', 2, 0, 5, '2010-12-21 00:00:00', '1-2m.gif', 0, 0, 'KG', 0.0000, 'min_order_level=""|max_order_level=""|product_box=""|', 0, '', '', '', '0', 1, '0000-00-00 00:00:00', 0, '2012-10-28 17:55:56', 627, '0000-00-00 00:00:00', 0),
(11, 1, 1, 'G01-01', 10.0000, 'KG', 0.0000, 0.0000, 0.0000, 'M', '', 0, 0, 5, '0000-00-00 00:00:00', '', 0, 0, '', 0.0000, 'min_order_level=null|max_order_level=null|product_box=null|', 0, '', '', '', '', 1, '2012-10-22 10:36:01', 0, '2012-10-28 17:55:17', 627, '0000-00-00 00:00:00', 0),
(12, 1, 1, 'G01-02', 10.0000, '', 0.0000, 0.0000, 0.0000, '', '', 0, 0, 5, '0000-00-00 00:00:00', '', 0, 0, '', 0.0000, 'min_order_level=null|max_order_level=null|product_box=null|', 0, '', '', '', '', 1, '2012-10-26 09:36:01', 0, '2012-10-28 17:55:17', 627, '0000-00-00 00:00:00', 0),
(13, 1, 1, 'G01-03', 10.0000, '', 0.0000, 0.0000, 0.0000, '', '', 0, 0, 5, '0000-00-00 00:00:00', '', 0, 0, '', 0.0000, 'min_order_level=null|max_order_level=null|product_box=null|', 0, '', '', '', '', 1, '2012-10-25 10:36:01', 0, '2012-10-28 17:55:17', 627, '0000-00-00 00:00:00', 0),
(14, 1, 2, 'L01', 10.0000, 'KG', 0.0000, 0.0000, 0.0000, 'M', '', 22, 0, 5, '2011-12-21 00:00:00', '', 0, 0, 'KG', 0.0000, 'min_order_level=""|max_order_level=""|product_box=""|', 0, '', '', '', '0', 0, '2012-10-25 10:36:01', 0, '2012-10-28 17:52:31', 627, '0000-00-00 00:00:00', 0),
(15, 1, 2, 'L02', 10.0000, 'KG', 0.0000, 0.0000, 0.0000, 'M', '', 0, 0, 5, '0000-00-00 00:00:00', '', 0, 0, 'KG', 0.0000, 'min_order_level=""|max_order_level=""|product_box=""|', 0, '', '', '', '0', 0, '2012-10-25 10:36:01', 0, '2012-10-28 17:52:31', 627, '0000-00-00 00:00:00', 0),
(16, 1, 2, 'L03', 10.0000, 'KG', 0.0000, 0.0000, 0.0000, 'M', '', 0, 0, 5, '0000-00-00 00:00:00', '', 0, 0, '', 0.0000, 'min_order_level=null|max_order_level=null|product_box=null|', 0, '', '', '', '', 0, '2012-10-25 10:36:01', 0, '2012-10-28 17:52:31', 627, '0000-00-00 00:00:00', 0);

-- --------------------------------------------------------

--
-- Structure de la table `#__virtuemart_products_en_gb`
--

CREATE TABLE IF NOT EXISTS `#__virtuemart_products_en_gb` (
  `virtuemart_product_id` int(1) unsigned NOT NULL,
  `product_s_desc` varchar(2000) NOT NULL DEFAULT '',
  `product_desc` varchar(18400) NOT NULL DEFAULT '',
  `product_name` char(180) NOT NULL DEFAULT '',
  `metadesc` varchar(400) NOT NULL DEFAULT '',
  `metakey` varchar(400) NOT NULL DEFAULT '',
  `customtitle` char(255) NOT NULL DEFAULT '',
  `slug` char(192) NOT NULL DEFAULT '',
  PRIMARY KEY (`virtuemart_product_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__virtuemart_products_en_gb` (`virtuemart_product_id`,`product_name`, `product_s_desc`, `product_desc`,  `metadesc`, `metakey`, `customtitle`, `slug`) VALUES
(1, 'Hand Shovel', 'Parent product custom field of type "Generic child variant" with template position="ontop"', '<p>Nice hand shovel to dig with in the yard.</p><ul><li>Hand crafted handle with maximum grip torque</li><li>Titanium tipped shovel platter</li><li>Half degree offset for less accidents</li><li>Includes HowTo Video narrated by Bob Costas</li></ul><p><strong>Example of product with Parent product and Child products. The parent product has a custom field of type "<span>Generic child variant", useful to control the stock of the child products.</span></strong></p>', '', '', '', 'hand-shovel'),
(2, 'Ladder (Pattern)', 'Parent product used as pattern.', '<p>A really long ladder to reach high places.</p><ul><li>Hand crafted handle with maximum grip torque</li><li>Titanium tipped shovel platter</li><li>Half degree offset for less accidents</li><li>Includes HowTo Video narrated by Bob Costas</li></ul><p><strong>Example of a parent product used as pattern. The child products are added using the button "Child product" button from the product list. <br /><strong>For the child products, fields not filled in are taken from the parent. Fields that are changed are displayed.</strong><br /></strong></p>', '', '', '', 'ladder-pattern'),
(3, 'Shovel', 'Nice shovel.  You can dig your way to China with this one.', '<ul><li>Hand crafted handle with maximum grip torque</li><li>Titanium tipped shovel platter</li><li>Half degree offset for less accidents</li><li>Includes HowTo Video narrated by Bob Costas</li></ul><p><strong>Specifications</strong><br /> 5" Diameter<br /> Tungsten handle tip with 5 point loft</p>',  '', '', '', 'shovel'),
(4, 'Smaller Shovel', 'Product with Text input Plugin Custom field', 'This shovel is smaller but you''ll be able to dig real quick.<ul><li>Hand crafted handle with maximum grip torque</li><li>Titanium tipped shovel platter</li><li>Half degree offset for less accidents</li><li>Includes HowTo Video narrated by Bob Costas</li></ul>',  '', '', '', 'smaller-shovel'),
(5,  'Nice Saw', 'Product with Related Categories and Related Products associated', '<p>This saw is great for getting cutting through downed limbs.</p><ul><li>Hand crafted handle with maximum grip torque</li><li>Titanium tipped shovel platter</li><li>Half degree offset for less accidents</li><li>Includes HowTo Video narrated by Bob Costas</li></ul>', '', '', '', 'nice-saw'),
(6, 'Hammer', 'Product with custom fields with no cart attribute.', '<p>A great hammer to hammer away with.</p><ul><li>Hand crafted handle with maximum grip torque</li><li>Titanium tipped shovel platter</li><li>Half degree offset for less accidents</li><li>Includes HowTo Video narrated by Bob Costas</li></ul>',  '', '', '', 'hammer'),
(7, 'Chain Saw', 'Product with Custom field of type Cart variant and customer review', '<p>Don''t do it with an axe.  Get a chain saw.</p><ul><li>Tool-free tensioner for easy, convenient chain adjustment</li><li>3-Way Auto Stop; stops chain a fraction of a second</li><li>Automatic chain oiler regulates oil for proper chain lubrication</li><li>Small radius guide bar reduces kick-back</li></ul>',  '', '', '', 'chain-saw'),
(8, 'Circular Saw', 'Product with custom field of type Cart Variant', '<p>Cut rings around wood.  This saw can handle the most delicate projects.</p><ul><li>Patented Sightline; Window provides maximum visibility for straight cuts</li><li>Adjustable dust chute for cleaner work area</li><li>Bail handle for controlled cutting in 90ÔøΩ to 45ÔøΩ applications</li><li>1-1/2 to 2-1/2 lbs. lighter and 40% less noise than the average circular saw</li><li><strong>Includes:</strong>Carbide blade</li></ul>',  '', '', '', 'circular-saw'),
(9, 'Drill', 'Drill through anything.  This drill has the power you need for those demanding hole boring duties.', '<ul><li>High power motor and double gear reduction for increased durability and improved performance</li><li>Mid-handle design and two finger trigger for increased balance and comfort</li><li>Variable speed switch with lock-on button for continuous use</li><li><strong>Includes:</strong> Chuck key &amp; holder</li></ul><p><span style="color: #000000; font-size: medium;"><br /> </span></p>',  '', '', '', 'drill'),
(10, 'Power Sander', 'Blast away that paint job from the past.  Use this power sander to really show them you mean business.', '<ul><li>Lever activated paper clamps for simple sandpaper changes</li><li>Dust sealed rocker switch extends product life and keeps dust out of motor</li><li>Flush sands on three sides to get into corners</li><li>Front handle for extra control</li><li>Dust extraction port for cleaner work environment</li></ul>',  '', '', '', 'power-sander'),
(11, 'Hand Shovel cheap','', '',  '', '', '', 'hand-shovel-g01'),
(12, 'Hand Shovel enforced','', '',  '', '', '', 'hand-shovel-g02'),
(13, 'Hand Shovel heavy duty','', '',  '', '', '', 'hand-shovel-g03'),
(14, 'Metal Ladder','', '',  '', '', '', 'metal-ladder'),
(15, 'Wooden Ladder','', '<p>Loft ladders provide a safe and convenient solution to loft access and are quick and simple to install.</p>',  '', '', '', 'wooden-ladder'),
(16, 'Plastic Ladder','', '',  '', '', '', 'plastic-ladder');

--
-- Structure de la table `#__virtuemart_products_fr_fr`
--

CREATE TABLE IF NOT EXISTS `#__virtuemart_products_fr_fr` (
  `virtuemart_product_id` int(1) unsigned NOT NULL,
  `product_s_desc` varchar(2000) NOT NULL DEFAULT '',
  `product_desc` varchar(18400) NOT NULL DEFAULT '',
  `product_name` char(180) NOT NULL DEFAULT '',
  `metadesc` varchar(400) NOT NULL DEFAULT '',
  `metakey` varchar(400) NOT NULL DEFAULT '',
  `customtitle` char(255) NOT NULL DEFAULT '',
  `slug` char(192) NOT NULL DEFAULT '',
  PRIMARY KEY (`virtuemart_product_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__virtuemart_products_fr_fr` (`virtuemart_product_id`,`product_name`, `product_s_desc`, `product_desc`,  `metadesc`, `metakey`, `customtitle`, `slug`) VALUES
(1, 'Pelles', 'Parent product custom field of type "Generic child variant" with template position="ontop"', '<p>Nice hand shovel to dig with in the yard.</p><ul><li>Hand crafted handle with maximum grip torque</li><li>Titanium tipped shovel platter</li><li>Half degree offset for less accidents</li><li>Includes HowTo Video narrated by Bob Costas</li></ul><p><strong>Example of product with Parent product and Child products. The parent product has a custom field of type "<span>Generic child variant", useful to control the stock of the child products.</span></strong></p>', '', '', '', 'pelles'),
(2, 'Echelle (Modéle)', 'Parent product used as pattern.', '<p>A really long ladder to reach high places.</p><ul><li>Hand crafted handle with maximum grip torque</li><li>Titanium tipped shovel platter</li><li>Half degree offset for less accidents</li><li>Includes HowTo Video narrated by Bob Costas</li></ul><p><strong>Example of a parent product used as pattern. The child products are added using the button "Child product" button from the product list. <br /><strong>For the child products, fields not filled in are taken from the parent. Fields that are changed are displayed.</strong><br /></strong></p>', '', '', '', 'ladder-pattern'),
(3, 'Pelle', 'Nice shovel.  You can dig your way to China with this one.', '<ul><li>Hand crafted handle with maximum grip torque</li><li>Titanium tipped shovel platter</li><li>Half degree offset for less accidents</li><li>Includes HowTo Video narrated by Bob Costas</li></ul><p><strong>Specifications</strong><br /> 5" Diameter<br /> Tungsten handle tip with 5 point loft</p>',  '', '', '', 'shovel'),
(4, 'Pelle(petite)', 'Product with Text input Plugin Custom field', 'This shovel is smaller but you''ll be able to dig real quick.<ul><li>Hand crafted handle with maximum grip torque</li><li>Titanium tipped shovel platter</li><li>Half degree offset for less accidents</li><li>Includes HowTo Video narrated by Bob Costas</li></ul>',  '', '', '', 'smaller-shovel'),
(5, 'Scie PRO', 'Product with Related Categories and Related Products associated', '<p>This Scie is great for getting cutting through downed limbs.</p><ul><li>Hand crafted handle with maximum grip torque</li><li>Titanium tipped shovel platter</li><li>Half degree offset for less accidents</li><li>Includes HowTo Video narrated by Bob Costas</li></ul>', '', '', '', 'nice-scie'),
(6, 'Hammer', 'Product with custom fields with no cart attribute.', '<p>A great hammer to hammer away with.</p><ul><li>Hand crafted handle with maximum grip torque</li><li>Titanium tipped shovel platter</li><li>Half degree offset for less accidents</li><li>Includes HowTo Video narrated by Bob Costas</li></ul>',  '', '', '', 'hammer'),
(7, 'Tronçonneuse', 'Product with Custom field of type Cart variant and customer review', '<p>Don''t do it with an axe.  Get a chain Scie.</p><ul><li>Tool-free tensioner for easy, convenient chain adjustment</li><li>3-Way Auto Stop; stops chain a fraction of a second</li><li>Automatic chain oiler regulates oil for proper chain lubrication</li><li>Small radius guide bar reduces kick-back</li></ul>',  '', '', '', 'chain-Scie'),
(8, 'Scie circulaire', 'Product with custom field of type Cart Variant', '<p>Cut rings around wood.  This Scie can handle the most delicate projects.</p><ul><li>Patented Sightline; Window provides maximum visibility for straight cuts</li><li>Adjustable dust chute for cleaner work area</li><li>Bail handle for controlled cutting in 90ÔøΩ to 45ÔøΩ applications</li><li>1-1/2 to 2-1/2 lbs. lighter and 40% less noise than the average circular Scie</li><li><strong>Includes:</strong>Carbide blade</li></ul>',  '', '', '', 'scie-circulaire'),
(9, 'Perçeuse', 'Perceuse universelle.  Cette perceuse a la puissance nécessaire pour des travaux exigeants.', '<ul><li>Moteur haute puissance et double réducteur de vitesse pour des performances accrues et une meilleure durabilité</li><li>Mid-handle design and two finger trigger for increased balance and comfort</li><li>Interrupteur à variateur de vitesse avec bouton de verrouillage pour une utilisation continue</li><li><strong>Inclus:</strong> clés de serrage &amp; valise</li></ul><p><span style="color: #000000; font-size: medium;"><br /> </span></p>',  '', '', '', 'perceuse'),
(10, 'Ponceuse électrique', 'Blast away that paint job from the past.  Use this power sander to really show them you mean business.', '<ul><li>Lever activated paper clamps for simple sandpaper changes</li><li>Dust sealed rocker switch extends product life and keeps dust out of motor</li><li>Flush sands on three sides to get into corners</li><li>Front handle for extra control</li><li>Dust extraction port for cleaner work environment</li></ul>',  '', '', '', 'power-sander'),
(11, 'Pelle Promo','', '',  '', '', '', 'pelle-g01'),
(12, 'Pelle Robuste','', '',  '', '', '', 'pelle-g02'),
(13, 'Pelle grande','', '',  '', '', '', 'pelle-g03'),
(14, 'Echelle en métal','', '',  '', '', '', 'echelle-metal'),
(15, 'Echelle en bois','', '<p>Loft ladders provide a safe and convenient solution to loft access and are quick and simple to install.</p>',  '', '', '', 'echelle-bois'),
(16, 'Echelle en plastique','', '',  '', '', '', 'echelle-plastique');

INSERT IGNORE INTO `#__virtuemart_product_medias` (`id`,`virtuemart_product_id`, `virtuemart_media_id`) VALUES
(NULL, 1, 2),
(NULL, 2, 3),
(NULL, 3, 6),
(NULL, 4, 2),
(NULL, 5, 1),
(NULL, 6, 14),
(NULL, 7, 15),
(NULL, 8, 16),
(NULL, 9, 4),
(NULL, 10, 5);

INSERT IGNORE INTO `#__virtuemart_vendor_medias` (`id`,`virtuemart_vendor_id`, `virtuemart_media_id`) VALUES
(NULL, 1, 13);
--
-- Dumping data for table `#__virtuemart_product_categories`
--

INSERT IGNORE INTO `#__virtuemart_product_categories` (`virtuemart_category_id`, `virtuemart_product_id`, `ordering`) VALUES
(1, 1, NULL),
(3, 2, NULL),
(3, 3, NULL),
(3, 4, NULL),
(1, 5, NULL),
(1, 6, NULL),
(4, 7, NULL),
(2, 8, NULL),
(5, 9, NULL),
(3, 14, NULL),
(3, 15, NULL),
(3, 16, NULL)

;


--
-- Dumping data for table `#__virtuemart_product_manufacturers`
--

INSERT IGNORE INTO `#__virtuemart_product_manufacturers` (`virtuemart_product_id`, `virtuemart_manufacturer_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1);

--
-- Dumping data for table `#__virtuemart_product_prices`
--

INSERT INTO `#__virtuemart_product_prices` (`virtuemart_product_price_id`, `virtuemart_product_id`, `product_price`, `override`, `product_override_price`, `product_tax_id`, `product_discount_id`, `product_currency`, `product_price_publish_up`, `product_price_publish_down`, `virtuemart_shoppergroup_id`, `price_quantity_start`, `price_quantity_end`) VALUES
(1, 5, '24.99000', 0, '0.00000', NULL, NULL, '144', 0, 0,  NULL, 0, 0),
(2, 1, '4.49000', 0, '0.00000', NULL, NULL, '144', 0, 0, NULL, 0, 0),
(3, 2, '39.99000', 0, '0.00000', NULL, NULL, '144', 0, 0, NULL, 0, 0),
(4, 3, '24.99000', 0, '0.00000', NULL, NULL, '144', 0, 0, NULL, 0, 0),
(5, 4, '17.99000', 1, '77.00000', NULL, NULL, '144', 0, 0, NULL, 0, 0),
(6, 6, '4.99000', 0, '0.00000', NULL, NULL, '144', 0, 0, NULL, 0, 0),
(7, 7, '149.99000', 0, '0.00000', NULL, NULL, '144', 0, 0, NULL, 0, 0),
(8, 8, '220.90000', 0, '0.00000', NULL, NULL, '144', 0, 0, NULL, 0, 0),
(9, 9, '48.12000', 0, '0.00000', NULL, NULL, '144', 0, 0, NULL, 0, 0),
(10, 10, '74.99000', 0, '0.00000', NULL, NULL, '144', 0, 0, NULL, 0, 0),
(11, 11, '2.99000', 0, '0.00000', NULL, NULL, '144', 0, 0, NULL, 0, 0),
(12, 12, '14.99000', 0, '0.00000', NULL, NULL, '144', 0, 0, NULL, 0, 0),
(13, 13, '79.99000', 0, '0.00000', NULL, NULL, '144', 0, 0, NULL, 0, 0),
(14, 14, '49.99000', 0, '0.00000', NULL, NULL, '144', 0, 0, NULL, 0, 0),
(15, 15, '59.99000', 0, '0.00000', NULL, NULL, '144', 0, 0, NULL, 0, 0),
(16, 16, '3.99000', 0, '0.00000', NULL, NULL, '144', 0, 0, NULL, 0, 0);

--
-- Dumping data for table `#__virtuemart_shoppergroups`
--

INSERT IGNORE INTO `#__virtuemart_shoppergroups` (`virtuemart_shoppergroup_id`, `virtuemart_vendor_id`, `shopper_group_name`, `shopper_group_desc`, `default`) VALUES
(NULL, 1, 'Gold Level', 'Gold Level Shoppers.', 0),
(NULL, 1, 'Wholesale', 'Shoppers that can buy at wholesale.', 0);

--
-- Dumping data for table `#__virtuemart_worldzones`
--

INSERT INTO `#__virtuemart_worldzones` (`virtuemart_worldzone_id`, `zone_name`, `zone_cost`, `zone_limit`, `zone_description`, `zone_tax_rate`) VALUES
(1, 'Default', '6.00', '35.00', 'This is the default Shipment Zone. This is the zone information that all countries will use until you assign each individual country to a Zone.', 2),
(2, 'Zone 1', '1000.00', '10000.00', 'This is a zone example', 2),
(3, 'Zone 2', '2.00', '22.00', 'This is the second zone. You can use this for notes about this zone', 2),
(4, 'Zone 3', '11.00', '64.00', 'Another useful thing might be details about this zone or special instructions.', 2);


INSERT INTO `#__virtuemart_ratings` (`virtuemart_rating_id`, `virtuemart_product_id`, `rates`, `ratingcount`, `rating`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`) VALUES
(1, 7, 5, 1, 5.0, 1, '2012-10-26 10:28:32', 627, '2012-10-26 10:29:16', 627);

INSERT INTO `#__virtuemart_rating_reviews` (`virtuemart_rating_review_id`, `virtuemart_product_id`, `comment`, `review_ok`, `review_rates`, `review_ratingcount`, `review_rating`, `review_editable`, `lastip`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
(1, 7, 'I just purchased this saw to cut up some tree branches that fell into my yard. I am so happy, it is light weight enough for me (a lady) to use.', 0, 10, 2, 5.00, 0, '::1', 1, '2012-10-26 10:28:32', 627, '2012-10-26 10:29:16', 627, '0000-00-00 00:00:00', 0);

INSERT INTO `#__virtuemart_rating_votes` (`virtuemart_rating_vote_id`, `virtuemart_product_id`, `vote`, `lastip`, `created_on`, `created_by`, `modified_on`, `modified_by`) VALUES
(1, 7, 5, '::1', '2012-10-26 10:28:32', 627, '2012-10-26 10:29:16', 627);

-- --------------------------------------------------------

--
-- Structure de la table `#__virtuemart_vendors_fr_fr`
--

CREATE TABLE IF NOT EXISTS `#__virtuemart_vendors_fr_fr` (
  `vendor_letter_css` text NOT NULL,
  `vendor_letter_header_html` varchar(8000) NOT NULL DEFAULT '<h1>{vm:vendorname}</h1><p>{vm:vendoraddress}</p>',
  `vendor_letter_footer_html` varchar(8000) NOT NULL DEFAULT '<p>{vm:vendorlegalinfo}<br />Page {vm:pagenum}/{vm:pagecount}</p>',
  `metadesc` varchar(400) NOT NULL DEFAULT '',
  `metakey` varchar(400) NOT NULL DEFAULT '',
  `customtitle` char(255) NOT NULL DEFAULT '',
  `virtuemart_vendor_id` int(1) unsigned NOT NULL,
  `vendor_store_desc` text NOT NULL,
  `vendor_terms_of_service` text NOT NULL,
  `vendor_legal_info` text NOT NULL,
  `vendor_store_name` char(180) NOT NULL DEFAULT '',
  `vendor_phone` char(26) NOT NULL DEFAULT '',
  `vendor_url` char(255) NOT NULL DEFAULT '',
  `slug` char(192) NOT NULL DEFAULT '',
  PRIMARY KEY (`virtuemart_vendor_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `#__virtuemart_vendors_fr_fr`
--

INSERT INTO `#__virtuemart_vendors_fr_fr` (`vendor_letter_css`, `vendor_letter_header_html`, `vendor_letter_footer_html`, `metadesc`, `metakey`, `customtitle`, `virtuemart_vendor_id`, `vendor_store_desc`, `vendor_terms_of_service`, `vendor_legal_info`, `vendor_store_name`, `vendor_phone`, `vendor_url`, `slug`) VALUES
('', '<h1>{vm:vendorname}</h1><p>{vm:vendoraddress}</p>', '<p>{vm:vendorlegalinfo}<br />Page {vm:pagenum}/{vm:pagecount}</p>', '', '', '', 1, '<p>\r\n	We have the best tools for do-it-yourselfers. Check us out!</p>\r\n<p>\r\n	We were established in 1969 in a time when getting good tools was expensive, but the quality was good. Now that only a select few of those authentic tools survive, we have dedicated this store to bringing the experience alive for collectors and master mechanics everywhere.</p>\r\n<p>\r\n	You can easily find products selecting the category you would like to browse above.</p>', '<h5>\r\n	You haven''t configured any terms of service yet. Click <a href="/j3/administrator/index.php?option=com_virtuemart&view=user&task=editshop">here</a> to change this text.</h5>', '<p>\r\n	VAT-ID: XYZ-DEMO<br />\r\n	Reg.Nr: DEMONUMBER</p>', 'Washupito''s Tiendita', '', '', 'washupito-s-tiendita');

-- --------------------------------------------------------

--
-- Structure de la table `#__virtuemart_vendors_en_gb`
--

CREATE TABLE IF NOT EXISTS `#__virtuemart_vendors_en_gb` (
  `vendor_letter_css` text NOT NULL,
  `vendor_letter_header_html` varchar(8000) NOT NULL DEFAULT '<h1>{vm:vendorname}</h1><p>{vm:vendoraddress}</p>',
  `vendor_letter_footer_html` varchar(8000) NOT NULL DEFAULT '<p>{vm:vendorlegalinfo}<br />Page {vm:pagenum}/{vm:pagecount}</p>',
  `metadesc` varchar(400) NOT NULL DEFAULT '',
  `metakey` varchar(400) NOT NULL DEFAULT '',
  `customtitle` char(255) NOT NULL DEFAULT '',
  `virtuemart_vendor_id` int(1) unsigned NOT NULL,
  `vendor_store_desc` text NOT NULL,
  `vendor_terms_of_service` text NOT NULL,
  `vendor_legal_info` text NOT NULL,
  `vendor_store_name` char(180) NOT NULL DEFAULT '',
  `vendor_phone` char(26) NOT NULL DEFAULT '',
  `vendor_url` char(255) NOT NULL DEFAULT '',
  `slug` char(192) NOT NULL DEFAULT '',
  PRIMARY KEY (`virtuemart_vendor_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `#__virtuemart_vendors_en_gb`
--

INSERT INTO `#__virtuemart_vendors_en_gb` (`vendor_letter_css`, `vendor_letter_header_html`, `vendor_letter_footer_html`, `metadesc`, `metakey`, `customtitle`, `virtuemart_vendor_id`, `vendor_store_desc`, `vendor_terms_of_service`, `vendor_legal_info`, `vendor_store_name`, `vendor_phone`, `vendor_url`, `slug`) VALUES
('', '<h1>{vm:vendorname}</h1><p>{vm:vendoraddress}</p>', '<p>{vm:vendorlegalinfo}<br />Page {vm:pagenum}/{vm:pagecount}</p>', '', '', '', 1, '<p>\r\n	We have the best tools for do-it-yourselfers. Check us out!</p>\r\n<p>\r\n	We were established in 1969 in a time when getting good tools was expensive, but the quality was good. Now that only a select few of those authentic tools survive, we have dedicated this store to bringing the experience alive for collectors and master mechanics everywhere.</p>\r\n<p>\r\n	You can easily find products selecting the category you would like to browse above.</p>', '<h5>\r\n	You haven''t configured any terms of service yet. Click <a href="/j3/administrator/index.php?option=com_virtuemart&view=user&task=editshop">here</a> to change this text.</h5>', '<p>\r\n	VAT-ID: XYZ-DEMO<br />\r\n	Reg.Nr: DEMONUMBER</p>', 'Washupito''s Tiendita', '', '', 'washupito-s-tiendita');

