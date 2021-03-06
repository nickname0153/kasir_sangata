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

# MEMBUAT SQL FILTER PER BULAN & TAHUN
if($dataBulan and $dataTahun) {
	if($dataBulan=="00") {
		// Filter tahun
		$filterSQL	= "AND LEFT(p.tgl_pembelian,4)='$dataTahun'";
		
		$infoBulan	= "";
	}
	else {
		// Filter bulan dan tahun
		$filterSQL = "AND MID(p.tgl_pembelian,6,2)='$dataBulan' AND LEFT(p.tgl_pembelian,4)='$dataTahun'";
		
		$infoBulan	= $listBulan[$dataBulan].", ";
	}
}
else {
	$filterSQL = "";
}
?>
<html>
<head>
<title> :: Laporan Pembelian Barang per Bulan/ Tahun - Program Minimarket</title>
<link href="../styles/styles_cetak.css" rel="stylesheet" type="text/css"></head>
<body>
<h2>LAPORAN PEMBELIAN BARANG PER BULAN/ TAHUN</h2>
<table width="500" border="0"  class="table-list">
  <tr>
    <td colspan="3" bgcolor="#CCCCCC"><strong>KETERANGAN</strong></td>
  </tr>
  <tr>
    <td width="134"><strong> Bulan Penjualan </strong></td>
    <td width="15"><strong>:</strong></td>
    <td width="337"><?php echo $infoBulan.$dataTahun; ?></td>
  </tr>
</table>
<br />
<table class="table-list" width="850" border="0" cellspacing="1" cellpadding="2">
  <tr>
    <td width="28" rowspan="2" align="center" bgcolor="#CCCCCC"><strong>No</strong></td>
    <td width="70" rowspan="2" bgcolor="#CCCCCC"><strong>Tgl. Nota </strong></td>
    <td width="70" rowspan="2" bgcolor="#CCCCCC"><strong>No. Nota </strong></td>
    <td width="56" rowspan="2" bgcolor="#CCCCCC"><strong>Kode</strong></td>
    <td width="339" rowspan="2" bgcolor="#CCCCCC"><strong>Nama Barang </strong></td>
    <td colspan="3" align="center" bgcolor="#999999"><strong>HASIL  </strong></td>
  </tr>
  <tr>
    <td width="114" align="right" bgcolor="#CCCCCC"><strong>Harga  (Rp) </strong></td>
    <td width="56" align="right" bgcolor="#CCCCCC"><strong>Jumlah</strong></td>
    <td width="76" align="right" bgcolor="#CCCCCC"><strong>Total (Rp) </strong></td>
  </tr>
  <?php
  	// deklarasi variabel
	$totalHarga	= 0;
	$totalBarang	= 0;
	
	# Perintah untuk menampilkan data Rawat dengan filter Periode
	$mySql = "SELECT p.no_pembelian, p.tgl_pembelian, pi.kd_barang, barang.nm_barang, pi.harga_beli, pi.jumlah,
				(pi.harga_beli * pi.jumlah) As total_harga
				FROM pembelian As p, pembelian_item As pi
				LEFT JOIN barang ON pi.kd_barang = barang.kd_barang
				WHERE p.no_pembelian = pi.no_pembelian
				$filterSQL
				ORDER BY no_pembelian, kd_barang ASC";
	$myQry = mysql_query($mySql, $koneksidb)  or die ("Query salah : ".mysql_error());
	$nomor = 0; 
	while ($myData = mysql_fetch_array($myQry)) {
		$nomor++;		
		
		# Rekap data
		$totalHarga	= $totalHarga + $myData['total_harga'];  // Menghitung total modal beli
		$totalBarang= $totalBarang + $myData['jumlah'];      // Menghitung total barang terjual
	?>
  <tr>
    <td><?php echo $nomor; ?></td>
    <td><?php echo IndonesiaTgl2($myData['tgl_pembelian']); ?></td>
    <td><?php echo $myData['no_pembelian']; ?></td>
    <td><?php echo $myData['kd_barang']; ?></td>
    <td><?php echo $myData['nm_barang']; ?></td>
    <td align="right"><?php echo format_angka($myData['harga_beli']); ?></td>
    <td align="right"><?php echo $myData['jumlah']; ?></td>
    <td align="right"><?php echo format_angka($myData['total_harga']); ?></td>
  </tr>
  <?php } ?>
  <tr>
    <td colspan="6" align="right"><strong>GRAND TOTAL:</strong></td>
    <td align="right" bgcolor="#CCCCCC"><strong><?php echo format_angka($totalBarang); ?></strong></td>
    <td align="right" bgcolor="#CCCCCC"><strong><?php echo format_angka($totalHarga); ?></strong></td>
  </tr>
</table>
<img src="../images/btn_print.png" height="20" onClick="javascript:window.print()" />
</body>
</html>