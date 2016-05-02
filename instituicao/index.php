<?php
//variaveis globais
global $wpdb, $table_prefix;

//pega os dados do request
$rq = $_REQUEST["instituicao"];
$where = "";
//verifica se existe valor para ser pesquisado
if(!empty($rq["instituicao"])) {
  $where .= ' AND instituicao LIKE "%'.$rq["instituicao"].'%"';
}

//query para busca os dados na basededados
$query = "SELECT ins.id, ins.instituicao, ins.dt_cadastro
          FROM {$table_prefix}smssocial_instituicao ins
          WHERE ins.flg_atv = 1
            $where;";

//executa a query dos dados
$rs = $wpdb->get_results( $query );
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Instituição</h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          
          <form name="form" id="form" action="<?php home_url(); ?>?ctr=instituicao&mt=index" method="post" >
            <div class="col-md-4">
              <input type="text" name="instituicao[instituicao]" value="<?php echo $rq["instituicao"]; ?>" placeholder="Descrição do instituição" class="form-control">
            </div>
            <div class="col-md-4">
              <button class="btn " type="submit"> Pesquisar</button>&nbsp;
              <a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=instituicao&mt=addInstituicao" class="btn btn-primary" >+ Instituição</a>
            </div>
          </form>
          
        </div><!-- /.box-header -->

        <div class="box-body no-padding">
          <table id="tabela" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Numero</th>
                <th>Instituição</th>
                <th>Num. Pessoas Incritas</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php
              //varre os dados da tabela
              foreach($rs as $inst) {               
              ?>                 
                <tr>
                  <td><?php echo $inst->id; ?></td>
                  <td><?php echo $inst->instituicao; ?></td>
                  <td><?php echo get_total_users_instituicao($inst->id); ?></td>
                  <td>
                    <a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=instituicao&mt=addInstituicao&id=<?php echo $inst->id; ?>" title="Editar"> Editar </a> -
                    <a href='<?php bloginfo('template_url'); ?>/instituicao/actions.php?tp=delete&id=<?php echo $inst->id; ?>' onclick="return confirm('Deseja realmente excluir este instituição?');" title="Excluir"> Excluir </a>
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