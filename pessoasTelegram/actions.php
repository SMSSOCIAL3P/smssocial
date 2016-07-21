<?php
require_once('../../../../wp-load.php');

//variaveis globais
global $wpdb, $table_prefix;


//coloca como desativado
if($_REQUEST["tp"] == "atualizar") {

	//caminho da class
	require_once('../class/telegram.class.php');

	//pega a classe que grava a mensagem
	require_once('../telegram/Mensagem.class.php');

	//classe encapsulada dos contatos
	include('PessoasTelegram.class.php');

	//instancia a classe
	$Pessoa = new PessoasTelegram();


	//pega o token do telegram
	$conf = $Pessoa->getConfTelegram();

	//seta as confs telegram
	$telegram = new Telegram($conf->token);
	$resp = $telegram->recivedTelegram();
	//verifica se foi realizado com sucesso
	if($resp->ok) {

		$count = 0;
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

	
			} // fim msg

		} //fim foreach

	}  // fim realizacao
	
	$_SESSION["msgOk"] = "Atualização realizada com sucesso!";

} else if($_REQUEST["tp"] == "relacionar") {

	//pega os dados enviados
	$telegram_id = $_REQUEST["tel_id"];
	$pessoa_id = $_REQUEST["pes_id"];

	if(!empty($telegram_id) && !empty($pessoa_id)) {
		
		$dados = array();

		//pega a classe que grava a mensagem
		require_once('../pessoas/Pessoas.class.php');

		//insere na tabela de pessoas o id_telegram
		$pessoa = new Pessoas();
		$dados["id"] = $pessoa->updateTelegramPessoas($pessoa_id, $telegram_id);

		echo json_encode($dados);
	}	
	exit;
}

/*else { 

	//pega os dados do request
	$pessoas = $_REQUEST["pessoas"];
	
	//verifica se tem o id
	//verifica se irá fazer um update ou insert
	if($pessoas["id"] != "") {
		//metodo para alterar os dados das pessoas que foram passadas
		$Pessoa->updatePessoasTelegram($pessoas);
	} else {
		//insert valores
		$Pessoa->insertPessoasTelegram($pessoas);		
	} //fim verificacao do valor 
}*/

//para direcionar
$_SESSION["ctr"] = "pessoasTelegram";
$_SESSION["mt"] = "index";

//direciona para o index
wp_redirect( home_url() );
?>