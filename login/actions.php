<?php
/**
 * Action para validar o login do usuário que está tentando se logar
 */
//instancia as funcoes wordpress
require_once('../../../../wp-load.php');

//metodo
$mt = $_REQUEST["mt"];

switch($mt) {
	case 'fale_conosco':

		//pega o formulario
		$msg = $_REQUEST;

		/** 
		 * Utiliza a funcao mail do php para disparar os email, precisa configurar no servidor php.ini
		 * o smtp para disparo do mesmo
		 */ 

		//pega o email do admin do sistema
		$admin = get_users(1);	

		//monta o email
		$to = $admin[0]->data->user_email;
		$subject = "Fale Conosco - SMS Social";

		//
		$messagem = "Nome: " . $msg["nome"] . "<br>";
		$messagem = "Email: " . $msg["email"] . "<br>";
		$messagem = "Telefone: " . $msg["telefone"] . "<br>";
		$messagem = "Mensagem: " . $msg["texto"] . "<br>";
         
		$header = "From:" . $msg["email"] . " \r\n";		
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-type: text/html\r\n";
         
		$retval = mail ($to,$subject,$messagem,$header);
         
		if( $retval == true ) {
			$_SESSION["msgOk"] = "Mensagem enviada!";
		} else {
			$_SESSION["msgErro"] = "Mensagem não enviada!";
		}


		$_SESSION["ctr"] = "home";
		$_SESSION["mt"] = "index";

		break;
	case 'logoff':
		//destroi os valores
		wp_logout();

		break;
	default:
		//pega os valores do formulario
		$login = $_REQUEST["login"];
		$senha = $_REQUEST["senha"];

		//autentica no wordpress caso usuario e senha esteja correto
		$user = wp_authenticate($login,$senha);

		//verifica se o usuario e senha estao certos
		if($user->ID > 0) {
			//seta os cookies
			wp_set_auth_cookie($user->ID);

			//sessao de localizacao do sistema
			//$_SESSION["ctr"] = "home";
			//para direcionar
			$_SESSION["ctr"] = "home";
			$_SESSION["mt"] = "index";

		} else {
			$_SESSION["msgErro"] = "Usuario e/ou Senha incorretos!";
		}

		break;

}

//direciona para o index
wp_redirect( home_url() );

?>