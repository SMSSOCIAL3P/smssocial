<?php
/**
 * Classe para trabalhar os dados de configuracao do telegram do sms social
 */
class ConfTelegram {

	/**
	 * Metodo construtor da class
	 */ 
	public function __construct() {}

	/**
	 * Metodo para inserir token na base de dados tabela smssocial_conf_telegram
	 * 
	 * @Params
	 * @conf: array com os dados do formulario
	 */
	public function insertConfTelegram($conf) {

		//variaveis globais
		global $wpdb, $table_prefix;

		//colunas e valores
		$ctel["token"] 	= $conf["token"];
		$ctel["nome"] 	= $conf["nome"];
		
		//executa a insercao
		if($wpdb->insert("{$table_prefix}smssocial_conf_telegram", $ctel)) {
			$_SESSION["msgOk"] = "Token incluido com sucesso!";

			//return o valor inserido
			return  $wpdb->insert_id;

		} else {
			$_SESSION["msgErro"] = "Erro ao inserir o Token!";

			return false;
		} // fim verificacao
		
	} // fim insertConfTelegram

	/**
	 * Metodo para alterar os dados na tabela smssocial_conf_telegram
	 * 
	 * @Params:
	 * @conf: array com os dados para alteração na base de dados 
	 */ 
	public function updateConfTelegram( $conf ) {

		//variaveis globais
		global $wpdb, $table_prefix;

		//altera os valores para os valores passados
		$ctel["token"] 	= $conf["token"];
		$ctel["nome"] 	= $conf["nome"];

		//valor
		$where  = array('id' => $conf["id"]);

		//executa a alteracao
		if($wpdb->update("{$table_prefix}smssocial_conf_telegram", $ctel, $where)) {
			$_SESSION["msgOk"] = "Token alterado com sucesso!";
			//retorna o valor do id do post
			return $conf["id"];
		} else {
			$_SESSION["msgErro"] = "Erro ao alterar o Token!";

			return false;
		} // fim verificacao

	} // fim updateConfTelegram

} //fim class instituicao
?>