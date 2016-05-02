<?php
//variaveis globais
global $wpdb, $table_prefix;

//pega a instituicao do usuario logado | pega os dados do usuario que está logado
$current_user = wp_get_current_user();
$instituicao = get_user_meta($current_user->ID,"instituicao");

//pega os dados do request
$rq = $_REQUEST["mensagem"];
$where = "";
//verifica se existe valor para ser pesquisado
if(!empty($rq["mensagem"])) {
  $where .= ' AND p.post_content LIKE "%'.$rq["mensagem"].'%" ';
}
if(!empty($rq["grupo"])) {
  $where .= ' AND grp.grupo LIKE "%'.$rq["grupo"].'%" ';
}

if(!empty($rq["celular"])) {
  $where .= ' AND con.celular LIKE "%'.$rq["celular"].'%" ';
}
if(!empty($rq["deate"])) {
  //datas     
  $datas = explode("-",$rq["deate"]);
  $de = trim($datas[0]);
  $ate = trim($datas[1]);

  $where .= " AND p.post_date BETWEEN '" . FormataDataDB($de) . " 00:00:00' AND '" . FormataDataDB($ate) . " 23:59:59' ";
}

//query para busca os dados na basededados
$query = "SELECT p.ID AS id, m.meta_value AS chave_envio, p.post_content as recebida, cnt.nome, 
                cnt.celular, grp.grupo, p.post_date AS dt_cadastro
          FROM {$table_prefix}posts p, 
          {$table_prefix}postmeta m,
          {$table_prefix}smssocial_contato_mensagem cmsg,
          {$table_prefix}smssocial_contato cnt,
          {$table_prefix}smssocial_grupo_contato gcnt,
          {$table_prefix}smssocial_grupo grp
          WHERE p.ID = m.post_id
          AND cmsg.post_ID = p.ID
          AND cmsg.contato_id = cnt.id
          AND gcnt.contato_id = cnt.id
          AND gcnt.grupo_id = grp.id
          AND m.meta_key = 'chave_envio'
          AND grp.instituicao_id = $instituicao[0]
            $where
          ORDER BY p.post_date DESC ;";

//executa a query dos dados
$rs = $wpdb->get_results( $query );
?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/styles/plugins/daterangepicker/daterangepicker-bs3.css" />
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/styles/plugins/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/styles/plugins/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/styles/plugins/datepicker/bootstrap-datepicker.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Mensagens Recebidas</h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          
          <form name="form" id="form" action="<?php home_url(); ?>?ctr=respostaMsg&mt=index" method="post" >
            <div class="col-md-4">
              <input type="text" name="mensagem[mensagem]" value="<?php echo $rq["mensagem"]; ?>" placeholder="Mensagem" class="form-control">
            </div>
            <div class="col-md-4">
              <input type="text" name="mensagem[deate]" id="deate" value="<?php echo $rq["deate"]; ?>" placeholder="De/Até" class="form-control daterange" >
            </div>
            <div class="col-md-4">              
              <input type="text" name="mensagem[grupo]" value="<?php echo $rq["grupo"]; ?>" placeholder="Grupo" class="form-control">
            </div>
            <div class="col-md-12">&nbsp;</div>
            <div class="col-md-4">              
              <input type="text" name="mensagem[celular]" value="<?php echo $rq["celular"]; ?>" placeholder="Celular" class="form-control">
            </div>
            
            <div class="col-md-4" >
              <button class="btn " type="submit"> Pesquisar</button>&nbsp;
              
              <a href="<?php bloginfo('template_url'); ?>/respostaMsg/verificaRespostas.php" class="btn " >Atualizar</a>&nbsp;
            </div>
          </form>
          
        </div><!-- /.box-header -->

        <div class="box-body no-padding">
          <table id="tabela" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Chave Envio</th>
                <th>Mensagem Recebida</th>
                <th>Nome</th>
                <th>Celular</th>
                <th>Grupo</th>
                <th>Data</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php
              //varre os dados da tabela
              foreach($rs as $men) {
              ?>
                <tr>
                  <td><?php echo $men->chave_envio; ?></td>
                  <td><?php echo $men->recebida; ?></td>
                  <td><?php echo $men->nome; ?></td>
                  <td><?php echo $men->celular; ?></td>
                  <td><?php echo $men->grupo; ?></td>                  
                  <td><?php echo FormataData($men->dt_cadastro); ?></td>
                  <td>
                    <a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=respostaMsg&mt=addRespMensagem&id=<?php echo $men->id; ?>" title="Editar"> Responder </a> 
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

  $("#deate").daterangepicker({
    format: 'DD/MM/YYYY',
        locale: {
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar',
            fromLabel: 'De',
            toLabel: 'Até',
            customRangeLabel: 'Custom',
            daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex','Sab'],
            monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            firstDay: 1
        }
    
  });

  $("#atualizar").on( "click", function(){


  
  });

});
</script>