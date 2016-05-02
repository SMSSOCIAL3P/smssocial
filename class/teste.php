<?php
require_once('../../../../wp-load.php');
require_once('../class/sms.class.php');

//variaveis globais
global $wpdb, $table_prefix;

//busca o gateway configurado
$gw = $wpdb->get_row("SELECT id, usuario, senha, nome FROM {$table_prefix}smssocial_gateway_configuracao");

//instancia a class
$sms = new SMS($gw->usuario, $gw->senha, $gw->nome);

$sms->recivedSMS();


?>