<?php
/**
 * Classe para trabalhar os dados dos grupos do sms social
 */
class Grupos {

	/**
	 * Metodo construtor da class
	 */ 
	public function __construct() {}

	/**
	 * Metodo para inserir grupos na base de dados tabela smssocial_grupo
	 * 
	 * @Params
	 * @grupo: array com os dados do formulario
	 */
	public function insertGrupo($grupo) {

		//variaveis globais
		global $wpdb, $table_prefix;

		//colunas e valores
		$grp["grupo"] 			= $grupo["grupo"];		
		$grp["instituicao_id"] 	= $grupo["instituicao_id"];
		$grp["id_usuario_wp"] 	= $grupo["id_usuario_wp"];

		//verifica se existe o registro
		if($this->getRegistro($grupo)) {
			//executa a insercao
			if($wpdb->insert("{$table_prefix}smssocial_grupo", $grp)) {
				$_SESSION["msgOk"] = "Grupo incluido com sucesso!";

				//return o valor inserido
				return  $wpdb->insert_id;
			} else {
				$_SESSION["msgErro"] = "Erro ao inserir um novo Grupo!";

				return false;
			} // fim verificacao
		}
		
	} // fim insertGrupo

	/**
	 * Metodo para alterar os dados na tabela smssocial_grupo
	 * 
	 * @Params:
	 * @grupo: array com os dados para alteração na base de dados 
	 */ 
	public function updateGrupo( $grupo ) {

		//variaveis globais
		global $wpdb, $table_prefix;

		//altera os valores para os valores passados
		$grp = "";

		//altera os valores para os valores passados
		$grp["grupo"]		 	= $grupo["grupo"];
		$grp["instituicao_id"] 	= $grupo["instituicao_id"];
		$grp["id_usuario_wp"] 	= $grupo["id_usuario_wp"];
		$grp["dt_cadastro"] 	= date('Y-m-d H:i:s');

		//verifica se existe o registro
		if($this->getRegistro($grupo)) {

			//valor
			$where  = array('id'=>$grupo["id"]);

			//executa a alteracao
			if($wpdb->update("{$table_prefix}smssocial_grupo", $grp, $where)) {
				$_SESSION["msgOk"] = "Grupo alterado com sucesso!";

				//retorna o valor do id do post
				return $grupo["id"];
			} else {
				$_SESSION["msgErro"] = "Erro ao alterar do Grupo!";

				//retorna como falso
				return false;
			} // fim verificacao
		}

	} // fim updateGrupo

	/**
	 * Metodo para inativar o registro do grupo
	 * 
	 * @Params:
	 * @id: identificador da tabela para deixar o registro inativo
	 * 
	 */ 
	public function deleteGrupo($id) {

		//variaveis globais
		global $wpdb, $table_prefix;


		//seta como inativo o valor
		$grp["flg_atv"] = 0;

		//valor
		$where  = array('id'=>$id);

		//executa a alteracao
		if($wpdb->update("{$table_prefix}smssocial_grupo", $grp, $where)) {

			//retira os relacionamentos dos contatos
			$wpdb->delete('{$table_prefix}smssocial_grupo_contato', array( 'grupo_id' => $id ));

			$_SESSION["msgOk"] = "Grupo deletado com sucesso!";
		} else {
			$_SESSION["msgErro"] = "Erro ao deletar o Grupo!";
		} // fim verificacao	
		
	} // fim deleteInstituicao

	/**
	 * Metodo para verificar se existe o registro na tabela antes de gravar
	 * 
	 */
	 private function getRegistro($grupo) {
	 	//variaveis globais
		global $wpdb, $table_prefix;
		
	 	//verifica se já existe o grupo para aquela instituição
		$vGrupo = $wpdb->get_row("	SELECT * 
									FROM {$table_prefix}smssocial_grupo grp
									WHERE grp.grupo = '{$grupo['grupo']}' 
										AND grp.instituicao_id = {$grupo['instituicao_id']}");

		//verifica se o grupo existe
		if(!empty($vGrupo)) {
			$_SESSION["msgErro"] = "Este grupo para está instituição já existe!";

			return false;
		}//fim vGrupo

		return true;

	 } //fim getRegistro

} //fim class Grupos
?>