<!-- Pageslist -->
<div class="pageslist">
	<?php for($page=1;$page<=$pager['all'];$page++) {?>
		<?php if ($page==$pager['c']) {?>
			<b><?php echo $page?></b>
		<?php } else {?>
			<a href="<?php echo str_replace('$',$page,$pager['url'])?>"><?php echo $page?></a>
		<?php } ?>
	<?php } ?>
</div>
<!-- /Pageslist -->
