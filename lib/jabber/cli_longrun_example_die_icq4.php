<?php
// activate full error reporting
//error_reporting(E_ALL & E_STRICT);
/*
2do

Офлайн-сообщения
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
		'message' => 'Я Вам как Linux скажу, только Вы не обижайтесь. Этот чувак, конечно, получит Ваше сообщение, но лучше бы Вам общаться с ним через Jabber. А то не ровен час - аська сдохнет, старушка свое отжила. Его JID: %s.'."\n\n".'Искренне Ваш, Booboobook.',
	),
	'timeout' => 300,
	'timeout2' => 1200,
	'exclusion' => array(
		'401678098@icq.skovpen.org',
	),
);

/****************************************/

include 'XMPPHP/XMPP.php';
include 'XMPPHP/Log.php';

class ICQ_Die_Bot {
	protected $conn;
	protected $config;
	protected $my_jid;
	protected $last_rcv;
	protected $last_sent;
	protected $priority;
	protected $transports;

	public function __construct($config) {
		$this->config = $config;
		if (!isset($this->config['connection']['host'])) {
			$this->config['connection']['host'] = $this->config['connection']['server'];
		}

		if (!isset($this->config['connection']['port'])) {
			$this->config['connection']['port'] = 5222;
		}

		#Use XMPPHP_Log::LEVEL_VERBOSE to get more logging for error reports
		#If this doesn't work, are you running 64-bit PHP with < 5.2.6?
		$this->conn = new XMPPHP_XMPP($this->config['connection']['host'], $this->config['connection']['port'], $this->config['connection']['user'], $this->config['connection']['password'], $this->config['messages']['resource'], $this->config['connection']['server'], $printlog=true, $loglevel=XMPPHP_Log::LEVEL_INFO);

		$this->my_jid = $this->config['connection']['user'].'@'.$this->config['connection']['server'];

		$this->last_rcv = array();
		$this->last_sent = array();
		$this->priority = NULL;
		$this->transports = array();
	}

	protected function setPresence($pl) {
		$_jid = explode('/', $pl['from']);
		if (strpos($_jid[0], '@') === FALSE){
			if ($pl['show']!=='unavailable') {
				$this->transports[$_jid[0]] = $_jid[0];
			} else {
				unset($this->transports[$_jid[0]]);
			}
		}
		if (($_jid[0] !== $this->my_jid) || ($_jid[1]===$this->config['messages']['resource'])) {
			return;
		}
		$roster = $this->conn->roster->getRoster();
		$cnt = 0;
		if (isset($roster[$this->my_jid]['presence'])) {
			$max_priority = -128;
			foreach ($roster[$this->my_jid]['presence'] as $resource => $item) {
				if ($resource !== $this->config['messages']['resource'] && $item['priority'] > $max_priority) {
					$max_priority = $item['priority'];
					$cnt++;
				}
			}
		}
		if ($cnt>0) {
			if ($this->priority !== $max_priority) {
				$this->priority = $max_priority;
				$this->conn->presence($this->config['messages']['status'], 'available', null, 'available', $max_priority);
			}
		} else {
			if (!is_null($this->priority)) {
				$this->conn->disconnect();
			}
		}
	}

	public function start() {
		try {
			$this->conn->connect();
			while(!$this->conn->isDisconnected()) {
				$payloads = $this->conn->processUntil(array('message', 'presence', 'end_stream', 'session_start'));
				foreach($payloads as $event) {
					$pl = $event[1];
					switch($event[0]) {
						case 'message':
							$from = explode('/', $pl['from']);
							if ($from[0] === $this->my_jid) {
								if($pl['body'] == 'quit') {
									$this->conn->disconnect();
								}
								if($pl['body'] == 'break') {
									$this->conn->send("</end>");
								}
								if ($pl['body'] === 'roster') {
									print($pl['body']."\n");
									print_r($this->conn->roster->getRoster());
									$this->conn->message($pl['from'], print_r($this->conn->roster->getRoster(), true), $pl['type']);
								}
								if ($pl['body'] === 'presence') {
									$roster = $this->conn->roster->getRoster();
									$this->conn->message($pl['from'], print_r($roster[$this->my_jid]['presence'], true), $pl['type']);
								}
							}
							foreach ($this->transports as $transport) {
								if ((substr($pl['from'], $len=-strlen($transport), -$len) === $transport) && !in_array($pl['from'], $this->config['exclusion'])) {
									if (!isset($this->last_sent[$pl['from']]) || (time() - $this->last_rcv[$pl['from']] > $this->config['timeout']) || (time() - $this->last_sent[$pl['from']] > $this->config['timeout2'])) {
										$this->conn->message($pl['from'], sprintf($this->config['messages']['message'], $this->my_jid), $pl['type']);
										$this->last_sent[$pl['from']] = time();
									}
									$this->last_rcv[$pl['from']] = time();
									break;
								}
							}
						break;
						case 'presence':
							print "Presence: {$pl['from']} [{$pl['show']}] {$pl['status']}\n";
							$this->setPresence($pl);
						break;
						case 'session_start':
							print "Session Start\n";
							$this->conn->getRoster();
							$this->conn->presence($this->config['messages']['status']);
						break;
					}
				}
			}
		} catch(XMPPHP_Exception $e) {
			die($e->getMessage());
		}
	}
}

$bot = new ICQ_Die_Bot($config);
$bot->start();