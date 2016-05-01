<?php
//pega os dados do usuario que está logado
$current_user = wp_get_current_user();
if($current_user->ID > 0) {

  //pega o perfilSms do usuario logado
  $perfilSms = get_user_meta($current_user->ID,"perfilSms");
  $perfil = get_user_meta($current_user->ID,"wp_capabilities");

  //pega a instituicao do usuario logado
  $instituicao = get_user_meta($current_user->ID,"instituicao");
  
  //sessao para deixa o menu ativo
  $ctr  = $_SESSION["ctr"];
  $mt   = $_SESSION["mt"];
?>
  <!-- Left side column. contains the sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <!--<li class="header"> MENU </li>-->
        <?php
        if($perfilSms[0] == 1 && array_key_exists("administrator", $perfil[0])) {
        ?>
          <li class="treeview">
            <a style="background:#2D3B42;" href="<?php bloginfo( 'url' ); ?>/wp-admin" target="_blank">
              <i class="fa fa-wordpress"></i> <span>Administração Wordpress</span>
            </a>
          </li>
        <?php
        }
        ?>

        <li class="treeview <?php if($ctr == "mensagem" || $ctr == "respostaMsg") echo 'active'; ?>">
          <a style="background:#263238;" href="#">
            <i class="fa fa-comment"></i> <span>Mensagem</span> <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li <?php if($mt == "addMensagem") echo 'class="active"'; ?> ><a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=mensagem&mt=addMensagem"><i class="fa fa-comment-o"></i> Nova</a></li>
            <li <?php if($ctr == "mensagem" && $mt == "index") echo 'class="active"'; ?> ><a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=mensagem"><i class="fa fa-send"></i> Enviadas</a></li>
            <li <?php if($ctr == "respostaMsg" && ($mt == "index" || $mt == "addRespMensagem")) echo 'class="active"'; ?> ><a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=respostaMsg"><i class="fa fa-inbox"></i> Recebidas</a></li>
          </ul>
        </li>
        
        <li class="treeview <?php if($ctr == "pessoas" || $ctr == "grupos") echo 'active'; ?>">
          <a style="background:#1F292E;" href="#">
            <i class="fa fa-male"></i><span>Contatos</span><i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li <?php if($ctr == "pessoas" && ($mt == "index" || $mt == "addPessoas")) echo 'class="active"'; ?> ><a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=pessoas"><i class="fa fa-user"></i> Pessoas</a></li>
            <li <?php if($ctr == "grupos") echo 'class="active"'; ?> ><a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=grupos"><i class="fa fa-users"></i> Grupos</a></li>
            <li <?php if($ctr == "pessoas" && $mt == "importar") echo 'class="active"'; ?> ><a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=pessoas&mt=importar"><i class="fa fa-mail-reply"></i> Importar</a></li>
          </ul>
        </li>

        <li class="treeview <?php if($ctr == "relatorio") echo 'active'; ?> ">
          <a style="background:#182024;" href="#">
            <i class="fa fa-bar-chart"></i><span>Relatórios</span><i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li <?php if($mt == "relatorioMsgEnviada") echo 'class="active"'; ?> ><a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=relatorio&mt=relatorioMsgEnviada"><i class="fa fa-send"></i> Mensagens Enviada</a></li>
            <li <?php if($mt == "relatorioMsgRecebida") echo 'class="active"'; ?> ><a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=relatorio&mt=relatorioMsgRecebida"><i class="fa fa-inbox"></i> Mensagens Recebidas</a></li>
            <li <?php if($mt == "relatorioCreditos") echo 'class="active"'; ?> ><a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=relatorio&mt=relatorioCreditos"><i class="fa fa-dollar"></i> Créditos</a></li>
            <!--<li><a href="../layout/boxed.html"><i class="fa fa-circle-o"></i> Créditos</a></li>-->
          </ul>
        </li>

        <?php
        if($perfilSms[0] == 1) {
        ?>
          <li class="treeview <?php if($ctr == "gateway" || $ctr == "instituicao" || $ctr == "usuario") echo 'active'; ?>" >
            <a href="#">
              <i class="fa fa-cogs"></i><span>Admin</span><i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
              
              <li <?php if($ctr == "instituicao") echo 'class="active"'; ?> ><a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=instituicao"><i class="fa fa-university"></i> Instituição</a></li>
              <li <?php if($ctr == "gateway") echo 'class="active"'; ?> ><a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=gateway"><i class="fa fa-rocket"></i> Provedor</a></li>
              <li <?php if($ctr == "usuario") echo 'class="active"'; ?> ><a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=usuario"><i class="fa fa-key"></i> Usuario</a></li>
             
            </ul>
          </li>
        <?php
        }
        ?>

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
<?php } //fim verificacao ?>