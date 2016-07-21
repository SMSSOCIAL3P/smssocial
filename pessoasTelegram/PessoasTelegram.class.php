<?php
/**
 * Classe para trabalhar os dados das pessoas/contatos do sms social
 */

class PessoasTelegram {

	/**
	 * Metodo construtor da class
	 */ 
	public function __construct() {}

	/**
	 * Pega o token configurado do telegram
	 */ 
	public function getConfTelegram() {

		//variaveis globais
		global $wpdb, $table_prefix;

		//query para busca os dados na basededados
		$query = "SELECT * FROM {$table_prefix}smssocial_conf_telegram tel";

		//print $query;
		//executa a query dos dados
		$rs = $wpdb->get_row($query);

		return $rs;

	} //fim getToken

	/**
	 * Pega se a pessoa ja esta cadastrada no sms social com o id do telegram
	 */ 
	public function getOnePessoaTelegram($id_telegram) {

		//variaveis globais
		global $wpdb, $table_prefix;

		//query para pegar o telegram
		$query = "SELECT id FROM {$table_prefix}smssocial_telegram_contato WHERE id_telegram = \"".$id_telegram."\" ";
		
		//executa a query
		$rs = $wpdb->get_row($query);
		
		//retorna o result set
		return $rs->id; 

	} //fim getPessoaTelegram

	/**
	 * Pega se a pessoa ja esta cadastrada no sms social com o id do telegram
	 */ 
	public function getPessoaTelegram($id_telegram = null) {

		//variaveis globais
		global $wpdb, $table_prefix;

		//monta o where
		$where = "";
		if(!is_null($id_telegram)) {
			$where = " WHERE id_telegram = \"".$id_telegram."\" ";
		}

		//query para pegar o telegram
		$query = "SELECT * FROM {$table_prefix}smssocial_telegram_contato {$where} ";
		
		//executa a query
		$rs = $wpdb->get_results($query);
		
		//retorna o result set
		return $rs->id; 

	} //fim getPessoaTelegram

	/**
	 * Metodo para inserir pessoas na base de dados tabela smssocial_contatos
	 * 
	 * @Params:
	 * @pessoas -> array com os dados do formulario
	 */
	public function insertPessoasTelegram($pessoas) {

		//variaveis globais
		global $wpdb, $table_prefix;
		//onde estará os campos comos valores da tabela
		$pes = array();

		//colunas e valores		
		$pes["nome"]			= $pessoas["nome"];
		$pes["id_telegram"]		= $pessoas["id_telegram"];

		//executa a insercao
		if($wpdb->insert("{$table_prefix}smssocial_telegram_contato", $pes)) {
			//retorna o id da tabela inserida
			return $wpdb->insert_id;
		}

		return false;

	} // fim insertPessoas

	/**
	 * Metodo para alterar os dados na tabela smssocial_contato
	 * 
	 * @Params:
	 * @pessoas -> array com os dados para alteração na base de dados 
	 */ 
	public function updatePessoasTelegram($pessoas) {

		//variaveis globais
		global $wpdb, $table_prefix;

		//onde estará os campos comos valores da tabela
		$pes = array();
		$contato_id = $pessoas["id"];
		//colunas e valores		
		$pes["nome"]			= $pessoas["nome"];
		$pes["celular"] 		= $pessoas["celular"];
		$pes["email"]	 		= $pessoas["email"];
		$pes["dt_cadastro"]		= date("Y-m-d H:i:s");
		$pes["id_telegram"]		= $pessoas["id_telegram"];

		//valor
		$where  = array('id'=>$contato_id);

		/*$wpdb->update("{$table_prefix}smssocial_contato", $pes, $where);
		$wpdb->show_errors();
		$wpdb->print_error();
		exit;*/

		//executa a alteracao
		if($wpdb->update("{$table_prefix}smssocial_contato", $pes, $where)) {

			/*$wpdb->delete("{$table_prefix}smssocial_grupo_contato", array("contato_id" => $contato_id));
			$wpdb->show_errors();
			$wpdb->print_error();
			exit;*/

			//deleta o relacionamento com os contatos e insere novamente
			if($wpdb->delete("{$table_prefix}smssocial_grupo_contato", array("contato_id" => $contato_id))) {
			
				//varre os grupos 
				foreach($pessoas["grupo_id"] as $grp_id) {
					//seta os valores
					$grp_cont["grupo_id"] 	= $grp_id;
					$grp_cont["contato_id"] = $contato_id;

					//insere na tabela smssocial_grupo_contato
					if($wpdb->insert("{$table_prefix}smssocial_grupo_contato", $grp_cont)) {
						$bollGrupoContato = true;
					} else {
						$bollGrupoContato = false;
					}//fim insercao
				} // fim varrer os grupos

				//verifica qual mensagem ira imprimir
				if($bollGrupoContato) {
					$_SESSION["msgOk"] = "Pessoa alterada com sucesso!";

					return $contato_id;
				} else {
					$_SESSION["msgErro"] = "Erro ao re-inserir pessoas e seus grupos!";

					return false;
				} //fim verificacao de mensagem

			} else {
				$_SESSION["msgErro"] = "Erro ao deletar os registros de relacionamento dos grupos com os contatos!";
				return false;
			} //fim verificacao se deletou todos os registros		

		} else {
			$_SESSION["msgErro"] = "Erro ao alterar pessoa!";
			return false;
		} // fim verificacao

	} // fim updatePessoas

	/**
	 * Metodo para inativar o contato
	 * 
	 * Params:
	 * $id -> identificador da pessoa que sera inativada
	 */
	public function deletePessoasTelegram($id) {

		//variaveis globais
		global $wpdb, $table_prefix;

		//seta como inativo o valor
		$pes["flg_atv"] = 0;

		//valor
		$where  = array('id'=>$id);

		//executa a alteracao
		if($wpdb->update("{$table_prefix}smssocial_contato", $pes, $where)) {
			$_SESSION["msgOk"] = "Pessoas deletada com sucesso!";
			return true;
		} else {
			$_SESSION["msgErro"] = "Erro ao deletar a pessoa!";
		} // fim verificacao

		return false;

	} // deletePessoas

	/**
	 * Metodo void para exportar os dados em excel 
	 * 
	 */ 
	public function exportarPessoasTelegram() {
		//variaveis globais
		global $wpdb, $table_prefix;
		
		//pega a instituicao do usuario logado
		$current_user = wp_get_current_user();
		$instituicao = get_user_meta($current_user->ID,"instituicao");

		//query para busca os dados na basededados
		$query = "SELECT pes.id, pes.nome, pes.celular, pes.email, grp.grupo, ins.instituicao
		          FROM {$table_prefix}smssocial_contato pes
		          INNER JOIN {$table_prefix}smssocial_grupo_contato grp_pes ON pes.id = grp_pes.contato_id
		          INNER JOIN {$table_prefix}smssocial_grupo grp ON grp_pes.grupo_id = grp.id
		          INNER JOIN {$table_prefix}smssocial_instituicao ins ON ins.id = grp.instituicao_id
		          WHERE pes.flg_atv = 1 
		          	AND grp.flg_atv = 1 
		          	AND ins.flg_atv = 1
		          	AND grp.instituicao_id = $instituicao[0];";
		//print $query;
		//executa a query dos dados
		$rs = $wpdb->get_results($query);

		$table = "<table >
		            <thead>
		              <tr>
		                <th>Numero</th>
		                <th>Nome</th>
		                <th>Celular</th>
		                <th>Email</th>
		                <th>Grupo</th>
		                <th>Instituicao</th>
		                <th>Id Telegram</th>
		              </tr>
		            </thead>
		            <tbody>";

		//varre os dados da tabela
		foreach($rs as $pes) {               
			$table .= " <tr>
		                  <td>$pes->id</td>
		                  <td>$pes->nome</td>
		                  <td>$pes->celular</td>
		                  <td>$pes->email</td>
		                  <td>$pes->grupo</td>
		                  <td>$pes->instituicao</td>
		                  <td>$pes->id_telegram</td>
		                </tr>";

		} //foreach

		$table .= "</tbody>
		          </table>";

		//nome do arquivo
		$arquivo = "pessoas_".date('YmdHis').".xls";

		//headers para download
		header("Content-type: application/vnd.ms-excel");
		header("Content-type: application/force-download");
		header ("Cache-Control: no-cache, must-revalidate");
		header ("Content-type: application/x-msexcel");
		header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
		header ("Pragma: no-cache");
		
		echo $table;
		exit;

	} //fim exportarPessoas

} //fim class pessoas
?>