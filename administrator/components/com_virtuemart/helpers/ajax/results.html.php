<?php defined ( '_JEXEC' ) or die ();
	$jsons = array ();
	$jsons['alt'] = array (
		'0' =>JText::_('JNO'),
		'1' =>JText::_('JYES'),
		'publish' =>JText::_('COM_VIRTUEMART_PUBLISHED'),
		'unpublish' =>JText::_('COM_VIRTUEMART_UNPUBLISHED')
		) ;
	$jsons['title'] = array ( 
		'0' =>JText::_('COM_VIRTUEMART_ENABLE_ITEM'),
		'1' => JText::_('COM_VIRTUEMART_DISABLE_ITEM'),
		'publish' => JText::_('COM_VIRTUEMART_UNPUBLISH_ITEM'),
		'unpublish' => JText::_('COM_VIRTUEMART_PUBLISH_ITEM'),
		);
	$jsons['img'] = array (
		'ok',
		'remove',
		'unpublish' => 'unpublish',
		'publish' => 'publish'
		);

 ?>
<script type="text/javascript">
<!--
	Joomla.ajaxSearch = function(el) {
		
		var id = jQuery(el).attr('id'),form = jQuery('#adminForm'),url = form.attr('action');
		if (id === 'searchreset') {
			jQuery('#filter-bar input').val('');
		}
		
		inputs = form.serialize();
		jQuery.post( url, inputs+'&format=raw',
			function(html, status) {
				jQuery('#results').html(html);
			
			}
		);
		return false;
	}
	jQuery('#searchreset').removeAttr('onClick');
	jQuery('#filter-bar button').click( function (e) {
		e.preventDefault();
		Joomla.ajaxSearch(this);
		return false;
	});
	Joomla.tableOrdering = function(order, dir, task, form) {
		// if (typeof(form) === 'undefined') {
			form = document.getElementById('adminForm');
			/**
			 * Added to ensure Joomla 1.5 compatibility
			 */
			// if(!form){
				// form = document.adminForm;
			// }
		// }
		xref = form.task.value.substring(0,8);
		if (xref != 'massxref') form.task.value = task;
		console.log(xref,task,form.task.value);
		form.filter_order.value = order;
		form.filter_order_Dir.value = dir;
		Joomla.ajaxSearch(form);
		return false;
		// Joomla.submitform(task, form);
	}
	Joomla.submitform = function(pressbutton) {
		if (pressbutton) {
			document.adminForm.task.value = pressbutton;
		}
		if (typeof document.adminForm.onsubmit == "function") {
			document.adminForm.onsubmit();
		}
		if (typeof document.adminForm.fireEvent == "function") {
			document.adminForm.fireEvent('submit');
		}
		if (!pressbutton) { 
			Joomla.ajaxSearch(document.adminForm);
			return false;
		}
		document.adminForm.submit();
	}
	Joomla.taskJson = function(el, id) {

		var text = <?php echo json_encode($jsons) ?>,
			$el = jQuery(el),
			task = $el.data('task');
			f = document.adminForm,
			oldTask = f.task.value,
			cb = f[id],
			$img = $el.children('i'),
			// src = $img.attr('src');
			form = jQuery('#adminForm'),
			url = form.attr('action'),
			val = task.charAt( task.length-1 ),
			valNew = 0;
		//get the toggle value
		if (task == 'unpublish' || task == 'publish') {
			val = task
			if (task == 'unpublish' ) valNew = 'publish';
			else valNew = 'unpublish';
		} else {
			if (val == 0 ) valNew = 1;
		}
		if (cb) {
			for (var i = 0; true; i++) {
				var cbx = f['cb'+i];
				if (!cbx)
					break;
				cbx.checked = false;
			} // for
			cb.checked = true;
			f.boxchecked.value = 1;
		}
		$el.tooltip('destroy');
		$el.attr('data-original-title', text.title[val] ).tooltip();
		f.task.value= task;

		inputs = form.serialize();
		jQuery.post( url, inputs+'&format=json',
			function(data, status) {
				// console.log(data);
				var $alert =jQuery('<div class="alert '+data.type+' fade in">'+
					'<button type="button" class="close" data-dismiss="alert">&times;</button>'+
					data.message+' ('+text.alt[val]+')'+
					'</div>');
				jQuery('#results').before($alert);
				$alert.alert().bind('closed', function () {
					clearTimeout(t);
				});
				var t=setTimeout(function(){$alert.alert('close')},5000);
				$el.data('task', task.replace(val,valNew) );
				if (data.type !== 'alert-error')
				$img.toggleClass('icon-'+text.img[val]+' icon-'+text.img[valNew]); //attr('src', src.replace(text.img[val],text.img[valNew]) );
				f.task.value = oldTask;
			}
			, "json" );
		return false;
	}
-->
</script>