$(function() {
	cb = $('.scaffoldTable .multiSelect');
	if (cb.length == 0)
		return;
	cb.click(function(){
		$("input[name^=id]", $('.scaffoldTable')).attr("checked", $(this).attr("checked"));
	});
});

