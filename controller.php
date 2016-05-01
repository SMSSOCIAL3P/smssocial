<?php
/**
 * Arquivo para controlar para onde deve enviar os arquivos do menu
 * 
 */ 
//instancia as funcoes wordpress
require_once('../../../wp-load.php');

//pega o perfilSms do usuario logado
$perfilSms = get_user_meta($current_user->ID,"perfilSms");

//para falha de segurança de permissão para não poder 
switch ($_REQUEST["ctr"]) {
	case "grupo":
	case "gatewayConfig":
		if($perfilSms[0] != 1) {
			$_SESSION["msgErro"] = "Você não tem permissão para acessar esta página.";
			
			//direciona para o index
			wp_redirect( home_url() );
			exit;
		}
		break;

} //fim verificacao de permissão

//controlador para onde deve enviar.
$_SESSION["ctr"] = $_REQUEST["ctr"];

if(isset($_REQUEST["mt"]))
	$_SESSION["mt"] = $_REQUEST["mt"];
else 
	$_SESSION["mt"] = "index";

//mata a variavel
unset($_SESSION["rq"]);

//seta o request na sessao
$_SESSION["rq"] = $_REQUEST;

//direciona para o index
wp_redirect( home_url() );
?>