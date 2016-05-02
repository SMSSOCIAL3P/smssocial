<?php
/**
 * Classe para trabalhar os dados do gateway do sms social
 */
class Gateway {

	/**
	 * Metodo construtor da class
	 */ 
	public function __construct() {}

	/**
	 * Metodo para inserir gateway na base de dados tabela smssocial_gateway
	 * 
	 * @Params
	 * @gateway: array com os dados do formulario
	 */
	public function insertGateway($gateway) {

		//variaveis globais
		global $wpdb, $table_prefix;

		//colunas e valores
		$gtw["nome"] 	= $gateway["nome"];
		$gtw["usuario"]	= $gateway["usuario"];
		$gtw["senha"] 	= $gateway["senha"];
		
		//executa a insercao
		if($wpdb->insert("{$table_prefix}smssocial_gateway", $gtw)) {
			$_SESSION["msgOk"] = "Gateway incluido com sucesso!";

			//return o valor inserido
			return  $wpdb->insert_id;

		} else {
			$_SESSION["msgErro"] = "Erro ao inserir o Gateway!";

			return false;
		} // fim verificacao
		
	} // fim insertGrupo

	/**
	 * Metodo para alterar os dados na tabela smssocial_gateway
	 * 
	 * @Params:
	 * @gateway: array com os dados para alteração na base de dados 
	 */ 
	public function updateGateway( $gateway ) {

		//variaveis globais
		global $wpdb, $table_prefix;

		//altera os valores para os valores passados
		$gtw["nome"] 	= $gateway["nome"];
		$gtw["usuario"]	= $gateway["usuario"];
		$gtw["senha"] 	= $gateway["senha"];

		//valor
		$where  = array('id' => $gateway["id"]);

		//executa a alteracao
		if($wpdb->update("{$table_prefix}smssocial_gateway", $gtw, $where)) {
			$_SESSION["msgOk"] = "Gateway alterado com sucesso!";
			//retorna o valor do id do post
			return $gateway["id"];
		} else {
			$_SESSION["msgErro"] = "Erro ao alterar o Gateway!";

			return false;
		} // fim verificacao

	} // fim updateGateway

} //fim class instituicao
?>