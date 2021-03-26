<?php require_once('connections/dbsel.php'); ?>
<?php include('connections/comun.php'); ?>
<?php include_once('model_user.php'); ?>
<?php include_once('model_eventos.php'); ?>
<?php session_start();

if(isset($_SESSION['Id_user']))
{ 
$redirect=$_SESSION['redirect_to'];
unset($_SESSION['redirect_to']); 
header('Location: '.$redirect);
}
?>
 
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <title>Login | evenpot</title>

  	<!-- for Facebook -->
	<meta property="fb:app_id" content="1401397840112695"/>          
	<meta property="og:title" content="Descubre y crea eventos en tu ciudad"/>
	<meta property="og:site_name" content="evenpot" />
	<meta property="og:type" content="website"/>
	<meta property="og:url" content="https://www.evenpot.com"/>
	<meta property="og:image" content="https://www.evenpot.com/assets/img/bg01.jpg"/>
	<meta property="og:description" content="Unete a evenpot"/>   
	<meta property="og:street-address" content="Colombia"/>
	<meta property="og:locality" content=""/>
	<meta property="og:locale" content="es_ES">
			
	<!-- for Twitter -->          
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:creator" content="@evenpot">
	<meta name="twitter:site" content="@evenpot">
	<meta name="twitter:title" content="Descubre y crea eventos en tu ciudad" />
	<meta name="twitter:description" content="Unete a evenpot" />
	<meta name="twitter:image" content="https://www.evenpot.com/assets/img/bg01.jpg" />
	
	<?php include ('meta.php'); ?>
	<!-- CSS -->
    <link href="/css2/base.css" rel="stylesheet">
    
    <!-- CSS -->
	<link href="/css2/date_time_picker.css" rel="stylesheet">
	<link rel="stylesheet" href="/css/blueimp-gallery.css">
	<link rel="stylesheet" href="/css/blueimp-gallery-indicator.css">
	<link rel="stylesheet" href="admin/assets/css/bootstrap-fileupload.min.css" />
		
     <!-- Google web fonts -->
	<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Gochi+Hand' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400' rel='stylesheet' type='text/css'>
    
    <!--[if lt IE 9]>
      <script src="js/html5shiv.min.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->

<link href="/css/star-rating.css" rel="stylesheet">
<link rel='stylesheet' href='/css/jAlert.css'>

<style type="text/css">   
body{
    background: url(https://www.evenpot.com/images/backlogin.png);
	background-repeat: no-repeat;
	background-color: #233042;
	background-size: cover;
}

.vertical-offset-100{
    padding-top:100px;
}

.form-signin {
  max-width: 400px; 
  display:block;
  background-color: #f7f7f7;
  -moz-box-shadow: 0 0 3px 3px #888;
    -webkit-box-shadow: 0 0 3px 3px #888;
	box-shadow: 0 0 3px 3px #888;
  border-radius:2px;
}
.main{
	padding: 28px;
}
.social-box{
  margin: 0 auto;
  padding: 28px;
  border-bottom:1px #ccc solid;
}
.social-box a{
  font-weight:bold;
  font-size:18px;
  padding:8px;
}
.social-box a i{
  font-weight:bold;
  font-size:20px;
}
.heading-desc{
	font-size:20px;
	font-weight:bold;
	padding:18px 38px 0px 38px;
	
}
.form-signin .form-signin-heading,
.form-signin .checkbox {
  margin-bottom: 10px;
}
.form-signin .checkbox {
  font-weight: normal;
}
.form-signin .form-control {
  position: relative;
  font-size: 16px;
  height: 20px;
  padding: 20px;
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
}
.form-signin .form-control:focus {
  z-index: 2;
}
.form-signin input[type="text"] {
  margin-bottom: 10px;
  border-radius: 5px;
  
}
.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-radius: 5px;
}
.login-footer{
	background:#f0f0f0;
	margin: 0 auto;
	border-top: 1px solid #dadada;
	padding:20px;
}
.login-footer .left-section a{
	font-weight:bold;
	color:#8a8a8a;
	line-height:19px;
}
.mg-btm{
	margin-bottom:20px;
}
</style>

</head>
<body>

    <div class="layer"></div>
    <!-- Mobile menu overlay mask -->
	
    <header class="sticky"> 
    <?php include ('head_menu.php'); ?>
	</header>
<!-- /header container-->

<div class="section translucent-bg bg-image-1 blue">
<div class="container margin_60">
	<div class="row">
		<form class="form-signin mg-btm" method="post" action="login_success.php" >
    	<h3 class="heading-desc">Iniciar con facebook</h3>
		<div class="social-box">
			<div class="row mg-btm">
             <div class="col-md-12">
                <a href="#" class="btn btn-primary btn-block" onclick="fb_login();">
                  <i class="icon-facebook"></i>Ingresar con Facebook
                </a>
			</div>
			</div>
		</div>
		<h3 class="heading-desc">Iniciar con tu correo</h3>
		<div class="main">	
        
		<input type="text" name="Email" class="form-control input-lg" placeholder="Correo" required oninvalid="this.setCustomValidity('Debe llenar este campo')">
                    
        <input type="password" name="Clave" class="form-control" placeholder="Clave" required oninvalid="this.setCustomValidity('Debe llenar este campo')">
        <span class="help-block"><? echo $_SESSION["message"]; ?></span>
		</div>

		<div class="login-footer">
		<div class="row">
                        <div class="col-xs-6 col-md-6">
                            <div class="left-section">
								<a href="recover.php">¿Olvido su clave?</a><br>
								<a href="register.php">Registrese</a>
							</div>
                        </div>
                        
                        
                        <div class="col-xs-6 col-md-6 pull-right">
                            <input name="MM_insert2" type="hidden" value="form2" />
                            <button value="Ingresar" type="submit" class="btn btn-large btn-danger pull-right">Ingresar</button>
                        </div>
                    </div>
		
		</div>
      </form>
	</div>
</div>
</div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.4.0
    </div>
    <strong>Copyright &copy; 2019 <a href="https://evenpot.com">Evenpot</a>.</strong> All rights
    reserved.
  </footer>  

    <!-- script references -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
	</body>
</html>