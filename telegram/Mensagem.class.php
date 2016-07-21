<?php
/**
 * Classe para trabalhar os valores das mensagens do telegram
 * 
 */ 
class Mensagem {

	
	// metodo construtor da classe
	public function __construct() {}

	/**
	 * 
	 * Metodo para inserir os daddos na tabela de mensagens
	 * 
	 * Params:
	 * $mensagem = array com os dados a serem inseridos
	 * $ctel_id = identificacao do telegram
	 * $contatos =  contatos que será enviado
	 * 
	 */
	public function respMensagem($mensagem,$ctel_id, $pessoa_telegram_id) {

		//colunas e valores
		//insert colunas e valores
		$men["post_type"]		= "telegram_rec_msg_id";
		$men["post_content"] 	= $mensagem["mensagem"];
		$men["post_author"] 	= $mensagem["usuario_id"];

		//executa a insercao
		$post_id = wp_insert_post($men, true );

		//verifica se existe o id do post
		if(!is_wp_error($post_id)) {
			
			//seta a conf_id em postmeta
			add_post_meta($post_id, 'conf_telegram_id', $ctel_id);
			add_post_meta($post_id, 'telegram_recebida_msg_id', $mensagem["update_id"]);

			//variaveis globais
			global $wpdb, $table_prefix;

			//seta os valores da tabela smssocial_contato_mensagem
			$telMen["telegram_id"] = $pessoa_telegram_id;
			$telMen["post_ID"] = $post_id;

			if($wpdb->insert("{$table_prefix}smssocial_telegram_mensagem", $telMen)) {
				$erro = false;
			} else {
				$erro = true;
			}
			
			if($erro) {
				return false;
			}

		} else {

			return false;

		} // fim verificacao

		//return o valor inserido
		return $post_id;

	} //fim insertMensagem

	/**
	 * Metodo para pegar se a mensagem ja foi cadastrada
	 */ 
	public function getPostMeta($update_id = null) {
		
		//variaveis globais
		global $wpdb, $table_prefix;
		
		//verifica se o parametro esta nullo
		$where = "";
		if(!is_null($update_id)) {
			$where = " AND (meta.meta_key = 'telegram_recebida_msg_id' AND meta.meta_value = ".$update_id.") ";
		}//verifica o update

		//monta a query
		$query = "SELECT meta.post_id as post_id,  meta.meta_id as meta_id
          FROM {$table_prefix}postmeta meta
          WHERE 1 = 1
            $where;";

        //print $query;

        //executa a query
		$rs = $wpdb->get_row($query);

		//retorna a result set
		return $rs;

	} //modelo

	/**
	 * 
	 * Metodo para inserir os daddos na tabela de mensagens
	 * 
	 * Params:
	 * $mensagem = array com os dados a serem inseridos
	 * $ctel_id = identificacao da conf
	 * $contatos =  contatos que será enviado
	 * 
	 */
	public function insertMensagemTelegram($mensagem,$ctel_id, $contatos) {
		
		//colunas e valores
		//insert colunas e valores
		$men["post_type"]		= "telegram_msg_enviada";
		$men["post_content"] 	= $mensagem["mensagem"];
		$men["post_author"] 	= $mensagem["usuario_id"];

		//executa a insercao
		$post_id = wp_insert_post( $men, true );

		//verifica se existe o id do post
		if(!is_wp_error($post_id)) {
			
			//seta a gateway_id em postmeta
			add_post_meta($post_id, 'conf_telegram_id', $ctel_id);

			//verifica se existe categoria
			if(is_array($mensagem["categoria"])) {
				//insert taxonomias
				$this->insertTaxonomia($post_id, $mensagem["categoria"]);
			}//fim categoria
			
			//variaveis globais
			global $wpdb, $table_prefix;

			//varre os contatos para relacionar
			foreach($contatos as $cont) {
				//seta os valores da tabela smssocial_contato_mensagem
				$contMen["contato_id"] 	= $cont->id;
				$contMen["post_ID"] = $post_id;

				if($wpdb->insert("{$table_prefix}smssocial_contato_mensagem", $contMen)) {
					$erro = false;
				} else {
					$_SESSION["msgErro"] = "Erro ao inserir os relacionamentos da mensagem!";
					$erro = true;
				}
			} //fim foreach
			
			if(!$erro) {
				$_SESSION["msgOk"] = "Mensagem incluida com sucesso!";
			}

		} else {

			$_SESSION["msgErro"] = "Erro ao inserir uma nova mensagem!";			

		} // fim verificacao

		//return o valor inserido
		return $post_id;

	} //fim insertMensagemTelegram


	/**
	 * Metodo para altualizar a mensagem
	 * 
	 * Params:
	 * $mensagem = dados para serem atualizados da mensagem
	 * 
	 */ 
	public function updateMensagemTelegram($mensagem, $ctel_id, $contatos) {
		

		$this->insertMensagem($mensagem,$ctel_id, $contatos);

	} // fim updateMensagem

	
	/**
	 * 
	 * Metodo para inserir os dados na tabela de mensagens respondida smssocial_msg_respondida
	 * 
	 * Params:
	 * $mensagem = array com os dados a serem inseridos
	 * $ctel_id = identificacao da configuracao
	 * 
	 */
	public function insertMensagemRespondidaTelegram($mensagem,$ctel_id) {

		//colunas e valores
		//insert colunas e valores
		$men["post_type"]		= "telegram_resp_msg_id";
		$men["post_content"] 	= $mensagem["mensagem"];
		$men["post_author"] 	= $mensagem["usuario_id"];
		
		//executa a insercao
		$post_id = wp_insert_post( $men, true );

		//verifica se existe o id do post
		if(!is_wp_error($post_id)) {
			
			//seta a gateway_id em postmeta
			add_post_meta($post_id, 'conf_telegram_id', $ctel_id);
			add_post_meta($post_id, 'telegram_recebida_msg_id', $mensagem["rec_id"]);

			//variaveis globais
			global $wpdb, $table_prefix;

			//seta os valores da tabela smssocial_contato_mensagem
			$contMen["contato_id"] 	= $mensagem["contato_id"];
			$contMen["post_ID"] = $post_id;

			if($wpdb->insert("{$table_prefix}smssocial_contato_mensagem", $contMen)) {
				$erro = false;
			} else {
				$_SESSION["msgErro"] = "Erro ao inserir os relacionamentos da mensagem!";
				$erro = true;
			}
			
			if(!$erro) {
				$_SESSION["msgOk"] = "Mensagem enviada com sucesso!";
			}

		} else {

			$_SESSION["msgErro"] = "Erro ao inserir uma nova mensagem!";			

		} // fim verificacao

		//return o valor inserido
		return $post_id;

	} //fim insertMensagemRespondida

	/**
	 * Metodo para altualizar a mensagem respondida
	 * 
	 * Params:
	 * $mensagem = dados para serem atualizados da mensagem
	 * 
	 */ 
	public function updateMensagemRespondidaTelegram($mensagem, $ctel_id) {


		$this->insertMensagemRespondidaTelegram($mensagem,$ctel_id);

	} // fim updateMensagemRespondida

	/**
	 * Metodo void para exportar os dados em excel 
	 * 
	 */ 
	public function exportarMensagens() {
		//variaveis globais
		global $wpdb, $table_prefix;
		//pega a instituicao do usuario logado
		$current_user = wp_get_current_user();
		$instituicao = get_user_meta($current_user->ID,"instituicao");
	
		//query para busca os dados na basededados
		$query = "	SELECT 	p.ID AS id, 
							p.post_type AS tipo, 
							p.post_content AS mensagem, 
							p.post_date AS dt_cadastro, 
							c.nome, 
							c.celular, 
							g.grupo, 
							i.instituicao
					FROM {$table_prefix}posts p,
					{$table_prefix}smssocial_contato c,
					{$table_prefix}smssocial_contato_mensagem cm,
					{$table_prefix}smssocial_grupo_contato gc,
					{$table_prefix}smssocial_grupo g,
					{$table_prefix}smssocial_instituicao i
					WHERE p.ID = cm.post_ID
					AND cm.contato_id = c.id
					AND c.id = gc.contato_id
					AND gc.grupo_id = g.id
					AND g.instituicao_id = i.id
					AND g.instituicao_id = $instituicao[0]";

		//executa a query dos dados
		$rs = $wpdb->get_results($query);

		$table = "<table >
		            <thead>
		              <tr>
		                <th>Chave</th>
		                <th>Tipo</th>
		                <th>Mensagem</th>
        		        <th>Data Envio</th>
        		        <th>Pessoa</th>
        		        <th>Celular</th>
        		        <th>Grupo</th>
        		        <th>Instituicao</th>
		              </tr>
		            </thead>
		            <tbody>";

		//varre os dados da tabela
		foreach($rs as $men) {               
			$table .= " <tr>
		                  <td>$men->id</td>
		                  <td>$men->tipo</td>
		                  <td>$men->mensagem</td>
		                  <td>$men->dt_cadastro</td>
		                  <td>$men->nome</td>
		                  <td>$men->celular</td>
		                  <td>$men->grupo</td>
		                  <td>$men->instituicao</td>
		                </tr>";

		} //foreach

		$table .= "</tbody>
		          </table>";

		//nome do arquivo
		$arquivo = "mensagens_".date('YmdHis').".xls";

		//headers para download
		header("Content-type: application/vnd.ms-excel");
		header("Content-type: application/force-download");
		header ("Cache-Control: no-cache, must-revalidate");
		header ("Content-type: application/x-msexcel");
		header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
		header ("Pragma: no-cache");
		
		echo $table;
		exit;

	} //fim exportarMensagens


	/**
	 * 
	 * Metodo para inserir os daddos na tabela de posts
	 * 
	 * Params:
	 * $mensagem = dado a ser inserido
	 * $gw_id = identificacao do gateway
	 * $contato_id =  contatos que será enviado
	 * 
	 */
	public function insertMensagemRecebida($mensagem,$ctel_id, $contato_id) {
		
		//colunas e valores
		//insert colunas e valores
		$men["post_type"]		= "tel_msg_recebida";
		$men["post_content"] 	= $mensagem["mensagem"];
		$men["post_author"] 	= $mensagem["usuario_id"];
		$men["post_date"] 		= $mensagem["post_date"];

		//executa a insercao
		$post_id = wp_insert_post( $men, true );

		//verifica se existe o id do post
		if(!is_wp_error($post_id)) {
			
			//seta a gateway_id em postmeta
			add_post_meta($post_id, 'telegram_id', $ctel_id);
			add_post_meta($post_id, 'tel_chave_envio', $mensagem["chave_envio"]);
			add_post_meta($post_id, 'mensagem_id', $mensagem["msg_enviada_id"]);

			//variaveis globais
			global $wpdb, $table_prefix;

			//seta os valores da tabela smssocial_contato_mensagem
			$contMen["contato_id"] 	= $contato_id;
			$contMen["post_ID"] = $post_id;

			if($wpdb->insert("{$table_prefix}smssocial_contato_mensagem", $contMen)) {
				$erro = false;
			} else {
				$_SESSION["msgErro"] = "Erro ao inserir os relacionamentos da mensagem!";
				$erro = true;
			}
			
			if(!$erro) {
				$_SESSION["msgOk"] = "Mensagem enviada com sucesso!";
			}

		} else {

			$_SESSION["msgErro"] = "Erro ao inserir uma nova mensagem!";			

		} // fim verificacao

		//return o valor inserido
		return $post_id;

	} //fim insertMensagemRecebida


	/**
	 * Metodo para criar as categorias no wordpress
	 * 
	 * Parametros
	 * @param $post_id -> identificador dos post inserido
	 * @param $catagoria -> array com os nomes das catagorias a criar ou criadas
	 */
	public function insertTaxonomia($post_id, $categoria) {

		//inclusões
		require_once('../../../../wp-admin/includes/taxonomy.php');
		require_once('../../../../wp-includes/taxonomy.php');

		//insere as taxonomias
		$term_taxonomy_ids = wp_set_object_terms( $post_id, $categoria, 'category',true );

	} //fim createCategoria

} //fim class mensagem
?>