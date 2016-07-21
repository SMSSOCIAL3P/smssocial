<?php
/**
 * Classe para enviar, receber
 * 
 */ 
class Telegram {

	//atributos da class
	//public $user;
	//public $pass;
	//public $gwNome;
	public $token;

	/**
	 * Metodo construtor
	 */ 
	public function __construct($token) {	

		//intancia a configuracao com o token
		$this->token = $token;
	} //fim construtor


	/**
	 * Metodo para enviar os sms
	 * 
	 * retorno:
	 * true -> caso tenha enviado os sms
	 * msg de erro do gateway -> caso não tenha enviado os sms
	 * 
	 */ 
	public function sendTelegram($msg_id, $msg, $contatos) {


		//enviar
		$metodo = "sendMessage";

		//monta a url
		$url = "https://api.telegram.org/bot".$this->token."/{$metodo}";


		//varre os contatos para disparar os sms
		foreach($contatos as $contId => $chat_id) {
			//monta a mensagem
			$param = array('chat_id' => $chat_id, "text" => $msg); //send message

			//monta a chamado do curl
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_VERBOSE, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

			//executa
			$result = curl_exec($ch);
			//fecha a curl
			curl_close($ch);

		} //fim foreach dos contatos
		
		//retorno
		return true;

	} //fim do metodo de enviar o sms

	
	/**
	 * Metodo para recuperar as mensagens respondidas
	 * 
	 * retorno:
	 *  
	 */ 
	public function recivedTelegram() {

		//metod para pegar todo as mensagens
		$metodo = "getUpdates";

		//monta a url
		$url = "https://api.telegram.org/bot".$this->token."/{$metodo}";

		//monta a chamado do curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		//executa
		$result = curl_exec($ch);
		//fecha a curl
		curl_close($ch);

		//decoda o json
		$json = json_decode($result);

		//devolve os bot respondidos
		return $json;
		
	} //fim recivedSMS

	
} //fim class
?>