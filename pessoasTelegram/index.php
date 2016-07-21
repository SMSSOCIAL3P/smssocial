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
  $where .= ' AND pes.nome LIKE "%'.$rq["pessoa"].'%" ';
}

//query para busca os dados na basededados
$query = "SELECT pes.id, pes.nome, pes.id_telegram
          FROM {$table_prefix}smssocial_telegram_contato pes
          WHERE 1 = 1
            $where;";

//executa a query dos dados
$rs = $wpdb->get_results( $query );

$pessoas = "SELECT pes.id, pes.nome, pes.celular, pes.id_telegram FROM {$table_prefix}smssocial_contato pes
            WHERE pes.flg_atv = 1;";
//executa a query dos dados
$rsPessoas = $wpdb->get_results( $pessoas );
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Pessoas Telegram</h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          
          <form name="form" id="form" action="<?php home_url(); ?>?ctr=pessoasTelegram&mt=index" method="post" >
            <div class="col-md-4">
              <input type="text" name="pessoas[pessoa]" value="<?php echo $rq["pessoa"]; ?>" placeholder="Nome" class="form-control">
            </div>
            <div class="col-md-4">
              <button class="btn " type="submit"> Pesquisar</button>
              <a href="<?php bloginfo('template_url'); ?>/pessoasTelegram/actions.php?tp=atualizar" class="btn btn-primary" > Atualizar</a>
            </div>
          </form>
          
        </div><!-- /.box-header -->

        <div class="box-body no-padding">
          <table id="tabela" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Numero</th>
                <th>Nome</th>
                <th>Id Telegram</th>
                <th>Relacionar a Pessoa</th>
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
                  <td><?php echo $pes->id_telegram; ?></td>
                  <td>
                    <select name="pes[pessoa_relacinada]" id='pessoa_relacionada_<?php echo $pes->id_telegram; ?>' onchange="relacionar(<?php echo $pes->id_telegram; ?>);" class="form-control">
                      <option value="" > Selecione</option>
                      <?php
                      foreach($rsPessoas as $pessoas) {

                        $selected = '';
                        if($pessoas->id_telegram == $pes->id_telegram) { 
                          $selected = 'selected="selected"'; 
                        }
                      ?>
                        <option value="<?php echo $pessoas->id; ?>"  <?php echo $selected; ?> > <?php echo $pessoas->celular ." - ". $pessoas->nome; ?></option>
                      <?php
                      }
                      ?>
                    </select>
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

    relacionar = function(telegram_id) {

      $.ajax({
        url: '<?php bloginfo('template_url'); ?>/pessoasTelegram/actions.php',
        data: 'tp=relacionar&tel_id='+telegram_id+'&pes_id='+$("#pessoa_relacionada_"+telegram_id).val(),
        dataType: 'json',
        cache: false,
        type: 'POST',
        success: function(data)
        { 
          if(data) { 
            if(data.id != "") {
              alert("Relacionamento realizado!");
            } else {
              alert("Ocorreu algum erro ao relacionar os dados!");
            }

          } else {
            alert("Ocorreu algum erro ao relacionar os dados com o telegram!");
          }//fim if data
        }
      });

    }

  });
</script>