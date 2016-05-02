<?php
require_once('../../../../wp-load.php');

//classe encapsulada
include('Gateway.class.php');

//instancia a classe
$Gateway = new Gateway();

//pega os dados do request
$gateway = $_REQUEST["gateway"];

//verifica se tem o id
//verifica se irá fazer um update ou insert
if($gateway["id"] != "") {

	//alterar o gateway
	$Gateway->updateGateway($gateway);	

} else {
	//insert valores
	$Gateway->insertGateway($gateway);
	
} //fim verificacao do valor 

//para direcionar
$_SESSION["ctr"] = "gateway";
$_SESSION["mt"] = "index";

//direciona para o index
wp_redirect( home_url() );
?>