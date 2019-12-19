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
$idk = isset($_GET['idk']) ? $_GET['idk'] : 0;
$hal = isset($_GET['hal']) ? $_GET['hal'] : 1;
if($idk == 0) header("location:./index.php");
head($nama, $idk, "zipbukti");

echo '
	<p><h1 class="cyan-text center-align">'.$nama.'</h1></p>
	';
if(isset($_POST['tambah']) || isset($_POST['tambahkan'])) {
echo '
	<nav>
		<div class="nav-wrapper cyan lighten-1">
			<div class="col s12">
				<a href="'.URL.'index.php?hal='.$hal.'" class="breadcrumb">Daftar Kegiatan</a>
				<a href="'.URL.'rincian.php?idk='.$idk.'&hal='.$hal.'" class="breadcrumb tooltipped" data-position="bottom" data-delay="100" id="navKegiatan">
				<a href="" class="breadcrumb">Tambah Rincian Kegiatan</a>
			</div>
		</div>
	</nav>
	';
	if(isset($_POST['tambahkan'])) {
		$uraian = $_POST['uraian'];
		$debit = $_POST['debit'];
		$kredit = $_POST['kredit'];
		if($cek > 0) {
			//notif kegiatan pernah ditambahkan
			echo '
		<div class="red-text card grey lighten-4 center-align">
			Uraian seperti ini sudah pernah ditambahkan dalam kegiatan ini!<br>
		</div>
				';
		} else if(strlen($uraian) < 1) {
	//notif  rincian kegiatan gagal ditambahkan
			echo '
		<div class="red-text  card grey lighten-4 center-align">
			Uraian tidak boleh kosong !
		</div>
		';
		} else if($debit < 0 || $kredit < 0) {
	//notif  rincian kegiatan gagal ditambahkan
			echo '
		<div class="red-text  card grey lighten-4 center-align">
			Debit/Kredit tidak boleh negatif !
		</div>
		';
		} else {
	//notif rincian kegiatan berhasil di tambahkan
			$db->addRincianKegiatan($idp, $idk, $uraian, $debit, $kredit);
		}
	}
?>
	<form action="rincian.php?idk=<?php echo $idk.'&hal='.$hal;?>" method="POST">
	<div class="card" style="max-width: 480px;margin: 40px auto;">
		<div class="row"><br />
			<div class="col s12 input-field">
				<textarea name="uraian" id="uraian" class="validate tooltipped" data-position="right" data-delay="100" data-tooltip="Deskripsikan rincian dana"></textarea>
				<label for="uraian"><i class="material-icons">assignment</i> Rincian</label>
                <p class="red-text hide" id="labelUraian"></p>
                <br />
			</div>
			<div class="col s12 input-field">
				<input type="number" name="debit" id="debit" class="validate tooltipped" data-position="right" data-delay="100" data-tooltip="Isikan jumlah pemasukan atau debit dana" onkeypress="return isNumberKey(event)"/>
				<label for="debit"><i class="material-icons">add</i> Debit</label>
                <p class="red-text hide" id="labelDebit"></p>
                <br />
			</div>
			<div class="col s12 input-field">
				<input type="number" name="kredit" id="kredit" class="validate tooltipped" data-position="right" data-delay="100" data-tooltip="Isikan jumlah pengeluaran atau kredit dana" onkeypress="return isNumberKey(event)"/>
				<label for="kredit"><i class="material-icons">remove</i> Kredit</label>
                <p class="red-tex hide" id="labelKredit"></p>
                <br />
			</div>
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
		function isNumberKey(evt){
	  		var charCode = (evt.which) ? evt.which : event.keyCode
			if (charCode > 31 && (charCode < 48 || charCode > 57))
	 	    	return false;
			return true;
		}

		function validasi() {
			var uraian = $("textarea#uraian").val(),
				debit = $("input#debit").val(),
				kredit = $("input#kredit").val(),
				labelUraian = $("#labelUraian"),
				labeldebit = $("#labelDebit"),
				labelKredit = $("#labelKredit");
//				alert(debit);
			labelUraian.addClass("hide");
			labelDebit.addClass("hide");
			labelKredit.addClass("hide");
			
			if(uraian.length < 1 || debit < 0 || kredit < 0) {
				if(uraian.length < 1) labelUraiabn.html("uraian tidak boleh kosong").removeClass("hide");
				if(debit < 0) labelDebit.html("Isikan angka positif").removeClass("hide");
				if(kredit < 0) labelKredit.html("Isikan angka positif").removeClass("hide");
				return false;
			} else {
				return true;
			}
		}
	</script>

<?php
} else if(isset($_GET['ubah'])) {
//halaman ubah rincian kegiatan
	$hal = isset($_GET['hal']) ? $_GET['hal'] : 1;
	$idk = isset($_GET['idk']) ? $_GET['idk'] : 0;
	$id = isset($_GET['id']) ? $_GET['id'] : 0;
	if($idk == 0)
		echo '
		<script>
			window.location.href = "'.URL.'rincian.php?hal='.$hal.'";
		</script>
		';
	if($id == 0)
		echo '
		<script>
			window.location.href = "'.URL.'rincian.php?idk='.$idk.'&hal='.$hal.'";
		</script>
		';
	$uraian = $db->lihatRincianKegiatan($id, $idk, "uraian");
	$debit = $db->lihatRincianKegiatan($id, $idk, "debit");
	$kredit = $db->lihatRincianKegiatan($id, $idk, "kredit");
	echo '
		<nav>
		<div class="nav-wrapper cyan lighten-1">
			<div class="col s12">
				<a href="'.URL.'index.php?hal='.$hal.'" class="breadcrumb">Daftar Kegiatan</a>
				<a href="'.URL.'rincian.php?idk='.$idk.'&hal='.$hal.'" class="breadcrumb tooltipped" data-position="bottom" data-delay="100" id="navKegiatan">
				<a href="" class="breadcrumb">Ubah Rincian Kegiatan</a>
			</div>
		</div>
	</nav>
	<form action="rincian.php?idk='.$idk.'&hal='.$hal.'" method="POST">
		<div class="fixed-action-btn horizontal">
			<button class="btn-large btn-floating red tooltipped" type="submit" name="tambah" data-position="top" data-delay="100" data-tooltip="Klik untuk menambahkan rincian kegiatan">
				<i class="material-icons">add</i> Tambah
			</button>
		</div>
	</form>
	<form action="rincian.php?idk='.$idk.'&hal='.$hal.'" method="POST">
	';
?>
	<div class="card" style="max-width: 480px;margin: 40px auto;">
		<div class="row"><br />
			<div class="col s12 input-field">
				<textarea name="uraian" id="uraian" class="validate tooltipped" data-position="bottom" data-delay="100"
                	data-tooltip="Deskripsikan rincian dana. Contoh: Sertifikat (5), Pelakat (1)"><?php echo $uraian; ?></textarea>
				<label for="uraian"><i class="material-icons">assignment</i> Rincian</label>
                <p class="red-text hide kecil" id="labelUraian">Deskripsikan rincian dana.<br />Contoh: Sertifikat (5), Pelakat (1)</p>
                <br />
			</div>
			<div class="col s12 input-field inline"><i class="material-icons">add</i> Debit
				<input type="number" name="debit" id="debit" class="validate tooltipped" data-position="right" data-delay="100" data-tooltip="Isikan jumlah pemasukan atau debit dana" value="<?php echo $debit; ?>" onkeypress="return isNumberKey(event)"/>
                <p class="red-text hide" id="labelDebit"></p>
                <br />
			</div>
			<div class="col s12 input-field inline"><i class="material-icons">remove</i> Kredit
				<input type="number" name="kredit" id="kredit" class="validate tooltipped" data-position="right" data-delay="100" data-tooltip="Isikan jumlah pengeluaran atau kredit dana" value="<?php echo $kredit; ?>" onkeypress="return isNumberKey(event)"/>
                <p class="red-tex hide" id="labelKredit"></p>
                <br />
			</div>
			<div class="col s12">
				<center>
				<input type="hidden" name="uraian_sebelum" value="<?php echo $uraian;?>" />
				<input type="hidden" name="debit_sebelum" value="<?php echo $debit;?>" />
				<input type="hidden" name="kredit_sebelum" value="<?php echo $kredit;?>" />
				<input type="hidden" name="id_rincian" value="<?php echo $id;?>" />
				<input type="hidden" name="id_kegiatan" value="<?php echo $idk;?>" />
				<input type="hidden" name="hal" value="<?php echo $hal;?>" />
                <button class="btn-large submit-btn cyan waves-effect tooltipped" type="submit" onclick="return validasi();" name="ubahBtn" data-position="right" data-delay="100" data-tooltip="Klik untuk menambahkan">
					<i class="material-icons">save</i> Ubah
				</button>
				</center>
				<br />
			</div>
		</div>
	</div>
	</form>

	<script type="text/javascript">
		$("#uraian").change(
			function() {
				$("#labelUraian").html("Deskripsikan rincian dana.<br />Contoh: Sertifikat (5), Pelakat (1)");
			}
		);
		function isNumberKey(evt){
	  		var charCode = (evt.which) ? evt.which : event.keyCode
			if (charCode > 31 && (charCode < 48 || charCode > 57))
	 	    	return false;
			return true;
		}

		function validasi() {
			var uraian = $("textarea#uraian").val(),
				debit = $("input#debit").val(),
				kredit = $("input#kredit").val(),
				labelUraian = $("#labelUraian"),
				labeldebit = $("#labelDebit"),
				labelKredit = $("#labelKredit");
//				alert(debit);
			labelUraian.addClass("hide");
			labelDebit.addClass("hide");
			labelKredit.addClass("hide");
			
			if(uraian.length < 10 || debit < 0 || kredit < 0) {
				if(uraian.length < 10) labelUraiabn.html("Isikan uraian dana minimal 10 karakter").removeClass("hide");
				if(debit < 0) labelDebit.html("Isikan angka positif").removeClass("hide");
				if(kredit < 0) labelKredit.html("Isikan angka positif").removeClass("hide");
				return false;
			} else {
				return true;
			}
		}
	</script>

<?php
//	$db->updateRincianKegiatan1($idp, $idk, $id, $hal); //fungsi ada di inc.php
} else {
//halaman daftar rincian kegiatan
echo '
	<nav>
		<div class="nav-wrapper cyan lighten-1">
			<div class="col s12">
				<a href="'.URL.'index.php?hal='.$hal.'" class="breadcrumb">Daftar Kegiatan</a>
				<a href="'.URL.'rincian.php?idk='.$idk.'&hal='.$hal.'" class="breadcrumb tooltipped" data-position="bottom" data-delay="100" id="navKegiatan">
				</a>
			</div>
		</div>
	</nav>
	';
if(isset($_POST['ubahBtn'])) {
		$hal = $_POST['hal'];
		$id_kegiatan = $_POST['id_kegiatan'];
		$id_rincian = $_POST['id_rincian'];
		$uraian = $_POST['uraian'];
		$uraian_sebelum = $_POST['uraian_sebelum'];
		$debit = $_POST['debit'];
		$debit_sebelum = $_POST['debit_sebelum'];
		$kredit = $_POST['kredit'];
		$kredit_sebelum = $_POST['kredit_sebelum'];
		if(($uraian == $uraian_sebelum) && ($debit == $debit_sebelum) && ($kredit == $kredit_sebelum)) {
		//notif kegiatan diubah
			echo '
		<div class="green-text card grey lighten-4 center-align">
			Data rincian telah disimpan tanpa perubahan.
		</div>
			';
		} else if(strlen($uraian) < 1) {
	//notif  rincian kegiatan gagal ditambahkan
			echo '
		<div class="red-text  card grey lighten-4 center-align">
			Uraian tidak boleh kosong !
		</div>
		';
		} else if($debit < 0 || $kredit < 0) {
	//notif  rincian kegiatan gagal ditambahkan
			echo '
		<div class="red-text  card grey lighten-4 center-align">
			Debit/Kredit tidak boleh negatif !
		</div>
		';
		} else {
			$cek = mysqli_num_rows(mysqli_query($this->con, "SELECT id FROM rincian_kegiatan WHERE uraian = BINARY '$rincian' AND idk = '$idk'"));
			//notif kegiatan berhasil ditambahkan
				$db->updateRincianKegiatan3($idp, $id_kegiatan, $id_rincian, $uraian, $debit, $kredit, $rincian_sebelum, $debit_sebelum, $kredit_sebelum, $hal);
		}
	}
	$hal = isset($_GET['hal']) ? $_GET['hal'] : 1;
	if(isset($_GET['hapus'])) {
	//notif hapus rincian kegiatan
		$idhapus = isset($_GET['idhapus']) ? $_GET['idhapus'] : 0;
		$final = isset($_GET['final']) ? $_GET['final'] : 0;
		$db->deleteRincianKegiatan($idp, $idk, $idhapus, $final, $hal); //fungsi ada di inc.php
	}
echo '
			<form action="rincian.php?idk='.$idk.'&hal='.$hal.'" method="POST">
                <div class="fixed-action-btn horizontal">
				<button class="btn-large btn-floating red tooltipped" type="submit" name="tambah" data-position="top" data-delay="100" data-tooltip="Klik untuk menambahkan rincian kegiatan">
					<i class="material-icons">add</i> Tambah
				</button>
				</div>
			</form>
			<br />
';
	$db->viewRincianKegiatan($idp, $idk, $hal); //fungsi ada di inc.php
}

echo '
	<script>
		$(document).ready(function() {
		    $.ajax({
		        url:"http://localhost/p1/api.php?a=1&s=1-4-12-14&idk='.$idk.'&idr='.$id.'",
		        method:"GET", //First change type to method here
				contentType: "application/json",
				dataType: "json",
				success: function(data) {
					$("#navKegiatan")
						.html(data[0]+ " [" +data[1]+ "] [" +data[2]+ " Rincian]")
						.attr("data-tooltip", data[3]);
				 },
//				error: function(ts) { alert(+ts.responseText) }
			});
		});

  </script>
';
foot(1);
?>
