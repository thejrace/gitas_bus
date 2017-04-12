<?php

	require 'inc/init.php';

	$TEXT = "";

	if( $_POST ){
		$OK = 1;

		$INPUT_LIST = array(
			"email" 				=> array( array( "req" => true, "email" => true, "unique" => array( DBT_KULLANICILAR, Input::get("email") ) )  ,"" ),
			'name'  				=> array( array( "req" => true ),  "" ),
			'pass_1'  				=> array( array( "req" => true ),  "" ),
			'pass_2'  				=> array( array( "req" => true, "matches" => "pass_1" ),  "" )
		);


		$Validation = new Validation( new InputErrorHandler );
		// Formu kontrol et
		$Validation->check_v2( Input::escape($_POST), $INPUT_LIST );

		if( $Validation->failed() ){
			$OK = 0;
			$input_output = $Validation->errors()->js_format();
		} else {
			$Reg = new Register;
			if( !$Reg->action(Input::escape($_POST) ) ) $OK = 0;
		}


		if( $OK ){
			$TEXT = "Kayıt başarıyla gerçekleşti.";
		} else {
			$TEXT = "Kayıt sırasında bir hata oluştu.";
		}
	}



	require 'inc/header.php';

?>

	KAYIT
	------------

	<?php echo $TEXT ?>
	<form action="" method="post" id="">


		<input type="text" name="name" placeholder="İsim" />
		<input type="text" name="email" placeholder="Email" />
		<input type="text" name="pass_1" placeholder="Şifre" />
		<input type="text" name="pass_2" placeholder="Şifre Tekrar" />
		<select name="perm_level">
			<option value="1">Admin</option>
			<option value="2">Stokçu</option>
			<option value="3">Muhasebeci</option>
		</select>

		<input type="submit" value="Kayıt Ol" />
	</form>
 

<?php
	require 'inc/footer.php';