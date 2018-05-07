<?php
session_start();
include_once "../library/inc.seslogin.php";
include_once "../library/inc.connection.php";
include_once "../library/inc.library.php";

// Membuat daftar bulan
$listBulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret",
				 "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli",
				 "08" => "Agustus", "09" => "September", "10" => "Oktober",
				 "11" => "November", "12" => "Desember");

// Membaca data Bulan dan Tahun dari URL
$dataTahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$dataBulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');

# MEMBUAT FILTER BERDASARKAN TANGGAL & TAHUN
if($dataBulan and $dataTahun) {
	if($dataBulan=="00") {
		// Filter tahun
		$filterSQL	= "AND LEFT(tgl_returbeli,4)='$dataTahun'";
		
		$infoBulan	= "";
	}
	else {
		// Filter bulan dan tahun
		$filterSQL = "AND MID(tgl_returbeli,6,2)='$dataBulan' AND LEFT(tgl_returbeli,4)='$dataTahun'";
		
		$infoBulan	= $listBulan[$dataBulan].", ";
	}
}
else {
	$filterSQL = "";
}
?>
<html>
<head>
<title>:: Laporan Data Retur per Bulan/ Tahun - Program Minimarket</title>
<link href="../styles/styles_cetak.css" rel="stylesheet" type="text/css">
</head>
<body>
<h2>LAPORAN DATA RETUR PER BULAN/ TAHUN</h2>
<table width="400" border="0"  class="table-list">
  <tr>
    <td colspan="3" bgcolor="#F5F5F5"><strong>KETERANGAN</strong></td>
  </tr>
  <tr>
    <td width="148"><strong> Periode Bulan/Tahun</strong></td>
    <td width="15"><strong>:</strong></td>
    <td width="223"><?php echo $infoBulan.$dataTahun; ?></td>
  </tr>
</table>
<br />
<table class="table-list" width="800" border="0" cellspacing="1" cellpadding="2">
  <tr>
    <td width="28" align="center" bgcolor="#F5F5F5"><strong>No</strong></td>
    <td width="76" bgcolor="#F5F5F5"><strong>Tgl. Retur </strong></td>
    <td width="81" bgcolor="#F5F5F5"><strong>No. Retur </strong></td>
    <td width="271" bgcolor="#F5F5F5"><strong>Supplier</strong></td>
    <td width="253" bgcolor="#F5F5F5"><strong>Keterangan </strong></td>
    <td width="60" align="right" bgcolor="#F5F5F5"><strong>Jumlah </strong></td>
  </tr>
<?php
# Perintah untuk menampilkan returbeli dengan Filter Periode
$mySql = "SELECT returbeli.*, supplier.nm_supplier FROM returbeli 
			LEFT JOIN supplier ON returbeli.kd_supplier = supplier.kd_supplier 
			$filterSQL ORDER BY no_returbeli DESC";
$myQry = mysql_query($mySql, $koneksidb)  or die ("Query 1 salah : ".mysql_error());
$nomor = 0; $totalRetur = 0;
while ($myData = mysql_fetch_array($myQry)) {
	$nomor++;
	$noNota	= $myData['no_returbeli'];
	
	# Menghitung Total Belanja
	$my2Sql = "SELECT SUM(jumlah) As total_barang FROM returbeli_item WHERE no_returbeli='$noNota'";
	$my2Qry = mysql_query($my2Sql, $koneksidb)  or die ("Query 1 salah : ".mysql_error());
	$my2Data= mysql_fetch_array($my2Qry);
	$totalRetur = $totalRetur + $my2Data['total_barang'];
?>
  <tr>
    <td><?php echo $nomor; ?></td>
    <td><?php echo IndonesiaTgl2($myData['tgl_returbeli']); ?></td>
    <td><?php echo $myData['no_returbeli']; ?></td>
    <td><?php echo $myData['nm_supplier']; ?></td>
    <td><?php echo $myData['keterangan']; ?></td>
    <td align="right"><?php echo format_angka($my2Data['total_barang']); ?></td>
  </tr>
<?php } ?>
  <tr>
    <td colspan="5" align="right"><strong>GRAND TOTAL  : </strong></td>
    <td align="right" bgcolor="#F5F5F5"><strong><?php echo format_angka($totalRetur); ?></strong></td>
  </tr>
</table>
<img src="../images/btn_print.png" height="20" onClick="javascript:window.print()" />
</body>
</html>