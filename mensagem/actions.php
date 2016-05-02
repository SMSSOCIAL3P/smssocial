<?php
require_once('../../../../wp-load.php');
include('Mensagem.class.php');
//caminho da class
require_once('../class/sms.class.php');


//variaveis globais
global $wpdb, $table_prefix;

$Mensagem = new Mensagem();

//busca o gateway configurado
$gw = $wpdb->get_row("SELECT id, usuario, senha, nome FROM {$table_prefix}smssocial_gateway");

//verifica se existe um gateway configuraco
if(empty($gw->id)) {
	$_SESSION["msgErro"] = "Não existe Gateway Configurado, favor configurar um Gateway!";
	//para direcionar
	$_SESSION["ctr"] = "gateway";
	$_SESSION["mt"] = "index";

	//direciona para o index
	wp_redirect( home_url() );
} //fim verificacao id


//coloca como desativado
if($_REQUEST["tp"] == "delete") {

	//identificador do select
	$id = $_REQUEST["id"];

	//seta como inativo o valor
	$pes["flg_atv"] = 0;

	//valor
	$where  = array('id'=>$id);

	//executa a alteracao
	if($wpdb->update('{$table_prefix}smssocial_contato', $pes, $where)) {
		$_SESSION["msgOk"] = "Pessoas deletada com sucesso!";
	} else {
		$_SESSION["msgErro"] = "Erro ao deletar a pessoa!";
	} // fim verificacao

} else if ($_REQUEST["tp"] == "exportar") {

	//metodo para exportar os dados das pessoas
	$Mensagem->exportarMensagens();

} else if ($_REQUEST["tp"] == "cancelarAg") { //cancelamento do agendamento

	//identificador do select
	$post_id = $_REQUEST["post_id"];
	$contato_id = $_REQUEST["contato_id"];

	//monta a chave de envio
	$chave_envio = $post_id."_".$contato_id;

	//instancia a class
	$sms = new SMS($gw->usuario, $gw->senha, $gw->nome);

	$resposta = $sms->cancelarAgSMS($chave_envio);

	//verifica se retornou verdadeiro
	if($resposta == true) {

		//seta a gateway_id em postmeta
		add_post_meta($post_id, 'dt_cancelamento', date('Y-m-d H:i:s'));

		//atualiza o status para cancelado
		$Mensagem->atualizarEnvioMensagem($post_id);

		//atualiza a mensagem qnd enviada
		$_SESSION["msgOk"] .= " Mensagem cancelada com sucesso!";
		
	} else {
		$_SESSION["msgErro"] .= $resposta;
	}//fim envar sms

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

		$mensagem_id = $Mensagem->updateMensagem($mensagem, $gw->id, $contatos);

	} else {
		//insert valores
		$mensagem_id = $Mensagem->insertMensagem($mensagem, $gw->id, $contatos);
		
	} //fim verificacao do valor 

	//verifica se irá disparar a mensagem
	if($mensagem_id != "") {
		
		//instancia a class
		$sms = new SMS($gw->usuario, $gw->senha, $gw->nome);

		//pega os contatos
		$sql = "SELECT cnt.id as id, cnt.celular as celular
				FROM wp_smssocial_contato cnt
				INNER JOIN wp_smssocial_contato_mensagem cmsg ON cmsg.contato_id = cnt.id
				WHERE cmsg.post_ID = ".$mensagem_id;

		//executa a query
		$result = $wpdb->get_results($sql);
		$arrCelulares = array();
		//varre os resultados para enviar os dados e passar o array com o id do usuario e celular
		foreach($result as $val) {
			$arrCelulares[$val->id] = $val->celular;
		} //fim foreach

		//verifica se existe o agendamento
		if($mensagem["agendamento"] != "") {			
			//agenda sms
			$agendamento = FormataDataHoraDB($mensagem["agendamento"]);
			$resposta = $sms->sendAgendamentoSMS($mensagem_id,$mensagem["mensagem"],$arrCelulares,$agendamento);
		} else {
			//enviar sms
			$resposta = $sms->sendSMS($mensagem_id,$mensagem["mensagem"],$arrCelulares);
		}

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
$_SESSION["ctr"] = "mensagem";
$_SESSION["mt"] = "index";

//direciona para o index
wp_redirect( home_url() );
?>