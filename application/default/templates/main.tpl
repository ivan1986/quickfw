<html>
<head><title>Main.tpl</title></head>
<body>
{outHead name='h'}
Это файл Main.tpl</br>
Это урл {siteUrl url='test'}
<br />
{$content}
<br />
{getHead name='h'}HEAD{/getHead}
{outHead name='h'}

{addJS file='file.js'}
{addJS file='file.js'}
{addJS file='file2.js'}
{addJS file='file1.js'}
{addCSS file='file.css'}
{addCSS file='file1.css'}

А сюда у нас подключен блок test</br>
{include file="block:test"}<br />
{*include file="block:test/index//a/1/b/2"}<br />
{include file="block:test(123,55,'1\'1','1,1','$ttt')"}<br />
{include file="block:test.index()"*}<br />
А вот тут он закончился
{getHead name='123'}<script></script>{/getHead}
</body>
</html>