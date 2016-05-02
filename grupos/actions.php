<?php
require_once('../../../../wp-load.php');

//classe encapsulada
include('Grupos.class.php');

//instancia a classe
$Grupos = new Grupos();

//coloca como desativado
if($_REQUEST["tp"] == "delete") {

	//identificador do select
	$id = $_REQUEST["id"];

	//inativa o registro
	$Grupos->deleteGrupo($id);

} else { 

	//pega os dados do request
	$grupo = $_REQUEST["grupo"];

	//verifica se tem o id | verifica se irá fazer um update ou insert
	if($grupo["id"] != "") {
		
		//atualiza o registro
		$Grupos->updateGrupo($grupo);

	} else {
		//insert valores
		$Grupos->insertGrupo($grupo);
		
	} //fim verificacao do valor 

}
//para direcionar
$_SESSION["ctr"] = "grupos";
$_SESSION["mt"] = "index";

//direciona para o index
wp_redirect( home_url() );
?>