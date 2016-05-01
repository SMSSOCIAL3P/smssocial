<div class="login-box" style="width:720px;">
  <div class="login-logo">
    <div id="blog-title">
      <span>
        <!--<b>SMS</b> Social-->
        <img src="<?php bloginfo('stylesheet_directory'); ?>/imagens/smsSocial.png" width="160" title="SMSSocial">
      </span>
    </div>
  </div><!-- /.login-logo -->
  <div class="login-box-body">
    <section class="content-header">
      <h1>Fale Conosco</h1>
    </section>
    <div class="col-xs-12">&nbsp;</div>
    <form name="form" id="form" action="<?php bloginfo('template_url'); ?>/login/actions.php?mt=fale_conosco" method="post" >
      <div class="col-xs-12">
        <label> Nome </label>
        <input type="text" name="nome" value="" placeholder="Nome" class="form-control required">
      </div>
      <div class="col-xs-12">&nbsp;</div>

      <div class="col-xs-6">
        <label> Email </label>
        <input type="text" name="email" value="" placeholder="Email" class="form-control required">
      </div>

      <div class="col-xs-6">
        <label> Telefone </label>
        <input type="text" name="telefone" value="" placeholder="Telefone" class="form-control required">
      </div>
      <div class="col-xs-12">&nbsp;</div>

      <div class="col-xs-12">
        <label> Texto </label>
        <textarea rows="2" cols="50" name="texto" class="form-control required"></textarea>
      </div>
      <div class="col-xs-12">&nbsp;</div>
      <div class="row">
        <div class="col-xs-12">
          <button class="btn  btn-primary" type="submit">Enviar</button>
        </div>
      </div>
    </form>
  </div><!-- /.login-box-body -->
</div><!-- /.fale-box -->
<script>
$().ready(function() {
  $('#form').validate({});
});
</script>