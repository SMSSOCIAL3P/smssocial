<?php
//variaveis globais
global $wpdb, $table_prefix;

//pegando o valor do id 
$id = $_SESSION["rq"]["id"];

//busca os dados no banco
if(!empty($id)) {
	//filtrar o usuario que esta sendo editado
	$arg = array('search'=>$id);

	//busca todos os usuarios do wordpress
	$users = get_users($arg);

	//seta os campos
	$user["login"] = $users[0]->data->user_login;
	$user["nome"] = get_the_author_meta('first_name', $id);
	$user["sobrenome"] = get_the_author_meta('last_name', $id);
	$user["email"] = $users[0]->data->user_email;

	$disabled = "disabled='disabled'";
} //fim usuario
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Adicionar Usuário</h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">          
					<form name="form" id="form" action="<?php bloginfo('template_url'); ?>/usuario/actions.php" method="post" >
						<input type="hidden" name="usuario[id]" value="<?php echo $id; ?>">
						<div class="col-md-6">
							<label>Instituição</label>
							<?php 
							//variaveis globais
							$rsIns = get_instituicao();

							$instituicao = get_the_author_meta('instituicao', $id); 
							?>
							<select name="usuario[instituicao]" id="instituicao" class="form-control">
								<option value="" > Selecione </option>
								<?php
								$option = "";
								foreach($rsIns as $ins) {
									if($instituicao == $ins->id) {
										$selected = 'selected';
									} else {
										$selected = "";
									}
									$option .= "<option value='$ins->id' $selected> $ins->instituicao </option>";
								}
								echo $option;
								?>
							</select>
							<span class="description">Selecione uma Instituição.</span>
			            </div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-6">
							<label>Login <i>(obrigatório)</i></label>
							<input type="text" name="usuario[login]" value="<?php echo $user["login"]; ?>" placeholder="Login do Usuário" class="form-control" <?php print $disabled; ?>>
						</div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-6">
							<label>Nome <i>(obrigatório)</i></label>
							<input type="text" name="usuario[nome]" value="<?php echo $user["nome"]; ?>" placeholder="Nome do Usuário" class="form-control"  >
						</div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-6">
							<label>Sobrenome</label>
							<input type="text" name="usuario[sobrenome]" value="<?php echo $user["sobrenome"]; ?>" placeholder="Sobrenome do Usuário" class="form-control">
						</div>
						<div class="col-md-12">&nbsp;</div>
						<?php
						if(empty($disabled)) {
						?>
							<div class="col-md-6">
								<label>Senha</label>
								<input type="password" name="usuario[senha]" value="<?php echo $user["senha"]; ?>" placeholder="Senha" class="form-control">
							</div>
							<div class="col-md-12">&nbsp;</div>
						<?php
						}
						?>
						<div class="col-md-6">
							<label>Email</label>
							<input type="text" name="usuario[email]" value="<?php echo $user["email"]; ?>" placeholder="Email do Usuário" class="form-control">
						</div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-6">
							<label>Perfil SMS</label>
							<?php $perfilSms = get_the_author_meta('perfilSms', $id); ?>
							<select name="usuario[perfilSms]" id="perfilSms" class="form-control">
								<option value="" > Selecione </option>
								<option value="1" <?php if($perfilSms == 1){ echo "selected='selected'";} ?> > Administrador</option>
								<option value="2" <?php if($perfilSms == 2){ echo "selected='selected'";} ?> > Gestor</option>
							</select>
							<span class="description">Selecione um Perfil.</span>
			            </div>
						<div class="col-md-12">&nbsp;</div>
						
						<div class="col-md-12">
							<a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=usuario" class="btn btn-default">Voltar</a>&nbsp;
							<button class="btn  btn-primary" type="submit">Salvar</button>
						</div>
					</form>
       			</div><!-- /.box-header -->
			</div>
		</div>
	</div>
</section>
