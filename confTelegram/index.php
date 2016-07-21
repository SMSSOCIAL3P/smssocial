<?php
//variaveis globais
global $wpdb, $table_prefix;

//dados
$ctel = $wpdb->get_row("SELECT * FROM {$table_prefix}smssocial_conf_telegram ");
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Configuração do Telegram</h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">          
          <form name="form" id="form" action="<?php bloginfo('template_url'); ?>/confTelegram/actions.php" method="post" >
            <input type="hidden" name="confTelegram[id]" value="<?php echo $ctel->id; ?>">
            <div class="col-md-6">
              <label>Nome:</label>
              <input type="text" name="confTelegram[nome]" value="<?php echo $ctel->nome; ?>" placeholder="Nome do Bot do Telegram" class="form-control">
            </div>
            <div class="col-md-12">&nbsp;</div>
            <div class="col-md-6">
              <label>Token:</label>
              <input type="text" name="confTelegram[token]" value="<?php echo $ctel->token; ?>" placeholder="Token gerado pelo Bot do Telegram" class="form-control">
            </div>
            <div class="col-md-12">&nbsp;</div>
            <div class="col-md-12">&nbsp;</div>
            <div class="col-md-12">              
              <button class="btn  btn-primary" type="submit">Salvar</button>
            </div>
          </form>
        </div><!-- /.box-header -->
      </div>
    </div>
  </div>
</section>
