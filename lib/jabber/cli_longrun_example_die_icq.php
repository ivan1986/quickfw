<?php
// activate full error reporting
//error_reporting(E_ALL & E_STRICT);
/*
2do

Смена приоритета (при смене и при входе)
Таймаут
Таймаут 2
Офлайн-сообщения
Автоопределение транпортов и названий
*/


$config = array(
	'connection' => array (
		'user' => 'texhapb',
		'password' => 'TocDen90',
		'server' => 'jabber.ru',
//		'host' => '',
//		'port' => '',
	),
	'messages' => array(
		'resource' => 'ICQ die bot',
		'status' => 'ICQ Must Die!',
		'message' => 'Я Вам как Linux скажу, только Вы не обижайтесь. Этот чувак, конечно, получит Ваше сообщение, но лучше бы Вам общаться с ним через Jabber. А то не ровен час - аська сдохнет. Его JID: texhapb@jabber.ru.'."\n\n".'Искренне Ваш, Booboobook.',
	),
	'timeout' => 600,
	'timeout2' => 3600,
	'transports' => array('icq.skovpen.org', 'mrim.jabber.ru', 'icq.jabber.spbu.ru'),//!!!
	'exclusion' => array(
		'401678098@icq.skovpen.org',
	),
);

/****************************************/

if (!isset($config['connection']['host'])) {
	$config['connection']['host'] = $config['connection']['server'];
}

if (!isset($config['connection']['port'])) {
	$config['connection']['port'] = 5222;
}
//!!!var_dump($config);

$my_jid = $config['connection']['user'].'@'.$config['connection']['server'];

$last_rcv = array();
$last_sent = array();

include 'XMPPHP/XMPP.php';
include 'XMPPHP/Log.php';

#Use XMPPHP_Log::LEVEL_VERBOSE to get more logging for error reports
#If this doesn't work, are you running 64-bit PHP with < 5.2.6?
$conn = new XMPPHP_XMPP($config['connection']['host'], $config['connection']['port'], $config['connection']['user'], $config['connection']['password'], $config['messages']['resource'], $config['connection']['server'], $printlog=true, $loglevel=XMPPHP_Log::LEVEL_INFO);

try {
	$conn->connect();
	while(!$conn->isDisconnected()) {
		$payloads = $conn->processUntil(array('message', 'presence', 'end_stream', 'session_start'));
		foreach($payloads as $event) {
			$pl = $event[1];
			switch($event[0]) {
				case 'message':
/*					print "---------------------------------------------------------------------------------\n";
					print "Message from: {$pl['from']}\n";
					if($pl['subject']) print "Subject: {$pl['subject']}\n";
					print $pl['body'] . "\n";
					print "---------------------------------------------------------------------------------\n";
					if ($pl['body'] == 'превед')
						$conn->message($pl['from'], $body="кросафчег", $type=$pl['type']);
					else
						$conn->message($pl['from'], $body="Thanks for sending me \"{$pl['body']}\".", $type=$pl['type']);
					if($pl['body'] == 'quit') $conn->disconnect();
					if($pl['body'] == 'break') $conn->send("</end>");*/
					foreach ($config['transports'] as $transport) {
						echo($pl['from']."\n".$transport."\n");
						if ((substr($pl['from'], $len=-strlen($transport), -$len) === $transport) && !in_array($pl['from'], $config['exclusion']) 
						&& (!isset($last_rcv[$pl['from']]) || (time() - $last_rcv[$pl['from']] > $config['timeout']))//!!!
						) {
							$conn->message($pl['from'], $config['messages']['message'], $pl['type']);
							$last_rcv[$pl['from']] = time();
							echo(time()."\n");
							break;
						}
					}
				break;
				case 'presence':
					print "Presence: {$pl['from']} [{$pl['show']}] {$pl['status']}\n";
					//!!!var_dump($pl);
					//$conn->
				break;
				case 'session_start':
					print "Session Start\n";
					$conn->getRoster();
					$conn->presence($config['messages']['status']);
				break;
			}
		}
	}
} catch(XMPPHP_Exception $e) {
	die($e->getMessage());
}
