<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" name="form1" target="_self">
  <table width="985" border="0"  class="table table-responsive table-striped">
    <tr class="danger">
      <td colspan="3" bgcolor="#CCCCCC"><strong>FILTER DATA</strong></td>
    </tr>
    <tr>
      <td><strong>Bulan/ Tahun  </strong></td>
      <td><strong>:</strong></td>
      <td><div class="col-md-4">
      	<select name="cmbBulan" class="form-control">
          <?php
		// Membuat daftar Nama Bulan
		$listBulan = array("00" => "....", "01" => "01. Januari", "02" => "02. Februari", "03" => "03. Maret",
						 "04" => "04. April", "05" => "05. Mei", "06" => "06. Juni", "07" => "07. Juli",
						 "08" => "08. Agustus", "09" => "09. September", "10" => "10. Oktober",
						 "11" => "11. November", "12" => "12. Desember");
						 
		// Menampilkan Nama Bulan ke ComboBox (List/Menu)
		foreach($listBulan as $bulanKe => $bulanNm) {
			if ($bulanKe == $dataBulan) {
				$cek = " selected";
			} else { $cek=""; }
			echo "<option value='$bulanKe' $cek>$bulanNm</option>";
		}
	  ?>
        </select></div>
        <div class="col-md-2">
          <select name="cmbTahun" class="form-control">
            <?php
		# Baca tahun terendah(awal) di tabel Transaksi
		$thnSql = "SELECT MIN(LEFT(tgl_returbeli,4)) As tahun_kecil, MAX(LEFT(tgl_returbeli,4)) As tahun_besar FROM returbeli";
		$thnQry	= mysql_query($thnSql, $koneksidb) or die ("Error".mysql_error());
		$thnRow	= mysql_fetch_array($thnQry);
		$thnKecil = $thnRow['tahun_kecil'];
		$thnBesar = $thnRow['tahun_besar'];
		
		// Menampilkan daftar Tahun, dari tahun terkecil sampai Terbesar (tahun sekarang)
		for($thn= $thnKecil; $thn <= $thnBesar; $thn++) {
			if ($thn == $dataTahun) {
				$cek = " selected";
			} else { $cek=""; }
			echo "<option value='$thn' $cek>$thn</option>";
		}
	  ?>
        </select></div></td>
    </tr>
    <tr>
      <td width="140">&nbsp;</td>
      <td width="5">&nbsp;</td>
      <td width="241"><div class="col-md-8">
      	<input name="btnTampil" type="submit" value=" Tampilkan " class="btn btn-success" />
      <input name="btnCetak" type="submit" value=" Cetak " class="btn btn-primary" /></div></td>
    </tr>
  </table>
</form>