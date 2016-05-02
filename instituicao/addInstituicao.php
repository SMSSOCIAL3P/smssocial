<?php
//variaveis globais
global $wpdb, $table_prefix;

//pegando o valor do id 
$id = $_SESSION["rq"]["id"];

//usuario logado
$user = wp_get_current_user();

//busca os dados no banco
if(!empty($id)) {
	//dados
	$inst = $wpdb->get_row("SELECT * FROM {$table_prefix}smssocial_instituicao WHERE id = ".$id);
} //fim instituicao_id
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Adicionar Instituição</h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">          
					<form name="form" id="form" action="<?php bloginfo('template_url'); ?>/instituicao/actions.php" method="post" >
						<input type="hidden" name="instituicao[id]" value="<?php echo $id; ?>">

						<input type="hidden" name="instituicao[id_usuario_wp]" value="<?php echo $user->ID; ?>">

						<div class="col-md-6">
							<label> Descrição da instituicao </label>
							<input type="text" name="instituicao[instituicao]" value="<?php echo $inst->instituicao; ?>" placeholder="Descrição do instituicao" class="form-control required">
						</div>

						<div class="col-md-12">&nbsp;</div>

						<div class="col-md-12">
							<a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=instituicao" class="btn btn-default">Voltar</a>&nbsp;
							<button class="btn  btn-primary" type="submit">Salvar</button>
						</div>
					</form>
       			</div><!-- /.box-header -->
			</div>
		</div>
	</div>
</section>
<script>
$().ready(function() {
  $('#form').validate({});
});
</script>