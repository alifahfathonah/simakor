<?php
include('config.php');
/*
							PR

- halaman tentang
- tampilan di hp

							ADVANCE
- halaman pengaturan
	sorting
	tema
	biodata pengguna dll.
*/


define("FBC", "Simakor");
error_reporting(0);

function head($judul = "Simakor", $opsi = 0, $zipbukti = '') {
	echo '
<!DOCTYPE html>
<html>
 <head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>'.$judul.'</title>
 	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="assets/css/materialize.min.css">
	<style>
		.kecil{font-size: 12px}
	</style>
 </head>
 <body>
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/materialize.min.js"></script>
	<div class="navbar-fixed">
		<nav>
			<div class="nav-wrapper cyan">
				<a href="'.URL.'" class="brand-logo">'.FBC.'</a>
				<ul class="right hide-on-med-and-down">
		';
	if($opsi == -1) { //hanya digunakan untuk setup
	} else if($opsi == 0) {
		echo '
					<li><a href=""><a href="'.URL.'excel.php" class="waves-effect waves-light tooltipped" data-position="bottom" data-delay="100" data-tooltip="Download file excel seluruh daftar kegiatan"><i class="material-icons">file_download</i></a></li>
		';
	} else {
		echo '
					<li><a href=""><a href="'.URL.'excel.php?idk='.$opsi.'" class="waves-effect waves-light tooltipped" data-position="bottom" data-delay="100" data-tooltip="Download file excel seluruh rincian dana kegiatan"><i class="material-icons">file_download</i></a></li>
		';
		if($zipbukti == 'zipbukti') {
			echo '
						<li><a href=""><a href="'.URL.'bukti.php?idk='.$opsi.'&zip" class="waves-effect waves-light tooltipped" data-position="bottom" data-delay="100" data-tooltip="Download bukti kegiatan"><i class="material-icons">cloud_download</i></a></li>
			';}
	}
	echo '
					<li><a href="tentang.php">Tentang '.FBC.'</a></li>
				</ul>
			</div>
		</nav>
	</div>
';
}


function foot($s = 0) {
	echo '
	<footer class="page-footer cyan">
		<div class="footer-copyright">
			<div class="container">
				&copy; 2018 '.FBC.'. All right reserved';
	if($s == 1) echo '
				<a class="btn right cyan" href="keluar.php">Keluar</a>';
	echo '
			</div>
		</div>
	</footer>
 </body>
</html>
';
}

 class database {
 	private $dbHost, $dbUser, $dbName, $dbPass;
	public	$maksimal_per_halaman = 5,
			$order_kegiatan = "id",
			$order_rincian = "uraian",
			$sort_kegiatan = "ASC",
			$sort_rincian = "ASC";

	//constructor
	function __construct($h, $u, $n, $p){
		$this->dbHost = $h;
		$this->dbUser = $u;
		$this->dbName = $n;
		$this->dbPass = $p;
	}
	
	//koneksi MySQL
	function connectMySQL() {
		mysql_connect($this->dbHost, $this->dbUser, $this->dbPass);
		mysql_select_db($this->dbName);
	}
	
	//filter masukan ke sql
	function clean($i) {
		return htmlentities(mysql_real_escape_string(stripslashes(trim($i))));
	}

	//masuk
	function masuk($u, $s) {
		$u = $this->clean(strtolower($u));
		$s = MD5($this->clean($s));
		$query = "SELECT * FROM pengguna WHERE user = '$u' AND sandi='$s'";
		return mysql_query($query);
	}

	function teakhir_online($u) {
		$u = $this->clean(strtolower($u));
		$now = date("Y-m-d-H-i-s");
		$halaman = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$query = "UPDATE pengguna SET terakhir_online = '$now', halaman_terakhir = '$halaman' WHERE user = '$u'";
		return mysql_query($query);
	}

	//menambahkan kegiatan
	function addKegiatan($i, $k) {
		//i = id pengguna, k = nama kegiatan
		$i = $this->clean($i);
		$k = $this->clean($k);
		$tanggal = date('Y-m-d');
		$query = "INSERT INTO kegiatan (id, idp, kegiatan, debit, kredit, saldo, tanggal)
					VALUES (NULL, '$i', '$k', '0', '0', '0', '$tanggal')";
		$hasil = mysql_query($query) or $mysql_error = mysql_error();
		if($hasil) echo '
		<div class="green-text card grey lighten-4 center-align">
			Data kegiatan baru untuk <b>'.$k.'</b> berhasil ditambahkan.
		</div>
					';
		else echo '
		<div class="red-text card grey lighten-4 center-align">
			Data kegiatan baru gagal ditambahkan.<br>Keterangan: error code [ak01]<br><b>'.$mysql_error.'</b><br>
		</div>
			';
	}
	
	//menambahkan rincian kegiatan
	function addRincianKegiatan($idp, $idk, $uraian, $debit, $kredit) {
		//idp = id pengguna, idk = id kegiatan, uraian = uraian, debit = debit, kredit = kredit
		$idp = $this->clean($idp);
		$idk = $this->clean($idk);
		$uraian = $this->clean($uraian);
		$debit = ($this->clean($debit));
		$kredit = ($this->clean($kredit));
		$debit_new = abs($this->lihatKegiatan($idk, "debit") + $debit);
		$kredit_new = abs($this->lihatKegiatan($idk, "kredit") + $kredit);
		$saldo_new = $debit_new - $kredit_new;
		$saldo_rincian = $debit - $kredit;
		$tanggal = date('Y-m-d');
		$query = "INSERT INTO rincian_kegiatan (id, idp, idk, tanggal, uraian, debit, kredit, saldo, bukti)
					VALUES (NULL, '$idp', '$idk', '$tanggal', '$uraian', '$debit', '$kredit', '$saldo_rincian', 0)";
		$hasil = mysql_query($query) or $mysql_error = mysql_error(); //query untuk tabel rincian_kegiatan
		if($hasil) {
			$query = "SELECT * FROM rincian_kegiatan WHERE idk = '$idk' AND idp = '$idp'";
			$hasil = mysql_query($query) or die(mysql_error());
			$saldo = 0;
			while($data = mysql_fetch_array($hasil)) {
				$no++;
				$idr = $data['id'];
				$saldo += ($data['debit'] - abs($data['kredit']));
				}
			$query2 = "UPDATE kegiatan SET  debit = '$debit_new', kredit = '$kredit_new', saldo = '$saldo'
					WHERE id = '$idk' ";
			$hasil2 = mysql_query($query2) or $mysql_error2 = mysql_error(); //query untuk tabel kegiatan
			if($hasil2) echo '
			<div class="green-text card grey lighten-4 center-align">
				Data rincian kegiatan telah berhasil ditambahkan. [ <a href="'.URL.'rincian.php?idk='.$idk.'">Lihat </a>]
			</div>
							';
				else echo '
		<div class="red-text card grey lighten-4 center-align">
			Data rincian kegiatan gagal ditambahkan.<br>Keterangan: error code [ar01]<br><b>'.$mysql_error.'</b>
		</div>
						';
		} else echo '
		<div class="red-text card grey lighten-4 center-align">
			Data rincian kegiatan gagal ditambahkan.<br>Keterangan: error code [ar02]<br><b>'.$mysql_error.'</b>
		</div>
						';
	}
	
	//melihat data dari kegiatan
	function lihatKegiatan($id, $type) {
		$id = $this->clean($id);
		$type = $this->clean($type);
		$query = "SELECT * FROM kegiatan WHERE id = '$id'";
		$hasil = mysql_query($query);
		$data = mysql_fetch_array($hasil);
		if($type == 'idp') return $data['idp'];
		else if($type == 'kegiatan') return $data['kegiatan'];
		else if($type == 'debit') return $data['debit'];
		else if($type == 'kredit') return $data['kredit'];
		else if($type == 'saldo') return $data['saldo'];
		else if($type == 'tanggal') return $data['tanggal'];
		else return $data['id'];
	}

	//melihat data dari rincian kegiatan
	function lihatRincianKegiatan($id, $idk, $type) {
		$id = $this->clean($id);
		$idk = $this->clean($idk);
		$type = $this->clean($type);
		if($type == 'banyak_rincian') {
			$query = "SELECT * FROM rincian_kegiatan WHERE idk = '$idk'";
			$hasil = mysql_query($query);
			$banyak_rincian = mysql_num_rows($hasil);
			return $banyak_rincian;
		} else if($type == 'banyak_bukti') {
			$banyak_bukti = 0;
			$query = "SELECT * FROM rincian_kegiatan WHERE idk = '$idk'";
			$hasil = mysql_query($query);
			while($data = mysql_fetch_array($hasil)) $banyak_bukti += $data['bukti'];
			return $banyak_bukti;
		} else {
			$query = "SELECT * FROM rincian_kegiatan WHERE id = '$id'";
			$hasil = mysql_query($query);
			$data = mysql_fetch_array($hasil);
			if($type == 'idp') return $data['idp'];
			else if($type == 'idk') return $data['idk'];
			else if($type == 'tanggal') return $data['tanggal'];
			else if($type == 'uraian') return $data['uraian'];
			else if($type == 'debit') return $data['debit'];
			else if($type == 'kredit') return $data['kredit'];
			else if($type == 'saldo') return $data['saldo'];
			else if($type == 'bukti') return $data['bukti'];
			else return $data['id'];
		}
	}
	
	//menampilkan semua kegiatan
	function viewKegiatan($idp, $hal) {
		$max_hal = $this->maksimal_per_halaman;
		$query = "SELECT id FROM kegiatan WHERE idp = '$idp'";
		$hasil = mysql_query($query);
		$total_data = mysql_num_rows($hasil);
		$total_hal = ceil($total_data/$max_hal);
		if($hal > $total_hal) $hal = $total_hal;
		$mulai = ($hal > 1) ? ($hal * $max_hal - $max_hal) : 0;
		$no = $mulai;
		if($total_data > 0) {
			if($total_hal > 1 ) {
				if($hal == 1) {
					$status_hal = "disabled";
					$prev = 1;
				} else {
					$status_hal = "waves-effect";
					$prev = $hal-1;
				}
				echo '
	<ul class="pagination ">
    	<li class="'.$status_hal.'"><a href="?hal='.$prev.'"><i class="material-icons">chevron_left</i></a></li>
    			';
				for($i = 1; $i <= $total_hal; $i++) {
					if($i == $hal) $status_hal = "active grey";
					else $status_hal = "waves-effect";
					echo '
    	<li class="'.$status_hal.'"><a href="?hal='.$i.'">'.$i.'</a></li>
					';
				}
				if($hal == $total_hal) {
					$status_hal = "disabled";
					$next = $total_hal;
				} else {
					$status_hal = "waves-effect";
					$next = $hal+1;
				}
				echo '
		<li class="'.$status_hal.'"><a href="?hal='.$next.'"><i class="material-icons">chevron_right</i></a></li>
	</ul>
				';
			}

		$query = "SELECT * FROM kegiatan WHERE idp = '$idp' ORDER BY kegiatan ASC LIMIT $mulai,$max_hal";
		$hasil = mysql_query($query);

			echo '
		<table class="bordered highlight">
			<thead class="grey lighten-4 cyan-text">
				<tr>
					<th class="center-align">No</th>
					<th class="center-align">Kegiatan</th>
					<th class="right-align">Debit (Rp)</th>
					<th class="right-align">Kredit (Rp)</th>
					<th class="right-align">Saldo (Rp)</th>
					<th class="center-align">Opsi</th>
				</tr>
			</thead>
			<tbody>
			';
			while($data = mysql_fetch_array($hasil)) {
				$no++;
				echo '
				<tr>
					<td class="center-align">'.$no.'</td>
					<td>'.$data['kegiatan'].'</td>
					<td class="right-align">'.$this->uang($data['debit']).'</td>
					<td class="right-align">'.$this->uang($data['kredit']).'</td>
					<td class="right-align">'.$this->uang($data['saldo']).'</td>
					<td class="center-align">
						<a href="'.URL.'rincian.php?hal='.$hal.'&idk='.$data['id'].'" class="btn-small btn-floating green waves-effect waves-light tooltipped" data-position="top" data-delay="100" data-tooltip="Lihat Rincian Kegiatan"><i class="material-icons">description</i></a>
						<a href="'.URL.'index.php?hal='.$hal.'&id='.$data['id'].'&ubah" class="btn-small btn-floating yellow waves-effect waves-light tooltipped" data-position="top" data-delay="100" data-tooltip="Ubah Kegiatan"><i class="material-icons">edit</i></a>
						<a href="'.URL.'bukti.php?idk='.$data['id'].'&zip" class="btn-small btn-floating blue waves-effect waves-light tooltipped" data-position="top" data-delay="100" data-tooltip="Unduh Bukti Kegiatan"><i class="material-icons">cloud_download</i></a>
						<a href="'.URL.'excel.php?idk='.$data['id'].'" class="btn-small btn-floating cyan waves-effect waves-light tooltipped" data-position="top" data-delay="100" data-tooltip="Unduh File Excel Kegiatan"><i class="material-icons">file_download</i></a>
						<a href="'.URL.'index.php?hal='.$hal.'&idhapus='.$data['id'].'&hapus" class="modal-triggered btn-small btn-floating red waves-effect waves-light tooltipped" data-position="top" data-delay="100" data-tooltip="Hapus Kegiatan"><i class="material-icons">delete</i></a>
					</td>
				</tr>
				';
			}
			echo '
			</tbody>
			</table>
				';
			if($total_hal > 1 ) {
				if($hal == 1) {
					$status_hal = "disabled";
					$prev = 1;
				} else {
					$status_hal = "waves-effect";
					$prev = $hal-1;
				}
				echo '
	<ul class="pagination ">
    	<li class="'.$status_hal.'"><a href="?hal='.$prev.'"><i class="material-icons">chevron_left</i></a></li>
    			';
				for($i = 1; $i <= $total_hal; $i++) {
					if($i == $hal) $status_hal = "active grey";
					else $status_hal = "waves-effect";
					echo '
    	<li class="'.$status_hal.'"><a href="?hal='.$i.'">'.$i.'</a></li>
					';
				}
				if($hal == $total_hal) {
					$status_hal = "disabled";
					$next = $total_hal;
				} else {
					$status_hal = "waves-effect";
					$next = $hal+1;
				}
				echo '
		<li class="'.$status_hal.'"><a href="?hal='.$next.'"><i class="material-icons">chevron_right</i></a></li>
	</ul>
				';
			}
		}
		if($total_data<1) echo '
		<div class="card center-align cyan-text">
			<h3>BELUM ADA DATA</h3>
		</div>
							';
	}
		
	//menampilkan semua rincian kegiatan
	function viewRincianKegiatan($idp, $idk, $hal) {
		$query = "SELECT id FROM rincian_kegiatan WHERE idk = '$idk' AND idp = '$idp'";
		$hasil = mysql_query($query);
		$total_data = mysql_num_rows($hasil);
		$no = 0;
		if($total_data > 0) {
			$query = "SELECT * FROM rincian_kegiatan WHERE idk = '$idk' AND idp = '$idp' ORDER BY id ASC";
			$hasil = mysql_query($query) or die(mysql_error());

			echo '
		<table class="bordered highlight">
			<thead class="grey lighten-4 cyan-text">
				<tr>
					<th class="center-align">No</th>
					<th class="left-align">Tanggal</th>
					<th class="center-align">Uraian</th>
					<th class="right-align">Debit (Rp)</th>
					<th class="right-align">Kredit (Rp)</th>
					<th class="right-align">Saldo (Rp)</th>
					<th class="center-align">Bukti</th>
					<th class="center-align">Opsi</th>
				</tr>
			</thead>
			<tbody>
			';
			$saldo = 0;
			while($data = mysql_fetch_array($hasil)) {
				$no++;
				$idr = $data['id'];
				$saldo += ($data['debit'] - $data['kredit']);
				echo '
				<tr>
					<td class="center-align">'.$no.'</td>
					<td>'.$this->tanggal_indo($data['tanggal']).'</td>
					<td>'.$data['uraian'].'</td>
					<td class="right-align">'.$this->uang($data['debit']).'</td>
					<td class="right-align">'.$this->uang($data['kredit']).'</td>
					<td class="right-align">'.$this->uang($saldo).'</td>
					<td class="center-align">
				';
				if($data['bukti'] > 0) {
					echo '
						<a href="'.URL.'bukti.php?idk='.$idk.'&idr='.$idr.'" class="btn-small btn-floating green waves-effect waves-light tooltipped" data-position="top" data-delay="100" data-tooltip="Ada '.$this->lihatRincianKegiatan($idr,$idk,"bukti").' bukti keuangan"><i class="material-icons">check_circle</i></a>
						';
				} else {
					echo '
						<span class="btn-small btn-floating disabled waves-effect waves-light tooltipped" data-position="top" data-delay="100" data-tooltip="Bukti belum ada"><i class="material-icons">close</i></a>
						';
				}
					echo '
					</td>
					<td class="center-align">
						<a href="'.URL.'unggah.php?idk='.$idk.'&idr='.$data['id'].'" class="btn-small btn-floating blue waves-effect waves-light tooltipped" data-position="top" data-delay="100" data-tooltip="Upload bukti keuangan"><i class="material-icons">cloud_upload</i></a>
						<a href="'.URL.'rincian.php?ubah&hal='.$hal.'&idk='.$idk.'&id='.$data['id'].'" class="btn-small btn-floating yellow waves-effect waves-light tooltipped" data-position="top" data-delay="100" data-tooltip="Ubah Rincian ini"><i class="material-icons">edit</i></a>
						<a href="'.URL.'rincian.php?hal='.$hal.'&hapus&idk='.$idk.'&idhapus='.$data['id'].'" class="modal-triggered btn-small btn-floating red waves-effect waves-light tooltipped" data-position="top" data-delay="100" data-tooltip="Hapus Rincian ini"><i class="material-icons">delete</i></a>
					</td>
				</tr>
				';
			}
			echo '
			</tbody>
			</table>
				';
		}
		if($total_data<1) echo '
		<div class="card center-align cyan-text">
			<h3>BELUM ADA DATA</h3>
		</div>
							';
	}
	
	//mengambil nama kegiatan saja dari idp dan idk (yg cocok)
	function getKegiatan($idp, $id) {
		$idp = $this->clean($idp);
		$id = $this->clean($id);
		$return = "";
		$query = mysql_query("SELECT * FROM kegiatan WHERE id = '$id' AND idp = '$idp'");
		$cek = mysql_num_rows($query);
		if(!($cek < 1 || $id == 0)) {
			$row = mysql_fetch_array($query);
			$return = $row['kegiatan'];
		}
		return $return;
	}

	//mengubah kegiatan di database (sebagai proses)
	function updateKegiatan2($idp, $id, $ks, $k, $hal) {
		//ks = kegiatan sebelumnya, idp = id pengguna, id = id kegiatan, k = kegiatan, d = debit, kr = kredit, s = saldo, $hal = halaman
		$k = $this->clean($k);
		$ks = $this->clean($ks);
		$idp = $this->clean($idp);
		$id = $this->clean($id);
		$hal = $this->clean($hal);
		$query = mysql_query("SELECT kegiatan FROM kegiatan WHERE id = '$id' AND idp = '$idp'");
		$cek = mysql_num_rows($query);
		if($cek < 1 || $id == 0) {
			echo '
		<div class="red-text card grey lighten-4 center-align">
			Error !<br>Nama Kegiatan yang anda pilih tidak terdaftar !
		</div>';
		} else {
				$query = "UPDATE kegiatan SET kegiatan = '$k'
					WHERE id = '$id' ";
				$hasil = mysql_query($query) or $mysql_error = mysql_error();
				if($hasil) echo '
		<div class="green-text card grey lighten-4 center-align">
			Data kegiatan <b>'.$ks.'</b> telah berhasil diubah menjadi <b>'.$k.'</b>.
		</div>
							';
				else echo '
		<div class="red-text card grey lighten-4 center-align">
			Data kegiatan gagal diubah.<br>Keterangan: error code [uk02]<br /><b>".$mysql_error."</b>
		</div>
					';
		}
	}

	//mengubah kegiatan di database
	function updateRincianKegiatan3($idp, $idk, $id, $r, $d, $kr, $rs, $ds, $krs, $hal) {
		//rs = rincian sebelumnya, id = id rincian, r = rincian, d = debit, kr = kredit, $hal = halaman
		$idp = $this->clean($idp);
		$idk = $this->clean($idk);
		$id = $this->clean($id);
		$uraian = $this->clean($r);
		$debit = ($this->clean($d));
		$rs = $this->clean($rs);
		$ds = $this->clean($ds);
		$krs = $this->clean($krs);
		$kredit = ($this->clean($kr));
		$debit_new = abs($this->lihatKegiatan($idk, "debit") + $debit);
		$kredit_new = abs($this->lihatKegiatan($idk, "kredit") + $kredit);
		$saldo_new = $debit_new - $kredit_new;
		$hal = $this->clean($hal);
		$saldo_rincian = $debit - $kredit;
		$tanggal = date('Y-m-d');
		$query = "UPDATE rincian_kegiatan SET tanggal = '$tanggal', uraian = '$uraian', debit = '$debit', kredit = '$kredit', saldo = '$saldo_rincian'
					WHERE id = '$id'";
		$hasil = mysql_query($query) or $mysql_error = mysql_error(); //query untuk tabel rincian_kegiatan
		if($hasil) {
			$query = "SELECT * FROM rincian_kegiatan WHERE idk = '$idk' AND idp = '$idp'";
			$hasil = mysql_query($query) or die(mysql_error());
			$saldo = 0;
			while($data = mysql_fetch_array($hasil)) {
				$no++;
				$idr = $data['id'];
				$saldo += ($data['debit'] - abs($data['kredit']));
				}
			$query2 = "UPDATE kegiatan SET  debit = '$debit_new', kredit = '$kredit_new', saldo = '$saldo'
					WHERE id = '$idk' ";
			$hasil2 = mysql_query($query2) or $mysql_error2 = mysql_error(); //query untuk tabel kegiatan
			if($hasil2) echo '
			<div class="green-text card grey lighten-4 center-align">
				Data <b>'.$rs.'</b> telah berhasil diubah menjadi <b>'.$uraian.'</b>.<br />
				Data <b>'.$ds.'</b> telah berhasil diubah menjadi <b>'.$debit.'</b>.<br />
				Data <b>'.$krs.'</b> telah berhasil diubah menjadi <b>'.$kredit.'</b>.
			</div>
							';
				else echo '
		<div class="red-text card grey lighten-4 center-align">
			Data kegiatan gagal diubah.<br>Keterangan: error code [ur01]<br><b>'.$mysql_error2.'</b>
		</div>
						';
		} else echo '
		<div class="red-text card grey lighten-4 center-align">
			Data kegiatan gagal diubah.<br>Keterangan: error code [ur02]<br><b>'.$mysql_error.'</b>
		</div>
						';
	}

	/*
	//mengubah kegiatan di database (sebagai proses)
	function updateRincianKegiatan2($idp, $idk, $id, $u, $d, $kr, $hal) {
		//idp = id pengguna, idk = id kegiatan, id = id rincian,
		//u = uraian, d = debit, kr = kredit, s = saldo, $hal = halaman
		$idp = $this->clean($idp);
		$idk = $this->clean($idk);
		$id = $this->clean($id);
		$u = $this->clean($u);
		$d = $this->clean($d);
		$kr = $this->clean($kr);
		$hal = $this->clean($hal);
		$debit_old = $this->lihatRincianKegiatan($id, $idk, "debit");
		$kredit_old = $this->lihatRincianKegiatan($id, $idk, "kredit");
		$debit_selisih = ($d - $debit_old);
		$kredit_selisih = ($kr - $kredit_old);
		$debit_new = abs($this->lihatKegiatan($idk, "debit") + $debit_selisih);
		$kredit_new = abs($this->lihatKegiatan($idk, "kredit") + $kredit_selisih);
		$saldo_new = $debit_new - $kredit_new;
		$saldo_rincian = $d - $kr;
		$tanggal = date('Y-m-d');
		$query = mysql_query("SELECT uraian FROM rincian_kegiatan WHERE id = '$id' AND idp = '$idp' AND idk = '$idk'");
		$cek = mysql_num_rows($query);
		$uraian = $this->lihatRincianKegiatan($id, $idk, "uraian");
		if($cek < 1) {
			echo '<div class="error"><br>Error !<br>Nama Kegiatan yang anda pilih tidak terdaftar !<br><br>Keterangan: error code [ur06]<br><br></div>';
		} else if($id == 0) {
			echo '<div class="error"><br>Error !<br>Nama Kegiatan yang anda pilih tidak terdaftar !<br><br>Keterangan: error code [ur07]<br><br></div>';
		} else if ($uraian == $u) {
			$query = "UPDATE rincian_kegiatan SET uraian = '$u', debit = '$d', kredit = '$kr', saldo = '$saldo_rincian'
				WHERE id = '$id' ";
			$query2 = "UPDATE kegiatan SET  debit = '$debit_new', kredit = '$kredit_new', saldo = '$saldo_new'
				WHERE id = '$idk' ";
			$hasil = mysql_query($query) or $mysql_error = mysql_error(); //query untuk tabel rincian_kegiatan
			$hasil2 = mysql_query($query2) or $mysql_error = mysql_error(); //query untuk tabel kegiatan
			if($hasil2)	if($hasil) 	echo "<div class=\"sukses\"><br>Data rincian kegiatan <br><b>$uraian</b><br> telah disimpan tanpa perubahan uraian.<br><br></div>";
			else echo "<div class=\"error\"><br>Data kegiatan gagal diubah.<br>Keterangan: error code [ur03]<br><b>".$mysql_error."</b><br><br></div>";
		} else {
				$query = "UPDATE rincian_kegiatan SET uraian = '$u', debit = '$d', kredit = '$kr', saldo = '$saldo_rincian'
					WHERE id = '$id' ";
				$query2 = "UPDATE kegiatan SET  debit = '$debit_new', kredit = '$kredit_new', saldo = '$saldo_new'
					WHERE id = '$idk' ";
				$hasil = mysql_query($query) or $mysql_error = mysql_error(); //query untuk tabel rincian_kegiatan
				$hasil2 = mysql_query($query2) or $mysql_error = mysql_error(); //query untuk tabel kegiatan
				if($hasil2) if($hasil) echo "<div class=\"sukses\"><br>Data rincian kegiatan:<br> <b>$uraian <br>telah berhasil diubah menjadi <br><b>$u</b>.<br><br><br></div>";
				else echo "<div class=\"error\"><br>Data kegiatan gagal diubah.<br>Keterangan: error code [ur02]<br><b>".$mysql_error."</b><br><br></div>";
		}
	}
	*/
	//menghapus kegiatan
	function deleteKegiatan($idp, $id, $final = 0, $hal = 1) {
	//idp = id pengguna, id = id kegiatan, final 0 = konfirmasi, final 1 = aksi, hal = halaman
		$idp = $this->clean($idp);
		$id = $this->clean($id);
		$final = $this->clean($final);
		$hal = $this->clean($hal);
		$query = mysql_query("SELECT kegiatan FROM kegiatan WHERE id = '$id' AND idp = '$idp'");
		$cek = mysql_num_rows($query);
		if($cek < 1 || $id == 0) {
			echo '
		<div class="red-text card grey lighten-4 center-align">
			Error !<br>Nama Kegiatan yang anda pilih tidak terdaftar di akun ini !
		</div>
		';
		} else {
			if($final == 1) {
				$kegiatan = $this->lihatKegiatan($id, "kegiatan");
				$query = "DELETE FROM kegiatan WHERE id = '$id' ";
				$hasil = mysql_query($query) or $mysql_error = mysql_error();
				if($hasil) echo '
		<div class="green-text card grey lighten-4 center-align">
			Data kegiatan <b>'.$kegiatan.'</b> telah berhasil dihapus.
		</div>
							';
				else echo '
		<div class="red-text card grey lighten-4 center-align">
			Data kegiatan gagal dihapus.<br>Keterangan: error code [dk01]
		</div>
						';
			} else {
				$row = mysql_fetch_array($query);
				echo '
		<a class="waves-effect waves-light btn modal-trigger hide" id="hapus_modal" href="#hapus_konfirmasi">Hapus</a>
		<div id="hapus_konfirmasi" class="modal">
			<div class="modal-content">
				<h4>Menghapus Kegiatan</h4>
				<p>Anda yakin ingin menghapus kegiatan <b>'.$row['kegiatan'].'</b> ?</p>
			</div>
			<div class="modal-footer">
				<a href="'.URL.'index.php?hal='.$hal.'" class="modal-action modal-close waves-effect waves-grey red btn-flat">Tidak, Kembali!</a>
				<a href="'.URL.'index.php?hal='.$hal.'&hapus&idhapus='.$id.'&final=1" class="modal-action modal-close waves-effect waves-grey green btn-flat">Yakin, Lakukan!</a>
			</div>
		</div>
	<script>
		$(document).ready(function(){
			$(".modal-trigger").leanModal();
			$("#hapus_modal").click();
		});
	</script>
				  ';
			}
		}
	}

	//menghapus rincian kegiatan
	function deleteRincianKegiatan($idp, $idk, $id, $final = 0, $hal = 1) {
	//idp = id pengguna, idk = id kegiatan, id = id rincian kegiatan, final 0 = konfirmasi, final 1 = aksi, hal = halaman
		$idp = $this->clean($idp);
		$idk = $this->clean($idk);
		$id = $this->clean($id);
		$final = $this->clean($final);
		$hal = $this->clean($hal);
		$query = mysql_query("SELECT * FROM rincian_kegiatan WHERE id = '$id' AND idk = '$idk' AND idp = '$idp'");
		$cek = mysql_num_rows($query);
		if($cek < 1 || $id == 0) {
			echo '
		<div class="red-text card grey lighten-4 center-align">
			Error !<br>Rincian Kegiatan yang anda pilih tidak terdaftar di kegiatan ini !
		</div>
				';
		} else {
			if($final == 1) {
			//notif ketika rincian kegiatan benar-benar dihapus
				$row = mysql_fetch_array($query); // row dari tabel rincian_kegiatan
				$query2 = mysql_query("SELECT * FROM kegiatan WHERE id = '$idk' AND idp = '$idp'");
				$row2 = mysql_fetch_array($query2); // row dari tabel kegiatan
				$uraian = $row["uraian"];
				$debit_new = $row2["debit"] - $row["debit"];
				$kredit_new = $row2["kredit"] - $row["kredit"];
				$saldo_new = $debit_new - $kredit_new;
				$query3 = "UPDATE kegiatan SET  debit = '$debit_new', kredit = '$kredit_new', saldo = '$saldo_new' WHERE id = '$idk'";
				$query4 = "DELETE FROM rincian_kegiatan WHERE id = '$id' ";
				$hasil3 = mysql_query($query3) or $mysql_error3 = mysql_error();
				$hasil4 = mysql_query($query4) or $mysql_error4 = mysql_error();
				if($hasil3) {
					if($hasil4) echo '
		<div class="green-text card grey lighten-4 center-align">
			Data rincian kegiatan sebagai berikut telah berhasil dihapus.
			<br /> Uraian : <i>'.$row['uraian'].'</i>
			<br /> Debit  : <b>'.$this->uang($row['debit'],1).'</b>
			<br /> Kredit : <b>'.$this->uang($row['kredit'],1).'</b>
			<br /><br /> Saldo : <b>'.$this->uang($row2['saldo'],1).' - '.$this->uang($row['saldo'],1).' = '.$this->uang($saldo_new,1).'</b>
		</div>
								';
					else echo '
		<div class="red-text card grey lighten-4 center-align">
			Data rincian kegiatan gagal dihapus.<br>Keterangan: error code [dr01]<br><b>'.$mysql_error4.'</b>
		</div>
						';
				} else {
					echo '
		<div class="red-text card grey lighten-4 center-align">
			Data rincian kegiatan gagal dihapus.<br>Keterangan: error code [dr02]<br><b>'.$mysql_error3.'</b>
		</div>
						';
					
					}
			} else {
			//notif konfirmasi ketika akan menghapus rincian kegiatan
				$row = mysql_fetch_array($query);
				echo '
		<a class="waves-effect waves-light btn modal-trigger hide" id="hapus_modal" href="#hapus_konfirmasi">Hapus</a>
		<div id="hapus_konfirmasi" class="modal">
			<div class="modal-content">
				<h4>Menghapus Kegiatan</h4>
				<p>				Anda yakin ingin menghapus rincian kegiatan ini?
				<br /> Uraian : <i>'.$row['uraian'].'</i>
				<br /> Debit  : <b>'.$this->uang($row['debit'],1).'</b>
				<br /> Kredit : <b>'.$this->uang($row['kredit'],1).'</b></p>
			</div>
			<div class="modal-footer">
				<a href="'.URL.'rincian.php?hal='.$hal.'&idk='.$idk.'" class="modal-action modal-close waves-effect waves-grey red btn-flat">Tidak, Kembali!</a>
				<a href="'.URL.'rincian.php?hapus&final=1&hal='.$hal.'&idk='.$idk.'&idhapus='.$id.'" class="modal-action modal-close waves-effect waves-grey green btn-flat">Yakin, Lakukan!</a>
			</div>
		</div>
	<script>
		$(document).ready(function(){
			$(".modal-trigger").leanModal();
			$("#hapus_modal").click();
		});
	</script>
				  ';
			}
		}
	}

	function tanggal_indo($tanggal, $cetak_hari = false) {
		$hari = array( 1 => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu');
		$bulan = array(1 => 'Jan', 'Feb', 'Mare', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov','Des');
		$split = explode('-', $tanggal);
		$tgl_indo = $split[2] . '-' . $bulan[ (int)$split[1] ] . '-' . $split[0];
		if ($cetak_hari) {
			$num = date('N', strtotime($tanggal));
			return $hari[$num] . ', ' . $tgl_indo;
		}
		return $tgl_indo;
	}
	
	function uang($nominal,  $rp = 0) {
		if($nominal == "" ) $nominal = 0;
		if($rp == 1)
			$nominal = "Rp. ".number_format($nominal, 0, ",", ".");
		else
			$nominal = number_format($nominal, 0, ",", ".");
		return $nominal;
	}
	
	function max_text($text, $batas = 20, $titik = 1) {
		if(strlen($text) > $batas) {
			if($titik == 0)
				return substr($text, 0, $batas);
			else
				return substr($text, 0, $batas-3)."...";
		} else
			return $text;
	}
 }

$db = new database($host, $user, $dbname, $pass);
$db->connectMySQL();


?>