<?php
// Tornar o template disponível para tradução
// A tradução pode ser feita em /languages/
load_theme_textdomain( 'smssocial', TEMPLATEPATH . '/languages' );
 
$locale = get_locale();
$locale_file = TEMPLATEPATH . "/languages/$locale.php";
if ( is_readable($locale_file) )
   require_once($locale_file);
 
// Puxar o número de página
function get_page_number() {
          if ( get_query_var('paged') ) {
              echo ' | ' . __( 'Page ' , 'smssocial') . get_query_var('paged');
         }
 } // end get_page_number

//trabalhando com as sessoes do template
add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');

/**
 * Funcao para startar a sessao
 */ 
function myStartSession() {
    if(!session_id()) {
        session_start();
    }
} //fim myStartSession

/**
 * Funcao para destruir as funcao
 */ 
function myEndSession() {
    session_destroy ();
} //fim myEndSession


//Adiciona campos extras a tela de edição/inserção de usuários (by wptotal.com.br)
add_action('show_user_profile', 'add_extra_fields_user_profile');
add_action('edit_user_profile', 'add_extra_fields_user_profile');
add_action('user_new_form', 'add_extra_fields_user_profile');
//acrescentando os campos de perfil do thema quando ativo
function add_extra_fields_user_profile($user) {
?> 
	<h3>Campos para o SMS Social </h3>	 
	<table class="form-table">
		<tr>
			<th><label for="perfil">Perfil SMS</label></th>
			<td>
				<?php $perfilSms = get_the_author_meta('perfilSms', $user->ID); ?>
				<select name="perfilSms" id="perfilSms" >
					<option value="" > Selecione </option>
					<option value="1" <?php if($perfilSms == 1){ echo "selected='selected'";} ?> > Administrador</option>
					<option value="2" <?php if($perfilSms == 2){ echo "selected='selected'";} ?> > Gestor</option>
				</select>
				<span class="description">Selecione um Perfil.</span>
			</td>
		</tr>
		<tr>
			<th><label for="instituicao">Instituição</label></th>
			<td>
				<?php 
				//variaveis globais
				$rsIns = get_instituicao();

				$instituicao = get_the_author_meta('instituicao', $user->ID); 
				?>
				<select name="instituicao" id="instituicao" >
					<option value="" > Selecione </option>
					<?php
					$option = "";
					foreach($rsIns as $ins) {
						if($instituicao == $ins->id) {
							$selected = 'selected';
						} else {
							$selected = "";
						}
						$option .= "<option value='$ins->id' $selected> $ins->instituicao </option>";
					}
					echo $option;
					?>
				</select>
				<span class="description">Selecione uma Instituição.</span>
			</td>
		</tr>
	</table> 
<?php
} //fim add_extra_fields_user_profile

//Gravando campos extras inseridos no banco de dados (by wptotal.com.br)
add_action('personal_options_update', 'extra_fields_to_user_save');
add_action('edit_user_profile_update', 'extra_fields_to_user_save');
add_action('user_register', 'extra_fields_to_user_save');
/**
 * Funcao para gravar no banco os dados
 */  
function extra_fields_to_user_save($user_id) {
	if (!current_user_can('edit_user', $user_id))
		return false;
 	
	/* Campos a serem salvos */
	update_usermeta($user_id,'perfilSms',$_POST["perfilSms"]);
	update_usermeta($user_id,'instituicao',$_POST["instituicao"]);

} //fim extra_fields_to_user_save


//para criar as tabelas quando ativar o tema
add_action("after_switch_theme", "add_tables_smsSocial");

/**
 * Funcao para chamar as tabelas que seram instaladas quando ativado o plugin
 */
function add_tables_smsSocial () {

	include_once dirname( __FILE__ ) . '/install.php';

	smsSocial_create_tables();

} //fim add_tables_smsSocial


/**
 * Formata o campo para o padrao brasileiro
 * de AAAA-MM-DD para DD/MM/AAAA
 * @param $data aaaa-mm-dd
 * @return $data dd/mm/aaaa
 */
function FormataData($data){
	$data = substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);
	return $data;
}

/**
 * Formata do jeito que o DB aceita
 * de DD/MM/AAAA para AAAA-MM-DD
 * @param $data dd/mm/aaaa
 * @return $data aaaa-mm-dd
 */
function FormataDataDB($data){
	
	$data = substr($data,6,4)."-".substr($data,3,2)."-".substr($data,0,2);
	return $data;
	
}

/**
 * Formata do jeito que o DB aceita
 * de DD/MM/AAAA HH:mm para AAAA-MM-DD HH:mm
 * @param $data dd/mm/aaaa hh:mm
 * @return $data aaaa-mm-dd hh:mm
 */
function FormataDataHoraDB($data){
	
	$data = substr($data,6,4)."-".substr($data,3,2)."-".substr($data,0,2)." ".substr($data,11,5).":00";
	return $data;
	
}

/**
 * Formata do jeito que o Zenvia solicita
 */ 
function FormataAgendamento($data) {

}

/**
 * Funcao para retornar a instituicao
 * 
 * @param $id identificado da instituicao,caso seja nulo irá trazer tudo
 */ 
function get_instituicao($id = null) {
	//variaveis globais
	global $wpdb, $table_prefix;
	//verifica se existe o id
	if(!is_null($id)) {
		//executa a query
		$rsIns = $wpdb->get_row( "SELECT * FROM {$table_prefix}smssocial_instituicao ins WHERE ins.flg_atv = 1 AND ins.id = " . $id );
	} else {
		//executa a query
		$rsIns = $wpdb->get_results( "SELECT * FROM {$table_prefix}smssocial_instituicao ins WHERE ins.flg_atv = 1" );
	}

	//retorna o objeto 
	return $rsIns;
}

/**
 * Funcao para pegar a quantidade de usuarios relacionados a instituicao
 * 
 * @param $id identificado da instituicao,caso seja nulo irá trazer tudo
 */ 
function get_total_users_instituicao($id = null) {
	//variaveis globais
	global $wpdb, $table_prefix;
	//verifica se existe o id
	if(!is_null($id)) {
		//executa a query
		$where = " AND meta_value = " . $id ;
	} 

	//query para pegar a quantidade
	$rsIns = $wpdb->get_row( "SELECT COUNT(*) AS total FROM {$table_prefix}usermeta WHERE meta_key = 'instituicao' $where " );

	//retorna o objeto 
	return $rsIns->total;
}
?>