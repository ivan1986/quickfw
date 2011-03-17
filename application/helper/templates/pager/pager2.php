<!-- Pageslist -->
<div class="pageslist">
	<?php $ps = 10;
		$first = $pager['c'] - $ps;
		if($first<1) $first = 1;
		$last = $pager['c'] + $ps;
		if($last>$pager['all']) $last =  $pager['all'];
		
		if ($first>1):?>
			<a href="<?php echo str_replace('$',1,$pager['url'])?>">первая</a>
		<?php endif; ?>
		
		<?php if ($pager['c']>1):?>
			<a href="<?php echo str_replace('$',$pager['c']-1,$pager['url'])?>">предыдущая</a>
		<?php endif;
		
		for($page=$first;$page<=$last;$page++) {?>
			<?php if ($page==$pager['c']) {?>
				<b><?php echo $page?></b>
			<?php } else {?>
				<a href="<?php echo str_replace('$',$page,$pager['url'])?>"><?php echo $page?></a>
			<?php } ?>
		<?php }

		if ($pager['c']<$pager['all']) {?>
			<a href="<?php echo str_replace('$',$pager['c']+1,$pager['url'])?>">следующая</a>
		<?php } ?>
</div>
<!-- /Pageslist -->
