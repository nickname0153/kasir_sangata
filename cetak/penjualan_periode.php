<?php
session_start();
include_once "../library/inc.seslogin.php";
include_once "../library/inc.connection.php";
include_once "../library/inc.library.php";

if($_GET) {
	# Set Tanggal skrg
	$tglAwal 	= isset($_GET['tglAwal']) ? $_GET['tglAwal'] : "01-".date('m-Y');
	$tglAkhir 	= isset($_GET['tglAkhir']) ? $_GET['tglAkhir'] : date('d-m-Y');

	$filterSQL = " WHERE ( tgl_penjualan BETWEEN '".InggrisTgl($tglAwal)."' AND '".InggrisTgl($tglAkhir)."')";
}
else {
	$filterSQL = "";
}
?>
<html>
<head>
<title> :: Laporan Data Penjualan per Periode - Program Minimarket</title>
<link href="../styles/styles_cetak.css" rel="stylesheet" type="text/css"></head>
<body>
<h2>LAPORAN DATA PENJUALAN PER PERIODE</h2>
<table width="400" border="0"  class="table-list">
  <tr>
    <td colspan="3" bgcolor="#CCCCCC"><strong>KETERANGAN</strong></td>
  </tr>
  <tr>
    <td width="134"><strong>Periode Tanggal </strong></td>
    <td width="15"><strong>:</strong></td>
    <td width="337"><?php echo $tglAwal; ?> <strong>s/d</strong> <?php echo $tglAkhir; ?></td>
  </tr>
</table>
<br />
<table class="table-list" width="800" border="0" cellspacing="1" cellpadding="2">
  <tr>
    <td width="28" align="center" bgcolor="#F5F5F5"><strong>No</strong></td>
    <td width="69" bgcolor="#F5F5F5"><strong>Tgl. Nota </strong></td>
    <td width="97" bgcolor="#F5F5F5"><strong>No. Nota  </strong></td>
    <td width="146" bgcolor="#F5F5F5"><strong>Pelanggan </strong></td>
    <td width="192" bgcolor="#F5F5F5"><strong>Keterangan </strong></td>
    <td width="96" align="right" bgcolor="#F5F5F5"><strong>Total Barang</strong> </td>
    <td width="136" align="right" bgcolor="#F5F5F5"><strong>Total Belanja (Rp)</strong></td>
  </tr>
<?php
// Variabel data
$totalHarga	= 0;
$totalBarang= 0;
	
# Perintah untuk menampilkan Penjualan dengan Filter Periode
$mySql = "SELECT penjualan.*, pelanggan.nm_pelanggan FROM penjualan 
				LEFT JOIN pelanggan ON penjualan.kd_pelanggan = pelanggan.kd_pelanggan
				$filterSQL ORDER BY no_penjualan";
$myQry = mysql_query($mySql, $koneksidb)  or die ("Query 1 salah : ".mysql_error());
$nomor = 0;
while ($myData = mysql_fetch_array($myQry)) {
	$nomor++;
	$noNota	= $myData['no_penjualan'];
	
	# Menghitung Total Tiap Transaksi
	$my2Sql = "SELECT SUM((harga_jual - (harga_jual * diskon/100)) * jumlah) AS total_belanja,
					  SUM(jumlah) As total_barang 
					  FROM penjualan_item WHERE no_penjualan='$noNota'";
	$my2Qry = mysql_query($my2Sql, $koneksidb)  or die ("Query 1 salah : ".mysql_error());
	$my2Data= mysql_fetch_array($my2Qry);
	
	// Menjumlah Total Semua Transaksi yang ditampilkan
	$totalHarga	= $totalHarga + $my2Data['total_belanja'];
	$totalBarang	= $totalBarang + $my2Data['total_barang'];
?>
  <tr>
    <td><?php echo $nomor; ?></td>
    <td><?php echo IndonesiaTgl2($myData['tgl_penjualan']); ?></td>
    <td><?php echo $myData['no_penjualan']; ?></td>
    <td><?php echo $myData['kd_pelanggan']."/ ".$myData['nm_pelanggan']; ?></td>
    <td><?php echo $myData['keterangan']; ?></td>
    <td align="right"><?php echo format_angka($my2Data['total_barang']); ?></td>
    <td align="right"><?php echo format_angka($my2Data['total_belanja']); ?></td>
  </tr>
<?php } ?>
  <tr>
    <td colspan="5" align="right"><strong>GRAND TOTAL : </strong></td>
    <td align="right" bgcolor="#F5F5F5"><strong><?php echo format_angka($totalBarang); ?></strong></td>
    <td align="right" bgcolor="#F5F5F5"><strong>Rp. <?php echo format_angka($totalHarga); ?></strong></td>
  </tr>
</table>
<img src="../images/btn_print.png" height="20" onClick="javascript:window.print()" />
</body>
</html>