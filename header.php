<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?> style="margin-top: 0px !important;">

<head >
  <meta charset="UTF-8">
  <title>
  	<?php
      if ( is_single() ) { single_post_title(); }
      elseif ( is_home() || is_front_page() ) { bloginfo('name'); echo ' | '; bloginfo('description'); get_page_number(); }
      elseif ( is_page() ) { single_post_title(''); }
      elseif ( is_search() ) { bloginfo('name'); echo ' | Search results for ' . wp_specialchars($s); get_page_number(); }
      elseif ( is_404() ) { bloginfo('name'); echo ' | Not Found'; }
      else { bloginfo('name'); wp_title('|'); get_page_number(); }
  	?>
  </title>

	<meta http-equiv="content-type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

  <!-- Font Awesome Icons -->
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
  <!-- Ionicons -->
  <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />

	<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" />
  <link rel="icon" type="image/vnd.microsoft.icon" href="imagens/favicon.png" />
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery 2.1.4 -->
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/styles/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/styles/plugins/iCheck/icheck.min.js"></script>

    <!-- DATA TABES SCRIPT -->
    <script src="<?php bloginfo('template_url'); ?>/styles/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="<?php bloginfo('template_url'); ?>/styles/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>

    <!-- SlimScroll -->
    <script src="<?php bloginfo('template_url'); ?>/styles/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='<?php bloginfo('template_url'); ?>/styles/plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="<?php bloginfo('template_url'); ?>/js/app.min.js" type="text/javascript"></script>
    
    <!-- Demo -->
    <script src="<?php bloginfo('template_url'); ?>/js/demo.js" type="text/javascript"></script>
    <!-- Validate -->
    <script src="<?php bloginfo('template_url'); ?>/js/jquery.validate.js" type="text/javascript"></script>
    <!-- Mascaras -->
    <script src="<?php bloginfo('template_url'); ?>/js/jquery.meiomask.1.14.js" type="text/javascript"></script>
    <script src="<?php bloginfo('template_url'); ?>/js/mask.js" type="text/javascript"></script>

	<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
	<?php wp_head(); ?>
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url'); ?>" title="<?php printf( __( '%s latest posts', 'smssocial' ), wp_specialchars( get_bloginfo('name'), 1 ) ); ?>" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="<?php printf( __( '%s latest comments', 'smssocial' ), wp_specialchars( get_bloginfo('name'), 1 ) ); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
</head>
 
<body class="login-page skin-blue sidebar-mini">

  <div id="wrapper">

    <?php
    //pega os dados do usuario que está logado
    $current_user = wp_get_current_user();
    if($current_user->ID > 0) {
      
    ?>
    <header class="main-header">
        <!-- Logo -->
        <a href="<?php bloginfo('template_url'); ?>/controller.php?ctr=home" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini" style="margin-top:5px">
                <img src="<?php bloginfo('stylesheet_directory'); ?>/imagens/smsSocial-mono-s.png" width="40px" title="SMSSocial">
            </span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg" style="margin-top:5px">
                <!--<b>SMS</b> Social-->
                <img src="<?php bloginfo('stylesheet_directory'); ?>/imagens/smsSocial-mono.png" width="70%" title="SMSSocial">
            </span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <!-- Messages: style can be found in dropdown.less-->
              <li class="dropdown messages-menu">
                <a href="<?php home_url(); ?>?topo=fale_conosco" target="blank" title="Fale Conosco"><i class="fa fa-envelope-o"></i></a>
              </li>
              <!-- Tasks: style can be found in dropdown.less -->
              <li class="dropdown tasks-menu">
                <a href="<?php home_url(); ?>?topo=ajuda" target="blank" title="Ajuda"><i class="fa fa-flag-o"></i></a>
              </li>
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <span class="hidden-xs">                    
                    <?php echo $current_user->display_name; ?>
                  </span>
                </a>
              </li>
              <!-- Control Sidebar Toggle Button -->
              <li>                
                <a href="<?php bloginfo('template_url'); ?>/login/actions.php?mt=logoff" title="Logoff"><i class="glyphicon glyphicon-log-out"></i></a>
              </li>
            </ul>
          </div>
        </nav>
      </header>

    <?php
    } else {
    ?>
        <div id="header">
            <div id="masthead">
                     
                <div id="access"></div><!-- #access -->
     
            </div><!-- #masthead -->
        </div><!-- #header -->
    <?php
    } //fim current->ID
    ?>
    <div class="main">
