<?php
//variaveis globais
global $wpdb, $table_prefix;

//usuario logado
$user = wp_get_current_user();

//pega a instituicao do usuario logado
$instituicao = get_user_meta($user->ID,"instituicao");

//pegando o valor do id 
$id = $_SESSION["rq"]["id"];

//busca os dados no banco
if(!empty($id)) {
	$queryMsg = "SELECT p.post_content as mensagem, 
						cont.id as contato_id, 
						cont.nome as contato,
						grp.id as grupo_id, 
						grp.grupo as grupo
		         FROM {$table_prefix}smssocial_contato cont, 
		            {$table_prefix}smssocial_contato_mensagem cmsg, 
		            {$table_prefix}smssocial_grupo_contato gcont, 
		            {$table_prefix}smssocial_grupo grp, 
		            {$table_prefix}posts p, 
		            {$table_prefix}postmeta meta
		         WHERE cont.id = cmsg.contato_id
		           AND cmsg.post_ID = p.ID
		           AND gcont.contato_id = cont.id
		           AND gcont.grupo_id = grp.id
		           AND p.ID = meta.post_id
		           AND (meta.meta_key = 'telegram_id')
		           AND cont.flg_atv = 1 AND grp.flg_atv = 1
		           AND grp.instituicao_id = $instituicao[0]
				   AND p.ID = " . $id;
	//dados
	$mensagens = $wpdb->get_results($queryMsg);

	//monta uma forma mais facil de trabalhar nos selects
	$men = array();
	foreach( $mensagens as $mens ) {

		$men["mensagem"] = $mens->mensagem;
		$men["grupo_id"][$mens->grupo_id] = $mens->grupo_id;
		$men["grupo"][$mens->grupo_id] = $mens->grupo;
		$men["contato_id"][$mens->contato_id] = $mens->contato_id;
		$men["contato"][$mens->contato_id] = $mens->contato;

	} //fim foreach

	//pega as categorias do id
	$terms = wp_get_post_terms($id, 'category');


} //fim if id

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

//query para montar os contatos de seleção
$queryContato = "	SELECT cont.id, cont.nome, cont.celular 
					FROM {$table_prefix}smssocial_contato cont
					INNER JOIN {$table_prefix}smssocial_grupo_contato gcont ON cont.id = gcont.contato_id
					INNER JOIN {$table_prefix}smssocial_grupo grp on gcont.grupo_id = grp.id
					WHERE cont.flg_atv = 1
						AND grp.instituicao_id = $instituicao[0]
						AND cont.id_telegram IS NOT NULL
					GROUP BY cont.nome
					ORDER BY cont.nome;";
$rsContato  = $wpdb->get_results( $queryContato );

//autocomplete
foreach($rsContato as $autoCont) {
	//monta o array para os contatos
	$resultsContato["id"] = $autoCont->id;
	$resultsContato["nome"] = $autoCont->nome;

	$json[] = json_encode($resultsContato);
} // fim for each

//monta corretamente os dados que ira processar
$contats = implode(',', $json);


//query para montar os contatos de seleção
$queryCategoria = "	SELECT c.term_id AS id, c.name AS categoria
					FROM {$table_prefix}terms c
					ORDER BY c.name;";
//print $queryCategoria;
$rsCategoria = $wpdb->get_results( $queryCategoria );

//autocomplete
foreach($rsCategoria as $autoCat) {
	//monta o array para as categorias
	$resultsCategoria["id"] = $autoCat->id;
	$resultsCategoria["nome"] = $autoCat->categoria;

	$jsonCat[] = json_encode($resultsCategoria);
} // fim for each

//monta corretamente os dados que ira processar
$categorias = implode(',', $jsonCat);

?>

<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/styles/jquery-ui.1.12.0.css" />
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/styles/jquery.typeahead.css" />

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery-ui.1.11.4.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.typeahead.js"></script>

<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/styles/plugins/bootstrap-datetimepicker-master/css/bootstrap-datetimepicker.min.css" />
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/styles/plugins/bootstrap-datetimepicker-master/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/styles/plugins/bootstrap-datetimepicker-master/js/locales/bootstrap-datetimepicker.pt-BR.js"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Telegram Enviar Mensagem</h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">          
					<form name="form" id="form" action="<?php bloginfo('template_url'); ?>/telegram/actions.php" method="post" >
						<input type="hidden" name="msg[id]" id="msg_id" value="<?php echo $id; ?>">

						<input type="hidden" name="msg[usuario_id]" value="<?php echo $user->ID; ?>">

						<input type="hidden" name="tp" id="tp" value="">
						
						<div id="tabs" class="col-md-6" style="margin:15px;">
							<ul> 
								<li><a href="#grupos">Grupos</a></li> 
								<li><a href="#contatos">Contatos</a></li>
							</ul> 
							<div id="grupos"> <!-- ABA GRUPOS-->
								<div class="col-md-12" style="padding:0px">
									<div  class="col-md-10">
										<div class="typeahead-container">
								            <div class="typeahead-field">
									            <span class="typeahead-query">
													<input type="text" class="form-control" name="grupoAutoComplete" id="grupoAutoComplete" type="search" autocomplete="off">
												</span>
								            </div>
								        </div>
								    </div>
								    <div class="col-md-2">
							        	<button id="addGrupo" class="btn  btn-primary" type="button" > Incluir </button>
							        </div>
							        <div class="col-md-12">
										<ol id="ol_grupo"></ol>
									</div>
					            </div>
							</div> 
							<div id="contatos"> <!-- ABA CONTATOS-->
								<div class="col-md-12" style="padding:0px">
									<div class="col-md-10">
										<div class="typeahead-container">
								            <div class="typeahead-field">
									            <span class="typeahead-query">
													<input type="text" class="form-control" name="contAutoComplete" id="contAutoComplete" type="search" autocomplete="off">
												</span>
								            </div>
								        </div>
								    </div>
								    <div class="col-md-2">
							        	<button id="addContato" class="btn  btn-primary" type="button" > Incluir </button>
							        </div>
							        <div class="col-md-12">
										<ol id="ol_contato"></ol>
									</div>
					            </div>
							</div>
						</div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-12">
							<label>Categoria</label>
						</div>
						<div class="col-md-5">
							<div class="typeahead-container">
					            <div class="typeahead-field">
						            <span class="typeahead-query">
										<input type="text" class="form-control" name="catAutoComplete" id="catAutoComplete" type="search" autocomplete="off">
									</span>
					            </div>
					        </div>
					    </div>
					    <div class="col-md-1">					    	
				        	<button id="addCategoria" class="btn  btn-primary" type="button" > Incluir </button>
				        </div>
				        <div class="col-md-12">
							<span id="ol_categoria"></span>
						</div>
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-6">
							<label>Mensagem</label>
							<span ><i>Campo aceita até 160 caracteres.</i></span>
							<textarea name="msg[mensagem]" id="mensagem" col="15" rows="4" maxlength="160" placeholder="Mensagem a ser enviada" class="form-control"><?php echo $men["mensagem"]; ?></textarea>
						</div>
						
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-12">
							<a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=telegram" class="btn btn-default">Voltar</a>&nbsp;
							<button class="btn  btn-primary" id="enviar" type="button">Enviar Msg.</button>
						</div>
					</form>
       			</div><!-- /.box-header -->
			</div>
		</div>
	</div>
</section>

<script>
	$(function() { 
		//faz as abas
		$( "#tabs" ).tabs();

		//dados de contatos para o autocomplete
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

			if($("#grupoAutoCompleteID").val()) {
				//monta o que vai add
				var campo = "";

				campo += '<div class="col-md-6" id="grupo_' + $("#grupoAutoCompleteID").val() + '">';
				campo += '<div class="box ">';
				campo += '<div class="box-header with-border">';
				campo += '<input type="hidden" name="msg[grupo_id][]" class="grupoid" id="msg_grupo_'+$("#grupoAutoCompleteID").val()+'" value="'+$("#grupoAutoCompleteID").val()+'">';
				campo += '<h3 class="box-title">'+$("#grupoAutoComplete").val()+'</h3>';
				campo += '<div class="box-tools pull-right">';
				campo += '<button type="button" id="btn_grp_'+$("#grupoAutoCompleteID").val()+'" class="btn btn-box-tool" onClick="remGrupo('+ $("#grupoAutoCompleteID").val() +');">'
				campo += '<i class="fa fa-times"></i>';
				campo += '</button>';
				campo += '</div>';
				campo += '</div>';
				campo += '</div>';
				campo += '</div>';

				/*campo += '<li id="grupo_' + $("#grupoAutoCompleteID").val() + '" >';
				campo += '<input type="hidden" name="msg[grupo_id][]" class="grupoid" id="msg_grupo_'+$("#grupoAutoCompleteID").val()+'" value="'+$("#grupoAutoCompleteID").val()+'">';
				campo += $("#grupoAutoComplete").val();
				campo += '<button type="button" id="btn_grp_'+$("#contAutoCompleteID").val()+'" class="btn  btn-primary" onClick="remGrupo('+ $("#grupoAutoCompleteID").val() +');" > Remove </button>';
				campo += '</li>';*/

				//add
		        $("#ol_grupo").append(campo);

		        //limpa o campo
		        $("#grupoAutoComplete").val("");
		    }
	    });
	    
		//dados de contatos para o autocomplete
		var dataContato = [<?php echo $contats; ?>];
		$.typeahead({
            input: "#contAutoComplete",
            minLength: false,
            maxItem: 10,
            //order: "asc",
            hint: true,
            //cache: true,
            searchOnFocus: true,
            display: ["nome"],
            correlativeTemplate: true,            
            template: '<span class="name">{{nome}}</span>'+
            '<input type="hidden" id="contAutoCompleteID" value="{{id}}">',
            source: {
                teams: {
                    data: dataContato
                }
            },
            //debug: false
        });
				
		//funcao para add os contatos
		$("#addContato").click(function(){

			if($("#contAutoCompleteID").val()) {

				//monta o que vai add
				var campo = "";

				campo += '<div class="col-md-6" id="cont_' + $("#contAutoCompleteID").val() + '">';
				campo += '<div class="box ">';
				campo += '<div class="box-header with-border">';
				campo += '<input type="hidden" name="msg[contato_id][]" class="contatoid" id="msg_contato_'+$("#contAutoCompleteID").val()+'" value="'+$("#contAutoCompleteID").val()+'">';
				campo += '<h3 class="box-title">'+$("#contAutoComplete").val()+'</h3>';
				campo += '<div class="box-tools pull-right">';
				campo += '<button type="button" id="btn_'+$("#contAutoCompleteID").val()+'" class="btn btn-box-tool" onClick="remContato('+ $("#contAutoCompleteID").val() +');">'
				campo += '<i class="fa fa-times"></i>';
				campo += '</button>';
				campo += '</div>';
				campo += '</div>';
				campo += '</div>';
				campo += '</div>';
				
				/*campo += '<li id="cont_' + $("#contAutoCompleteID").val() + '" >';
				campo += '<input type="hidden" name="msg[contato_id][]" class="contatoid" id="msg_contato_'+$("#contAutoCompleteID").val()+'" value="'+$("#contAutoCompleteID").val()+'">';
				campo += $("#contAutoComplete").val();
				campo += '<button type="button" id="btn_'+$("#contAutoCompleteID").val()+'" class="btn  btn-primary" onClick="remContato('+ $("#contAutoCompleteID").val() +');" > Remove </button>';
				campo += '</li>';*/

				//add
		        $("#ol_contato").append(campo);

		        //limpa o campo
		        $("#contAutoComplete").val("");
			}
	    });

		//dados das categorias para o autocomplete
		var dataCategoria = [<?php echo $categorias; ?>];
		$.typeahead({
            input: "#catAutoComplete",
            minLength: false,
            maxItem: 10,
            //order: "asc",
            hint: true,
            //cache: true,
            searchOnFocus: true,
            display: ["nome"],
            correlativeTemplate: true,            
            template: '<span class="name">{{nome}}</span>'+
            '<input type="hidden" id="catAutoCompleteID" value="{{nome}}">',
            source: {
                teams: {
                    data: dataCategoria
                }
            },
            //debug: false
        });
				
		//funcao para add os contatos
		$("#addCategoria").click(function(){
			var valor = $("#catAutoCompleteID").val();
			var txt = valor;
			if(valor) {				
				//tira as tags
				String.prototype.stripHTML = function() {return this.replace(/<.*?>/g, '');}				
				txt = txt.stripHTML();
			} else {
				valor = $("#catAutoComplete").val();
				txt = valor;
			}

			//monta o que vai add
			var campo = "";

			campo += '<div class="col-md-3" id="cat_' + txt + '">';
			campo += '<div class="box ">';
			campo += '<div class="box-header with-border">';
			campo += '<input type="hidden" name="msg[categoria][]" class="categoriaid" id="msg_categoria_'+txt+'" value="'+txt+'">';
			campo += '<h3 class="box-title">'+txt+'</h3>';
			campo += '<div class="box-tools pull-right">';
			campo += '<button type="button" id="btn_'+txt+'" class="btn btn-box-tool" onClick="remCategoria(\''+ txt +'\');">'
			campo += '<i class="fa fa-times"></i>';
			campo += '</button>';
			campo += '</div>';
			campo += '</div>';
			campo += '</div>';
			campo += '</div>';


			/*campo += '<span id="cat_' + txt + '" >';
			campo += '<input type="hidden" name="msg[categoria][]" class="categoriaid" id="msg_categoria_'+txt+'" value="'+txt+'">';
			campo += txt;
			campo += '<button type="button" id="btn_'+txt+'" class="btn  btn-primary" onClick="remCategoria(\''+ txt +'\');" > X </button>';
			campo += '</span>';*/

			//add
	        $("#ol_categoria").append(campo);

	        //limpa o campo
	        $("#catAutoComplete").val("");
	    });

		//popula os campos com o grupo, contato e categoria
		if($("#msg_id").val() != "") {

			<?php

			if(is_array($men['grupo_id'])) {

				//varre os dados
				foreach($men['grupo_id'] as $key => $id) {
			?>

				var id = "<?php print $id; ?>";
				var grupo = "<?php print $men['grupo'][$key]; ?>";

				var campo = "";
				campo += '<div class="col-md-6" id="grupo_' + id + '">';
				campo += '<div class="box ">';
				campo += '<div class="box-header with-border">';
				campo += '<input type="hidden" name="msg[grupo_id][]" class="grupo_id" id="grupo_id'+id+'" value="'+id+'">';
				campo += '<h3 class="box-title">'+grupo+'</h3>';
				campo += '<div class="box-tools pull-right">';
				campo += '<button type="button" id="btn_grp_'+id+'" class="btn btn-box-tool" onClick="remGrupo('+ id +');">'
				campo += '<i class="fa fa-times"></i>';
				campo += '</button>';
				campo += '</div>';
				campo += '</div>';
				campo += '</div>';
				campo += '</div>';

				/*campo += '<li id="grupo_' + id + '" >';
				campo += '<input type="hidden" name="msg[grupo_id][]" class="grupo_id" id="grupo_id'+id+'" value="'+id+'">';
				campo += grupo;
				campo += '<button type="button" id="btn_grp_'+id+'" class="btn  btn-primary" onClick="remGrupo('+ id +');" > Remove </button>';
				campo += '</li>';*/

				//add
		        $("#ol_grupo").append(campo);

			<?php
				} //fim foreach
			}

			if(is_array($men['contato_id'])) {

				//varre os dados
				foreach($men['contato_id'] as $cont_chave => $cont_id) {
			?>

				var cont_id = "<?php print $cont_id; ?>";
				var contato = "<?php print $men['contato'][$cont_chave]; ?>";

				var campo = "";

				campo += '<div class="col-md-6" id="cont_' + cont_id + '">';
				campo += '<div class="box ">';
				campo += '<div class="box-header with-border">';
				campo += '<input type="hidden" name="msg[contato_id][]" class="contatoid" id="msg_contato_'+cont_id+'" value="'+cont_id+'">';
				campo += '<h3 class="box-title">'+contato+'</h3>';
				campo += '<div class="box-tools pull-right">';
				campo += '<button type="button" id="btn_'+cont_id+'" class="btn btn-box-tool" onClick="remContato('+ cont_id +');">'
				campo += '<i class="fa fa-times"></i>';
				campo += '</button>';
				campo += '</div>';
				campo += '</div>';
				campo += '</div>';
				campo += '</div>';

				//monta o que vai add
				
				/*campo += '<li id="cont_' + cont_id + '" >';
				campo += '<input type="hidden" name="msg[contato_id][]" class="contatoid" id="msg_contato_'+cont_id+'" value="'+cont_id+'">';				
				campo += contato;
				campo += '<button type="button" id="btn_'+cont_id+'" class="btn  btn-primary" onClick="remContato('+ cont_id +');" > Remove </button>';
				campo += '</li>';*/

				//add
		        $("#ol_contato").append(campo);

			<?php
				} //fim foreach
			}

			//verifica se existe categoria relacionada
			if(is_array($terms)) {
				//varre os dados				
				foreach($terms as $cat) {
			?>
					var cat_id = "<?php print $cat->term_id; ?>";
					var txt = "<?php print $cat->name; ?>";

					//monta o que vai add
					var campo = "";

					campo += '<div class="col-md-3" id="cat_' + txt + '">';
					campo += '<div class="box ">';
					campo += '<div class="box-header with-border">';
					campo += '<input type="hidden" name="msg[categoria][]" class="categoriaid" id="msg_categoria_'+txt+'" value="'+txt+'">';
					campo += '<h3 class="box-title">'+txt+'</h3>';
					campo += '<div class="box-tools pull-right">';
					campo += '<button type="button" id="btn_'+txt+'" class="btn btn-box-tool" onClick="remCategoria(\''+ txt +'\');">'
					campo += '<i class="fa fa-times"></i>';
					campo += '</button>';
					campo += '</div>';
					campo += '</div>';
					campo += '</div>';
					campo += '</div>';

					/*
					campo += '<span id="cat_' + txt + '" >';
					campo += '<input type="hidden" name="msg[categoria][]" class="categoriaid" id="msg_categoria_'+txt+'" value="'+txt+'">';
					campo += txt;
					campo += '<button type="button" id="btn_'+txt+'" class="btn  btn-primary" onClick="remCategoria(\''+ txt +'\');" > X </button>';
					campo += '</span>';*/

					//add
			        $("#ol_categoria").append(campo);
			<?php
				}
			}
			?>

		} //fim verificacao se precisa popular o grupo

		//enviar o form
		$("#enviar").on("click",function(){
			if($("#mensagem").val() != "") {
				if($(".contatoid").val()) {
					$("#form").submit();
				} else if($(".grupoid").val()) {
					$("#form").submit();
				} else {
					alert("Favor incluir um grupo ou um contato!");
				}
			} else {
				alert("Favor incluir uma mensagem!");
			}

			return false;			
		});
	    
	});
	//remove o contato add
	function remContato(id) {
		$(function() {
			//remove os dados adicionados
			$("#cont_"+id).remove();
			//$("#msg_contato_"+id).remove();
			//$("#btn_"+id).remove();
		});
    }

    //remove o grupo add
	function remGrupo(id) {
		$(function() {
			//remove os dados adicionados
			$("#grupo_"+id).remove();
			//$("#msg_contato_"+id).remove();
			//$("#btn_"+id).remove();
		});
    }

    //remove a categoria add
	function remCategoria(categoria) {
		$(function() {
			//remove os dados adicionados
			$("#cat_"+categoria).remove();
		});
    }
</script>