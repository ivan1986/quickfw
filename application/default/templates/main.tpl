<html>
<head><title>Main.tpl</title></head>
<body>
{outHead name='h'}
Это файл Main.tpl</br>
Это урл {siteUrl url='test'}
{$content}
{getHead name='h'}HEAD{/getHead}
А сюда у нас подключен модуль test</br>

{addJS file='file.js'}
{addJS file='file.js'}
{addCSS file='file.css'}
{addCSS file='file.css'}

{include file="module:test"}<br />
{include file="module:test/index//a/1/b/2"}<br />
{include file="module:test(123,55,'1\'1','1,1','$ttt')"}<br />
А вот тут он закончился
{getHead name='h'}HEAD{/getHead}
</body>
</html>