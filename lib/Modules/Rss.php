<?php

class Rss
{

	public function parse($data,$count=-1)
	{
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->loadXML($data,LIBXML_NOERROR | LIBXML_NOWARNING);

		$result=array();
		$xpath = new DOMXPath($dom);
		$query='/rss/channel/*[name()!=\'item\']';
		foreach ($xpath->query($query) as $entry)
			$result[$entry->nodeName]=$entry->nodeValue;

		if (isset($result['image']))
		{
			$result['image']=array();
			$query='/rss/channel/image/*';
			foreach ($xpath->query($query) as $entry)
				$result['image'][$entry->nodeName]=$entry->nodeValue;
		}

		$result['news']=array();
		$query='/rss/channel/item';
		$cnt=0;
		foreach ($xpath->query($query) as $entry)
		{
			if ($count>=0 && $cnt++>=$count)
				break;
			$n=array();
			for($i=0;$i<$entry->childNodes->length;$i++)
				$n[$entry->childNodes->item($i)->nodeName]=$entry->childNodes->item($i)->nodeValue;
			$result['news'][]=$n;
		}

		return $result;
	}

}
?>