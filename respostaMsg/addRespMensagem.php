<?php
//variaveis globais
global $wpdb, $table_prefix;

//usuario logado
$user = wp_get_current_user();
$instituicao = get_user_meta($user->ID,"instituicao");

//pegando o valor do id 
$rec_id = $_SESSION["rq"]["id"];

//busca os dados no banco
$queryMsg = " SELECT p.ID AS id, cnt.id AS contato_id, p.post_content as recebida, cnt.nome, 
                cnt.celular, grp.grupo, p.post_date AS dt_cadastro
	          FROM {$table_prefix}posts p, 
	          {$table_prefix}postmeta m,
	          {$table_prefix}smssocial_contato_mensagem cmsg,
	          {$table_prefix}smssocial_contato cnt,
	          {$table_prefix}smssocial_grupo_contato gcnt,
	          {$table_prefix}smssocial_grupo grp
	          WHERE p.ID = m.post_id
	          AND cmsg.post_ID = p.ID
	          AND cmsg.contato_id = cnt.id
	          AND gcnt.contato_id = cnt.id
	          AND gcnt.grupo_id = grp.id
	          AND m.meta_key = 'chave_envio'
	          AND grp.instituicao_id = $instituicao[0]
	          AND p.ID = " . $rec_id;
//dados
$men = $wpdb->get_row($queryMsg);

//menrespondida
$queryResposta = "	SELECT p.ID AS resp_id, p.post_content AS mensagem
					FROM {$table_prefix}posts p, 
						{$table_prefix}postmeta m, 
						{$table_prefix}smssocial_contato_mensagem cmsg
					WHERE p.ID = m.post_id 
					AND cmsg.post_ID = p.ID
					AND m.meta_key = 'recebida_msg_id' 
					AND m.meta_value = '" . $rec_id . "'
					AND cmsg.contato_id = " . $men->contato_id;
//executa a query					
$respondida = $wpdb->get_row($queryResposta);
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Mensagem Recebida</h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">          
					<form name="form" id="form" action="<?php bloginfo('template_url'); ?>/respostaMsg/actions.php" method="post" >

						<input type="hidden" name="msg[id]" value="<?php echo $respondida->resp_id; ?>">

						<input type="hidden" name="msg[rec_id]" value="<?php echo $rec_id; ?>">

						<input type="hidden" name="msg[usuario_id]" value="<?php echo $user->ID; ?>">

						<div class="col-md-6">
							<label>Nome</label>
							<input type="hidden" name="msg[contato_id]" value="<?php echo $men->contato_id;?>" />
							<input type="text" name="msg[contato]" class="form-control" value="<?php echo $men->nome.' - '. $men->celular; ?>" />
			            </div>						
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-6">
							<label>Grupo</label>
							<input type="hidden" name="msg[grupo_id]" value="<?php echo $men->grupo_id;?>" />
							<input type="text" name="msg[grupo]" class="form-control" value="<?php echo $men->grupo; ?>" />
			            </div>
			            <div class="col-md-4">
							<label>Data</label>
							<input type="text" name="msg[data]" class="form-control" value="<?php echo FormataData($men->dt_cadastro); ?>" />
			            </div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-6">
							<label>Mensagem Recebida</label>
							<textarea name="msg[recebida]" col="15" rows="4" maxlength="160" class="form-control"><?php echo $men->recebida; ?></textarea>
						</div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-6">
							<label>Responder Mensagem</label>
							<textarea name="msg[mensagem]" col="15" rows="4" maxlength="160" placeholder="Responder Mensagem" class="form-control"><?php echo $respondida->mensagem; ?></textarea>
						</div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-12">
							<a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=respostaMsg" class="btn btn-default">Voltar</a>&nbsp;
							<button class="btn  btn-primary" type="submit">Enviar Msg</button>
						</div>
					</form>
       			</div><!-- /.box-header -->
			</div>
		</div>
	</div>
</section>
