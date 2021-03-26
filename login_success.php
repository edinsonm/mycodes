<?php require_once('connections/dbsel.php'); ?>
<?php include('connections/comun.php'); ?>
<?php include_once('model_user.php'); ?>
<?php include_once('model_eventos.php'); ?>
<?php require_once('mail_sender.php'); ?>
<?php require_once('connections/OpenGraph.php'); ?>
<?php session_start();

if(isset($_SESSION['Id_user']))
{ 
$Id_user=$_SESSION['Id_user'];
}//echo "Con session Evenpot";

require_once('Facebook/autoload.php'); // change path as needed
//require_once('connections/OpenGraph.php');

# /js-login.php
$fb = new Facebook\Facebook([
  'app_id' => '1401397840112695',
  'app_secret' => 'ece48ed0bfc19a11aa9b3e3a4af976b2',
  'default_graph_version' => 'v3.1',
  ]);

$helper = $fb->getJavaScriptHelper();

//$userfb = $fb->getUser();


if (!$_POST["MM_insert2"]){

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (! isset($accessToken)) {
  echo 'No cookie set or no OAuth data could be obtained from cookie.';
  exit;
}

// Logged in
//var_dump($accessToken->getValue());

$_SESSION['fb_access_token'] = (string) $accessToken;

	try {
	  // Get the \Facebook\GraphNodes\GraphUser object for the current user.
	  // If you provided a 'default_access_token', the '{access-token}' is optional.
	  $response = $fb->get('/me?fields=id,first_name,middle_name,last_name,gender,location,birthday,picture,email', $accessToken);
	} catch(\Facebook\Exceptions\FacebookResponseException $e) {
	  // When Graph returns an error
	  echo 'Graph returned an error: ' . $e->getMessage();
	  exit;
	} catch(\Facebook\Exceptions\FacebookSDKException $e) {
	  // When validation fails or other local issues
	  echo 'Facebook SDK returned an error: ' . $e->getMessage();
	  exit;
	}

	//$userfb = $fb->getUser();
	//echo $userfb;
	$user = $response->getGraphUser();
	$userfb = $user['id'];
 
	try{
	//$user = $response->getGraphUser();
	//echo $user['email'];
	mysql_select_db($database_dbsel, $dbsel);
	$LoginRS__query="SELECT * FROM user WHERE Correo='".$user['email']."'";
	$LoginRS = mysql_query($LoginRS__query, $dbsel) or die(mysql_error());
	  if($row = mysql_fetch_array($LoginRS)){
		$update=1;  
	  	$_SESSION['Id_user']= $row['Id_user'];
		$_SESSION["k_name"] = $row['Nombre'];
		$_SESSION["k_apel"] = $row['Apellidos'];
		
		if($row['Nombre']) $Nombre=$row['Nombre'];
		  else  $Nombre=$user['first_name']; 
		  
		  if($row['Nombre2']) $Nombre2=$row['Nombre2'];
		  else $Nombre2=$user['middle_name'];
		  
		  if($row['Apellidos']) $Apellidos=$row['Apellidos'];
		  else $Apellidos=$user['last_name'];
		  
		  $time = strtotime($user['birthday']);
		  $Birthday = date('Y-n-j', $time);
		  
		  if($row['Gender']!=0) $Gender=$row['Gender'];
		  else  {
		  if($user['gender']=='male') $Gender='1';
		  if($user['gender']=='female') $Gender='2';
		  }

		  if($row['Birthday']!='') $Birthday=$row['Birthday'];
		  else  {
		  $Birthday=($user['Birthday']);
		  }
		  
		  //if($row['Photo_user']) $Photo_user=$row['Photo_user'];
		  //else 
		  $Photo_user = file_get_contents("https://graph.facebook.com/".$userfb."/picture?type=large");
		  file_put_contents('images/profile/photofb'.$userfb.'.jpg', $Photo_user);
		  $Photo_user = 'images/profile/photofb'.$userfb.'.jpg';
		  
		  $Fecha_cnx=date("Y-n-j").date(" H:i:s");
		  
		  
		  $Link=$user['link'];
		  
		  if($row['Nick']) $Nick=$row['Nick'];
		  else $Nick=$user['username'];
		  
		  //if ($user['location']['name']) $Location=$user['location']['name'];
		  //$Bio=$user['bio'];
		  $Timezone=$user['timezone'];
		  $Locale=$user['locale'];
		  
		  if(update_user($row['Id_user'], $Nombre, $Nombre2, $Apellidos, $Birthday, $Photo_user, $Gender, $Fecha_cnx, $Link, $Nick, $Timezone, $Locale, $userfb)==true)
		  $message="";
		  else $message="Error de base de datos";
	  }
	  else{
		  $Nombre=$user['first_name']; 
		  $Nombre2=$user['middle_name'];
		  $Apellidos=$user['last_name'];
		  $Correo=$user['email'];
		  $time = strtotime($user['birthday']);
		  $Birthday = date('Y-n-j', $time );
		  $Fecha_cnx=date("Y-n-j").date(" H:i:s");
		  if($user['gender']=='male')
		  {$Gender='1';}
		  if($user['gender']=='female')
		  {$Gender='2';}
		  if($user['picture']){
		  $Photo_user = "https://graph.facebook.com/".$userfb."/picture?type=large";}
		  $Link=$user['link'];
		  $Username=$user['email'];
		  $Location=$user['location']['name'];
		  $Bio=$user['bio'];
		  $Timezone=$user['timezone'];
		  $Locale=$user['locale'];
		  $Age_range=$user['age_range'];
		  $Fecha=date("Y-n-j").date(" H:i:s");
		  
	
		 $insertSQL = sprintf("Insert into user (Nombre, Nombre2, Apellidos, Correo, Birthday, Age_range, Photo_user, Fecha_cnx, Fecha, Gender, Link, Username, Location, Bio, Timezone, Locale, Source, fb_user) 
							VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
	   					GetSQLValueString($Nombre, "text"), 
						GetSQLValueString($Nombre2, "text"), 
						GetSQLValueString($Apellidos, "text"), 
						GetSQLValueString($Correo, "text"), 
						GetSQLValueString($Birthday, "text"),
						GetSQLValueString($Age_range, "text"),
						GetSQLValueString($Photo_user, "text"),
						GetSQLValueString($Fecha_cnx, "text"),
						GetSQLValueString($Fecha, "text"),
						GetSQLValueString($Gender, "text"),
						GetSQLValueString($Link, "text"),
						GetSQLValueString($Username, "text"),
						GetSQLValueString($Location, "text"),
						GetSQLValueString($Bio, "text"),
						GetSQLValueString($Timezone, "text"),
						GetSQLValueString($Locale, "text"),
						GetSQLValueString($Source, "text"),
						GetSQLValueString($userfb, "text"));
						$Result1 = mysql_query($insertSQL, $dbsel) or die(mysql_error());
						
			$Asunto="Bienvenido a evenpot";
			$ImagenPpal = "assets/img/bg01.jpg";
			$Text1="Bienvenido a evenpot";
			$Text2="Tu registro mediante Facebook fue exitoso";
			$Text3="";
			$Textbutton="Descubrir eventos";
			$URLButton = "http://www.evenpot.com/"; 
			new_send_mail($Asunto, $Correo, $ImagenPpal, $Text1, $Text2, $Text3, $Textbutton, $URLButton);	
		
		 $LoginRS__query=sprintf("SELECT Id_user, Nombre, Nombre2, Nick, Apellidos FROM user WHERE Correo='%s'", $user['email']);
   	  $LoginRS = mysql_query($LoginRS__query, $dbsel) or die(mysql_error());
	  $row = mysql_fetch_array($LoginRS);
	  
		$_SESSION['Id_user']= $row['Id_user'];
		$_SESSION["k_name"] = $Nombre;
		$_SESSION["k_apel"] = $Apellidos;
		unset($_SESSION['verifica']);
		unset($_SESSION['redirect_to']);
		$_SESSION['verifica']=1;
		$redirect = "welcome";			
		header('Location: '.$redirect);
		} //cierre else
			
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){
		$Ip_ini = $_SERVER['HTTP_CLIENT_IP'];}
		else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$Ip_ini =  $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
		$Ip_ini =  $_SERVER['REMOTE_ADDR'];
		$Fecha_cnx=date("Y-n-j").date(" H:i:s");
		$query = 'INSERT INTO event_log (Id_user, Fecha_ini, Ip_ingreso) VALUES (\''.$row['Id_user'].'\',\''.$Fecha_cnx.'\',\''.$Ip_ini.'\')';
		mysql_query($query) or die(mysql_error());
		}
	}
	catch (FacebookApiException $e) {
    error_log($e);
    $userfb = null;
	}
}
	 
function quitar($mensaje)
{
    $nopermitidos = array("'",'\\','<','>',"\"");
    $mensaje = str_replace($nopermitidos, "", $mensaje);
    return $mensaje;
}
      
if (isset($_POST["MM_insert2"]) && ($_POST["MM_insert2"] == "form2")) {
if(trim($_POST["Email"]) != "" && trim($_POST["Clave"]) != "")
{
    // Puedes utilizar la funcion para eliminar algun caracter en especifico
    //$usuario = strtolower(quitar($HTTP_POST_VARS["usuario"]));
    //$password = $HTTP_POST_VARS["password"];
    
    // o puedes convertir los a su entidad HTML aplicable con htmlentities
    $Email = strtolower(htmlentities($_POST["Email"], ENT_QUOTES));    
    $Clave = $_POST["Clave"];
	
    $result = mysql_query('SELECT Clave, Correo, Nombre, Apellidos, Id_user FROM user WHERE Correo=\''.$Email.'\'');
    if($row = mysql_fetch_array($result)){
        if($row["Clave"] == $Clave){
            $_SESSION["Id_user"] = $row['Id_user'];
			$_SESSION["k_name"] = $row['Nombre'];
			$_SESSION["k_apel"] = $row['Apellidos'];
			
            //Elimina el siguiente comentario si quieres re-dirigir automáticamente a index.php
            //Ingreso exitoso, ahora sera dirigido a la pagina principal.
			
			if (!empty($_SERVER['HTTP_CLIENT_IP'])){
        	$Ip_ini = $_SERVER['HTTP_CLIENT_IP'];}
       		else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        	$Ip_ini =  $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else {
    		$Ip_ini =  $_SERVER['REMOTE_ADDR'];
			$Fecha_cnx=date("Y-n-j").date(" H:i:s");
			$query = 'INSERT INTO event_log (Id_user, Fecha_ini, Ip_ingreso)
                VALUES (\''.$row['Id_user'].'\',\''.$Fecha_cnx.'\',\''.$Ip_ini.'\')';   
            mysql_query($query) or die(mysql_error());
			}
        }else{
            $_SESSION["message"]= 'Clave incorrecta';
			$redirect="login.php";
			header('Location: '.$redirect);
        }
    }else{
        $_SESSION["message"]= 'Usuario no existe';
		$redirect="login.php";
		header('Location: '.$redirect);
    }
    mysql_free_result($result);
}else{
    $_SESSION["message"]= 'Debe especificar un usuario y clave';
	$redirect="login.php";
	header('Location: '.$redirect);
}
mysql_close();
}

if((isset($_SESSION['Id_user']))&&(isset($_SESSION['redirect_to']))) { 
unset($_SESSION['verifica']); 
$redirect=$_SESSION['redirect_to'];
unset($_SESSION['redirect_to']); 
header('Location: '.$redirect);
}
else if(isset($_SESSION['Id_user'])&& ($redirect == ""))
{ 
unset($_SESSION['verifica']);
$_SESSION['verifica']=1;
$redirect="/";
header('Location: '.$redirect);
}
?>