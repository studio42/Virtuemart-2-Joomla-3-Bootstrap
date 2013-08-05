/**
 * list country.js: General Javascript Library for VirtueMart Administration
 * Note Patrick kohl : Found many bugs, completly brake in case of multiple list!
 * Now the json cache (optCache) is common for all calls(states are always same for each country) 
 * and not set for each select list anymore.
 * @package	VirtueMart
 * @subpackage Javascript Library
 * @author Patrick Kohl
 * @copyright Copyright (c) 2011VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

(function($){
	var undefined, optCache = [], ids,
	methods = {
		list: function(options) {
			var self = this ;
			var dest = options.dest;
			var ids = options.ids;
			if (ids.length) methods.update(self,dest,ids);

			$(self).change( function() { methods.update(self,dest)});
		},
		update: function(org,dest,ids) {
			var opt = $(org),
				optValues = opt.val() || [],
				byAjax = [] ;
			if (!$.isArray(optValues)) optValues = jQuery.makeArray(optValues);
			if ( typeof  org.oldValues !== "undefined") {
				//remove if not in optValues
				$.each(org.oldValues, function(key, oldValue) {
					if ( ($.inArray( oldValue, optValues )) < 0 ) $(dest+'group'+oldValue+"").remove();
				});
			}
			//push in 'byAjax' values and do it in ajax
			$.each(optValues, function(optkey, optValue) {
				if( optCache[ 'd'+optValue ] === undefined ) byAjax.push( optValue );
			});
			if (byAjax.length >0) {
				$.getJSON('index.php?option=com_virtuemart&view=state&format=json&virtuemart_country_id=' + byAjax,
						function(result){
						
						// Max Bitte Testen
						// NOTE patrick Kohl, does not work with 2 list(id is same
						var virtuemart_state_id = $(dest);
						var status = virtuemart_state_id.attr('required');
						
						if(status == 'required') {
							if( result[byAjax].length > 0 ) {
								virtuemart_state_id.attr('required','required');
							} else {
								virtuemart_state_id.removeAttr('required');
							}
						}
						
						// ENDE

						$.each(result, function(key, value) {
							if (value.length >0) {
								optCache[ 'd'+key ] = value ;
							} else { 
								optCache[ 'd'+key ] = 0 ;
							}
						});
						methods.addToList(opt,optValues,dest);
						if ( typeof  ids !== "undefined") {
							var states =  ids.length ? ids.split(',') : [] ;
							$.each(states, function(k,id) {
								$(dest).find('[value='+id+']').attr("selected","selected");
							});
						}
						$(dest).trigger("liszt:updated");
					}
				);
			} else {
				methods.addToList(opt,optValues,dest)
				$(dest).trigger("liszt:updated");
			}
			org.oldValues = optValues ;
			
		},
		addToList: function(opt,values,dest) {
			var id = $(dest).attr('id');
			$.each(values, function(dataKey, dataValue) { 
				var groupId = dest+'group'+dataValue, groupExist = $(groupId).size(); 

				if ( ! groupExist ) {
					var datas = optCache[ 'd'+dataValue ];
					if (datas.length >0) {
					var label = opt.find("option[value='"+dataValue+"']").text();
					var group ='<optgroup id="'+id+'group'+dataValue+'" label="'+label+'">';
					$.each( datas  , function( key, value) {
						if (value) group +='<option value="'+ value.k +'">'+ value.v +'</option>';
					});
					group += '</optgroup>';
					$(dest).append(group);
					
					}
				}
			});
		}
	};

	$.fn.vm2front = function( method ) {
		
		if ( methods[method] ) {
		  return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
		  $.error( 'Method ' +  method + ' does not exist on Vm2 front jQuery library' );
		}    
	
	};
})(jQuery)
