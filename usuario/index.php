<?php

//verifica se é o admin do wordpress
$current_user = wp_get_current_user();
$perfil = get_user_meta($current_user->ID,"wp_capabilities");
$admin = false;
if(array_key_exists("administrator", $perfil[0])) {
  $admin = true;
}

//pega os dados do request
$rq = $_REQUEST["usuario"];
//verifica se existe valor para ser pesquisado
if(!empty($rq["usuario"])) {
  $arg = array('search'=>$rq["usuario"]);
}

//busca todos os usuarios do wordpress
$users = get_users($arg);
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Usuários</h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          
          <form name="form" id="form" action="<?php home_url(); ?>?ctr=pessoas&mt=index" method="post" >
            <div class="col-md-4">
              <input type="text" name="usuario[usuario]" value="<?php echo $rq["usuario"]; ?>" placeholder="Login" class="form-control">
            </div>
            <div class="col-md-4">
              <button class="btn " type="submit"> Pesquisar</button>&nbsp;
              <a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=usuario&mt=addUsuario" class="btn btn-primary" >+ Usuario</a>
            </div>
          </form>
          
        </div><!-- /.box-header -->

        <div class="box-body no-padding">
          <table id="tabela" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Login</th>
                <th>Perfil SMS</th>
                <th>Instituição</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php
              //varre os dados da tabela
              foreach($users as $user) {              
                //para não ver o admin do wordpress
                if(!$admin) {
                  $perfil = get_user_meta($user->data->ID,"wp_capabilities");
                  if(array_key_exists("administrator", $perfil[0])) {
                    continue;
                  }
                }

                //funcao complementar para pegar os atributos add
                $userMeta = get_user_meta($user->data->ID);
                //trabalha os dados para apresentar se é admin ou gestor
                $perfilSMS = "";
                if($userMeta["perfilSms"][0] == 1) {
                  $perfilSMS = "Administrador";
                } else if($userMeta["perfilSms"][0] == 2) {
                  $perfilSMS = "Gestor";
                }// fim usermeta

                //verifica se existe o valor na tabela, caso exista traz o nome
                $ins = "";
                if(!empty($userMeta["instituicao"][0])) {
                  $ins = get_instituicao($userMeta["instituicao"][0]);
                }
              ?>
                <tr>                  
                  <td><?php echo $userMeta["first_name"][0]." ".$userMeta["last_name"][0]; ?></td>
                  <td><?php echo $user->data->user_login; ?></td>
                  <td><?php echo $perfilSMS; ?></td>
                  <td><?php echo $ins->instituicao; ?></td>
                  <td>
                    <a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=usuario&mt=addUsuario&id=<?php echo $user->data->ID; ?>" title="Editar"> Editar </a>
                  </td>
                </tr>
              <?php
              } //foreach
              ?>
            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
  </div>
</section>

<script type="text/javascript">
  $(function () {
    $('#tabela').dataTable({
      "bPaginate": true,
      "bLengthChange": false,
      "bFilter": false,
      "bSort": true,
      "bInfo": true,
      "bAutoWidth": false,
      "language": {
        "paginate" : {
             "previous": "Anterior",
             "next": "Próximo"
          },
            "lengthMenu": "Apresentando _MENU_ records per page",
            "zeroRecords": "Não encontramos - desculpe",
            "info": "Página _PAGE_ de _PAGES_",
            "infoEmpty": "Sem registros.",
            "infoFiltered": "(filtered from _MAX_ total records)"
        }
    });
  });
</script>