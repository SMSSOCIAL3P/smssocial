<?php
require_once('../../../../wp-load.php');

//classe encapsulada
include('ConfTelegram.class.php');

//instancia a classe
$ConfTelegram = new ConfTelegram();

//pega os dados do request
$confTelegram = $_REQUEST["confTelegram"];

//verifica se tem o id
//verifica se irá fazer um update ou insert
if($confTelegram["id"] != "") {

	//alterar o gateway
	$ConfTelegram->updateConfTelegram($confTelegram);	

} else {
	//insert valores
	$ConfTelegram->insertConfTelegram($confTelegram);
	
} //fim verificacao do valor 

//para direcionar
$_SESSION["ctr"] = "confTelegram";
$_SESSION["mt"] = "index";

//direciona para o index
wp_redirect( home_url() );
?>