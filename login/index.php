
<div class="login-box">
  <div class="login-logo">
    <div id="blog-title">
      <span>
        <!--<b>SMS</b> Social-->
        <img src="<?php bloginfo('stylesheet_directory'); ?>/imagens/smsSocial.png" width="320" title="SMSSocial">
      </span>
    </div>
  </div><!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg" style="font-size:18pt">Bem-vindo ao SMS Social!</p>
    <form action="<?php bloginfo('template_url'); ?>/login/actions.php?mt=login" method="post">
      <div class="form-group has-feedback">
        <input type="text" class="form-control" name="login" placeholder="Login"/>
        <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="senha" placeholder="Senha"/>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Entrar</button>
        </div><!-- /.col -->
      </div>
    </form>
    <br>
    <!--
    <div class="login-box-msg">      
      <a style="font-size:25pt" href="#"><i style="margin-right:15px" class="fa fa-facebook-official"></i></a>
      <a style="font-size:25pt" href="#"><i class="fa fa-twitter"></i></a>
    </div>
    -->
    <div class="login-box-msg">
      
        <a style="margin-right:15px" href="<?php home_url(); ?>?topo=ajuda" target="_blank">Ajuda </a> 
        <a href="<?php home_url(); ?>?topo=fale_conosco" target="_blank"> Fale Conosco</a>
    </div>
  </div><!-- /.login-box-body -->
</div><!-- /.login-box -->

<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>