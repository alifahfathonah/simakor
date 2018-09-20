<?php
include 'inc.php';

$nama = "";
$user = "";
$sandi = "";
$email = "";

head(FBC." SETUP", -1);

echo '
	<p><h1 class="cyan-text center-align">'.FBC.' SETUP</h1></p>
	<nav>
		<div class="nav-wrapper cyan lighten-1">
			<div class="col s12">
				<a href="" class="breadcrumb">Simakor</a>
				<a href="" class="breadcrumb">Daftar Pengguna Baru</a>
			</div>
		</div>
	</nav>
	';	

if(isset($_POST['daftar'])) {
	//notif ketika menambahkan pengguna
	$nama = $db->clean($_POST['nama']);
	$user = strtolower($db->clean($_POST['user']));
	$email = $db->clean($_POST['email']);
	$sandi = MD5($db->clean($_POST['sandi']));
	$tanggal = date('Y-m-d');
	$terakhir_online = date('Y-m-d-H-i-s');
	$err = 0;
	$cek = mysql_num_rows(mysql_query("SELECT * FROM pengguna WHERE user = '$user'"));
	$cek2 = mysql_num_rows(mysql_query("SELECT * FROM pengguna WHERE email = '$email'"));
	if($cek > 0) {
		echo '
		<div class="red-text  card grey lighten-4 center-align">
			<i class="material-icons tiny">error</i> Nama Pengguna sudah terdaftar !
		</div>
		';
	} else if($cek2 > 0) {
		echo '
		<div class="red-text  card grey lighten-4 center-align">
			<i class="material-icons tiny">error</i> Email sudah terdaftar !
		</div>
		';
	} else {
	//menambahkan pengguna
		$query = "INSERT INTO pengguna (id, nama, user, email, sandi, tanggal, terakhir_online, halaman_terakhir)
					VALUES (NULL, '$nama', '$user', '$email', '$sandi', '$tanggal', '$terakhir_online', '')";
		$hasil = mysql_query($query) or $mysql_error = mysql_error();
		if($hasil) {
			echo '
		<div class="green-text card grey lighten-4 center-align">
			<i class="material-icons tiny">done</i> Data pengguna baru untuk <b>'.$nama.' ( <i>'.$user.' - '.$email.'</i>)</b> berhasil ditambahkan.<br />
			Anda akan dialihkan ke halaman masuk atau <a href="./masuk.php">klik di sini !</a>
		</div>
		<script>
			setTimeout(function() {
				window.location = "'.URL.'masuk.php";
			}, 6000);

		</script>
					';
		} else {
			echo '
		<div class="red-text card grey lighten-4 center-align">
			<i class="material-icons tiny">error</i> Data pengguna baru gagal ditambahkan.<br />Keterangan: error code [ap01]<br /><b>'.$mysql_error.'</b><br>
		</div>
			';}
	}
}
?>
	<form action="setup.php" method="POST">
	<div class="card" style="max-width: 480px;margin: 40px auto;">
		<div class="row"><br />
			<div class="col s12 input-field">
				<input type="text" name="nama" id="nama" class="validate tooltipped" data-position="top" data-delay="100" data-tooltip="Isikan nama organisasi"/>
				<label for="nama"><i class="material-icons">font_download</i> Nama Organisasi</label>
                <p class="red-text hide" id="labelNama"></p>
                <br />
			</div>
			<div class="col s12 input-field">
				<input type="text" name="user" id="user" class="validate tooltipped" data-position="top" data-delay="100" data-tooltip="Isikan nama pengguna untuk masuk(username untuk login)"/>
				<label for="user"><i class="material-icons">account_circle</i> Nama Pengguna</label>
                <p class="red-text hide" id="labelUser"></p>
                <br />
			</div>
			<div class="col s12 input-field">
				<input type="text" name="email" id="email" class="validate tooltipped" data-position="top" data-delay="100" data-tooltip="Isikan email"/>
				<label for="email"><i class="material-icons">email</i> Email</label>
                <p class="red-text hide" id="labelEmail"></p>
				<br />
			</div>
			<div class="col s10 input-field">
				<input type="password" name="sandi" id="sandi" class="validate tooltipped" data-position="top" data-delay="100" data-tooltip="Isikan kata sandi (min. 6 huruf)"/>
				<label for="sandi"><i class="material-icons">lock</i> Kata Sandi</label>
                <p class="red-text hide" id="labelSandi"></p>
				<br />
			</div>
			<div class="col s2 input-field" id="lihatSandi2">
				<button type="button" name="lihatSandi" id="lihatSandi" class="btn-floating cyan waves-effect">
                <i class="material-icons">visibility</i>
			</button>
			</div>
			<div class="col s12 center-align">
                <button style="width:360px;height:40px;" class="btn submit-btn cyan waves-effect waves-light tooltipped" type="submit" onclick="return validasi();" name="daftar" data-position="bottom" data-delay="100" data-tooltip="Klik untuk mendaftar">
					<i class="material-icons">add_circle</i> Daftar
				</button>
				<br />
				<p class="center-align"> atau Masuk dengan akun yang sudah ada? <a href="./masuk.php">Klik Di Sini</a></p>
				<br />
			</div>
		</div>
	</div>
	</form>
	<script type="text/javascript">
		$(document).ready(function() {

			var toggled = false;
			$("#lihatSandi2").click(function() {
				toggled = !toggled;
				if(toggled) {
					$("#sandi").attr("type", "text");
					$("#sandi").removeClass("cyan");
					$("#sandi").addClass("grey");
					$("#lihatSandi").html("<i class=\"material-icons\">visibility_off</i>");
				} else {
					$("#sandi").attr("type", "password");
					$("#sandi").removeClass("grey");
					$("#sandi").addClass("cyan");
					$("#lihatSandi").html("<i class=\"material-icons\">visibility</i>");
				}
				});
		
		function validasi() {
			var nama = $("#nama").val(),
				user = $("#user").val(),
				email = $("#email").val(),
				sandi = $("#sandi").val(),
				labelNama = $("#labelNama"),
				labelUser = $("#labelUser"),
				labelEmail = $("#labelEmail"),
				labelSandi = $("#labelSandi"),
				filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
			
			labelNama.addClass("hide");
			labelUser.addClass("hide");
			labelEmail.addClass("hide");
			labelSandi.addClass("hide");
			if ( nama == "" || user == "" || email == "" || sandi == "") {
				if(nama == "") labelNama.html("Nama organisasi harus di isi !").removeClass("hide");
				if(user == "") labelUser.html("Nama Pengguna harus di isi !").removeClass("hide");
				if(email == "") labelEmail.html("Email harus di isi !").removeClass("hide");
				if(sandi == "") labelSandi.html("Kata Sandi harus di isi !").removeClass("hide");
				return false;
			} else if (nama.length < 4 || user.length < 4 || sandi.length < 6 || filter.test(email) == false){
					if (nama.length < 4) labelNama.html("Nama organisasi minimal 4 karakter !").removeClass("hide");
					if (user.length < 4) labelUser.html("Nama Pengguna minimal 4 karakter !").removeClass("hide");
					if (sandi.length < 6) labelSandi.html("Kata Sandi minimal 6 karakter !").removeClass("hide");
					if(filter.test(email)  == false) labelEmail.html("Email tidak valid !").removeClass("hide");
				return false;
			} else {
				return false;
			}
		}
		});
	</script>
<?php
foot();
?>
