<?php defined ( '_JEXEC' ) or die ();
//script to add in the results ajax list render
 ?>
<script type="text/javascript">
	var orderBtn = jQuery('td.order .jgrid');
	jQuery("#results .hasTooltip,#results .jgrid").tooltip({"html":true}),
	jQuery('.btn-group label').addClass('btn');
	orderBtn.addClass('btn btn-mini').children('.uparrow').toggleClass('uparrow icon-uparrow');
	orderBtn.children('.downarrow').toggleClass('downarrow icon-downarrow');
	<?php echo $scripts ?>
</script>
