<?php

class Rss
{

	public function parse($data)
	{
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->loadXML($data);

		$result=array();
		$xpath = new DOMXPath($dom);
		$query='/rss/channel/*[name()!=\'item\']';
		foreach ($xpath->query($query) as $entry)
			$result[$entry->nodeName]=$entry->nodeValue;

		$result['news']=array();
		$query='/rss/channel/item';
		foreach ($xpath->query($query) as $entry)
		{
			$n=array();
			for($i=0;$i<$entry->childNodes->length;$i++)
				$n[$entry->childNodes->item($i)->nodeName]=$entry->childNodes->item($i)->nodeValue;
			$result['news'][]=$n;
		}

		return $result;
	}

}
?>