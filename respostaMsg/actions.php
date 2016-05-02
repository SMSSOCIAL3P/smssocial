<?php
require_once('../../../../wp-load.php');
include('../mensagem/Mensagem.class.php');

//variaveis globais
global $wpdb, $table_prefix;

$msg = new Mensagem();
//print_r($_REQUEST);exit;

//pega os dados do request
$mensagem = $_REQUEST["msg"];

//busca o gateway configurado
$gw = $wpdb->get_row("SELECT id, usuario, senha, nome FROM {$table_prefix}smssocial_gateway");

//verifica se existe um gateway configuraco
if(empty($gw->id)) {
	$_SESSION["msgErro"] = "Não existe Gateway Configurado, favor configurar um Gateway!";
	//para direcionar
	$_SESSION["ctr"] = "gatewayConfig";
	$_SESSION["mt"] = "index";

	//direciona para o index
	wp_redirect( home_url() );
} //fim verificacao id

//onde estará os campos comos valores da tabela
$men = "";
$mensagem_id = ""; //variavel para setar o identificador da mensagem

//verifica se tem o id
//verifica se irá fazer um update ou insert
if($mensagem["id"] != "") {

	$mensagem_id = $msg->updateMensagemRespondida($mensagem, $gw->id);

} else {
	//insert valores
	$mensagem_id = $msg->insertMensagemRespondida($mensagem, $gw->id);
	
} //fim verificacao do valor 


//caminho da class
require_once('../class/sms.class.php');

//instancia a class
$sms = new SMS($gw->usuario, $gw->senha, $gw->nome);

//query para pegar os contatos que seram gravados
$queryContatos = "SELECT * FROM {$table_prefix}smssocial_contato WHERE id = " .$mensagem["contato_id"];
$contatos = $wpdb->get_row( $queryContatos );
$arrCelulares = array();
$arrCelulares[$contatos->id] = $contatos->celular;

//enviar sms
$resposta = $sms->sendSMS($mensagem_id,$mensagem["mensagem"],$arrCelulares);
if($resposta == true) {
	$_SESSION["msgOk"] .= " Mensagem respondida com sucesso!";	
} else {
	$_SESSION["msgErro"] .= $resposta;
}//fim envar sms	

//para direcionar
$_SESSION["ctr"] = "respostaMsg";
$_SESSION["mt"] = "index";

//direciona para o index
wp_redirect( home_url() );
?>