<?php
//variaveis globais
global $wpdb, $table_prefix;
//pega a instituicao do usuario logado | pega os dados do usuario que está logado
$current_user = wp_get_current_user();
$instituicao = get_user_meta($current_user->ID,"instituicao");

//query para montar os grupos de seleção
$queryGrupo = "	SELECT id, grupo 
				FROM {$table_prefix}smssocial_grupo 
				WHERE flg_atv = 1 
					AND instituicao_id = $instituicao[0];";
$rsGrupo  = $wpdb->get_results( $queryGrupo );

?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Importar Pessoas</h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">          
					<form name="form" id="form" enctype="multipart/form-data" action="<?php bloginfo('template_url'); ?>/pessoas/actions.php?tp=importar" method="post" >
						<input type="hidden" name="pessoas[id]" value="<?php echo $id; ?>">
						<div class="col-md-6">
							<label>Grupo</label>
							<select name="pessoas[grupo_id]" id="grupo_id" class="form-control">
								<option value=""> Selecione o Grupo </option>
								<?php
								$option = "";
								foreach($rsGrupo as $grupo) {
									$option .= "<option value='$grupo->id' $selected> $grupo->grupo </option>";
								}
								echo $option;
								?>
							</select>
			            </div>
			            <div class="col-md-12">&nbsp;</div>
						<div class="col-md-12">
							<span>***Deverá importar as pessoas em uma planilha .csv com tabulação no seguinte formato:</span>
							<br>
							Contato 1;5511999999991<br>
							Contato 2;5511999999992<br>
							Contato 3;5511999999993<br>
							Contato 4;5511999999994<br>
							Contato 5;5511999999995<br>
						</div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-6">
							<label>Arquivo</label>							
							<input type="file" class="form-control" name="importar[]" id="importar" placeholder="Arquivo a ser importado" >
						</div>						
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-12">
							<button class="btn  btn-primary" type="submit">Importar Pessoas</button>
						</div>
					</form>
       			</div><!-- /.box-header -->
			</div>
		</div>
	</div>
</section>