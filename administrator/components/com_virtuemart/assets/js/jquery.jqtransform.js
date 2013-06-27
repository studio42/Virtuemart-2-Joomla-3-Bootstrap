/*
 *
 * jqTransform
 * by mathieu vilaplana mvilaplana@dfc-e.com
 * Designer ghyslain armand garmand@dfc-e.com
 *
 *
 * Version 1.0 25.09.08
 * Version 1.1 06.08.09
 * Add event click on Checkbox and Radio
 * Auto calculate the size of a select element
 * Can now, disabled the elements
 * Correct bug in ff if click on select (overflow=hidden)
 * No need any more preloading !!
 * 
 ******************************************** */
 
(function(jQuery){
	var defaultOptions = {preloadImg:true};
	var jqTransformImgPreloaded = false;

	var jqTransformPreloadHoverFocusImg = function(strImgUrl) {
		//guillemets to remove for ie
		strImgUrl = strImgUrl.replace(/^url\((.*)\)/,'jQuery1').replace(/^\"(.*)\"jQuery/,'jQuery1');
		var imgHover = new Image();
		imgHover.src = strImgUrl.replace(/\.([a-zA-Z]*)jQuery/,'-hover.jQuery1');
		var imgFocus = new Image();
		imgFocus.src = strImgUrl.replace(/\.([a-zA-Z]*)jQuery/,'-focus.jQuery1');				
	};

	
	/***************************
	  Labels
	***************************/
	var jqTransformGetLabel = function(objfield){
		var selfForm = jQuery(objfield.get(0).form);
		var oLabel = objfield.next();
		if(!oLabel.is('label')) {
			oLabel = objfield.prev();
			if(oLabel.is('label')){
				var inputname = objfield.attr('id');
				if(inputname){
					oLabel = selfForm.find('label[for="'+inputname+'"]');
				} 
			}
		}
		if(oLabel.is('label')){return oLabel.css('cursor','pointer');}
		return false;
	};
	
	/* Hide all open selects */
	var jqTransformHideSelect = function(oTarget){
		var ulVisible = jQuery('.jqTransformSelectWrapper ul:visible');
		ulVisible.each(function(){
			var oSelect = jQuery(this).parents(".jqTransformSelectWrapper:first").find("select").get(0);
			//do not hide if click on the label object associated to the select
			if( !(oTarget && oSelect.oLabel && oSelect.oLabel.get(0) == oTarget.get(0)) ){jQuery(this).hide();}
		});
	};
	/* Check for an external click */
	var jqTransformCheckExternalClick = function(event) {
		if (jQuery(event.target).parents('.jqTransformSelectWrapper').length === 0) { jqTransformHideSelect(jQuery(event.target)); }
	};

	/* Apply document listener */
	var jqTransformAddDocumentListener = function (){
		jQuery(document).mousedown(jqTransformCheckExternalClick);
	};	
			
	/* Add a new handler for the reset action */
	var jqTransformReset = function(f){
		var sel;
		jQuery('.jqTransformSelectWrapper select', f).each(function(){sel = (this.selectedIndex<0) ? 0 : this.selectedIndex; jQuery('ul', jQuery(this).parent()).each(function(){jQuery('a:eq('+ sel +')', this).click();});});
		jQuery('a.jqTransformCheckbox, a.jqTransformRadio', f).removeClass('jqTransformChecked');
		jQuery('input:checkbox, input:radio', f).each(function(){if(this.checked){jQuery('a', jQuery(this).parent()).addClass('jqTransformChecked');}});
	};

	/***************************
	  Buttons
	 ***************************/
	jQuery.fn.jqTransInputButton = function(){
		return this.each(function(){
			var newBtn = jQuery('<button id="'+ this.id +'" name="'+ this.name +'" type="'+ this.type +'" class="'+ this.className +' jqTransformButton"><span><span>'+ jQuery(this).attr('value') +'</span></span>')
				.hover(function(){newBtn.addClass('jqTransformButton_hover');},function(){newBtn.removeClass('jqTransformButton_hover')})
				.mousedown(function(){newBtn.addClass('jqTransformButton_click')})
				.mouseup(function(){newBtn.removeClass('jqTransformButton_click')})
			;
			jQuery(this).replaceWith(newBtn);
		});
	};
	
	/***************************
	  Text Fields 
	 ***************************/
	jQuery.fn.jqTransInputText = function(){
		return this.each(function(){
			var jQueryinput = jQuery(this);
	
			if(jQueryinput.hasClass('jqtranformdone') || !jQueryinput.is('input')) {return;}
			jQueryinput.addClass('jqtranformdone');
	
			var oLabel = jqTransformGetLabel(jQuery(this));
			oLabel && oLabel.bind('click',function(){jQueryinput.focus();});
	
			var inputSize=jQueryinput.width();
			if(jQueryinput.attr('size')){
				inputSize = jQueryinput.attr('size')*10;
				jQueryinput.css('width',inputSize);
			}
			
			jQueryinput.addClass("jqTransformInput").wrap('<div class="jqTransformInputWrapper"><div class="jqTransformInputInner"><div></div></div></div>');
			var jQuerywrapper = jQueryinput.parent().parent().parent();
			jQuerywrapper.css("width", inputSize+10);
			jQueryinput
				.focus(function(){jQuerywrapper.addClass("jqTransformInputWrapper_focus");})
				.blur(function(){jQuerywrapper.removeClass("jqTransformInputWrapper_focus");})
				.hover(function(){jQuerywrapper.addClass("jqTransformInputWrapper_hover");},function(){jQuerywrapper.removeClass("jqTransformInputWrapper_hover");})
			;
	
			//If this is safari we need to add an extra class
			jQuery.browser.safari && jQuerywrapper.addClass('jqTransformSafari');
			jQuery.browser.safari && jQueryinput.css('width',jQuerywrapper.width()+16);
			this.wrapper = jQuerywrapper;
			
		}); 
	};
	
	/***************************
	  Check Boxes 
	 ***************************/	
	jQuery.fn.jqTransCheckBox = function(){
		return this.each(function(){
			if(jQuery(this).hasClass('jqTransformHidden')) {return;}

			var jQueryinput = jQuery(this);
			var inputSelf = this;

			//set the click on the label
			var oLabel=jqTransformGetLabel(jQueryinput);
			oLabel && oLabel.click(function(){aLink.trigger('click');});
			
			var aLink = jQuery('<a href="#" class="jqTransformCheckbox"></a>');
			//wrap and add the link
			jQueryinput.addClass('jqTransformHidden').wrap('<span class="jqTransformCheckboxWrapper"></span>').parent().prepend(aLink);
			//on change, change the class of the link
			jQueryinput.change(function(){
				this.checked && aLink.addClass('jqTransformChecked') || aLink.removeClass('jqTransformChecked');
				return true;
			});
			// Click Handler, trigger the click and change event on the input
			aLink.click(function(){
				//do nothing if the original input is disabled
				if(jQueryinput.attr('disabled')){return false;}
				//trigger the envents on the input object
				jQueryinput.trigger('click').trigger("change");	
				return false;
			});

			// set the default state
			this.checked && aLink.addClass('jqTransformChecked');		
		});
	};
	/***************************
	  Radio Buttons 
	 ***************************/	
	jQuery.fn.jqTransRadio = function(){
		return this.each(function(){
			if(jQuery(this).hasClass('jqTransformHidden')) {return;}

			var jQueryinput = jQuery(this);
			var inputSelf = this;
				
			oLabel = jqTransformGetLabel(jQueryinput);
			oLabel && oLabel.click(function(){aLink.trigger('click');});
	
			var aLink = jQuery('<a href="#" class="jqTransformRadio" rel="'+ this.name +'"></a>');
			jQueryinput.addClass('jqTransformHidden').wrap('<span class="jqTransformRadioWrapper"></span>').parent().prepend(aLink);
			
			jQueryinput.change(function(){
				inputSelf.checked && aLink.addClass('jqTransformChecked') || aLink.removeClass('jqTransformChecked');
				return true;
			});
			// Click Handler
			aLink.click(function(){
				if(jQueryinput.attr('disabled')){return false;}
				jQueryinput.trigger('click').trigger('change');
	
				// uncheck all others of same name input radio elements
				jQuery('input[name="'+jQueryinput.attr('name')+'"]',inputSelf.form).not(jQueryinput).each(function(){
					jQuery(this).attr('type')=='radio' && jQuery(this).trigger('change');
				});
	
				return false;					
			});
			// set the default state
			inputSelf.checked && aLink.addClass('jqTransformChecked');
		});
	};
	
	/***************************
	  TextArea 
	 ***************************/	
	jQuery.fn.jqTransTextarea = function(){
		/* return this.each(function(){
			var textarea = jQuery(this);
	
			if(textarea.hasClass('jqtransformdone')) {return;}
			textarea.addClass('jqtransformdone');
	
			oLabel = jqTransformGetLabel(textarea);
			oLabel && oLabel.click(function(){textarea.focus();});
			
			var strTable = '<table cellspacing="0" cellpadding="0" border="0" class="jqTransformTextarea">';
			strTable +='<tr><td id="jqTransformTextarea-tl"></td><td id="jqTransformTextarea-tm"></td><td id="jqTransformTextarea-tr"></td></tr>';
			strTable +='<tr><td id="jqTransformTextarea-ml">&nbsp;</td><td id="jqTransformTextarea-mm"><div></div></td><td id="jqTransformTextarea-mr">&nbsp;</td></tr>';	
			strTable +='<tr><td id="jqTransformTextarea-bl"></td><td id="jqTransformTextarea-bm"></td><td id="jqTransformTextarea-br"></td></tr>';
			strTable +='</table>';					
			var oTable = jQuery(strTable)
					.insertAfter(textarea)
					.hover(function(){
						!oTable.hasClass('jqTransformTextarea-focus') && oTable.addClass('jqTransformTextarea-hover');
					},function(){
						oTable.removeClass('jqTransformTextarea-hover');					
					})
				;
				
			textarea
				.focus(function(){oTable.removeClass('jqTransformTextarea-hover').addClass('jqTransformTextarea-focus');})
				.blur(function(){oTable.removeClass('jqTransformTextarea-focus');})
				.appendTo(jQuery('#jqTransformTextarea-mm div',oTable))
			;
			this.oTable = oTable;
			if(jQuery.browser.safari){
				jQuery('#jqTransformTextarea-mm',oTable)
					.addClass('jqTransformSafariTextarea')
					.find('div')
						.css('height',textarea.height())
						.css('width',textarea.width())
				;
			}
		}); */
	};
	
	/***************************
	  Select 
	 ***************************/	
	jQuery.fn.jqTransSelect = function(){
		return this.each(function(index){
			var jQueryselect = jQuery(this);

			if(jQueryselect.hasClass('jqTransformHidden')) {return;}
			if(jQueryselect.attr('multiple')) {return;}

			var oLabel  =  jqTransformGetLabel(jQueryselect);
			/* First thing we do is Wrap it */
			var jQuerywrapper = jQueryselect
				.addClass('jqTransformHidden')
				.wrap('<div class="jqTransformSelectWrapper"></div>')
				.parent()
				.css({zIndex: 10-index,width: '200px'})
			;
			
			/* Now add the html for the select */
			jQuerywrapper.prepend('<div><span></span><a href="#" class="jqTransformSelectOpen"></a></div><ul></ul>');
			var jQueryul = jQuery('ul', jQuerywrapper).css({width: '200px'}).hide();
			/* Now we add the options */
			jQuery('option', this).each(function(i){
				var oLi = jQuery('<li><a href="#" index="'+ i +'">'+ jQuery(this).html() +'</a></li>');
				jQueryul.append(oLi);
			});
			
			/* Add click handler to the a */
			jQueryul.find('a').click(function(){
					jQuery('a.selected', jQuerywrapper).removeClass('selected');
					jQuery(this).addClass('selected');	
					/* Fire the onchange event */
					if (jQueryselect[0].selectedIndex != jQuery(this).attr('index') && jQueryselect[0].onchange) { jQueryselect[0].selectedIndex = jQuery(this).attr('index'); jQueryselect[0].onchange(); }
					jQueryselect[0].selectedIndex = jQuery(this).attr('index');
					jQuery('span:eq(0)', jQuerywrapper).html(jQuery(this).html());
					jQueryul.hide();
					return false;
			});
			/* Set the default */
			jQuery('a:eq('+ this.selectedIndex +')', jQueryul).click();
			jQuery('span:first', jQuerywrapper).click(function(){jQuery("a.jqTransformSelectOpen",jQuerywrapper).trigger('click');});
			oLabel && oLabel.click(function(){jQuery("a.jqTransformSelectOpen",jQuerywrapper).trigger('click');});
			this.oLabel = oLabel;
			
			/* Apply the click handler to the Open */
			var oLinkOpen = jQuery('a.jqTransformSelectOpen', jQuerywrapper)
				.click(function(){
					//Check if box is already open to still allow toggle, but close all other selects
					if( jQueryul.css('display') == 'none' ) {jqTransformHideSelect();} 
					if(jQueryselect.attr('disabled')){return false;}

					jQueryul.slideToggle('fast', function(){					
						var offSet = (jQuery('a.selected', jQueryul).offset().top - jQueryul.offset().top);
						jQueryul.animate({scrollTop: offSet});
					});
					return false;
				})
			;

			// Set the new width
			var iSelectWidth = jQueryselect.outerWidth();
			var oSpan = jQuery('span:first',jQuerywrapper);
			var newWidth = (iSelectWidth > oSpan.innerWidth())?iSelectWidth+oLinkOpen.outerWidth():jQuerywrapper.width();
			jQuerywrapper.css('width',newWidth);
			jQueryul.css('width',newWidth-2);
			oSpan.css({width:iSelectWidth});
		
			// Calculate the height if necessary, less elements that the default height
			//show the ul to calculate the block, if ul is not displayed li height value is 0
			jQueryul.css({display:'block',visibility:'hidden'});
			var iSelectHeight = (jQuery('li',jQueryul).length)*(jQuery('li:first',jQueryul).height());//+1 else bug ff
			(iSelectHeight < jQueryul.height()) && jQueryul.css({height:iSelectHeight,'overflow':'hidden'});//hidden else bug with ff
			jQueryul.css({display:'none',visibility:'visible'});
			
		});
	};
	jQuery.fn.jqTransform = function(options){
		var opt = jQuery.extend({},defaultOptions,options);
		
		/* each form */
		 return this.each(function(){
			var selfForm = jQuery(this);
			if(selfForm.hasClass('jqtransformdone')) {return;}
			selfForm.addClass('jqtransformdone');
			
			jQuery('input:submit, input:reset, input[type="button"]', this).jqTransInputButton();			
			jQuery('input:text, input:password', this).jqTransInputText();			
			jQuery('input:checkbox', this).jqTransCheckBox();
			jQuery('input:radio', this).jqTransRadio();
			jQuery('textarea', this).jqTransTextarea();
			
			if( jQuery('select', this).jqTransSelect().length > 0 ){jqTransformAddDocumentListener();}
			selfForm.bind('reset',function(){var action = function(){jqTransformReset(this);}; window.setTimeout(action, 10);});
			
			//preloading dont needed anymore since normal, focus and hover image are the same one
			/*if(opt.preloadImg && !jqTransformImgPreloaded){
				jqTransformImgPreloaded = true;
				var oInputText = jQuery('input:text:first', selfForm);
				if(oInputText.length > 0){
					//pour ie on eleve les ""
					var strWrapperImgUrl = oInputText.get(0).wrapper.css('background-image');
					jqTransformPreloadHoverFocusImg(strWrapperImgUrl);					
					var strInnerImgUrl = jQuery('div.jqTransformInputInner',jQuery(oInputText.get(0).wrapper)).css('background-image');
					jqTransformPreloadHoverFocusImg(strInnerImgUrl);
				}
				
				var oTextarea = jQuery('textarea',selfForm);
				if(oTextarea.length > 0){
					var oTable = oTextarea.get(0).oTable;
					jQuery('td',oTable).each(function(){
						var strImgBack = jQuery(this).css('background-image');
						jqTransformPreloadHoverFocusImg(strImgBack);
					});
				}
			}*/
			
			
		}); /* End Form each */
				
	};/* End the Plugin */

})(jQuery);
				   