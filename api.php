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
$action = isset($_GET['a']) ? $_GET['a'] : 0;
$status = isset($_GET['s']) ? $_GET['s'] : 0;
$idk = isset($_GET['idk']) ? $_GET['idk'] : 0;
$idr = isset($_GET['idr']) ? $_GET['idr'] : 0;

if($status <= 0) die("Error [1]");

if($action == 1) {
	if($status <= 0) die("Error [2]");
	$status = explode("-",$status);
	$out = array();
	for($i = 0; $i <= count($status)-1; $i++) {
		$out[] = lihat($db, $idk, $idr, intval($status[$i]));
	}
	echo json_encode($out);
	}

if($action == 2);

function lihat($db, $idk, $idr, $status) {
	switch($status) {
		case 1 : return $db->max_text($db->lihatKegiatan($idk, "kegiatan")); break;
		case 2 : return $db->uang($db->lihatKegiatan($idk, "debit"), 1); break;
		case 3 : return $db->uang($db->lihatKegiatan($idk, "kredit"), 1); break;
		case 4 : return $db->uang($db->lihatKegiatan($idk, "saldo"), 1); break;
		case 5 : return $db->tanggal_indo($db->lihatKegiatan($idk, "tanggal")); break;
		case 6 : return $db->tanggal_indo($db->lihatRincianKegiatan($idr, $idk, "tanggal")); break;
		case 7 : return $db->max_text($db->lihatRincianKegiatan($idr, $idk, "uraian")); break;
		case 8 : return $db->uang($db->lihatRincianKegiatan($idr, $idk, "debit"), 1); break;
		case 9 : return $db->uang($db->lihatRincianKegiatan($idr, $idk, "kredit"), 1); break;
		case 10 : return $db->uang($db->lihatRincianKegiatan($idr, $idk, "saldo"), 1); break;
		case 11 : return $db->lihatRincianKegiatan($idr, $idk, "bukti"); break;
		case 12 : return $db->lihatRincianKegiatan($idr, $idk, "banyak_rincian"); break;
		case 13 : return $db->lihatRincianKegiatan($idr, $idk, "banyak_bukti"); break;
		/*tanpa fungsi filter*/
		case 14 : return $db->lihatKegiatan($idk, "kegiatan"); break;
		case 15 : return $db->lihatKegiatan($idk, "debit"); break;
		case 16 : return $db->lihatKegiatan($idk, "kredit"); break;
		case 17 : return $db->lihatKegiatan($idk, "saldo"); break;
		case 18 : return $db->lihatKegiatan($idk, "tanggal"); break;
		case 19 : return $db->lihatRincianKegiatan($idr, $idk, "tanggal"); break;
		case 20 : return $db->lihatRincianKegiatan($idr, $idk, "uraian"); break;
		case 21 : return $db->lihatRincianKegiatan($idr, $idk, "debit"); break;
		case 22 : return $db->lihatRincianKegiatan($idr, $idk, "kredit"); break;
		case 22 : return $db->lihatRincianKegiatan($idr, $idk, "saldo"); break;
		default: return "Error [3]";
	}
}
?>
