<?php

require_once "Auth/OpenID/Consumer.php";
require_once "Auth/OpenID/FileStore.php";
require_once "Auth/OpenID/SReg.php";
require_once "Auth/OpenID/PAPE.php";

require_once QFWPATH.'/QuickFW/Auth.php';

class OidController extends QuickFW_Auth
{

	public function __construct()
	{
		QFW::$view->mainTemplate='';
		$this->session();
	}

	public function indexAction($clean = false)
	{
		if ($clean == 1)
		{
			unset($_SESSION['openID']);
			QFW::$router->redirect(Url::A());
		}
		if (!empty($_SESSION['openID']))
		{
			var_dump($_SESSION['openID']);
		}
		?>
<form method="get" action="<?php echo Url::C('try') ?>">
<input type="text" name="openid_identifier" value="<?php echo QFW::$view->esc(
	'http://quickfw.ib.br/openid/server.php/idpage?user=ivan') ?>" />
<input type="submit" value="Verify" />
</form>
<a href="<?php echo Url::A('1') ?>">выйти</a>
		<?php
	}

	/* array(
		'fullname' => 'Full Name',
		'nickname' => 'Nickname',
		'dob' => 'Date of Birth',
		'email' => 'E-mail Address',
		'gender' => 'Gender',
		'postcode' => 'Postal Code',
		'country' => 'Country',
		'language' => 'Language',
		'timezone' => 'Time Zone',
	); */

	public function tryAction()
	{
		if (empty($_GET['openid_identifier']))
			$this->err('Expected an OpenID URL');
		$consumer = $this->getConsumer();
		$auth_request = $consumer->begin($_GET['openid_identifier']);
		if (!$auth_request)
        	$this->err('Authentication error; not a valid OpenID');

		$sreg_request = Auth_OpenID_SRegRequest::build(
			array('nickname'), // Required
			array('fullname', 'email') // Optional
			);
		if ($sreg_request)
			$auth_request->addExtension($sreg_request);
		
		$redirect = $auth_request->shouldSendRedirect();

		$server = $this->getServer();
		$trustRoot = $server.Url::C('');
		echo $trustRoot;

		$query = $redirect ? 
			$auth_request->redirectURL($server.Url::C(''), $server.Url::C('finish')) :
			$auth_request->htmlMarkup($server.Url::C(''),
				$server.Url::C('finish'), false, array('id' => 'openid_message'));
        if (Auth_OpenID::isFailure($query))
            $this->err("Could not redirect to server: " . $query->message);
		
		if ($redirect)
			QFW::$router->redirect((string)$query);
		else
			die($query);
	}

	public function finishAction()
	{
		if (empty($_GET['openid_identity']))
			$this->err('Expected an OpenID URL');
		$consumer = $this->getConsumer();
		$response = $consumer->complete($this->getServer().Url::A());
		if ($response->status == Auth_OpenID_CANCEL)
			$this->err('Verification cancelled.');
		else if ($response->status == Auth_OpenID_FAILURE)
			$this->err('OpenID authentication failed: ' . $response->message);
		else if ($response->status == Auth_OpenID_SUCCESS)
		{
			$openid = $response->getDisplayIdentifier();

			$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
			$sreg = $sreg_resp->contents();

			$_SESSION['openID'] = $sreg + array('id' => $openid);
			QFW::$router->redirect(Url::C('index'));
		}
	}

	private function err($msg)
	{
		die($msg);
	}

	private function getConsumer()
	{
		$path = TMPPATH.'/openid';
		if (!is_dir($path))
			mkdir($path);
		$store = new Auth_OpenID_FileStore($path);
		return new Auth_OpenID_Consumer($store);
	}

	private function getServer()
	{
		return 'http://'.$_SERVER['HTTP_HOST'].
			($_SERVER['SERVER_PORT']==80?'':$_SERVER['SERVER_PORT']);
	}

}

?>
