<?php
//classes para gerar os gráficos
require_once(get_template_directory() .'/class/HighchartsOptions.class.php');
require_once(get_template_directory() .'/class/Highcharts.class.php');

//variaveis globais
global $wpdb, $table_prefix;

//pega a instituicao do usuario logado | pega os dados do usuario que está logado
$current_user = wp_get_current_user();
$instituicao = get_user_meta($current_user->ID,"instituicao");

//pega os dados do request
$rq = $_REQUEST["mensagem"];
$from = "";
$where = "";
//verifica se existe valor para ser pesquisado
if(!empty($rq["grupo"])) {
  $where .= ' AND g.grupo LIKE "%'.$rq["grupo"].'%" ';
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
$query = "SELECT COUNT(*) AS total, p.post_date AS dt_cadastro
          FROM {$table_prefix}posts p,
          {$table_prefix}smssocial_contato c,
          {$table_prefix}smssocial_contato_mensagem cm,
          {$table_prefix}smssocial_grupo_contato gc,
          {$table_prefix}smssocial_grupo g
          $from
          WHERE p.ID = cm.post_ID
          AND cm.contato_id = c.id
          AND c.id = gc.contato_id
          AND gc.grupo_id = g.id
          AND p.post_type IN ('msg_recebida')
          AND g.instituicao_id = $instituicao[0]
          $where
          GROUP BY DATE(p.post_date);";
//print $query;
//executa a query dos dados
$resultado = $wpdb->get_results( $query );

//configura o grafico
$container = 'container';
$hc = new Highcharts('line',$container);
$hc->setTitulo('Msg. Enviadas');
$hc->setTituloLateral('Qtd.');

foreach ($resultado as $res) {	
	$hc->addCategoria(FormataData($res->dt_cadastro));
	$hc->addValores("Msg. Recebida", $res->total);	
}

$relatorio = $hc->draw();
$relatorio .= "<div id='container' style='width: 100%; height: 350px; margin: 0 auto; text-align: left;' ></div>";

?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/styles/plugins/daterangepicker/daterangepicker-bs3.css" />
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/styles/plugins/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/styles/plugins/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/styles/plugins/datepicker/bootstrap-datepicker.js"></script>

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/highcharts/js/highcharts.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/highcharts/js/modules/exporting.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Relatório de Mensagens Recebidas</h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          
          <form name="form" id="form" action="<?php home_url(); ?>?ctr=relatorio&mt=relatorioMsgEnviada" method="post" >
            <div class="col-md-3">
              <input type="text" name="mensagem[deate]" id="deate" value="<?php echo $rq["deate"]; ?>" placeholder="De/Até" class="form-control daterange" >
            </div>
            <div class="col-md-3">              
              <input type="text" name="mensagem[grupo]" value="<?php echo $rq["grupo"]; ?>" placeholder="Sub Grupo" class="form-control">
            </div>            
            <div class="col-md-3">              
              <input type="text" name="mensagem[categoria]" value="<?php echo $rq["categoria"]; ?>" placeholder="Categoria" class="form-control">
            </div>
            <div class="col-md-3" >
              <button class="btn " type="submit"> Pesquisar</button>&nbsp;
            </div>
          </form>          
        </div><!-- /.box-header -->

        <div class="box-body no-padding">
        	<div class="form-group col-md-12">
        	<?php
        		echo $relatorio;
        	?>
        	</div>
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
