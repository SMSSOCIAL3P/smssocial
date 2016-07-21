<?php
require_once('../../../../wp-load.php');
include('Mensagem.class.php');
//caminho da class
require_once('../class/telegram.class.php');

//variaveis globais
global $wpdb, $table_prefix;

$Telegram = new Mensagem();

//busca a configuracao do telegram
$ctel = $wpdb->get_row("SELECT id, token FROM {$table_prefix}smssocial_conf_telegram");

//verifica se existe uma configuracao do telegram
if(empty($ctel->id)) {
	$_SESSION["msgErro"] = "Não existe configuração do Telegram, favor configurar!";
	//para direcionar
	$_SESSION["ctr"] = "conf_telegram";
	$_SESSION["mt"] = "index";

	//direciona para o index
	wp_redirect( home_url() );
} //fim verificacao id


if ($_REQUEST["tp"] == "atualizar") {

	//caminho da class
	require_once('../class/telegram.class.php');

	//classe encapsulada dos contatos
	include('../pessoasTelegram/PessoasTelegram.class.php');

	//instancia a classe
	$Pessoa = new PessoasTelegram();

	//pega o token do telegram
	$conf = $Pessoa->getConfTelegram();

	//seta as confs telegram
	$telegram = new Telegram($conf->token);
	$resp = $telegram->recivedTelegram();
	//verifica se foi realizado com sucesso
	if($resp->ok) {

		$countPes = 0;
		$countMsg = 0;
		//varre a reposta
		foreach($resp->result as $chave => $res) {

			//pega o update id
			$update_id = $res->update_id;

			//pega o id da mensagem
			$msg_id = $res->message->message_id;

			//pega o usuario
			$user_id = $res->message->from->id;
			$user_name = $res->message->from->first_name;

			//pega a mensagem
			$msg = $res->message->text;

			//verifica se existe registro cadastrado
			$id_pessoa = $Pessoa->getOnePessoaTelegram($user_id);		
			//verifica se pessoa esta vazio para cadastrar
			if(empty($id_pessoa)) {

				//monta o array
				$arrPessoa["nome"] = $user_name;
				$arrPessoa["id_telegram"] = $user_id;

				//cadastra pessoa
				$id_pessoa = $Pessoa->insertPessoasTelegram($arrPessoa);
				if($id_pessoa) {
					//conta quantas pessoas foram inseridas
					$count++;
				}

			}// fim verificacao das pessoas
			
			//insere a mensagem
			if(!empty($msg) && substr($msg,0,1) != "/") {

				//instancia a class de mensagem
				$mensagem = new Mensagem();

				//verifico se a msg ja foi cadastrada
				$postMeta = $mensagem->getPostMeta($update_id);							
				if(!empty($postMeta)) {
					continue;
				}

				//usuario logado
				$user = wp_get_current_user();
				//monta as mensagens
				$arrayMensagem["mensagem"] = $msg;
				$arrayMensagem["usuario_id"] = $user->ID;
				$arrayMensagem["update_id"] = $update_id;

				//peta a mensagem nova
				$post_id = $mensagem->respMensagem($arrayMensagem, $conf->id, $id_pessoa);

				$countMsg++;
			} // fim msg

		} //fim foreach

	}  // fim realizacao
	
	$_SESSION["msgOk"] = "Atualização realizada com sucesso! {$countPes} pessoas incluidas a serem relacionadas e {$countMsg} mensagens atualizadas.";


} else if ($_REQUEST["tp"] == "responder") {

	//pega os dados do request
	$mensagem = $_REQUEST["msg"];

	//onde estará os campos comos valores da tabela
	$men = "";
	$mensagem_id = ""; //variavel para setar o identificador da mensagem

	//verifica se tem o id
	//verifica se irá fazer um update ou insert
	if($mensagem["id"] != "") {

		$mensagem_id = $Telegram->updateMensagemRespondidaTelegram($mensagem, $ctel->id);

	} else {
		//insert valores
		$mensagem_id = $Telegram->insertMensagemRespondidaTelegram($mensagem, $ctel->id);
		
	} //fim verificacao do valor 

	//verifica se irá disparar a mensagem
	if($mensagem_id != "") {
		
		//instancia a class
		$telegram = new Telegram($ctel->token);

		//pega os contatos
		$sql = "SELECT cnt.id as id, cnt.id_telegram
				FROM wp_smssocial_contato cnt
				INNER JOIN wp_smssocial_contato_mensagem cmsg ON cmsg.contato_id = cnt.id
				WHERE cmsg.post_ID = ".$mensagem_id;

		//executa a query
		$result = $wpdb->get_results($sql);
		$arrChats = array();
		//varre os resultados para enviar os dados e passar o array com o id do usuario e celular
		foreach($result as $val) {
			$arrChats[$val->id] = $val->id_telegram;
		} //fim foreach

		//enviar sms
		$resposta = $telegram->sendTelegram($mensagem_id,$mensagem["mensagem"],$arrChats);

		if($resposta == true) {
			//atualiza a mensagem qnd enviada
			//$Mensagem->atualizarEnvioMensagem($mensagem_id);

			$_SESSION["msgOk"] .= " Mensagem enviada com sucesso!";
			
		} else {
			$_SESSION["msgErro"] .= $resposta;
		}//fim envar sms
		
	}//fim enviar
	
} else if ($_REQUEST["tp"] == "exportar") {

	//metodo para exportar os dados das pessoas
	$Telegram->exportarMensagens();

} else { 

	//pega os dados do request
	$mensagem = $_REQUEST["msg"];

	//pega os contatos dos sub-grupos selecionados
	if(isset($mensagem["grupo_id"])) {
		$grpIds = implode(",", $mensagem["grupo_id"]);		
		$whereGrp = " AND grc.grupo_id IN (".$grpIds.") ";
	}

	//verifica se existe algum contato que deve ser disparado a msg
	if(isset($mensagem["contato_id"])) {
		$contatosIds = implode(",", $mensagem["contato_id"]);
		//verifica se existe para não trazer tudo
		if(isset($whereGrp)){
			$whereContato = " OR cont.id IN (".$contatosIds.") ";
		} else {
			$whereContato = " AND cont.id IN (".$contatosIds.") ";
		}
	}

	//query para pegar os contatos que seram gravados
	$queryContato = "SELECT DISTINCT cont.id
					FROM {$table_prefix}smssocial_contato cont
					INNER JOIN {$table_prefix}smssocial_grupo_contato grc ON cont.id = grc.contato_id 
					WHERE cont.flg_atv = 1 {$whereGrp} {$whereContato};";
	//print $queryContato;
	$contatos = $wpdb->get_results( $queryContato );
	
	//onde estará os campos comos valores da tabela
	$men = "";
	$mensagem_id = ""; //variavel para setar o identificador da mensagem

	//verifica se tem o id
	//verifica se irá fazer um update ou insert
	if($mensagem["id"] != "") {

		$mensagem_id = $Telegram->updateMensagemTelegram($mensagem, $ctel->id, $contatos);

	} else {
		//insert valores
		$mensagem_id = $Telegram->insertMensagemTelegram($mensagem, $ctel->id, $contatos);
		
	} //fim verificacao do valor 

	//verifica se irá disparar a mensagem
	if($mensagem_id != "") {
		
		//instancia a class
		$telegram = new Telegram($ctel->token);

		//pega os contatos
		$sql = "SELECT cnt.id as id, cnt.id_telegram
				FROM wp_smssocial_contato cnt
				INNER JOIN wp_smssocial_contato_mensagem cmsg ON cmsg.contato_id = cnt.id
				WHERE cmsg.post_ID = ".$mensagem_id;

		//executa a query
		$result = $wpdb->get_results($sql);
		$arrChats = array();
		//varre os resultados para enviar os dados e passar o array com o id do usuario e celular
		foreach($result as $val) {
			$arrChats[$val->id] = $val->id_telegram;
		} //fim foreach

		//enviar sms
		$resposta = $telegram->sendTelegram($mensagem_id,$mensagem["mensagem"],$arrChats);

		if($resposta == true) {
			//atualiza a mensagem qnd enviada
			//$Mensagem->atualizarEnvioMensagem($mensagem_id);

			$_SESSION["msgOk"] .= " Mensagem enviada com sucesso!";
			
		} else {
			$_SESSION["msgErro"] .= $resposta;
		}//fim envar sms
		
	}//fim enviar

} //fim if do tipo

//para direcionar
$_SESSION["ctr"] = "telegram";

if ($_REQUEST["tp"] == "atualizar") {
	$_SESSION["mt"] = "msgRecebida";
} else {
	$_SESSION["mt"] = "index";
}

//direciona para o index
wp_redirect( home_url() );
?>