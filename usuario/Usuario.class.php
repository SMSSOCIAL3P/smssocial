<?php
/**
 * Classe para trabalhar os usuarios do wordpress
 * 
 */ 
class Usuario {

	
	// metodo construtor da classe
	public function __construct() {}

	/**
	 * 
	 * Metodo para inserir os usuarios
	 * 
	 * Params:
	 * @param $userData array com os dados a serem inseridos
	 * @param $perfil id do perfilSms
	 * @param $instituicao id da instituicao que esta relacionado
	 * 
	 */
	public function insertUsuario($userData, $perfilSms, $instituicao) {
		
		//insert valores
		$user_id = wp_insert_user( $userData ) ;

		//On success
		if ( !is_wp_error( $user_id ) ) {

			/* Campos a serem salvos */
			update_usermeta($user_id,'perfilSms',$perfilSms);
			update_usermeta($user_id,'instituicao',$instituicao);

		    $_SESSION["msgOk"] = "Usuário criado com sucesso!";
		} else {
			$_SESSION["msgErro"] = "Erro ao inserir um novo usuário!";
		}

		//return o valor inserido
		return $user_id;

	} //fim insertUsuario

	/**
	 * Metodo para altualizar o usuario
	 * 
	 * Params:
	 * @param $userData array com os dados a serem alterados
	 * @param $perfil id do perfilSms
	 * @param $instituicao id da instituicao que esta relacionado
	 * 
	 */ 
	public function updateUsuario($userData, $perfilSms, $instituicao) {
		
		//metodo para alterar os dados dos usuarios que foram passadas
		$user_id = wp_update_user( $userData ) ;

		//On success
		if ( !is_wp_error( $user_id ) ) {

			/* Campos a serem salvos */
			update_usermeta($user_id,'perfilSms',$perfilSms);
			update_usermeta($user_id,'instituicao',$instituicao);

		    $_SESSION["msgOk"] = "Usuário alterado com sucesso!";
		} else {
			$_SESSION["msgErro"] = "Erro ao alterar o usuário!";
		}

		return $user_id;

	} // fim updateUsuario

} //fim class usuario
?>