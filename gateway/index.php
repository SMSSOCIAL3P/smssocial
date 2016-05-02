<?php
//variaveis globais
global $wpdb, $table_prefix;

//dados
$gtw = $wpdb->get_row("SELECT * FROM {$table_prefix}smssocial_gateway ");

?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Configuração do Provedor</h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">          
          <form name="form" id="form" action="<?php bloginfo('template_url'); ?>/gateway/actions.php" method="post" >
            <input type="hidden" name="gateway[id]" value="<?php echo $gtw->id; ?>">
            <div class="col-md-6">
              <label>Gateways</label>
              <select name="gateway[nome]" id="nome" class="form-control">
                <option value=""> Selecione o Gateway </option>
                <option value="Zenvia" <?php if($gtw->nome == "Zenvia"){ echo "selected"; } ?> > Zenvia </option>
              </select>
            </div>
            <div class="col-md-12">&nbsp;</div>
            <div class="col-md-6">
              <label>Usuário</label>
              <input type="text" name="gateway[usuario]" value="<?php echo $gtw->usuario; ?>" placeholder="Usuário do Gateway" class="form-control">
            </div>
            <div class="col-md-12">&nbsp;</div>
            <div class="col-md-6">
              <label>Senha</label>
              <input type="text" name="gateway[senha]" value="<?php echo $gtw->senha; ?>" placeholder="Senha do Gateway" class="form-control">
            </div>
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
