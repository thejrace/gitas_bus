<?php

	require 'inc/defs.php';
	session_start();
	if( isset( $_SESSION["login_session"] )  ) header("Location: index.php");

?>
<!-- NE BAKTIN CANIM -->
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- IE render en son versiyona gore -->
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1">
		<link rel="stylesheet" href="<?php echo URL_RES_CSS ?>main.css" />
        <script type="text/javascript" src="<?php echo URL_RES_JS ?>common.js"></script>
		<script type="text/javascript" src="<?php echo URL_RES_JS ?>main.js"></script>
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,600,300,800,700,400italic|PT+Serif:400,400italic" />

        <title>BUS v2 Giriş</title>


        <style>

	        
	        .login-cont {
	        	margin-top:30px;
	        	font-family:'Open Sans', 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;
	        }

        	.login-form {
        		padding-top:20px;/
        		background:#fff;
        		border:1px solid #d8d8d8;
        		margin:0 auto;
        		font-size:14px;
        	}

        	.login-logo {
			    text-align: center;
			    font-size: 30px;
			}

			.login-header {
				text-align: center;
			    font-size: 15px;
			    margin:20px 0;
			}


        </style>

	</head>
	<body>
		<div id="popup-overlay"></div>
    	<div id="popup" >
        </div>
		<div class="login-cont">
			
			<div class="login-logo">OBAREY INC.</div>
			<div class="login-header">Giriş Yap</div>

			<div class="login-form">
				<form action="" method="post" id="login">
					<div class="input-container">
						<label for="login_email">Eposta</label>
						<input type="email" class="req email" name="email" id="login_email" />
					</div>
					<div class="input-container">
						<label for="login_pass">Şifre</label>
						<input type="password" class="req" name="pass" id="login_pass" />
					</div>
					<div class="input-container">
						<label for="remember_me">Beni Hatırla</label>
						<input type="checkbox" name="remember_me" id="remember_me"  />
					</div>
					<div class="input-container submit-center">
						<input type="hidden" name="type" value="login" />
						<input type="submit" class="sbut red"  value="Giriş" />
					</div>
				</form>
			</div>

		</div>


		<script type="text/javascript">

			AHReady(function(){

				add_event($AH("login"), "submit", function(ev){

					if( FormValidation.check( this ) ){
						Popup.start_loader();
						AHAJAX_V3.req( Base.AJAX_URL + "login.php", serialize(this), function(res){
							Popup.off();
							if( res.ok ){
								location.reload(true);
							} else {
								FormValidation.show_serverside_errors( res.inputret );
								if( res.text != "" ){
									set_html( $AHC('login-header'), res.text );
								}
							}

						});

					}

					event_prevent_default(ev);
				});



			});


		</script>
	</body>
</html>