<?php
include 'inc.php';

$user = strip_tags(strtolower($_POST['user']));
$sandi = $_POST['sandi'];
session_start();
//cek apakah sudah login
if($_SESSION['status'] =="login"){
	header("location:./");
}

head("Masuk", -1);

//halaman login
?>
	<form action="masuk.php" method="POST" style="max-width: 480px;margin: 40px auto;">
    <div class="card">
    <p><br /><h4 class="cyan-text center-align header">Masuk</h4></p>
		<div class="row">
			<div class="col s12 input-field">
				<input type="text" name="user" id="user" class="validate tooltipped" data-position="right" data-delay="100" data-tooltip="Isikan nama pengguna anda" value="<?php echo $user; ?>"/>
				<label for="user"><i class="material-icons">face</i> Nama Pengguna</label>
                <p class="red-text hide" id="labelUser"></p>
                <br />
			</div>
			<div class="col s10 input-field">
				<input type="password" name="sandi" id="sandi" class="validate tooltipped" data-position="top" data-delay="100" data-tooltip="Isikan kata sandi anda"/>
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
<?php
if(isset($_POST['masuk'])) {
	$err = 0;
	$terakhir_online = date("Y-m-d-H-i-s");
	$login = $db->masuk($user, $sandi);
	$cek=mysqli_num_rows($login);
	$row = mysqli_fetch_array($login);
	if ($cek> 0) {
		//aksi jika login berhasil
		$idp = $row['id'];
		$_SESSION['idp'] = $idp;
		$_SESSION['nama'] = $row['nama'];
		$_SESSION['user'] = $row['user'];
		$_SESSION['status'] = 'login';
		$_SESSION['terakhir_online'] = $terakhir_online;
		$query = mysqli_query($this->con, "UPDATE pengguna SET terakhir_online = '$terakhir_online' WHERE id = '$idp'");
		header('location:index.php');
	} else {
		//notif jika login gagal
		echo '
		<div class="red-text" id="error">
			Nama pengguna dan Kata sandi tidak cocok !
		</div><br />
		';
	}
}
?>
                	<button style="width:360px;height:40px;" class="btn waves-effect waves-light cyan tooltipped" type="submit" onclick="return validasi();" name="masuk" data-position="bottom" data-delay="100" data-tooltip="Klik untuk masuk">Masuk</button>
                <br /><br />
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
					$("#lihatSandi").removeClass("cyan");
					$("#lihatSandi").addClass("grey");
					$("#lihatSandi").html("<i class=\"material-icons\">visibility_off</i>");
				} else {
					$("#sandi").attr("type", "password");
					$("#lihatSandi").removeClass("grey");
					$("#lihatSandi").addClass("cyan");
					$("#lihatSandi").html("<i class=\"material-icons\">visibility</i>");
				}
			});

			function validasi() {
				var user = $("input#user").val(),
					sandi = $("input#sandi").val(),
					labelUser = $("#labelUser"),
					labelSandi = $("#labelSandi");
					
				labelUser.addClass("hide");
				labelSandi.addClass("hide");
				if(user == "" || sandi == "") {
					if(user == "") labelUser.html("Isikan nama pengguna anda").removeClass("hide");
					if(sandi == "") labelSandi.html("Isikan kata sandi anda").removeClass("hide");
					return false;
				} else {
					return true;
				}
			}
		});
	</script>

<?php
foot(); ?>