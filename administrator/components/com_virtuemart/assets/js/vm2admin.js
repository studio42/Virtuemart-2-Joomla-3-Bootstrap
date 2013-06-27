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
                            $().removeClass("vmicon-16-remove");
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

        tabs:function (cookie) {
            var tabscount = this.find('div.tabs').length;
            if ($.cookie(cookie) == null || cookie == "product0" || tabscount == 1) var idx = 0;
            else var idx = $.cookie(cookie);
            if (idx == null) idx = 0;
            var options = { path:'/', expires:2},
                list = '<ul id="tabs">';

            var tabswidth = 100 / tabscount;
            this.find('div.tabs').each(
                function (i) {
                    list += '<li style="width:' + tabswidth + '%"><span>' + $(this).attr('title') + '</span></li>';
                    $(this).removeAttr('title');
                }
            );
            this.prepend(list + '</ul>');
            this.children('div').hide();
            // select & open menu
            var li = $('#tabs li'),
                div = this.children('div');
            li.eq(idx).addClass('current');
            div.eq(idx).slideDown(1000);

            li.click(
                function () {
                    if ($(this).not(".current")) {
                        var idx = li.index(this);
                        oldIndex = $(this).addClass("current").siblings('li.current').removeClass("current").index();
                        if (oldIndex !== -1) {
                            if (cookie !== "") $.cookie(cookie, idx, options);
                            div.eq(idx).slideDown();
                            div.eq(oldIndex).slideUp();
                        }
                    }
                }
            );
            return this;
        },
        accordeon:function () {
            var idx = $.cookie('accordeon'),
                options = { path:'/', expires:2},
                div = this.children('div') ,
                h3 = this.children('h3'),
                A = this.find('.menu-list a');
            if (idx == null) idx = 0;
            div.hide();
            h3.eq(idx).addClass('current');
            div.eq(idx).show();

            h3.click(
                function () {
                    var menu = $(this);
                    if (menu.not(".current")) {
                        menu.siblings('h3.current').removeClass("current").next().slideUp(200);
                        menu.addClass("current").next().slideDown(200);
                        $.cookie('accordeon', h3.index(this), options);
                    }
                }
            );
            A.click(
                function () {
                    $.cookie('vmapply', '0', options);
                }
            );
        },
        media:function (mediatype, total) {
            var page = 0,
                max = 24,
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

            jQuery("#fancybox-title").delegate(".media-pagination span", "click", function (event) {
                var newPage = $(this).text();
                display(newPage);
                event.preventDefault();
            });
            container.delegate("a.vm_thumb", "click", function (event) {
                jQuery.fancybox({
                    "type":"image",
                    "titlePosition":"inside",
                    "title":this.title,
                    "href":this.href
                });
                event.preventDefault();
            });
            jQuery("#media-dialog").delegate(".vm_thumb_image", "click", function (event) {
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
                    jQuery(this).clone().appendTo(container).unbind("click").append('<div class="vmicon vmicon-16-remove" title="remove"></div><div class="edit-24-grey" title="' + vm2string.editImage + '"><div>');
                    that.hide().fadeIn();
                }

            });

            jQuery("#admin-ui-tabs").delegate("div.vmicon-16-remove", "click", function () {
                jQuery(this).closest(".vm_thumb_image").fadeOut("500", function () {
                    jQuery(this).remove()
                });
            });
            jQuery("#admin-ui-tabs").delegate("span.vmicon-16-remove", "click", function () {
                jQuery(this).closest(".removable").fadeOut("500", function () {
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

            container.delegate(".edit-24-grey", "click", function () {

                var data = jQuery(this).parent().find("input").val();
                jQuery.getJSON("index.php?option=com_virtuemart&view=media&task=viewJson&format=json&virtuemart_media_id=" + data,
                    function (datas, textStatus) {
                        if (datas.msg == "OK") {
                            jQuery("#vm_display_image").attr("src", datas.file_root + datas.file_url);
                            jQuery("#vm_display_image").attr("alt", datas.file_title);
                            jQuery("#file_title").html(datas.file_title);
							var lang = datas.file_lang.split(',');
							jQuery("#vmlangimg").val(lang).trigger("liszt:updated");
                            if (datas.published == 1) jQuery("#adminForm [name=media_published]").attr('checked', true);
                            else jQuery("#adminForm [name=media_published]").attr('checked', false);
                            if (datas.file_is_downloadable == 0) {
                                jQuery("#media_rolesfile_is_displayable").attr('checked', true);
                                //jQuery("#adminForm [name=media_roles]").filter("value='file_is_downloadable'").attr('checked', false);
                            }
                            else {
                                //jQuery("#adminForm [name=media_roles]").filter("value='file_is_displayable'").attr('checked', false);
                                jQuery("#media_rolesfile_is_downloadable").attr('checked', true);
                            }
                            jQuery("#adminForm [name=file_title]").val(datas.file_title);
                            jQuery("#adminForm [name=file_description]").val(datas.file_description);
                            jQuery("#adminForm [name=file_meta]").val(datas.file_meta);
                            jQuery("#adminForm [name=file_url]").val(datas.file_url);
                            jQuery("#adminForm [name=file_url_thumb]").val(datas.file_url_thumb);
                            jQuery("[name=active_media_id]").val(datas.virtuemart_media_id);
                            if (datas.file_url_thumb !== "undefined") {
                                jQuery("#vm_thumb_image").attr("src", datas.file_root + datas.file_url_thumb);
                            }
                            else {
                                jQuery("#vm_thumb_image").attr("src", "");
                            }
                        } else jQuery("#file_title").html(datas.msg);
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
                        jQuery.getJSON("index.php?option=com_virtuemart&view=media&task=viewJson&format=json&mediatype=" + mediatype + "&start=" + start,
                            function (data) {
                                if (data.imageList != "ERROR") {
                                    display.cache[start] = data.imageList;
                                    jQuery("#media-dialog").html(display.cache[start]);
                                    jQuery(".page").text("Page(s) " + (start + 1));
                                } else {
                                    jQuery(".page").text("No  more results : Page(s) " + (start + 1));
                                }
                            }
                        );
                    } else jQuery("#media-dialog").html(display.cache[start]);
                    page = this.oldPage = this.page;
                    $('.media-pagination').children().removeClass('media-page-selected');
                    $('.media-pagination').children().eq(start + 3).addClass('media-page-selected');
                }
            }
        },
        tips:function (image) {
            var xOffset = -20; // x distance from mouse
            var yOffset = 10; // y distance from mouse
            tip = this;
            tip.unbind().hover(
                function (e) {
                    tip.t = this.title;
                    this.title = '';
                    tip.top = (e.pageY + yOffset);
                    tip.left = (e.pageX + xOffset);
                    $('body').append('<p id="vtip"><img id="vtipArrow" /><B>' + $(this).html() + '</B><br/ >' + tip.t + '</p>');
                    $('#vtip #vtipArrow').attr("src", image);
                    $('#vtip').css("top", tip.top + "px").css("left", tip.left + "px").fadeIn("slow");
                },
                function () {
                    this.title = tip.t;
                    $("#vtip").fadeOut("slow").remove();
                }
            ).mousemove(
                function (e) {
                    tip.top = (e.pageY + yOffset);
                    tip.left = (e.pageX + xOffset);
                    $("#vtip").css("top", tip.top + "px").css("left", tip.left + "px");
                }
            ).mousedown(
                function (e) {
                    this.title = tip.t;
                    $("#vtip").fadeOut("slow").remove();
                }
            ).mouseup(
                function (e) {
                    this.title = tip.t;
                    $("#vtip").fadeOut("slow").remove();
                }
            );

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
