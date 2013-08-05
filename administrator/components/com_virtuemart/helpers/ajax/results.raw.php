<?php defined ( '_JEXEC' ) or die ();
//script to add in the results ajax list render
 ?>
<script type="text/javascript">
	var tips = jQuery("#results .hasTooltip,#results .jgrid");
	if (tips.length) tips.tooltip({"html":true});
	jQuery('td.order .jgrid').addClass('btn btn-mini');
	<?php echo $scripts ?>
</script>
