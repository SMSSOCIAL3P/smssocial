<?php
/**
 * Classe para trabalhar os dados das instituicoes do sms social
 */
class Instituicao {

	/**
	 * Metodo construtor da class
	 */ 
	public function __construct() {}

	/**
	 * Metodo para inserir instituicao na base de dados tabela smssocial_instituicao
	 * 
	 * @Params
	 * @$instituicoes: array com os dados do formulario
	 */
	public function insertInstituicao($instituicao) {

		//variaveis globais
		global $wpdb, $table_prefix;

		//array para montar os dados novos
		$inst = array();

		//insert colunas e valores		
		$inst["instituicao"] 	= $instituicao["instituicao"];
		$inst["id_usuario_wp"] 	= $instituicao["id_usuario_wp"];
		
		//verifica se existe o registro
		if($this->getRegistro($instituicao)) {
			//executa a insercao
			if($wpdb->insert("{$table_prefix}smssocial_instituicao", $inst)) {
				
				$_SESSION["msgOk"] = "Instituição incluida com sucesso!";

				//return o valor inserido
				return  $wpdb->insert_id;

			} else {
				
				$_SESSION["msgErro"] = "Erro ao inserir um novo instituição!";

				return false;
			} // fim verificacao
		}

	} // fim insertInstituicao

	/**
	 * Metodo para alterar os dados na tabela smssocial_instituicao
	 * 
	 * @Params:
	 * @instituicao: array com os dados para alteração na base de dados 
	 */ 
	public function updateInstituicao( $instituicao ) {

		//variaveis globais
		global $wpdb, $table_prefix;

		//altera os valores para os valores passados		
		$inst["instituicao"] 	= $instituicao["instituicao"];
		$inst["id_usuario_wp"] 	= $instituicao["id_usuario_wp"];
		$inst["dt_cadastro"] 	= date('Y-m-d H:i:s');

		//verifica se existe o registro
		if($this->getRegistro($instituicao)) {

			//valor
			$where  = array('id'=>$instituicao["id"]);

			//executa a alteracao
			if($wpdb->update("{$table_prefix}smssocial_instituicao", $inst, $where)) {
				$_SESSION["msgOk"] = "Instituição alterada com sucesso!";
				//retorna o valor do id do post
				return $instituicao["id"];
			} else {
				$_SESSION["msgErro"] = "Erro ao alterar do instituição!";
				//retorna como falso
				return false;
			} // fim verificacao
		}

	} // fim updateInstituicao

	/**
	 * Metodo para inativar o registro da instituicao
	 * 
	 * @Params:
	 * @id: identificador da tabela para deixar o registro inativo
	 * 
	 */ 
	public function deleteInstituicao($id) {

		//variaveis globais
		global $wpdb, $table_prefix;

		//seta como inativo o valor
		$inst["flg_atv"] = 0;

		//valor
		$where  = array('id'=>$id);

		//executa a alteracao
		if($wpdb->update("{$table_prefix}smssocial_instituicao", $inst, $where)) {
			$_SESSION["msgOk"] = "Instituição deletado com sucesso!";
		} else {
			$_SESSION["msgErro"] = "Erro ao deletar o instituição!";
		} // fim verificacao
		
	} // fim deleteInstituicao

	/**
	 * Metodo para verificar se existe o registro na tabela antes de gravar
	 * 
	 */
	 private function getRegistro($instituicao) {
	 	//variaveis globais
		global $wpdb, $table_prefix;
		
	 	//verifica se já existe o grupo para aquela instituição
		$vInstituicao = $wpdb->get_row("SELECT * 
										FROM {$table_prefix}smssocial_instituicao
										WHERE instituicao = '{$instituicao['instituicao']}'");

		//verifica se o grupo existe
		if(!empty($vInstituicao)) {
			$_SESSION["msgErro"] = "Esta instituição já existe!";

			return false;
		}//fim vGrupo

		return true;

	 } //fim getRegistro

} //fim class instituicao
?>