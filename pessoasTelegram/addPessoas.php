<?php
//variaveis globais
global $wpdb, $table_prefix;

//pegando o valor do id 
$id = $_SESSION["rq"]["id"];

//pega a instituicao do usuario logado
$current_user = wp_get_current_user();
$instituicao = get_user_meta($current_user->ID,"instituicao");

//busca os dados no banco
if(!empty($id)) {
	//dados
	$query = "	SELECT * 
				FROM {$table_prefix}smssocial_contato pes 
				INNER JOIN {$table_prefix}smssocial_grupo_contato grp_pes ON grp_pes.contato_id = pes.id
				INNER JOIN {$table_prefix}smssocial_grupo grp ON grp.id = grp_pes.grupo_id				
				WHERE grp.instituicao_id = $instituicao[0]
				 AND pes.id = ".$id;	
	$arrayPessoa = $wpdb->get_results( $query );
	
	//monta o array corretamente
	$pes = array();
	foreach($arrayPessoa as $pessoa) {
		//monta o objeto		
		$pes["nome"] 			= $pessoa->nome;
		$pes["celular"] 		= $pessoa->celular;
		$pes["email"]			= $pessoa->email;
		$pes["id_telegram"]		= $pessoa->id_telegram;
		$pes["grupo_id"][] 		= $pessoa->grupo_id;
		$pes["grupo_nome"][] 	= $pessoa->grupo;
	}
} //fim contato

//query para montar os grupos de seleção
$queryGrupo = "SELECT id, grupo FROM {$table_prefix}smssocial_grupo where flg_atv = 1 AND instituicao_id = $instituicao[0];";
$rsGrupo  = $wpdb->get_results( $queryGrupo );

//query para montar os sub grupos de seleção
$queryGrupo = "	SELECT id, grupo 
				FROM {$table_prefix}smssocial_grupo 
				WHERE flg_atv = 1 
					AND instituicao_id = $instituicao[0]
				ORDER BY grupo;";

$rsGrupo  = $wpdb->get_results( $queryGrupo );

//autocomplete
foreach($rsGrupo as $autoGrupo) {
	//monta o array para os Grupos
	$resultsGrupo["id"] = $autoGrupo->id;
	$resultsGrupo["grupo"] = $autoGrupo->grupo;

	$jsonGrupo[] = json_encode($resultsGrupo);
} // fim for each

//monta corretamente os dados que ira processar
$grupos = implode(',', $jsonGrupo);

?>

<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/styles/jquery-ui.1.12.0.css" />
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/styles/jquery.typeahead.css" />

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery-ui.1.11.4.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.typeahead.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Adicionar Pessoas</h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">          
					<form name="form" id="form" action="<?php bloginfo('template_url'); ?>/pessoas/actions.php" method="post" >
						<input type="hidden" name="pessoas[id]" id="pessoas_id" value="<?php echo $id; ?>">
						<div class="col-md-12">
							<label>Grupo</label>
						</div>
						<div class="col-md-12">
							<div class="col-md-6">
								<div class="typeahead-container">
						            <div class="typeahead-field">
							            <span class="typeahead-query">
											<input type="text" class="form-control" name="grupoAutoComplete" id="grupoAutoComplete" type="search" autocomplete="off">
										</span>
						            </div>
						        </div>
						    </div>
						    <div class="col-md-3">
					        	<button id="addGrupo" class="btn  btn-primary" type="button" > Incliur </button>
					        </div>
					        <div class="col-md-12">
								<ol id="ol_grupo"></ol>
							</div>
			            </div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-6">
							<label>Nome</label>
							<input type="text" name="pessoas[nome]" id="nome" value="<?php echo $pes["nome"]; ?>" placeholder="Nome da Pessoa" class="form-control required">
						</div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-6">
							<label>Celular</label>
							<input type="text" name="pessoas[celular]" id="celular" value="<?php echo $pes["celular"]; ?>" placeholder="DDI(55) + DDD + Celular da Pessoa" class="form-control required" alt="celular">
						</div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-6">
							<label>Email</label>
							<input type="text" name="pessoas[email]" value="<?php echo $pes["email"]; ?>" placeholder="Email da Pessoa" class="form-control">
						</div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-6">
							<label>Identificador Telegram</label>
							<input type="text" name="pessoas[id_telegram]" value="<?php echo $pes["id_telegram"]; ?>" placeholder="Id Telegram" class="form-control">
						</div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-12">
							<a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=pessoas" class="btn btn-default">Voltar</a>&nbsp;							
							<button class="btn  btn-primary" id="salvar" type="submit">Salvar</button>
						</div>
					</form>
       			</div><!-- /.box-header -->
			</div>
		</div>
	</div>
</section>
<script>
$(function() {

	 $('#form').validate({});

	//dados de grupo para o autocomplete
	var dataGrupo = [<?php echo $grupos; ?>];
	$.typeahead({
        input: "#grupoAutoComplete",
        minLength: false,
        maxItem: 10,
        //order: "asc",
        hint: true,
        //cache: true,
        searchOnFocus: true,
        display: ["grupo"],
        correlativeTemplate: true,            
        template: '<span class="name">{{grupo}}</span>'+
        '<input type="hidden" id="grupoAutoCompleteID" value="{{id}}">',
        source: {
            teams: {
                data: dataGrupo
            }
        },
        //debug: false
    });
			
	//funcao para add os grupos
	$("#addGrupo").click(function(){
		//verifica se esta vazio
		if($("#grupoAutoCompleteID").val()) {
			//monta o que vai add
			var campo = "";
			campo += '<div class="col-md-6" id="grupo_' + $("#grupoAutoCompleteID").val() + '">';
			campo += '<div class="box ">';
			campo += '<div class="box-header with-border">';
			campo += '<input type="hidden" name="pessoas[grupo_id][]" class="grupoid" id="grupo_id'+$("#grupoAutoCompleteID").val()+'" value="'+$("#grupoAutoCompleteID").val()+'">';
			campo += '<h3 class="box-title">'+$("#grupoAutoComplete").val()+'</h3>';
			campo += '<div class="box-tools pull-right">';
			campo += '<button type="button" id="btn_grp_'+$("#grupoAutoCompleteID").val()+'" class="btn btn-box-tool" onClick="remGrupo('+ $("#grupoAutoCompleteID").val() +');">'
			campo += '<i class="fa fa-times"></i>';
			campo += '</button>';
			campo += '</div>';
			campo += '</div>';
			campo += '</div>';
			campo += '</div>';

			/*
			campo += '<li id="grupo_' + $("#grupoAutoCompleteID").val() + '" >';
			campo += '<input type="hidden" name="pessoas[grupo_id][]" class="grupo_id" id="grupo_id'+$("#grupoAutoCompleteID").val()+'" value="'+$("#grupoAutoCompleteID").val()+'">';
			campo += $("#grupoAutoComplete").val();
			campo += '<button type="button" id="btn_grp_'+$("#contAutoCompleteID").val()+'" class="btn  btn-primary" onClick="remGrupo('+ $("#grupoAutoCompleteID").val() +');" > Remove </button>';
			campo += '</li>';*/

			//add
	        $("#ol_grupo").append(campo);

	        //limpa o campo
	        $("#grupoAutoComplete").val("");
	    } else {
	    	alert("Cadastre este grupo, em Menu->Contatos>Grupos!");
	    } //fim verificacao id se esta vazio

    });

	//popula os campos com o grupo	
	if($("#pessoas_id").val() != "") {

		<?php
		if(is_array($pes['grupo_id'])) {			
			//varre os dados
			foreach($pes['grupo_id'] as $key => $id) {
		?>			
			var id = "<?php print $id; ?>";
			var grupo = "<?php print $pes['grupo_nome'][$key]; ?>";

			var campo = "";

			campo += '<div class="col-md-6" id="grupo_' + id + '">';
			campo += '<div class="box ">';
			campo += '<div class="box-header with-border">';
			campo += '<input type="hidden" name="pessoas[grupo_id][]" class="grupoid" id="grupo_id'+id+'" value="'+id+'">';
			campo += '<h3 class="box-title">'+grupo+'</h3>';
			campo += '<div class="box-tools pull-right">';
			campo += '<button type="button" id="btn_grp_'+id+'" class="btn btn-box-tool" onClick="remGrupo('+ id +');">'
			campo += '<i class="fa fa-times"></i>';
			campo += '</button>';
			campo += '</div>';
			campo += '</div>';
			campo += '</div>';
			campo += '</div>';

			/*
			campo += '<li id="grupo_' + id + '" >';
			campo += '<input type="hidden" name="pessoas[grupo_id][]" class="grupo_id" id="grupo_id'+id+'" value="'+id+'">';
			campo += grupo;
			campo += '<button type="button" id="btn_grp_'+id+'" class="btn  btn-primary" onClick="remGrupo('+ id +');" > Remove </button>';
			campo += '</li>';*/

			//add
	        $("#ol_grupo").append(campo);

		<?php
			} //fim foreach
		}
		?>

	} //fim verificacao se precisa popular o grupo

});

//remove o grupo add
function remGrupo(id) {
	$(function() {
		//remove os dados adicionados
		$("#grupo_"+id).remove();
		//$("#msg_contato_"+id).remove();
		//$("#btn_"+id).remove();
	});
}
</script>