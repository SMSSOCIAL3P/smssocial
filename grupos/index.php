<?php
//variaveis globais
global $wpdb, $table_prefix;

//pega a instituicao do usuario logado | pega os dados do usuario que está logado
$current_user = wp_get_current_user();
$instituicao = get_user_meta($current_user->ID,"instituicao");
$perfilSms = get_user_meta($current_user->ID,"perfilSms");

//pega os dados do request
$rq = $_REQUEST["grupo"];
$where = "";
//verifica se existe valor para ser pesquisado
if(!empty($rq["grupo"])) {
  $where .= ' AND grp.grupo = '.$rq["grupo"];
}
if(!empty($rq["instituicao_id"])) {
  $where .= ' AND grp.instituicao_id = '.$rq["instituicao_id"];
}

//query para busca os dados na basededados
$query = "SELECT grp.id, grp.grupo, grp.dt_cadastro, ins.instituicao
          FROM {$table_prefix}smssocial_grupo grp
          INNER JOIN {$table_prefix}smssocial_instituicao ins ON grp.instituicao_id = ins.id
          WHERE grp.flg_atv = 1 
            AND ins.flg_atv = 1
            AND grp.instituicao_id = $instituicao[0]
            $where;";

//executa a query dos dados
$rs = $wpdb->get_results( $query );

//somente o admin pode filtrar pela instituição
if($perfilSms[0] == 1) {
  //pega as intituições
  $rsIns = $wpdb->get_results("SELECT * FROM {$table_prefix}smssocial_instituicao WHERE flg_atv = 1");
}

//pega quantas pessoas estão em cada grupo
$rsGrupoTotal = $wpdb->get_results("SELECT COUNT(*) AS total, grupo_id FROM wp_smssocial_grupo_contato GROUP BY grupo_id;");
if(!empty($rsGrupoTotal)) {
  $grpTotal = array();
  //mota corretamente os totais
  foreach($rsGrupoTotal as $grp){
    $grpTotal[$grp->grupo_id] = $grp->total;
  }
}
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Grupos</h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          
          <form name="form" id="form" action="<?php home_url(); ?>?ctr=grupos&mt=index" method="post" >
            <div class="col-md-4">
              <input type="text" name="grupo[grupo]" value="<?php echo $rq["grupo"]; ?>" placeholder="Descrição do grupo" class="form-control">
            </div>
            
            <?php if($perfilSms[0] == 1) { ?>
              <div class="col-md-4">              
                <select name="grupo[instituicao_id]" id="instituicao_id" class="form-control">
                  <option value=""> Selecione a Instituição </option>
                  <?php
                  $option = "";
                  foreach($rsIns as $ins) {
                    if($rq["instituicao_id"] == $ins->id) {
                      $selected = 'selected';
                    } else {
                      $selected = "";
                    }

                    $option .= "<option value='$ins->id' $selected> $ins->instituicao </option>";
                  }
                  echo $option;
                  ?>
                </select>
              </div>
            <?php } ?>

            <div class="col-md-4">
              <button class="btn " type="submit"> Pesquisar</button>&nbsp;
              <a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=grupos&mt=addGrupos" class="btn btn-primary" >+ Grupo</a>
            </div>
          </form>
          
        </div><!-- /.box-header -->

        <div class="box-body no-padding">
          <table id="tabela" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Numero</th>
                <th>Grupos</th>
                <th>Instituicao</th>
                <th>Data</th>
                <th>Num. Pessoas Incritas</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php
              //varre os dados da tabela
              foreach($rs as $grp) {
              ?>                 
                <tr>
                  <td><?php echo $grp->id; ?></td>
                  <td><?php echo $grp->grupo; ?></td>
                  <td><?php echo $grp->instituicao; ?></td>
                  <td><?php echo FormataData($grp->dt_cadastro); ?></td>
                  <td><?php echo empty($grpTotal[$grp->id]) ? 0 : $grpTotal[$grp->id]; ?></td>
                  <td>
                    <a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=grupos&mt=addGrupos&id=<?php echo $grp->id; ?>" title="Editar"> Editar </a> -
                    <a href='<?php bloginfo('template_url'); ?>/grupos/actions.php?tp=delete&id=<?php echo $grp->id; ?>' onclick="return confirm('Deseja realmente excluir este grupo?');" title="Excluir"> Excluir </a>
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