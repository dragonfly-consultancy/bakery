<?php
ob_start();
error_reporting (E_ALL ^ E_NOTICE);
session_start();
include('include/database.php');
if(isset($_SESSION['Status']))
{
if($_SESSION['Status'] == "login_success" &&  $_SESSION['activated'] == "Y" && $_SESSION['locked'] == "N")
{
echo "<script type='text/javascript'>window.location.href = 'index.php';</script>";

}
}

/*
if(isset($_POST['sub']))
{


function filter($var)
{

    return preg_replace('/ [^a-za-z0-9\s@.]/',' ' , $var);
}


 $username = filter($_POST['username']);
 $password = filter($_POST['password']);
 $location = filter($_POST['location']);
 $upass_sha1 = sha1($password);

   $db = new Database();
   $getRow = $db->getRow('SELECT * FROM users WHERE username = ? AND password = ?',[$username , $password]);

  if($getRow > 0)
  {


      
           $userLevel = $getRow['user_level'];
           $activated = $getRow['activated'];
           $locked = $getRow['locked'];
           $userid = $getRow['userid'];
     

     // store values to session
    $_SESSION['userid'] = $userid;
    $_SESSION['username']= $username;
    $_SESSION['password']= $password;
    $_SESSION['userlevel']= $userLevel;
    $_SESSION['activated']= $activated;
    $_SESSION['locked']= $locked;
    $_SESSION['Status']="login_success";
    $_SESSION['location'] = $location;
    
    if($activated == "Y" && $locked == "N")
    {

        header('Location:index.php');
    }
    else
    {

        $error = "Sorry!! your account has been locked. please contact office department ";
        $error_title = "Locked!";
    }


  }
else
{


    $error = "Sorry!! Username or password incorrect.";
    $error_title = "Incorrected!";
}


}*/

function load_location()  
 {  
      
      $output = '';  
      $db = new Database();
      $query = $db->getRows('SELECT * FROM location_master');
      $data = $query;
        foreach($data as $query) 
        {   
            $output .= '<option value="'.$query['id'].'">'.htmlspecialchars($query['name']).'</option>';
        }
        return $output;  
 }  

?>

<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

<head>
        <meta charset="utf-8" />
    <style>
    body.login {
        background: radial-gradient(circle at 50% 20%, #1e293b 0%, #090d16 100%) !important;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .login .logo {
        text-align: center;
        margin-bottom: 24px !important;
    }
    .login .logo img {
        max-height: 54px;
        filter: drop-shadow(0 4px 16px rgba(0, 240, 255, 0.3));
    }
    .login .content {
        background: #0f172a !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 16px !important;
        padding: 32px !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), 0 0 30px rgba(0, 240, 255, 0.1) !important;
        width: 100%;
        max-width: 420px;
        margin: 0 auto !important;
    }
    .login .content .form-title {
        color: #ffffff;
        font-size: 20px;
        font-weight: 800;
        text-align: center;
        margin-bottom: 24px;
        letter-spacing: -0.02em;
    }
    .login .content .form-control {
        background: #1e293b !important;
        border: 1px solid #334155 !important;
        border-radius: 8px !important;
        color: #ffffff !important;
        height: 42px !important;
        font-size: 14px !important;
        padding: 8px 14px !important;
        transition: all 0.2s ease;
    }
    .login .content .form-control:focus {
        border-color: #00f0ff !important;
        box-shadow: 0 0 12px rgba(0, 240, 255, 0.3) !important;
    }
    .login .content label {
        color: #94a3b8 !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        margin-bottom: 6px;
    }
    #btn-login {
        background: linear-gradient(135deg, #00f0ff 0%, #0284c7 100%) !important;
        color: #090d16 !important;
        border: none !important;
        border-radius: 8px !important;
        height: 44px !important;
        font-size: 14px !important;
        font-weight: 800 !important;
        letter-spacing: 0.05em !important;
        text-transform: uppercase !important;
        box-shadow: 0 4px 16px rgba(0, 240, 255, 0.35) !important;
        transition: all 0.2s ease !important;
        margin-top: 10px;
    }
    #btn-login:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 22px rgba(0, 240, 255, 0.5) !important;
    }
    </style>
    </head>
    <!-- END HEAD -->

    <body class="login">
        <div style="width:100%; max-width:440px;">
            <div id="message"></div>
          
            <div class="logo">
                <a href="<?php echo site_url(); ?>">
                    <img src="../assets/img/logo/voltix_logo.png" alt="Voltix Tech Admin" />
                </a>
            </div>
            
            <div class="content">
                <div class="form-title">
                    ⚡ Admin Portal Sign In
                </div>
                <!-- BEGIN LOGIN FORM -->
                <form class="login-form" id="login_form" method="POST" style="background:transparent;padding:0;">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        <span> Enter username and password. </span>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Username</label>
                        <input class="form-control" type="text" autocomplete="off" placeholder="Enter username" name="username" value="admin" required/>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Password</label>
                        <input class="form-control" type="password" autocomplete="off" placeholder="Enter password" name="password" value="admin" required/>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Dispatch Location</label>
                        <select class="form-control" name="location">
                            <?php echo load_location(); ?> 
                        </select>
                    </div>
                    <div class="form-actions" style="border:none;padding:0;margin-top:16px;">
                        <button type="submit" class="btn btn-block uppercase" name="sub" id="btn-login">Sign In &rarr;</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- BEGIN CORE PLUGINS -->
        <script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>

</html>

<script>
$('document').ready(function()
{ 

  $(document).on('submit','#login_form', function()
    {

       var data = $(this).serialize();
    
   $.ajax({
    
   type : 'POST',
   url  : 'process/login-process.php',
   data : data,
   beforeSend: function()
   { 
    $("#error").fadeOut();
    $("#btn-login").html('sending ...');
   },
   success :  function(response)
      {  

        var jsonobj = JSON.parse(response);
     
             if(jsonobj.response =="ok"){
         
     setTimeout(' window.location.href = "index.php"; ',4000);
             $("#message").fadeIn(1000, function(){  
      $("#message").html('<div class="alert alert-dismissable alert-success"><i class="ion-close-round"></i>&nbsp; <div style="text-align:center;"><strong>'+jsonobj.message_title+'</strong><p>'+jsonobj.message+'</p></div><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>');    
    
           $("#btn-login").html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Sign In');
         });


     }
     else if(jsonobj.response =="lock"){
         
      $("#message").fadeIn(1000, function(){  
      $("#message").html('<div class="alert alert-dismissable alert-warning"><i class="ion-close-round"></i>&nbsp; <div style="text-align:center;"><strong>'+jsonobj.message_title+'</strong><p>'+jsonobj.message+'</p></div><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>');    
    
           $("#btn-login").html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Sign In');
         });
     }
     else{

        $("#message").fadeIn(1000, function(){      
          $("#message").html('<div class="alert alert-dismissable alert-danger"><i class="ion-close-round"></i>&nbsp; <div style="text-align:center;"><strong>'+jsonobj.message_title+'</strong><p>'+jsonobj.message+'</p></div><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>');
           $("#btn-login").html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Sign In');
         });

     }
     }
   });
    return false;

    });


});

</script>



