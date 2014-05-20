<?php
// Access
defined('_JEXEC') or die();

// Calculating Categories Per Row
$perRow = VmConfig::get('homepage_categories_per_row', 4);
$spanClass = 'span'.floor (12/$perRow );
$cCount = 1 ;
$cTotal = count($this->categories);
?>

<div class="category-view">

	<h4><?php echo JText::_('COM_VIRTUEMART_CATEGORIES') ?></h4>
	<ul class="thumbnails">
	<?php
	// Start the Output
	foreach ($this->categories as $category) {

		// Category Link
		$caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id, FALSE);
		?>
		<li class="<?php echo $spanClass ?>">
		  <div class="thumbnail">
			<div class="text-center">
				<?php if (!empty($category->images)) { ?>
					<a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>">
						<?php echo $category->images[0]->displayMediaFull("", false); ?>
					</a>
					<?php
					}
				?>
			</div>
			<div class="caption">
				<a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>">
					<h5><?php echo $category->category_name ?></h5>
				</a>
			</div>
		  </div>
		</li>
		<?php 
		// see if whe must add a new line
		if ($cCount == $perRow && $cTotal>0 ) { ?>
			</ul>
			<ul class="thumbnails">
			<?php 
			$cCount =0;
		}
		$cTotal--;
		$cCount++;
	  } ?>
	</ul>
</div>