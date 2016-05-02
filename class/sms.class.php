<?php
/**
 * Classe para enviar, receber  e saber os creditos
 * 
 */ 
class SMS {

	//atributos da class
	//public $user;
	//public $pass;
	//public $gwNome;
	public $gateway;

	/**
	 * Metodo construtor
	 */ 
	public function __construct($user, $pass, $gw) {	

		//inclui a classe que foi configurada
		include("gateways/{$gw}.class.php");

		//intancia o gateway
		$this->gateway = new $gw($user, $pass);
	} //fim construtor

	/**
	 * Metodo para enviar os sms
	 * 
	 * retorno:
	 * true -> caso tenha enviado os sms
	 * msg de erro do gateway -> caso não tenha enviado os sms
	 * 
	 */ 
	public function sendSMS($msg_id, $msg, $contatos) {

		//envia para a classe do gateway para disparar o sms
		$enviarSMS = $this->gateway->sendSMS($msg_id, $msg, $contatos);
		//retorna a mensagem
		return $enviarSMS;

	} //fim do metodo de enviar o sms

	/**
	 * Metodo para enviar os sms agendados
	 * 
	 * Parametros
	 * @param msg_id -> identificador da mensagem do banco de dados
	 * @param msg -> mensagem a ser enviadas
	 * @param contatos -> celulares de contatos
	 * @param agendamento -> data do agendamento YYYY-MM-DD HH:MM:SS
	 * 
	 * Retorno:
	 * @param true -> caso tenha agendado os sms
	 * @param msg de erro do gateway -> caso não tenha enviado os sms	 
	 * 
	 */ 
	public function sendAgendamentoSMS($msg_id, $msg, $contatos, $agendamento) {

		//envia para a classe do gateway para disparar o sms
		$enviarSMS = $this->gateway->sendSMS($msg_id, $msg, $contatos, $agendamento);
		//retorna a mensagem
		return $enviarSMS;

	} //fim do metodo de enviar o sms

	/**
	 * Metodo para recuperar as mensagens respondidas
	 * 
	 * retorno:
	 *  
	 */ 
	public function recivedSMS() {

		//busca os sms respondidos
		$respostaSMS = $this->gateway->recivedSMS();
		//devolve os sms respondidos
		return $respostaSMS;
		
	} //fim recivedSMS

	/**
	 * Metodo para cancelar 
	 * 
	 */
	public function cancelarAgSMS($chave_envio) {

		//busca os sms respondidos
		$respostaSMS = $this->gateway->cancelarAgSMS($chave_envio);
		//devolve os sms respondidos
		return $respostaSMS;

	} //fim cancelarAgSMS

} //fim class
?>