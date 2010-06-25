<?php

require_once "Auth/OpenID/Consumer.php";
require_once dirname(__FILE__)."/ZendStore.php";
require_once "Auth/OpenID/SReg.php";
require_once "Auth/OpenID/PAPE.php";

require_once QFWPATH.'/QuickFW/Auth.php';

class OidController extends QuickFW_Auth
{

	public function indexAction($return = false)
	{
		if (!empty($_SESSION))
			var_dump($_SESSION);
		$return = $return ? $return : $_SERVER['REQUEST_URI'];
		if (!empty($_SESSION['openID']['error']))
		{
			echo '<p class="error">'.$_SESSION['openID']['error'].'</p>';
			unset($_SESSION['openID']['error']);
		} ?>

<form method="get" action="<?php echo Url::C('try') ?>">
<input type="hidden" name="return" value="<?php echo QFW::$view->esc($return); ?>" />
<input type="text" name="openid_identifier" value="<?php echo QFW::$view->esc(
	'http://quickfw.ib.br/openid/server.php/idpage?user=ivan') ?>" />
<input type="submit" value="Verify" />
</form>
<a href="<?php echo Url::C('clean') ?>">выйти</a>
		<?php
	}

	public function cleanAction()
	{
		unset($_SESSION['openID']);
		QFW::$router->redirect(Url::C());
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

	/**
	 * Действеи перенаправляет пользователя на сервер для авторизации
	 */
	public function tryAction()
	{
		$this->session();
		$_SESSION['openID']['return'] = $_REQUEST['return'];
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

	/**
	 * Сюда приходит пользователь с сервера авторизации
	 *
	 * После пользователь перенаправляется на url,
	 * <br>в $_SESSION['openID'] данные авторизации
	 */
	public function finishAction()
	{
		$this->session();
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
			$return = $_SESSION['openID']['return'];

			$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
			$sreg = $sreg_resp->contents();

			$_SESSION['openID'] = array(
				'sreg' => $sreg,
				'id' => $openid,
			);
			QFW::$router->redirect($return);
		}
	}

	/**
	 * Сообщение при ошибке авторизации - в $_SESSION['openID']['error']
	 *
	 * @param string $msg сообщение об ошибке
	 */
	private function err($msg)
	{
		$_SESSION['openID']['error'] = $msg;
		QFW::$router->redirect($_SESSION['openID']['return']);
	}

	/**
	 * Возвращает класс хранилища для библиотеки
	 *
	 * @return Auth_OpenID_Consumer класс хранилища
	 */
	private function getConsumer()
	{
		return new Auth_OpenID_Consumer(new Auth_OpenID_ZendStore(Cache::get()));
	}

	/**
	 * формирует имя сервера
	 *
	 * @return string url сервера
	 */
	private function getServer()
	{
		return 'http://'.$_SERVER['HTTP_HOST'].
			($_SERVER['SERVER_PORT']==80?'':$_SERVER['SERVER_PORT']);
	}

}

?>
