<!-- Pageslist -->
<div class="pageslist">
	<?php if ($pager['c']!=1) {?><a href="<?php echo str_replace('$',$pager['c']-1,$pager['url'])?>">&larr; пред.</a><?php } ?>
	<?php if ($pager['all']<=8) {?>
		<?php for($page=1;$page<=$pager['all'];$page++) {?>
			<?php if ($page==$pager['c']) {?><b><?php echo $page?></b>
			<?php } else {?>
			<a href="<?php echo str_replace('$',$page,$pager['url'])?>"><?php echo $page?></a>
			<?php }?>
		<?php }?>
	<?php } else {?>
		<?php for($page=1;$page<4;$page++) {?>
			<?php if ($page==$pager['c']) {?><b><?php echo $page?></b>
			<?php } else {?>
			<a href="<?php echo str_replace('$',$page,$pager['url'])?>"><?php echo $page?></a>
			<?php }?>
		<?php }?>
		<?php if ($pager['c']>5) {?>&hellip;<?php } ?>
		<?php if ($pager['c']>4 && $pager['c']<$pager['all']-1) {?><a href="<?php echo str_replace('$',$pager['c']-1,$pager['url'])?>"><?php echo $pager['c']-1?></a><?php } ?>
		<?php if ($pager['c']>3 && $pager['c']<$pager['all']-2) {?><b><?php echo $pager['c']?></b><?php } ?>
		<?php if ($pager['c']>2 && $pager['c']<$pager['all']-3) {?><a href="<?php echo str_replace('$',$pager['c']+1,$pager['url'])?>"><?php echo $pager['c']+1?></a><?php } ?>
		<?php if ($pager['c']<$pager['all']-4) {?>&hellip;<?php } ?>
		<?php for($page=$pager['all']-2;$page<=$pager['all'];$page++) {?>
			<?php if ($page==$pager['c']) {?><b><?php echo $page?></b>
			<?php } else {?>
			<a href="<?php echo str_replace('$',$page,$pager['url'])?>"><?php echo $page?></a>
			<?php }?>
		<?php }?>
	<?php }?>
	<?php if ($pager['c']!=$pager['all']) {?><a href="<?php echo str_replace('$',$pager['c']+1,$pager['url'])?>">след. &rarr;</a><?php } ?>
</div>
<!-- /Pageslist -->
