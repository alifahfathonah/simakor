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
$namafile = FBC."-".$user."-".$idk."-".date("YmdHis");
header("Content-type: application/vnd-ms-excel");
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=".$namafile.".xls");
echo '
<html>
 <head>
	<style>
		#simakor {
			font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
			border-collapse: collapse;
			width: 100%;
		}

		#simakor td, #simakor th {
			border: 1px solid #ddd;
			padding: 8px;
		}

		#simakor tr:nth-child(even){background-color: #f2f2f2;}

		#simakor tr:hover {background-color: #ddd;}

		#simakor th {
			padding-top: 12px;
			padding-bottom: 12px;
			text-align: center;
			background-color: cyan;
			color: black;
		}

		#simakor .tengah {text-align: center;}

		#simakor .kanan {text-align: right;}

		#simakor .kiri {text-align: left;}
	</style>
 </head>
<body>
	<table id="simakor">
';
?>
<?php
if($idk == 0) {
		$query = "SELECT id FROM kegiatan WHERE idp = '$idp'";
		$hasil = mysql_query($query);
		$total_data = mysql_num_rows($hasil);
		if($total_data > 0) {
			$no = 0;
			$total_saldo = 0;
			$total_debit = 0;
			$total_kredit = 0;
			$query = "SELECT * FROM kegiatan WHERE idp = '$idp' ORDER BY $db->order_kegiatan $db->sort_kegiatan";
			$hasil = mysql_query($query);

			echo '
		<tr>
			<th>No</th>
			<th>Kegiatan</th>
			<th>Debit (Rp)</th>
			<th>Kredit (Rp)</th>
			<th>Saldo (Rp)</th>
		</tr>
			';
			while($data = mysql_fetch_array($hasil)) {
				$no++;
				$total_saldo += $data['saldo'];
				$total_debit += $data['debit'];
				$total_kredit += $data['kredit'];
				echo '
		<tr>
			<td class="tengah">'.$no.'</td>
			<td class="kiri">'.$data['kegiatan'].'</td>
			<td class="kanan">'.$data['debit'].'</td>
			<td class="kanan">'.$data['kredit'].'</td>
			<td class="kanan">'.$data['saldo'].'</td>
		</tr>
			';
			}
		echo '
		<tr>
			<th class="tengah" colspan="2">Total</td>
			<th class="kanan">'.$total_debit.'</td>
			<th class="kanan">'.$total_kredit.'</td>
			<th class="kanan">'.$total_saldo.'</td>
		</tr>
		';
		}
		if($total_data<1) echo '
		<tr>
			<th>BELUM ADA DATA</th>
		</tr>
							';
} else {
		$query = "SELECT id FROM rincian_kegiatan WHERE idk = '$idk' AND idp = '$idp'";
		$hasil = mysql_query($query);
		$total_data = mysql_num_rows($hasil);
		if($total_data > 0) {
			$no = 0;
			$total_saldo = 0;
			$total_debit = 0;
			$total_kredit = 0;
			$query = "SELECT * FROM rincian_kegiatan WHERE idk = '$idk' AND idp = '$idp' ORDER BY $db->order_rincian $db->sort_rincian";
			$hasil = mysql_query($query) or die(mysql_error());

			echo '
		<tr>
			<th>No</th>
			<th>Tanggal</th>
			<th>Bukti</th>
			<th>Uraian</th>
			<th>Debit (Rp)</th>
			<th>Kredit (Rp)</th>
			<th>Saldo (Rp)</th>
		</tr>
			';
			while($data = mysql_fetch_array($hasil)) {
				$no++;
				$total_saldo += $data['saldo'];
				$total_debit += $data['debit'];
				$total_kredit += $data['kredit'];
				echo '
		<tr>
			<td class="tengah">'.$no.'</td>
			<td class="tengah">'.$db->tanggal_indo($data['tanggal']).'</td>
			<td class="tengah">'.$data['bukti'].'</td>
			<td class="kiri">'.$data['uraian'].'</td>
			<td class="kanan">'.$data['debit'].'</td>
			<td class="kanan">'.$data['kredit'].'</td>
			<td class="kanan">'.$data['saldo'].'</td>
		</tr>
				';
			}
		echo '
		<tr>
			<th class="tengah" colspan="4">Total</td>
			<th class="kanan">'.$total_debit.'</td>
			<th class="kanan">'.$total_kredit.'</td>
			<th class="kanan">'.$total_saldo.'</td>
		</tr>
		';
		}
		if($total_data<1) echo '
		<tr>
			<th>BELUM ADA DATA</th>
		</tr>
							';
}
echo '
	</table>
 </body>
</html>
';
?>