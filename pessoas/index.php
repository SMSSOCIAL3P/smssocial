<?php
//variaveis globais
global $wpdb, $table_prefix;

//pega a instituicao do usuario logado | pega os dados do usuario que está logado
$current_user = wp_get_current_user();
$instituicao = get_user_meta($current_user->ID,"instituicao");

//pega os dados do request
$rq = $_REQUEST["pessoas"];
$where = "";
//verifica se existe valor para ser pesquisado
if(!empty($rq["pessoa"])) {
  $where .= ' AND pes.nome LIKE "%'.$rq["pessoa"].'%" OR pes.celular LIKE "%'. $rq["pessoa"] .'%" ';
}

//query para busca os dados na basededados
$query = "SELECT pes.id, pes.nome, pes.celular, grp.grupo
          FROM {$table_prefix}smssocial_contato pes
          INNER JOIN {$table_prefix}smssocial_grupo_contato grp_pes ON pes.id = grp_pes.contato_id
          INNER JOIN {$table_prefix}smssocial_grupo grp ON grp.id = grp_pes.grupo_id
          WHERE pes.flg_atv = 1 AND grp.flg_atv = 1
            AND grp.instituicao_id = $instituicao[0]
            $where;";

//executa a query dos dados
$rs = $wpdb->get_results( $query );
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Pessoas</h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          
          <form name="form" id="form" action="<?php home_url(); ?>?ctr=pessoas&mt=index" method="post" >
            <div class="col-md-4">
              <input type="text" name="pessoas[pessoa]" value="<?php echo $rq["pessoa"]; ?>" placeholder="Nome/Celular" class="form-control">
            </div>
            <div class="col-md-4">
              <button class="btn " type="submit"> Pesquisar</button>&nbsp;
              <a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=pessoas&mt=addPessoas" class="btn btn-primary" >+ Pessoas</a>
            </div>
          </form>
          
        </div><!-- /.box-header -->

        <div class="box-body no-padding">
          <table id="tabela" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Numero</th>
                <th>Nome</th>
                <th>Celular</th>
                <th>Grupo</th>                
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php
              //varre os dados da tabela
              foreach($rs as $pes) {               
              ?>                 
                <tr>
                  <td><?php echo $pes->id; ?></td>
                  <td><?php echo $pes->nome; ?></td>
                  <td><?php echo $pes->celular; ?></td>
                  <td><?php echo $pes->grupo; ?></td>
                  <td>
                    <a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=pessoas&mt=addPessoas&id=<?php echo $pes->id; ?>" title="Editar"> Editar </a> -
                    <a href='<?php bloginfo('template_url'); ?>/pessoas/actions.php?tp=delete&id=<?php echo $pes->id; ?>' onclick="return confirm('Deseja realmente excluir esta pessoa?');" title="Excluir"> Excluir </a>
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
          
          <form name="formExportar" id="formExportar" action="<?php bloginfo('template_url'); ?>/pessoas/actions.php?tp=exportar" method="post" >
            <div class="col-md-4">
              <button class="btn" type="submit"> Exportar dados </button>&nbsp;
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
</script>