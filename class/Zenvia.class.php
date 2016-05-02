<?php
/**
 * Classe para fazer as funções de enviar, receber e saber os créditos
 * 
 * É necessário estar ativo no php o CUrl no php.ini
 * 
 */ 
class Zenvia {

	public $statusCode;
	public $detailCode;
	public $key;
	public $url;

	/**
	 * Metodo construtor da classe
	 */ 
	public function __construct($user, $pass) {

		//seta os valores do usario e senha
		$this->key = base64_encode($user.":".$pass);

		//fixa os status do Zenvia
		$this->statusCode = array(	"00" => "Ok",
									"01" => "Scheduled",
									"02" => "Sent",
									"03" => "Delivered",
									"04" => "Not Received",
									"05" => "Blocked - No Coverage",
									"06" => "Blocked - Black listed",
									"07" => "Blocked - Invalid Number",
									"08" => "Blocked - Content not allowed",
									"08" => "Blocked - Message Expired",
									"09" => "Blocked",
									"10" => "Error");
		//fixa os details code
		$this->detailCode =  array(	"000" => "Message Sent",
									"002" => "Message successfully canceled",
									"010" => "Empty message content",
									"011" => "Message body invalid",
									"012" => "Message content overflow",
									"013" => "Incorrect or incomplete ‘to’ mobile number",
									"014" => "Empty ‘to’ mobile number",
									"015" => "Scheduling date invalid or incorrect",
									"016" => "ID overflow",
									"017" => "Parameter ‘url’ is invalid or incorrect",
									"018" => "Field ‘from’ invalid",
									"021" => "‘id’ fieldismandatory",
									"080" => "Message with same ID already sent",
									"100" => "Message Queued",
									"110" => "Message sent to operator",
									"111" => "Message confirmation unavailable",
									"120" => "Message received by mobile",
									"130" => "Message blocked",
									"131" => "Message blocked by predictive cleansing",
									"132" => "Message already canceled",
									"133" => "Message content in analysis",
									"134" => "Message blocked by forbidden content",
									"135" => "Aggregate is Invalid or Inactive",
									"136" => "Message expired",
									"140" => "Mobile number not covered",
									"141" => "International sending not allowed",
									"145" => "Inactive mobile number",
									"150" => "Message expired in operator",
									"160" => "Operator network error",
									"161" => "Message rejected by operator",
									"162" => "Message cancelled or blocked by operator",
									"170" => "Bad message",
									"171" => "Bad number",
									"172" => "Missing parameter",
									"180" => "Message ID notfound",
									"190" => "Unknown error",
									"200" => "Messages Sent",
									"210" => "Messages scheduled but Account Limit Reached",
									"240" => "File empty or not sent",
									"241" => "File too large",
									"242" => "File readerror",
									"300" => "Received messages found",
									"301" => "No received messages found",
									"400" => "Entity saved",
									"900" => "Authentication error",
									"901" => "Account type not support this operation.",
									"990" => "Account Limit Reached – Please contact support",
									"998" => "Wrong operation requested",
									"999" => "Unknown Error");

	} //fim construtor

	/**
	 * Metodo para retirar as possiveis formatações
	 * epaço em branco
	 * -
	 * .
	 * ,
	 * 
	 */ 
	private function retiraFormatacao($contato) {

		$contato = trim($contato);
		$contato = str_replace("-","",str_replace(".","",str_replace(",","",$contato)));
		return $contato;

	} //fim retiraFormatacao

	/**
	 * Metodo para enviar os sms 
	 * 
	 * Parametros:
	 * $msg_id = id da mensagem no banco de dados para saber qual a mensagem que 
	 * 				foi enviada na resposta
	 * $msg = mensagem em texto corrido não pode haver quebra de linha
	 * $contatos = array com os contatos para onde vai ser enviado no formato 
	 * 				chave = id do contato, valor = celular do contato
	 */ 
	public function sendSMS ($msg_id=null, $msg=null, $contatos=null) {
		//dados a serem enviados
		$dados = '{
					"sendSmsMultiRequest": {
						"sendSmsRequestList": [';


		//varre os contatos para disparar os sms
		foreach($contatos as $contId => $contato) {
			//retira os espaços em branco e as pontuações.
			$pessoa = $this->retiraFormatacao($contato);
			//monta o post
			$dados .= '{"to": "'.$pessoa.'",
						"msg": "'.$msg.'",
						"id": "'.$msg_id."_".$contId.'"},';

		} //fim foreach dos contatos
		//dados tratados para enviar
		$dados = substr($dados,0,-1).']}}';

		//chama o curl
		$ch = curl_init();

		//DEBUG DOS ENVIOS DOS SMS
		//curl_setopt($ch, CURLOPT_VERBOSE, true);

		//seta as configurações conforme o gateway
		curl_setopt($ch, CURLOPT_URL, "https://api-rest.zenvia360.com.br/services/send-sms-multiple");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		//fala que vai utilizar post
		curl_setopt($ch, CURLOPT_POST, TRUE);
		//coloca os posts que irá usar
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);
		//seta o cabeçalho com a chave
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(	"Content-Type: application/json",
													"Authorization: Basic {$this->key}",
													"Accept: application/json"
													));
		//executa o envio dos dados
		$result = curl_exec($ch);

		//fecha o curl
		curl_close($ch);
		
		//decodifica o resultado de retorno
		$resp = json_decode($result);

		//retorna o detalhe para imprimir na tela o envio
		if($resp->sendSmsMultiResponse->sendSmsResponseList[0]->statusCode == 00 ) {
			return true;
		}
		//retorna o erro
		return $resp->sendSmsMultiResponse->sendSmsResponseList[0]->detailDescription;
		
	} //fim sendSMS

	/**
	 * Metodo para receber os sms
	 */ 
	public function recivedSMS() {
		//chama o curl para buscar as respostas do sms
		$ch = curl_init();

		//DEBUG DOS ENVIOS DOS SMS
		//curl_setopt($ch, CURLOPT_VERBOSE, true);

		//seta os parametros
		curl_setopt($ch, CURLOPT_URL, "https://api-rest.zenvia360.com.br/services/received/list");
		//"https://api-rest.zenvia360.com.br/services/received/search/2014-08-22T00:00:00/2014-08-22T23:59:59");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		//fala que vai utilizar post
		curl_setopt($ch, CURLOPT_POST, TRUE);
		//seta o cabeçalho com a chave
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
												  "Content-Type: application/json",
												  "Accept: application/json",
												  "Authorization: Basic {$this->key}"												  
												));

		$response = curl_exec($ch);
		//fecha o curl
		curl_close($ch);

		//decodifica o resultado de retorno
		$resp = json_decode($response);

		//existe resposta
		if($resp->receivedResponse->detailCode == 300) {
			
			//monta o array com as respostas
			$respostaRecebida = array();

			//varre as respostas dos sms
			foreach($resp->receivedResponse->receivedMessages as $key => $arrDados) {
				//trabalha a data
				$data = substr($arrDados->dateReceived,0,10) ." ".substr($arrDados->dateReceived,11);

				//monta o array de resposta
				$respostaRecebida[$key]["dt_cadastro"] 	= $arrDados->dateReceived;
				$respostaRecebida[$key]["celular"] 		= $data;
				$respostaRecebida[$key]["recebida"] 	= $arrDados->body;
				$respostaRecebida[$key]["chave_envio"] 	= $arrDados->mtId;

			} //fim foreach
			
			return $respostaRecebida;

		} //fim verificacao da existencia da resposta

		return false;

	} //fim recivedSMS

	/**
	 * Metodo para buscar os créditos no gateway
	 */ 
	public function getCreditsSMS() {} //fim getCreditsSMS

} //fim class
?>