<?php
//variaveis globais
global $wpdb, $table_prefix;

//pega a instituicao do usuario logado
$current_user = wp_get_current_user();
$instituicao = get_user_meta($current_user->ID,"instituicao");

//pega os dados do request
$rq = $_REQUEST["mensagem"];
$from = "";
$where = "";
//verifica se existe valor para ser pesquisado
if(!empty($rq["mensagem"])) {
  $where .= ' AND p.post_content LIKE "%'.$rq["mensagem"].'%" ';
}
if(!empty($rq["grupo"])) {
  $where .= ' AND grp.grupo LIKE "%'.$rq["grupo"].'%" ';
}
if(!empty($rq["categoria"])) {
  $from = ",
          {$table_prefix}term_relationships tr,
          {$table_prefix}term_taxonomy tt,
          {$table_prefix}terms t ";
  $where .= ' AND tr.object_id = p.ID AND tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.term_id = t.term_id AND t.name LIKE "%'.$rq["categoria"].'%" ';
}

if(!empty($rq["deate"])) {
  //datas     
  $datas = explode("-",$rq["deate"]);
  $de = trim($datas[0]);
  $ate = trim($datas[1]);

  $where .= " AND p.post_date BETWEEN '" . FormataDataDB($de) . " 00:00:00' AND '" . FormataDataDB($ate) . " 23:59:59' ";
}

//query para busca os dados na basededados
$query = "SELECT p.ID as id,  p.post_content as mensagem, 
                cont.nome, cont.celular, grp.grupo, cont.id as contato_id,
                p.post_date as dt_cadastro, p.post_date_gmt as agendado, p.post_type
          FROM {$table_prefix}smssocial_contato cont, 
            {$table_prefix}smssocial_contato_mensagem cmsg, 
            {$table_prefix}smssocial_grupo_contato gcont, 
            {$table_prefix}smssocial_grupo grp, 
            {$table_prefix}posts p, 
            {$table_prefix}postmeta meta
            $from
          WHERE cont.id = cmsg.contato_id
            AND cmsg.post_ID = p.ID
            AND gcont.contato_id = cont.id
            AND gcont.grupo_id = grp.id
            AND p.ID = meta.post_id
            AND (p.post_type IN ('msg_enviada','msg_cancelada','msg_respondida'))
            AND (meta.meta_key = 'gateway_id' AND meta.meta_value = 1)
            AND cont.flg_atv = 1 AND grp.flg_atv = 1
            AND grp.instituicao_id = $instituicao[0]
            $where
          ORDER BY p.ID DESC, cont.nome ASC;";
//print $query;
//executa a query dos dados
$rs = $wpdb->get_results( $query );

?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/styles/plugins/daterangepicker/daterangepicker-bs3.css" />
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/styles/plugins/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/styles/plugins/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/styles/plugins/datepicker/bootstrap-datepicker.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Mensagens Enviadas</h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          
          <form name="form" id="form" action="<?php home_url(); ?>?ctr=mensagem&mt=index" method="post" >
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
              <input type="text" name="mensagem[categoria]" value="<?php echo $rq["categoria"]; ?>" placeholder="Categoria" class="form-control">
            </div>
            <div class="col-md-8" >
              <button class="btn " type="submit"> Pesquisar</button>&nbsp;
              <a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=mensagem&mt=addMensagem" class="btn btn-primary" >+ Mensagem</a>
            </div>
          </form>
          
        </div><!-- /.box-header -->

        <div class="box-body no-padding">
          <table id="tabela" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Chave</th>
                <th>Mensagem</th>
                <th>Nome</th>
                <th>Celular</th>
                <th>Grupo</th>
                <th>Categoria</th>
                <th>Data Envio</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php
              //varre os dados da tabela
              foreach($rs as $men) {
                //verifica se foi agendado
                if($men->agendado != "0000-00-00 00:00:00" && $men->post_type == "msg_enviada") {
                  //mensagens agendadas
                  $ag = true;
                  $css = "style=\"color: blue;\"";

                } else {
                  //verifica se foi cancelada
                  if($men->post_type == "msg_cancelada") {
                    $css = "style=\"color: red;\"";
                  } else { 
                    $css = "";
                  }

                  $ag = false;

                }//fim verificacao do css

              ?>   
                <tr <?php echo $css; ?> >
                  <td><?php echo $men->id; ?></td>
                  <td><?php echo $men->mensagem; ?></td>
                  <td><?php echo $men->nome; ?></td>
                  <td><?php echo $men->celular; ?></td>
                  <td><?php echo $men->grupo; ?></td>
                  <td><?php echo get_the_term_list( $men->id, 'category','',','); ?></td>
                  <td><?php echo FormataData($men->dt_cadastro); ?></td>
                  <td>
                    <a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=mensagem&mt=addMensagem&id=<?php echo $men->id; ?>" title="Editar"> Editar </a> 
                    <?php
                    if($ag) {
                    ?>
                      - <a href='<?php bloginfo('template_url'); ?>/mensagem/actions.php?tp=cancelarAg&post_id=<?php echo $men->id; ?>&contato_id=<?php echo $men->contato_id; ?>' onclick="return confirm('Deseja realmente cancelar este agendamento?');" title="Cancelar Mensagem"> Cancelar </a>
                    <?php
                    }
                    ?>
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
    <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">          
          <form name="formExportar" id="formExportar" action="<?php bloginfo('template_url'); ?>/mensagem/actions.php?tp=exportar" method="post" >
            <div class="col-md-4">
              <button class="btn" type="submit"> Exportar mensagens </button>&nbsp;
            </div>
          </form>          
        </div><!-- /.box-header -->
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

$(function() {
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
});
</script>