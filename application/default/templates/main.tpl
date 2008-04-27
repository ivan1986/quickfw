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
А сюда у нас подключен модуль test</br>
{outHead name='h'}

{addJS file='file.js'}
{addJS file='file.js'}
{addJS file='file2.js'}
{addJS file='file1.js'}
{addCSS file='file.css'}
{addCSS file='file.css'}

{include file="module:test#aaa"}<br />
{*include file="module:test/index//a/1/b/2"}<br />
{include file="module:test(123,55,'1\'1','1,1','$ttt')"}<br />
{include file="module:test.index()"}<br />
{include file="module:QFW/Catlist"*}<br />
А вот тут он закончился
{getHead name='123'}<script></script>{/getHead}
</body>
</html>