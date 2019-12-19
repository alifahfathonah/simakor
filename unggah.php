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
$idr = isset($_GET['idr']) ? $_GET['idr'] : 0;
if($idk == 0 || $idr == 0) header("location:./index.php");

head($nama, $idk); //fungsi head di inc.php, berisi tag html <head> ... </head><body>
	$currentDir = getcwd();
    $uploadDirectory = "/uploads/";
    $errors = array(); // Store all foreseen and unforseen errors here

    $fileExtensions = array('jpeg','jpg','png'); // Get all the file extensions

    $fileName = $idr."-".$db->max_text($db->lihatRincianKegiatan($idr, $idk, "uraian"), 15, 0);
    $fileSize = $_FILES['bukti']['size'];
    $fileTmpName  = $_FILES['bukti']['tmp_name'];
    $fileType = $_FILES['bukti']['type'];
    $fileExtension = strtolower(end(explode('.',$_FILES['bukti']['name'])));

    $uploadPath = $currentDir . $uploadDirectory . $idp."/".$idk."/";

    if (isset($_POST['unggah'])) {
		if(!is_dir($uploadPath)) {
			mkdir($uploadPath, 0777, true);
			$file_index = fopen($uploadPath.'index.php', 'w');
			fwrite($file_index, '<?php header("Location: '.URL.'"); ?>');
			fclose($file_index);
			if(!file_exists($currentDir.$uploadDirectory.$idp.'/index.php')) {
				$file_index = fopen($currentDir.$uploadDirectory . $idp.'/index.php', 'w');
				fwrite($file_index, '<?php header("Location: '.URL.'"); ?>');
				fclose($file_index);				
			}
		}
		$uploadPath = $uploadPath . basename($fileName.".".$fileExtension);

		$byte = "byte";
		$fs = $fileSize;
		if($fs >= 1024*1024) {
			$fs = number_format($fs / (1024*1024), 2);
			$byte = "MB";
		} else if($fs >= 1024) {
			$fs = number_format($fs / (1024), 2);
			$byte = "kB";
		}
        if ($fileSize == 0) {
            $errors[] = 'Silahkan pilih foto bukti keuangan!';
        }
        if ($fileSize > 1024*1024 *2) {
            $errors[] = 'Ukuran foto: <b>'.$fs.' '.$byte.'</b><br />Ukuran foto tidak boleh <b>lebih dari 2 MB</b>!';
        }
        if (! in_array($fileExtension,$fileExtensions)) {
            $errors[] = $fileExtension.' Hanya dapat mengunggah foto JPEG atau PNG !';
        }

        if (empty($errors)) {
			$mysqli_error = '';
        	if(file_exists($uploadPath)) unlink($uploadPath);//memastikan jmlah bukti di mysql bertambah jika foto juga bertambah dalam dir
            $didUpload = move_uploaded_file($fileTmpName, $uploadPath);
			$query = "UPDATE rincian_kegiatan SET bukti = 1 WHERE id = '$idr' AND idk = '$idk' AND idp = '$idp'";
			$hasil = mysqli_query($this->con, $query) or $errors[] = mysqli_error($this->con); //query untuk tabel rincian_kegiatan

            if ($didUpload && $hasil) {
                $berhasil = file_exists($uploadPath).' Foto <b>'.basename($fileName).'.'.$fileExtension.'</b> [<i>'.$fs.' '.$byte.'</i>] berhasil diunggah sebagai bukti keuangan.';
			} else if(count($errors) == 1) {
	            $pesan = 'Terjadi kesalahan.<br />'.$errors[0].'!';
            } else {
	            $pesan = 'Terjadi kesalahan. Silahkan coba lagi!';
            }
        } else {
			$pesan = "Error:\n";
            foreach ($errors as $error) {
                $pesan .= $error."<br />";
            }
        }
    }
echo '
	<p><h1 class="cyan-text center-align">'.$nama.'</h1></p>
	<nav>
		<div class="nav-wrapper cyan lighten-1">
			<div class="col s12">
				<a href="'.URL.'index.php?hal='.$hal.'" class="breadcrumb">Daftar Kegiatan</a>
				<a href="'.URL.'rincian.php?idk='.$idk.'&hal='.$hal.'" class="breadcrumb">
					'.$db->max_text($db->lihatKegiatan($idk,"kegiatan")).' ['.$db->uang($db->lihatKegiatan($idk,"saldo"),1).'] ['.$db->lihatRincianKegiatan($idr,$idk,"banyak_bukti").' bukti]
				</a>
				<a href="" class="breadcrumb">
					Upload Bukti '.$db->lihatRincianKegiatan(0,$idk,"rincian").'
				</a>
			</div>
		</div>
	</nav>
	';	
 if(strlen($berhasil) !=0) {
	echo '
		<div class="green-text  card grey lighten-4 center-align">
			'.$berhasil.'
		</div>
	';
 }
 if(strlen($pesan) !=0) {
	echo '
		<div class="red-text  card grey lighten-4 center-align">
				'.$pesan.'
		</div>
	';
 }
?>

	<form action="unggah.php?<?php echo "idr=$idr&idk=$idk"; ?>" method="POST" enctype="multipart/form-data">
	<div class="card" style="max-width: 480px;margin: 40px auto;">
        <div class="row">
			<div class="file-field input-field col s12">
				<div class="btn-large cyan">
					<span><i class="material-icons medium left">attachment</i> Foto </span>
					<input name="bukti" type="file" accept="image/*">
				</div>
				<div class="file-path-wrapper">
					<input id="file_path" class="file-path" type="text" placeholder="Pilih Foto Bukti Keuangan">
				</div>
			</div>
        </div>
		<div class="row">
			<div class="col s12 center-align">
                <button class="btn-large submit-btn cyan waves-effect tooltipped" type="submit" onclick="return validasi();" name="unggah" data-position="right" data-delay="100" data-tooltip="Klik untuk menambahkan">
					<i class="material-icons">cloud_upload</i> Unggah
				</button>
				<br />
				<br />
			</div>
		</div>
	</div>
	</form>
<?php
foot(1);
?>