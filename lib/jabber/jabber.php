<?php

require_once 'XMPPHP/XMPP.php';

QFW::$libs['jabber'] = new XMPPHP_XMPP(
	QFW::$config['jabber']['host'], QFW::$config['jabber']['port'],
	QFW::$config['jabber']['user'], QFW::$config['jabber']['pass'],
	QFW::$config['jabber']['resource'], QFW::$config['jabber']['server'],
	!QFW::$config['QFW']['release'], XMPPHP_Log::LEVEL_INFO);

?>