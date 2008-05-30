<!-- Pageslist -->
<div class="pageslist">
	{if $pager.c!=1}<a href="{$pager.url|replace:'$':$pager.c-1}">&larr; пред.</a>{/if}
	{if $pager.all<=8}
		{section name="page" start=$pager.b+1 loop=$pager.all+1 step=1}
			{if $smarty.section.page.index==$pager.c}
			<b>{$smarty.section.page.index}</b>
			{else}
			<a href="{$pager.url|replace:'$':$smarty.section.page.index}">{$smarty.section.page.index}</a>
			{/if}
		{/section}
	{else}
		{section name="page" start=1 loop=4 step=1}
			{if $smarty.section.page.index==$pager.c}
			<b>{$smarty.section.page.index}</b>
			{else}
			<a href="{$pager.url|replace:'$':$smarty.section.page.index}">{$smarty.section.page.index}</a>
			{/if}
		{/section}
		{if $pager.c>5}&hellip;{/if}
		{if $pager.c>4 and $pager.c<$pager.all-1}<a href="{$pager.url|replace:'$':$pager.c-1}">{$pager.c-1}</a>{/if}
		{if $pager.c>3 and $pager.c<$pager.all-2}<b>{$pager.c}</b>{/if}
		{if $pager.c>2 and $pager.c<$pager.all-3}<a href="{$pager.url|replace:'$':$pager.c+1}">{$pager.c+1}</a>{/if}
		{if $pager.c<$pager.all-4}&hellip;{/if}
		{section name="page" start=$pager.all-2 loop=$pager.all+1 step=1}
			{if $smarty.section.page.index==$pager.c}
			<b>{$smarty.section.page.index}</b>
			{else}
			<a href="{$pager.url|replace:'$':$smarty.section.page.index}">{$smarty.section.page.index}</a>
			{/if}
		{/section}
	{/if}
	{if $pager.c!=$pager.all}<a href="{$pager.url|replace:'$':$pager.c+1}">след. &rarr;</a>{/if}
</div>
<!-- /Pageslist -->
