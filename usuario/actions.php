<?php
require_once('../../../../wp-load.php');

//inclui a class para trabalhar
include('Usuario.class.php');

//instancia a class
$Usuario = new Usuario();


//pega os dados do request
$usuario = $_REQUEST["usuario"];

//dados para serem inputados ou alterados
$userData = array( 	'user_email' 	=> $usuario["email"],
					'first_name'	=> $usuario["nome"],
					'last_name'		=> $usuario["sobrenome"]);

//verifica se tem o id
//verifica se irá fazer um update ou insert
if($usuario["id"] != "") {
	//seta o id do usuario para alterar
	$userData['ID'] = $usuario["id"];

	//atualiza os dados do usuario
	$Usuario->updateUsuario($userData,$usuario["perfilSms"],$usuario["instituicao"]);

} else {
	//dados de insercao
	$userData['user_login'] = $usuario["login"];
	$userData['user_pass']	= $usuario["senha"];

	if(username_exists( $usuario["login"] )) {
		//mensagem de aviso
		$_SESSION["msgAviso"] = "Usuário já cadastrado!";
	} else {
		//insere um novo usuario
		$Usuario->insertUsuario($userData,$usuario["perfilSms"],$usuario["instituicao"]);	
	}

} //fim verificacao do valor 

//para direcionar
$_SESSION["ctr"] = "usuario";
$_SESSION["mt"] = "index";

//direciona para o index
wp_redirect( home_url() );
?>