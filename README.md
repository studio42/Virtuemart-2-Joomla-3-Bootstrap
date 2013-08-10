Virtuemart 2.0 Joomla 3 Bootstrapped
=============

**This is an convertion for joomla 3.0**

VIrtuemart 2 is a online shop solution for joomla 2.5.

New features
------------
**Full Shop Front-end administration** not only product or category.

Include now a **front-End administrator menu**

In shop **direct** edit link for products,categories and manufacturers.(more to come soon)

All is converted to **Bootstrap**

THe code is **joomla 3.0** compatible(tested on joomla 3.1 and joomla 2.5)

**New dashboard**

**Simplified and more advanced** Virtuemart plugin settings.

All List are now updated **without complet page reload**. No javascript and css refresh and size reduced by 2.

Tasks in lists view, publishing ..., only reload **260 Octets** (the message itself) and **not 80 Kbs**.

Old parameters(jparameter) are removed and use now **New joomla formFields**.

Compatible with most **PDF Engines***

Clone product **with plugin** correctly, if the method is in the plugin.

Commit update include many bug fix, i applied and share to the user.    
Most of this fix are not included in Official release.

This repositry is a derivated work from original 2.0.22a stable zip package from virtuemart 2 Official site (without aditional languages & files).
See http://dev.virtuemart.net/projects/virtuemart/files for original packages without this changes and fixes.
You can install new languages from original release.
Install Steps
-------------
1-Download https://github.com/studio42/Virtuemart-2-Joomla-3-Bootstrap/archive/master.zip and install as all joomla component  
2-Download and install https://github.com/studio42/Virtuemart2-all-in-one-joomla3/archive/master.zip as all joomla component

The pdf enchanced is included in all-in-one installer no need to install anything more.  
All-in-one main plugins and modules are ready to run

info
------------
This release have to use joomla-pdf-document-view from github to make the orders PDF.  
If you have always installed original virtuemart 2.0.20.b package, only to do is download and install this release and https://github.com/studio42/joomla-pdf-document-view/archive/master.zip
Of course, use the Joomla installer to include all in your website.

If you want better PDF, then look the full explain at http://studio42.github.io/joomla-pdf-document-view/index.html. But you have to update the original virtuemart with this repository to use another PDF class.

Plugin Update
------------
To update your plugin, simply use this minimal tutorial  
XML Manifest tags to change  
`<install>` to `<extension>`  
`<params>` to `<fields><fieldsets>`  
and `<param>` to `<field>`  
Now all plugins are compatible with this release and joomla 3.0

If you use old element with a path then replace  
eg. for virtuemart elements  
 ```<params addpath="/administrator/components/com_virtuemart/elements" /> ```  
to  
 ```<fieldset name="options" addfieldpath="/administrator/components/com_virtuemart/models/fields"> ```


Not all fix (but most) listed here are fixed
-------------
1-Incompatible j1.5 & j1.7 :
JComponentHelper::filterText(unfixed)
If you try to edit a product in virtuemart 2.0.2 this does not work, upgrade to j2.5+

2-remove unwanted languagevars in url

3-fix to clone plugins

4-fix to switch lang
router.php

product edit :

5-bad Browser TITLE(product edit)  
6-add_new_price do not work (new product)  
7-price-remove button remove original price container, and it's imposible to readd it without saving the product.  
8-add child product, is visible in a new product(but cannot work of course)  
9-removing a plugin from a product, does not call it on save,  to inform it it's not existing. At end you have orphan tables in your plugin.  
10-price set to 0.0 are displayed as "priced" products.(confusing)  
11- on save product without price do an PHP error in product model , but because redirection, this is not visible.  
reason mprices is not set in :
` foreach($data['mprices']['product_price'] as $k => $product_price){`
12- possible same issu and fix as 11 : if (!empty($data['childs'])) {  TO  if (isset($data['childs'])) {
13- product_edit_information.php html price :
```    		<td valign="top">
    			<!-- Product pricing -->
	before :
    <table>
    	<tr>```
AND :
```    	<a href="#" id="add_new_price" ">  TO <a href="#" id="add_new_price">```
14- product_edit_information.php
```    	<input type="hidden" value="<?php echo $this->product->ordering ?>" name="ordering">```
bad HTML : code to move inside a `  <td></td>`

15- product_edit_customer.php 
 after > notification_template
`    					</div>
    				</label>`
invert TO
`    					</label>
    				</div>`   
16- product_edit_customer.php  
before : `$aflink`  
remove one `div` some line before  

17- product_edit_custom.php 
`    				<div><?php echo  '<div class="inline">'.$this->customsList; ?></div>`
	TO
`    				<div class="inline"><?php echo  $this->customsList; ?></div>`
				
18- product_edit_price.php  
`class="adminform" class="productPriceTable"`  
19- general HTML ID and array:  
HTML Error: `character "[" is not allowed in the value of attribute "id"`  
eg.  
`id="mprices[product_price_publish_up][]` (BAD)  
TO `mprices-product_price_publish_up-0` (OK)  
(NOTE : can brake a javascript if the value is used in a script).  

20- closing tag : `div` is missing for `div class="mailing"`  
21- missing open `td` before `VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)`  
22- after `echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PARENT')`, closing tag `div` is missing  
23- do not load customer for new product :  
24- remove intnotes unwanted "tabs" :  

