<?php
//variaveis globais
global $wpdb, $table_prefix;

//usuario logado
$user = wp_get_current_user();

//pega a instituicao do usuario logado | pega os dados do usuario que está logado
$instituicao = get_user_meta($user->ID,"instituicao");
$perfilSms = get_user_meta($user->ID,"perfilSms");

//pegando o valor do id 
$id = $_SESSION["rq"]["id"];

//busca os dados no banco
if(!empty($id)) {
	//dados
	$grp = $wpdb->get_row("SELECT * FROM {$table_prefix}smssocial_grupo WHERE id = ".$id);	
} //fim grupo_id

//somente o admin pode filtrar pela instituição
$whereIns = "";
if($perfilSms[0] == 2) {
	$whereIns = " AND id = $instituicao[0] ";
}

//pega as intituições
$rsIns = $wpdb->get_results("SELECT * FROM {$table_prefix}smssocial_instituicao WHERE flg_atv = 1 $whereIns");
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Adicionar Grupo</h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">          
					<form name="form" id="form" action="<?php bloginfo('template_url'); ?>/grupos/actions.php" method="post" >
						<input type="hidden" name="grupo[id]" value="<?php echo $id; ?>">

						<input type="hidden" name="grupo[id_usuario_wp]" value="<?php echo $user->ID; ?>">

						<div class="col-md-6">
							<label>Instituição</label>
							<select name="grupo[instituicao_id]" id="instituicao_id" class="form-control required">
								<option value=""> Selecione a Instituição </option>
								<?php
								$option = "";
								foreach($rsIns as $ins) {
									if($grp->instituicao_id == $ins->id) {
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
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-6">
							<label> Descrição do Grupo </label>
							<input type="text" name="grupo[grupo]" value="<?php echo $grp->grupo; ?>" placeholder="Descrição do Grupo" class="form-control required">
						</div>						
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-12">
							<a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=grupos" class="btn btn-default">Voltar</a>&nbsp;
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