Virtuemart 2.0
=============
This repositry is from original 2.0.20.b stable zip package from virtuemart 2 Official site (without aditional languages & files).
See http://dev.virtuemart.net/projects/virtuemart/files for all packages.

VIrtuemart 2 is a online shop solution for joomla 2.5.

Commit update are only some bug fix, i applied and share to the user, for now only some fixes.    
Most of this fix are not included in Official release.  
This release have to use joomla-pdf-document-view from github to make the orders PDF.  
If you have always installed original virtuemart 2.0.20.b package, only to do is download and install this release and https://github.com/studio42/joomla-pdf-document-view/archive/master.zip
Of course, use the Joomla installer to include all in your website.


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

