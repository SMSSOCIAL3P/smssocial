<?php
//inclusao do header.php
get_header();

//menu
get_template_part( "menu" );

//mensagens
if(!empty($_SESSION["msgOk"])) {
  
  $msg = $_SESSION["msgOk"];
  $class=" alert-success ";
  $_SESSION["msgOk"] = "";
  $display = "style='display:block;'";

} else if(!empty($_SESSION["msgErro"])){

  $msg = $_SESSION["msgErro"];
  $class=" alert-danger ";
  $_SESSION["msgErro"] = "";
  $display = "style='display:block;'";

} else if(!empty($_SESSION["msgAviso"])) {

  $msg = $_SESSION["msgAviso"];
  $class=" alert-warning ";
  $_SESSION["msgAviso"] = "";
  $display = "style='display:block;'";

} else {

  $display = "style='display:none;'";

}

$alert = "<div class=\"row\" $display >
            <div class='col-md-12'>
                <div class=\"alert $class alert-dismissable\"> $msg </div>
            </div>
          </div>";


  //pega os dados do usuario que está logado
  $current_user = wp_get_current_user();

  //verifica se é do topo
  if(!empty($_REQUEST["topo"])) {
    
    if($current_user->ID > 0) {
      print "<div class=\"content-wrapper\">";
    
        get_template_part( $_REQUEST["topo"] );

      print "</div>";
    } else {

      get_template_part( $_REQUEST["topo"] );

    }
  } else {    

    //verifica se o usuario está logado caso não esteja direciona para o login
    if(empty($current_user->ID)) {
      //printa a mensagem
      echo $alert;
      //tela de login do sistema
      get_template_part( "login/index" );

    } else {

  ?>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">

      <?php

          if($_REQUEST["ctr"]) {
            $ctr = $_REQUEST["ctr"];

            if($_REQUEST["mt"]) {
              $mt = $_REQUEST["mt"];
            }

          } else {
            //para onde deve direcionar a tela
            $ctr = $_SESSION["ctr"]; //controlador
            $mt = $_SESSION["mt"]; //metodo      
          }
          //printa a mensagem
          echo $alert;
          
          //tela de welcome
          get_template_part( $ctr."/".$mt );
      ?>

      </div><!-- /.content-wrapper -->
  <?php
    } //fim if usuario logado

  }//fim verificacao do topo

  //inclusao do footer.php
  get_footer(); 
?>