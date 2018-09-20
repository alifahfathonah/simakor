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
$tipe = isset($_GET['t']) ? $_GET['t'] : 0;
$idk = isset($_GET['idk']) ? $_GET['idk'] : 0;
$idr = isset($_GET['idr']) ? $_GET['idr'] : 0;

head($nama, $idk, 'zipbukti'); //fungsi head di inc.php, berisi tag html <head> ... </head><body>

echo '
	<p><h1 class="cyan-text center-align">'.$nama.'</h1></p>
	<nav>
		<div class="nav-wrapper cyan lighten-1">
			<div class="col s12">
				<a href="'.URL.'index.php?hal='.$hal.'" class="breadcrumb">Daftar Kegiatan</a>
';
if($idk != 0) {
	echo '
				<a href="'.URL.'rincian.php?idk='.$idk.'&hal='.$hal.'" class="breadcrumb">
					'.$db->max_text($db->lihatKegiatan($idk,"kegiatan")).' ['.$db->uang($db->lihatKegiatan($idk,"saldo"),1).'] ['.$db->lihatRincianKegiatan($idr,$idk,"banyak_bukti").' bukti]
	';
	if($idr != 0) {
		echo '
				<a href="'.URL.'rincian.php?idr='.$idr.'&idk='.$idk.'&hal='.$hal.'" class="breadcrumb">
					'.$db->max_text($db->lihatRincianKegiatan($idr, $idk,"uraian")).'
				</a>
';
	}
}
echo '
				<a href="" class="breadcrumb">Lihat Bukti Keuangan</a>
			</div>
		</div>
	</nav>
	';
echo '
	<div class="container left-align" id="forminner">
	<br />
';
$path = './uploads/'.$idp.'/';
if($idk != 0) $path .= $idk.'/';
$files = array_values( //menyusun ulang key dari array
			array_diff( //menurangi/menghilangkan value array
				scandir($path), //scan directory. (parameter) sumber array yg akan dikurangi
				array('..', '.')) //(parameter) array yang dikurangi dari sumber
			);
$ext = array("php", "zip");
$ext_hapus = array("zip");
//print_r($files);
$files_idr = array();
$files_hapus = array();
$arr = array();
$array = array();
$array = $files;
for($i=1; $i <= count($files); $i++) {
	if(strpos($files[$i-1], $idr.'-') !== false) $files_idr[] = $files[$i-1];
}
if($idr != 0) $array = $files_idr;
for($i=1; $i <= count($array); $i++) {
	if(!in_array(getExt($array[$i-1]), $ext)) $arr[] = $array[$i-1]; // untuk idr tertentu saja
	if(in_array(getExt($array[$i-1]), $ext_hapus)) $files_hapus[] = $array[$i-1];
}

//print_r($arr);
for($i=1; $i <= count($files_hapus); $i++) {
	//menghapus file zip sebelumnya
	$file_hapus = './uploads/'.$idp.'/'.$idk.'/'.$files_hapus[$i-1];
	$now = strtotime(date("YmdHis"));
	$date_file =strtotime(date("YmdHis", filemtime($file_hapus)));
	if(round(($now-$date_file)/60) >= 60 ) unlink($file_hapus); //menghapus setelah 60 menit
}
for($i=1; $i <= count($arr); $i++) {
	
  echo '
	<div class="row valign-wrapper">
		<div class="col s12 m12">
			<a href="./unduh.php?idk='.$idk.'&fn='.$arr[$i-1].'" class="tooltipped" data-position="bottom" data-delay="100" data-tooltip="Klik untuk mengunduh">
      		<div class="card">
        		<div class="card-image">
          			<img src="./uploads/'.$idp.'/'.$idk.'/'.$arr[$i-1].'">
          			<a href="./unduh.php?idk='.$idk.'&fn='.$arr[$i-1].'" class="btn-floating halfway-fab waves-effect waves-light cyan right tooltipped" data-position="bottom" data-delay="100" data-tooltip="Klik untuk mengunduh"><i class="material-icons">cloud_download</i></a>
		        </div>
    	    </a>
				<div class="card-content">
        	  		<p class="black-text">'.$arr[$i-1].'</p>
	        	</div>
			</div>
		</div>
  </div>
	';
}
echo '</div>';

if(isset($_GET['zip'])) {
	$files = $arr;
	$destination = './uploads/'.$idp.'/'.$idk.'/';
	$filename = $idp.'-'.$idk.'-'.$db->max_text($db->lihatRincianKegiatan($idr, $idk, 'uraian'), 15, 0).'-'.date('YmdHis').'.zip';
	$result = createZip($files, $destination, $filename);
//	print_r($array);
	if($result === true) echo '
	<script>
		window.location.href="./unduh.php?idk='.$idk.'&fn='.$filename.'";
	</script>';
}
echo '</div>';
foot(1);

function createZip($files = array(), $destination = '', $namafile = 'file.zip', $overwrite = false) {
   if(file_exists($destination.$namafile) && !$overwrite) { return false; }
   $validFiles = array();
   if(is_array($files)) {
      foreach($files as $file) {
         if(file_exists($destination.$file)) {
            $validFiles[] = $file;
         }
      }
   }
   if(count($validFiles)) {
      $zip = new ZipArchive();
      if($zip->open($destination.$namafile,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
         return false;
      }
      foreach($validFiles as $file) {
         $zip->addFile($destination.$file,$file);
      }
      $zip->close();
      return file_exists($destination.$namafile);
   }else{
      return false;
   }
}

function getExt($str) {
	return end(explode(".", $str));
}

?>