<?php
require_once('../../../../wp-load.php');
require_once(get_template_directory() .'/class/sms.class.php');

//variaveis globais
global $wpdb, $table_prefix;

//busca o gateway configurado
$gw = $wpdb->get_row("SELECT id, usuario, senha, nome FROM {$table_prefix}smssocial_gateway");

//instancia a class
$sms = new SMS($gw->usuario, $gw->senha, $gw->nome);
//respostas
$respostasSMS = $sms->recivedSMS();

//verifica se a resposta não esta vazia
if(is_array($respostasSMS)) {

	//variavel auxiliar
	$erro = false;
	//contador dos registros
	$cont = 0;

	//usuario que executou
	$user = wp_get_current_user(); //$user->ID

	//traz a mensagem para ser instanciada
	include(get_template_directory() .'/mensagem/Mensagem.class.php');
	//instancia a mensagem
	$Mensagem = new Mensagem();

	//grava na tabela smssocial_msg_recebida
	//varre os dados para inserir na base de dados
	foreach($respostasSMS as $dados) {
		//seta a variavel para ser trabalhada
		$chave_envio = $dados["chave_envio"];
		
		//verifica se existe a data senão coloca a data e hora da execução 
		if($dados["dt_cadastro"] == "") {
			$dados["dt_cadastro"] = date("Y-m-d H:i:s");
		}

		//separa para pegar o id da mensagem e do contato
		//0=mensagem, 1=contato
		$arrChave = explode("_", $chave_envio);

		//seta os dados para ser inseridos
		$msgRec["mensagem"] 				= $dados["recebida"];
		$msgRec["usuario_id"] 				= $user->ID;
		$msgRec["post_date"] 				= $dados["dt_cadastro"];
		$msgRec["chave_envio"] 				= $chave_envio;		
		$msgRec["msg_enviada_id"] 			= $arrChave["0"];
		
		//executa a insercao
		$mensagem_id = $Mensagem->insertMensagemRecebida($msgRec, $gw->id, $arrChave["1"]);
		if(empty($mensagem_id)) {
			$erro = true;			
		} // fim verificacao

		$cont++;
		$mensagem_id = "";
	} //fim foreach

	//verifica qual mensagem deverá exibir
	if(!$erro) {
		$_SESSION["msgOk"] = "Foram encontrados ". $cont ." SMS respondido(s).";
	} else {
		$_SESSION["msgErro"] = "Erro ao inserir na base os SMS respondidos!";
	}
	
} else {
	$_SESSION["msgOk"] = "Não foram encontrados SMS respondidos.";
} //fim verificacao

//para direcionar
$_SESSION["ctr"] = "respostaMsg";
$_SESSION["mt"] = "index";

//direciona para o index
wp_redirect( home_url() );
?>