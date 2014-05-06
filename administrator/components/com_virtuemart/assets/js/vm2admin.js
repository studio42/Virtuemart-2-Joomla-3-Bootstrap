// based on http://www.seomoves.org/blog/web-design-development/dynotable-a-jquery-plugin-by-bob-tantlinger-2683/
(function ($) {
    $.fn.extend({
        dynoTable:function (options) {

            var defaults = {
                removeClass:'.row-remover',
                cloneClass:'.row-cloner',
                addRowTemplateId:'#add-template',
                addRowButtonId:'#add-row',
                lastRowRemovable:true,
                orderable:true,
                dragHandleClass:".drag-handle",
                insertFadeSpeed:"slow",
                removeFadeSpeed:"fast",
                hideTableOnEmpty:true,
                onRowRemove:function () {
                },
                onRowClone:function () {
                },
                onRowAdd:function () {
                },
                onTableEmpty:function () {
                },
                onRowReorder:function () {
                }
            };

            options = $.extend(defaults, options);

            var cloneRow = function (btn) {
                var clonedRow = $(btn).closest('tr').clone();
                var tbod = $(btn).closest('tbody');
                insertRow(clonedRow, tbod);
                options.onRowClone();
            }

            var insertRow = function (clonedRow, tbod) {
                var numRows = $(tbod).children("tr").length;
                if (options.hideTableOnEmpty && numRows == 0) {
                    $(tbod).parents("table").first().show();
                }

                $(clonedRow).find('*').andSelf().filter('[id]').each(function () {
                    //change to something else so we don't have ids with the same name
                    // this.id += "_" + numRows;
                });

                //finally append new row to end of table
                $(tbod).append(clonedRow);
                bindActions(clonedRow);
                $(tbod).children("tr:last").hide().fadeIn(options.insertFadeSpeed);
            }

            var removeRow = function (btn) {
                var tbod = $(btn).parents("tbody:first");
                var numRows = $(tbod).children("tr").length;

                if (numRows > 1 || options.lastRowRemovable === true) {
                    var trToRemove = $(btn).parents("tr:first");
                    $(trToRemove).fadeOut(options.removeFadeSpeed, function () {
                        $(trToRemove).remove();
                        options.onRowRemove();
                        if (numRows == 1) {
                            if (options.hideTableOnEmpty) {
                                $(tbod).parents('table').first().hide();
                            }
                            // we want to remove the class remove
                            $().removeClass("icon-remove");
                            options.onTableEmpty();
                        }
                    });
                }
            }

            var bindClick = function (elem, fn) {
                $(elem).click(fn);
            }

            var bindCloneLink = function (lnk) {
                bindClick(lnk, function () {
                    var btn = $(this);
                    cloneRow(btn);
                    return false;
                });
            }

            var bindRemoveLink = function (lnk) {
                bindClick(lnk, function () {
                    var btn = $(this);
                    removeRow(btn);
                    return false;
                });
            }

            var bindActions = function (obj) {
                obj.find(options.removeClass).each(function () {
                    bindRemoveLink($(this));
                });

                obj.find(options.cloneClass).each(function () {
                    bindCloneLink($(this));
                });
            }

            return this.each(function () {
                //Sanity check to make sure we are dealing with a single case
                if (this.nodeName.toLowerCase() == 'table') {
                    var table = $(this);
                    var tbody = $(table).children("tbody").first();

                    if (options.orderable && jQuery().sortable) {
                        $(tbody).sortable({
                            handle:options.dragHandleClass,
                            helper:function (e, ui) {
                                ui.children().each(function () {
                                    $(this).width($(this).width());
                                });
                                return ui;
                            },
                            items:"tr",
                            update:function (event, ui) {
                                options.onRowReorder();
                            }
                        });
                    }

                    $(table).find(options.addRowTemplateId).each(function () {
                        $(this).removeAttr("id");
                        var tmpl = $(this);
                        tmpl.remove();
                        bindClick($(options.addRowButtonId), function () {
                            //options.onBeforeRowAdd();
                            var newTr = tmpl.clone();
                            insertRow(newTr, tbody);
                            options.onRowAdd();
                            return false;
                        });
                    });
                    bindActions(table);

                    var numRows = $(tbody).children("tr").length;
                    if (options.hideTableOnEmpty && numRows == 0) {
                        $(table).hide();
                    }
                }
            });
        }
    });
})(jQuery);


(function ($) {

    var methods = {
        accordeon:function () {
			var last=$.cookie('vmmenuActiveAccordion');
			if (last!=null) {
				//remove default collapse settings
				$("#vmmenu .collapse").removeClass('in');
				//show the last visible group
				$("#"+last).collapse("show");
			}
			//when a group is shown, save it as the active accordion group
			$("#vmmenu").bind('shown', function() {
				var active=$("#vmmenu .in").attr('id');
				$.cookie('vmmenuActiveAccordion', active);
			});
        },
        media:function (mediatype, total) {
            var page = 0,
                max = 16,
                container = jQuery(this);
            var pagetotal = Math.ceil(total / max);
            var cache = new Array();

            var formatTitle = function (title, currentArray, currentIndex, currentOpts) {
                var pagination = '' , pagetotal = total / max;
                if (pagetotal > 0) {
                    pagination = '<span><<</span><span><</span>';
                    for (i = 0; i < pagetotal; i++) {
                        pagination += '<span>' + (i + 1) + '</span>';
                    }
                    pagination += '<span>></span><span>>></span>';
                }
                return '<div class="media-pagination">' + (title && title.length ? '<b>' + title + '</b>' : '' ) + ' ' + pagination + '</div>';
            }

            jQuery("#fancybox-title").on( "click", ".media-pagination span", function (event) {
                var newPage = $(this).text();
                display(newPage);
                event.preventDefault();
            });
            container.on("click", "a.vm_thumb", function (event) {
                jQuery.fancybox({
                    "type":"image",
                    "titlePosition":"inside",
                    "title":this.title,
                    "href":this.href
                });
                event.preventDefault();
            });
            jQuery("#media-dialog").on( "click", ".vm_thumb_image", function (event) {
                event.preventDefault();
                var id = $(this).find('input').val(), ok = 0;
                var inputArray = new Array();
                $('#ImagesContainer input:hidden').each(
                    function () {
                        inputArray.push($(this).val())
                    }
                );
                if ($.inArray(id, inputArray) == -1) {
                    that = jQuery(this);
                    jQuery(this).clone().appendTo(container).unbind("click").append('<div class="icon-remove pull-right" title="remove"></div><div class="icon-edit pull-left" title="' + vm2string.editImage + '"><div>');
                    that.hide().fadeIn();
                }

            });

            jQuery("#adminForm").on("click", "div.icon-remove, div.icon-remove", function () {
                jQuery(this).closest(".vm_thumb_image").fadeOut("500", function () {
                    jQuery(this).remove()
                });
            });
            jQuery("#adminForm").on("click", "span.icon-remove, span.icon-remove", function () {
				var removable = jQuery(this).closest(".removable") ;
				parent = removable.parent();
				// console.log(parent.children().length,parent.data("lastrowunremovable"));
				if (parent.data('lastrowunremovable') == true && parent.children().length == 1 ) {
					alert('Last item cannot be removed');
					return ;
				}
                removable.fadeOut("500", function () {
                    jQuery(this).remove()
                });
            });

            jQuery("#addnewselectimage2").fancybox({
                "hideOnContentClick":false,
                "autoDimensions":true,
                "titlePosition":"inside",
                "title":"Media list",
                "titleFormat":formatTitle,
                "onComplete":function () {
                    $('.media-pagination').children().eq(page + 3).addClass('media-page-selected');
                }
            });

            container.on("click",".icon-edit",  function () {

                var data = jQuery(this).parent().find("input").val();
					tmpl = $('#adminForm input[name=tmpl]').val();
				if (tmpl == 'component') data+='&tmpl=component';
                jQuery.getJSON( vmBaseUrl+"index.php?option=com_virtuemart&view=media&task=viewjson&format=json&virtuemart_media_id=" + data,
                    function (datas, textStatus) {
                        if (datas.msg == "OK") {
							var form = jQuery("#adminForm");
                            jQuery("#vm_display_image")
								.attr("src", datas.file_root + datas.file_url)
								.attr("alt", datas.file_title)
								.attr("title", datas.file_title);
                            jQuery("#file_title").html(datas.file_title);
							var lang = datas.file_lang.split(',');
							jQuery("#vmlangimg").val(lang).trigger("liszt:updated");
                            // if (datas.published == 1) 
							jQuery("#media_published"+datas.published,form).trigger('click');
                            // else jQuery("[name=media_published]",form).attr('checked', false);
                            if (datas.file_is_downloadable == 0) {
                                jQuery("#media_rolesfile_is_displayable").attr('checked', true);
                                //jQuery("#adminForm [name=media_roles]").filter("value='file_is_downloadable'").attr('checked', false);
                            }
                            else {
                                //jQuery("#adminForm [name=media_roles]").filter("value='file_is_displayable'").attr('checked', false);
                                jQuery("#media_rolesfile_is_downloadable").attr('checked', true);
                            }
                            jQuery("[name=file_title]",form).val(datas.file_title);
                            jQuery("[name=file_description]",form).val(datas.file_description);
                            jQuery("[name=file_meta]",form).val(datas.file_meta);
                            jQuery("[name=file_url]",form).val(datas.file_url);
                            jQuery("[name=file_url_thumb]",form).val(datas.file_url_thumb);
                            jQuery("[name=active_media_id]",form).val(datas.virtuemart_media_id);
                            if (datas.file_url_thumb !== "undefined") {
                                jQuery("#vm_thumb_image").attr("src", datas.file_root + datas.file_url_thumb)
								.attr("alt", datas.file_title)
								.attr("title", datas.file_title);
                            }
                            else {
                                jQuery("#vm_thumb_image").attr("src", "")
								.attr("alt", datas.file_title)
								.attr("title", datas.file_title);
                            }
                        } else jQuery("#file_title").html(datas.msg);
						$('#image_desc_accordion').collapse('show');
                    }).fail(function() {
						location.reload();
					});

            });

            var display = function (num) {
                if (typeof this.page == "undefined") {
                    this.oldPage = this.page = 0;

                }
                if (typeof display.cache == "undefined") {
                    display.cache = new Array();
                }
                switch (num) {
                    case '<':
                        if (this.page > 0) --this.page;
                        else return;
                        break;
                    case '>':
                        if (this.page < pagetotal - 1) ++this.page;
                        else return;
                        break;
                    case '<<':
                        this.page = 0;
                        break;
                    case '>>':
                        this.page = pagetotal - 1;
                        break;
                    default :
                        this.page = num - 1;
                        break;
                }
                if (this.oldPage != this.page) {
                    //var cache = this.cache ;
                    var start = this.page;
                    if (typeof display.cache[start] == "undefined") {
                        jQuery.getJSON( vmBaseUrl+"index.php?option=com_virtuemart&view=media&task=viewjson&format=json&mediatype=" + mediatype + "&start=" + start,
                            function (data) {
                                if (data.imageList != "ERROR") {
                                    display.cache[start] = data.imageList;
                                    jQuery("#media-dialog").html(display.cache[start]);
                                    jQuery(".page").text("Page(s) " + (start + 1));
                                } else {
                                    jQuery(".page").text("No  more results : Page(s) " + (start + 1));
                                }
                            }
                        ).fail(function() {
							location.reload();
						});
                    } else jQuery("#media-dialog").html(display.cache[start]);
                    page = this.oldPage = this.page;
                    $('.media-pagination').children().removeClass('media-page-selected');
                    $('.media-pagination').children().eq(start + 3).addClass('media-page-selected');
                }
            }
        },
        toggle:function () {
            var options = { path:'/', expires:2};
            if ($.cookie('vmmenu')) {
                var status = $.cookie('vmmenu');
                if (status == 'hide') {
                    this.removeClass('vmicon-show').addClass('vmicon-hide');
                    $('.menu-wrapper').toggle('slide');
                }
            }

            this.click(function () {
                $this = $(this);
                if ($this.hasClass('vmicon-show')) {
                    $this.removeClass('vmicon-show').addClass('vmicon-hide');
                    $('.menu-wrapper').toggle('slide');
                    $.cookie('vmmenu', 'hide', options);
                } else {
                    $this.removeClass('vmicon-hide').addClass('vmicon-show');
                    $('.menu-wrapper').toggle('slide');
                    $.cookie('vmmenu', 'show', options);
                }
            });
        },

        yydynoTable:function (options) {
            var defaults = {
                removeClass:'.row-remover',
                cloneClass:'.row-cloner',
                addRowTemplateId:'#add-template',
                addRowButtonId:'#add-row',
                lastRowRemovable:true,
                orderable:true,
                dragHandleClass:".drag-handle",
                insertFadeSpeed:"slow",
                removeFadeSpeed:"fast",
                hideTableOnEmpty:true,
                onRowRemove:function () {
                },
                onRowClone:function () {
                },
                onRowAdd:function () {
                },
                onTableEmpty:function () {
                },
                onRowReorder:function () {
                }
            };

            options = $.extend(defaults, options);

            var cloneRow = function (btn) {
                var clonedRow = $(btn).closest('tr').clone();
                var tbod = $(btn).closest('tbody');
                insertRow(clonedRow, tbod);
                options.onRowClone();
            }

            var insertRow = function (clonedRow, tbod) {
                var numRows = $(tbod).children("tr").length;
                if (options.hideTableOnEmpty && numRows == 0) {
                    $(tbod).parents("table").first().show();
                }

                $(clonedRow).find('*').andSelf().filter('[id]').each(function () {
                    //change to something else so we don't have ids with the same name
                    // this.id += "_" + numRows;
                });

                //finally append new row to end of table
                $(tbod).append(clonedRow);
                bindActions(clonedRow);
                $(tbod).children("tr:last").hide().fadeIn(options.insertFadeSpeed);
            }

            var removeRow = function (btn) {
                var tbod = $(btn).parents("tbody:first");
                var numRows = $(tbod).children("tr").length;

                if (numRows > 1 || options.lastRowRemovable === true) {
                    var trToRemove = $(btn).parents("tr:first");
                    $(trToRemove).fadeOut(options.removeFadeSpeed, function () {
                        $(trToRemove).remove();
                        options.onRowRemove();
                        if (numRows == 1) {
                            if (options.hideTableOnEmpty) {
                                $(tbod).parents('table').first().hide();
                            }
                            // we want to remove the class remove
                            $(".price-remove > span").removeClass("vmicon-16-price-remove");
                            options.onTableEmpty();
                        }
                    });
                }
            }

            var bindClick = function (elem, fn) {
                $(elem).click(fn);
            }

            var bindCloneLink = function (lnk) {
                bindClick(lnk, function () {
                    var btn = $(this);
                    cloneRow(btn);
                    return false;
                });
            }

            var bindRemoveLink = function (lnk) {
                bindClick(lnk, function () {
                    var btn = $(this);
                    removeRow(btn);
                    return false;
                });
            }

            var bindActions = function (obj) {
                obj.find(options.removeClass).each(function () {
                    bindRemoveLink($(this));
                });

                obj.find(options.cloneClass).each(function () {
                    bindCloneLink($(this));
                });
            }

            return this.each(function () {
                //Sanity check to make sure we are dealing with a single case
                if (this.nodeName.toLowerCase() == 'table') {
                    var table = $(this);
                    var tbody = $(table).children("tbody").first();

                    if (options.orderable && jQuery().sortable) {
                        $(tbody).sortable({
                            handle:options.dragHandleClass,
                            helper:function (e, ui) {
                                ui.children().each(function () {
                                    $(this).width($(this).width());
                                });
                                return ui;
                            },
                            items:"tr",
                            update:function (event, ui) {
                                options.onRowReorder();
                            }
                        });
                    }

                    $(table).find(options.addRowTemplateId).each(function () {
                        $(this).removeAttr("id");
                        var tmpl = $(this);
                        tmpl.remove();
                        bindClick($(options.addRowButtonId), function () {
                            //options.onRowAdd();
                            var newTr = tmpl.clone();
                            insertRow(newTr, tbody);
                            options.onRowAdd();
                            return false;
                        });
                    });
                    bindActions(table);

                    var numRows = $(tbody).children("tr").length;
                    if (options.hideTableOnEmpty && numRows == 0) {
                        $(table).hide();
                    }
                }
            });
        }


    };

    $.fn.vm2admin = function (method) {

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on Vm2 admin jQuery library');
        }

    };
})(jQuery);

// load defaut scripts 
jQuery.noConflict();
jQuery(document).ready(function($) {
    function updateBtnState(btn, input, updateRadios) {
        btn.toggleClass('active', input.prop('checked'));
		btn.find('i').toggleClass('icon-unpublish icon-publish');
        btn.toggleClass('disabled', input.prop('disabled'));
    }

    $(document).live('change', '.btn-toggle input', function(e) {
        var input = $(e.target);
        // radio button that are automatically unchecked dont trigger a change event
        if (input.is(':radio')) {
            var selector = 'input[type="radio"][name="' + input.attr('name') + '"]';
            $(selector).each(function() {
                var input = $(this),
                    btn = input.parents('.btn-toggle');
                updateBtnState(btn, input);
            });
        } else {
            var btn = input.parents('.btn-toggle');
            updateBtnState(btn, input);
        }
    });
	var sidebarHide=$.cookie('sidebarHide');

    $('#sidebar-toggle').click(function(){
		$(this).find('i').toggleClass('icon-chevron-left icon-chevron-right');
		$('.j-sidebar-container').slideToggle().toggleClass('span3');
		$('#j-main-container').toggleClass('span12 span9');
		if (sidebarHide=="1") sidebarHide="0";
		else sidebarHide="1";
		$.cookie('sidebarHide', sidebarHide);
		// console.log(sidebarHide);
	});
	if (sidebarHide == "1") {
		// hide it
		sidebarHide = "0";
		 $('#sidebar-toggle').trigger('click');
	}
	$('.hasTooltip').tooltip();
	var timeout,lastSearch = null;
	$('#search').on('keypress input',function(){
		// TODO add ajax keyup on search
		var value = this.value;
		if(value != lastSearch) {
			// Save the "last" value
			lastSearch = value;
			// Delay before search in the case of typing
			if(timeout) { clearTimeout(timeout); }
			// Start new time out
			timeout = setTimeout(function() {
				// Do the search
				Joomla.ajaxSearch(this);
				// console.warn("Doing search for " + value + ", time waited");
				// Process....
			},'600');
		}
	});
	$( "#j-main-container" ).on("click", "ul.pagination-list a",function(e){

		var form = $('#adminForm'),url = $(this).attr('href');
		// administrator pagination is different as front shop
		if (url.length > 2) {
			form.find('input[name="limitstart"]').remove();
			e.preventDefault();
			inputs = form.serialize();
			$.post( url, inputs+'&format=raw',
				function(html, status) {
					$('#results').html(html);
				
				}
			).fail(function() {
				location.reload();
			});
			return false;
		}

	});

    // $('.btn-toggle').each(function() {
        // var btn = $(this),
            // input = btn.find('input');
        // updateBtnState(btn, input);
    // });
});
// TODO toggle sidebar
// $('a.toggles').click(function() {
    // $('a.toggles i').toggleClass('icon-chevron-left icon-chevron-right');

    // $('#sidebar').animate({
        // width: 'toggle'
    // }, 100);
    // $('#content').toggleClass('span12 span9');
// });

// $('#content').toggle(

// function() {
    // $(this).css({
        // 'margin-left': '0'
    // });
// }, function() {
    // $(this).css({
        // 'margin-left': '2.127659574%'
    // });
// });
