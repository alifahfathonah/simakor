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
if($idk == 0) header("location:./index.php");

if(isset($_REQUEST["fn"])){
    // Get parameters
    $file = urldecode($_REQUEST["fn"]); // Decode URL-encoded string
    $filepath = './uploads/'.$idp.'/'.$idk.'/'.$file;
//    header("Location: $filepath");
    // Process download

    if(file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Content-Length: ' . filesize($filepath));
        flush(); // Flush system output buffer
        readfile($filepath);
        exit;
    } //else echo $filepath;

}
?>