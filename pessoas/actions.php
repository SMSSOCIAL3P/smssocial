<?php
require_once('../../../../wp-load.php');

//classe encapsulada dos contatos
include('Pessoas.class.php');

//variaveis globais
global $wpdb, $table_prefix;

//instancia a classe
$Pessoa = new Pessoas();

//coloca como desativado
if($_REQUEST["tp"] == "delete") {

	//identificador do select
	$id = $_REQUEST["id"];

	//metodo para inativar o contato 
	$Pessoa->deletePessoas($id);

} else if($_REQUEST["tp"] == "importar") { //importar os mais contatos para dentro da base de dados

	//pega o file importado
	$arquivo_name = $_FILES['importar']['name'];
	$arquivo_temp = $_FILES['importar']['tmp_name'];
	$pessoas = $_REQUEST["pessoas"];
	
	//verifica se existe um arquivo a ser importado
	if($arquivo_name[0] != ""){
		
		//metodo para importar as pessoas
		$Pessoa->importarPessoas($arquivo_name, $arquivo_temp, $pessoas["grupo_id"]);
		
	} else {
		$_SESSION["msgErro"] = "Favor escolha um arquivo para importar!";
	}//fim verificacao do arquivo

} else if ($_REQUEST["tp"] == "exportar") {

	//metodo para exportar os dados das pessoas
	$Pessoa->exportarPessoas();

} else { 

	//pega os dados do request
	$pessoas = $_REQUEST["pessoas"];
	
	//verifica se tem o id
	//verifica se irá fazer um update ou insert
	if($pessoas["id"] != "") {
		//metodo para alterar os dados das pessoas que foram passadas
		$Pessoa->updatePessoas($pessoas);
	} else {
		//insert valores
		$Pessoa->insertPessoas($pessoas);		
	} //fim verificacao do valor 
}
//para direcionar
$_SESSION["ctr"] = "pessoas";
$_SESSION["mt"] = "index";

//direciona para o index
wp_redirect( home_url() );
?>