<?php
//class para trabalhar com o banco de dados
require_once('../../../../wp-load.php');

//classe encapsulada dos contatos
include('Instituicao.class.php');

//instancia a classe
$Instituicao = new Instituicao();

//coloca como desativado
if($_REQUEST["tp"] == "delete") {

	//identificador do select
	$id = $_REQUEST["id"];
	//inativa o registro
	$Instituicao->deleteInstituicao($id);
	
} else { 

	//pega os dados do request
	$instituicao = $_REQUEST["instituicao"];

	//verifica se tem o id irá fazer um update senao insert
	if($instituicao["id"] != "") {
		
		//chama o metodo para alterar os dados
		$Instituicao->updateInstituicao($instituicao);

	} else {

		//chama o metodo para inserir a instituicao
		$Instituicao->insertInstituicao($instituicao);
		
	} //fim verificacao do valor 
}
//para direcionar
$_SESSION["ctr"] = "instituicao";
$_SESSION["mt"] = "index";

//direciona para o index
wp_redirect( home_url() );
?>