<?php
include 'inc.php';
// mengaktifkan session
session_start();
 
// cek apakah user telah login, jika belum login maka di alihkan ke halaman login
if($_SESSION['status'] !="login"){
	header("location:./masuk.php");
}
$idp = $_SESSION['idp'];
$nama = $_SESSION['nama'];
$user = $_SESSION['user'];

$db->teakhir_online($user);
head($nama); //fungsi head di inc.php, berisi tag html <head> ... </head><body>

echo '
	<p><h1 class="cyan-text center-align">'.$nama.'</h1></p>
	';	
if(isset($_POST['tambah']) || isset($_POST['tambahkan'])) {
//halaman tambah kegiatan
	$hal = isset($_GET['hal']) ? $_GET['hal'] : 1;
?>
	<nav>
		<div class="nav-wrapper cyan lighten-1">
			<div class="col s12">
				<a href="?hal=<?php echo $hal;?>" class="breadcrumb">Daftar Kegiatan</a>
				<a href="#!" class="breadcrumb">Tambah Kegiatan</a>
			</div>
		</div>
	</nav>
<?php
if(isset($_POST['tambahkan'])) {
	$kegiatan = $_POST['kegiatan'];
	$cek = mysqli_num_rows(mysqli_query($this->con, "SELECT id FROM kegiatan WHERE kegiatan = '$kegiatan'"));
	if($cek > 0) {
	//notif ketika kegiatan error ditambahkan
		echo '
		<div class="red-text  card grey lighten-4 center-align">
			Nama Kegiatan Sudah Pernah Ditambahkan !
		</div>
		';
	} else {
	//notif ketika berhasil ditambahkan
		$db->addKegiatan($idp, $kegiatan);
	}
}
?>
	<form action="index.php?hal=<?php echo $hal;?>" method="POST">
	<div class="card" style="max-width: 480px;margin: 40px auto;">
		<div class="row"><br />
			<div class="col s12 input-field">
				<input type="text" name="kegiatan" id="kegiatan" class="validate tooltipped" data-position="right" data-delay="100" data-tooltip="Isikan nama kegiatan"/>
				<label for="kegiatan"><i class="material-icons">assignment</i> Nama Kegiatan</label>
                <p class="red-text hide" id="labelKegiatan"></p>
                <br />
			</div>
			<div class="col s12">
				<center>
                <button class="btn-large submit-btn cyan waves-effect tooltipped" type="submit" onclick="return validasi();" name="tambahkan" data-position="right" data-delay="100" data-tooltip="Klik untuk menambahkan">
					<i class="material-icons">add_circle</i> Tambah
				</button>
				</center>
				<br />
			</div>
		</div>
	</div>
	</form>
	<script type="text/javascript">
		function validasi() {
			var kegiatan = $("#kegiatan").val(),
				labelKegiatan = $("#labelKegiatan");
			
			labelKegiatan.addClass("hide");
			if (kegiatan == "") {
				$("#labelKegiatan").html("Nama Kegiatan harus di isi !").removeClass("hide");
				return false;
			} else if (kegiatan.length < 5){
				$("#labelKegiatan").html("Nama Kegiatan minimal 5 karakter !").removeClass("hide");
				return false;
			} else {
				return true;
			}
		}
	</script>

<?php
} else if(isset($_GET['ubah'])) {
//halaman ubah nama kegiatan
	$hal = isset($_GET['hal']) ? $_GET['hal'] : 1;
	$id = isset($_GET['id']) ? $_GET['id'] : 0;
	if($id == 0)
		echo '
		<script>
			window.location.href="'.URL.'?hal='.$hal.'";
		</script>
		';
	$nama_kegiatan = $db->getKegiatan($idp, $id);
?>
	<nav>
		<div class="nav-wrapper cyan lighten-1">
			<div class="col s12">
				<a href="?hal=<?php echo $hal;?>" class="breadcrumb">Daftar Kegiatan</a>
				<a href="#!" class="breadcrumb">Ubah Kegiatan</a>
			</div>
		</div>
	</nav>
	<form action="index.php?hal='.$hal.'" method="POST">
		<div class="fixed-action-btn horizontal">
			<button class="btn-large btn-floating red tooltipped" type="submit" name="tambah" data-position="top" data-delay="100" data-tooltip="Klik untuk menambahkan kegiatan">
				<i class="material-icons">add</i> Tambah
			</button>
		</div>
	</form>
	<form action="index.php?hal=<?php echo $hal;?>" method="POST">
	<div class="card" style="max-width: 480px;margin: 40px auto;">
		<div class="row"><br />
			<div class="col s12 input-field">
				<input type="text" name="kegiatan" id="kegiatan" class="validate tooltipped" data-position="right" data-delay="100" data-tooltip="Isikan nama kegiatan" value="<?php echo $nama_kegiatan; ?>"/>
				<label for="user"><i class="material-icons">assignment</i> Nama Kegiatan</label>
                <p class="red-text hide" id="labelKegiatan"></p>
				<input type="hidden" name="kegiatan_sebelum" id="kegiatan_sebelum" value="<?php echo $nama_kegiatan;?>" />
				<input type="hidden" name="id_kegiatan" value="<?php echo $id;?>" />
                <br />
			</div>
			<div class="col s12">
				<center>
                <button class="btn-large submit-btn cyan waves-effect tooltipped" type="submit" onclick="return validasi();" name="ubahBtn" data-position="right" data-delay="100" data-tooltip="Klik untuk menambahkan">
					<i class="material-icons .show-on-large">save</i> Ubah
				</button>
				</center>
				<br />
			</div>
		</div>
	</div>
	</form>
	<script type="text/javascript">
		function validasi() {
			var kegiatan = $("#kegiatan").val(),
				kegiatan_sebelum = $("#kegiatan_sebelum").val(),
				labelKegiatan = $("#labelKegiatan");
			
			labelKegiatan.addClass("hide");
			if (kegiatan == "") {
				$("#labelKegiatan").html("Nama Kegiatan harus di isi !").removeClass("hide");
				return false;
			} else if (kegiatan.length < 5){
				$("#labelKegiatan").html("Nama Kegiatan minimal 5 karakter !").removeClass("hide");
				return false;
			} else if (kegiatan == kegiatan_sebelum) {
				$("#labelKegiatan").html("Nama Kegiatan tidak berubah !").removeClass("hide");
				return false;
			} else {
				return true;
			}
		}
	</script>
<?php
} else {
//halaman tampilkan kegiatan
	$hal = isset($_GET['hal']) ? $_GET['hal'] : 1;
	echo '
	<nav>
		<div class="nav-wrapper cyan lighten-1">
			<div class="col s12">
				<a href="'.URL.'?hal='.$hal.'" class="breadcrumb">Daftar Kegiatan</a>
				<a href="'.URL.'?hal='.$hal.'" class="breadcrumb">Halaman '.$hal.'</a>
			</div>
		</div>
	</nav>
			<form action="index.php?hal='.$hal.'" method="POST">
                <div class="fixed-action-btn horizontal">
				<button class="btn-large btn-floating red tooltipped" type="submit" name="tambah" data-position="top" data-delay="100" data-tooltip="Klik untuk menambahkan kegiatan">
					<i class="material-icons">add</i> Tambah
				</button>
				</div>
			</form>
	';
	
	if(isset($_POST['ubahBtn'])) {
		$id_kegiatan = $_POST['id_kegiatan'];
		$kegiatan = $_POST['kegiatan'];
		$kegiatan_sebelum = $_POST['kegiatan_sebelum'];
		if($kegiatan == $kegiatan_sebelum) {
		//notif kegiatan diubah
			echo '
		<div class="green-text card grey lighten-4 center-align"><br>Data kegiatan <b>'.$kegiatan.'</b> telah disimpan tanpa perubahan.<br><br></div>
			';
		} else {
			$cek = mysqli_num_rows(mysqli_query($this->con, "SELECT id FROM kegiatan WHERE kegiatan = BINARY '$kegiatan'"));
			if($cek > 0) {
			//notif kegiatan pernah ditambahkan
				echo '
				<div class="red-text card grey lighten-4 center-align">
					Nama Kegiatan Sudah Pernah Ditambahkan !<br>
					<a href="index.php?hal='.$hal.'&ubah&id='.$id_kegiatan.'" class="btn cyan"><i class="material-icons">edit</i> UBAH</a>
				</div>
				';
			} else {
			//notif kegiatan berhasil ditambahkan
			$db->updateKegiatan2($idp, $id_kegiatan, $kegiatan_sebelum, $kegiatan, $hal);
			}
		}
	}

	if(isset($_GET['hapus'])) {
	//notif konfirmasi hapus kegiatan
		$idhapus = isset($_GET['idhapus']) ? $_GET['idhapus'] : 0;
		$final = isset($_GET['final']) ? $_GET['final'] : 0;
		$db->deleteKegiatan($idp, $idhapus, $final, $hal);
	}

	//menmpilkan daftar kegiatan
	$db->viewKegiatan($idp, $hal);
}

foot(1);
?>
