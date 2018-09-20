<?php
include 'inc.php';
// mengaktifkan session
session_start();
$status = 1;
// cek apakah user telah login, jika belum login maka di alihkan ke halaman login
if($_SESSION['status'] !="login"){
	$status = 0;
}
$idp = $_SESSION['idp'];
$nama = $_SESSION['nama'];
$user = $_SESSION['user'];

$db->teakhir_online($user);
head($nama, -1); //fungsi head di inc.php, berisi tag html <head> ... </head><body>
?>
    <p class="center-align"><h4 class="cyan-text center-align header">Tentang <?php echo FBC; ?></h4></p>
	<div class="card" style="max-width: 720px;margin: 20px auto;">
		<div class="row">
			<div class="col s12">
<p align="justify">
&emsp;&emsp;
<b><em>Simakor</em></b> adalah akronim dari <i>Sistem Keuangan Organisasi</i>, yaitu sistem aplikasi berbasis web yang
bertujuan untuk mempermudah manajemen keuangan terutama bagi organisasi dilingkungan kampus atau perguruan tinggi,
namun secara umum sistem manajemen keuangan Simakor dapat digunakan secara luas oleh jenis-jenis organisasi lain.
<br/>
</p>
<p align="justify">
&emsp;&emsp;
Hal yang dapat dilakukan dalam Simakor adalah untuk <i>mencatat Kegiatan beserta jumalah dana masuk (debit), dana keluar
(kredit) dan saldonya. Dengan setiap kegiatan dapat diberikan rincian yang juga memiliki fasilitas pencatat dana debit,
kredit, saldo bahkan bukti keuangan untuk setiap rincian pengeluaran dari suatu kegiatan</i>.
<br/>
</p>
<p align="justify">
&emsp;&emsp;
Salah satu keunggulan <em>Simakor</em> selain dapat diakses kapanpun karena berbasis web yang artinya dapat diakses selama
ada koneksi internet, juga adalah adanya fasilitas export ke file excel. Jadi, semua data yang terekam dapat di export ke
dalam bentuk tabel dengan format <b>.xls</b> yang dapat dibuka di program <i>MS. Excel dan sejenisnya</i>. Selain itu,
bukti-bukti keuangan yang sudah diunggah dapat diunduh baik diunduh per-rincian, maupun per-kegiatan dalam format <b>.zip</b>.
<br/>
</p>
<p align="justify">
&emsp;&emsp;
Diharapkan dengan kehadiran Simakor sebagai solusi bagi manajemen keuangan organisasi dapat menjadi lebih mudah terutama
saat menjelang masa rekapitulasi maupun pertanggungjawaban.
</p>
<p>
Dibuat oleh: <b><em>Fajar Budi Cahyanto</em></b>
<br/>
Kredit Design: <b><em>Ferdian Maulana</em></b>
<br />
</p>
            </div>
		</div>
	</div>

<?php
foot($status);	
?>