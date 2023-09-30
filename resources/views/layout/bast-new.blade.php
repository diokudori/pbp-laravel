<?php 

$bulan = array (
		1 => 'Januari',
		'Februari',
		'Maret',
		'April',
		'Mei',
		'Juni',
		'Juli',
		'Agustus',
		'September',
		'Oktober',
		'November',
		'Desember'
	);

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<style type="text/css">
		td, p{
			font-size: 8pt;
		}
		.list td{
  			border: 1px solid black;
  			border-collapse: collapse;
  			text-align: left;
  			font-size: 9pt;
		}

		@page {
  header: page-header;
  footer: page-footer;
  margin-top: 275px;
}
		*{
			font-family: "Calibri";
		}

		.t-head td{
			font-size: 7pt;
		}

		footer {
  position: fixed;
  bottom: 0;}
	</style>
</head>
<body>
	<htmlpageheader name="page-header">
    <table style="width: 100%">
	<tr>
		<td style="width: 25%"><img src="{{public_path('assets/images/logo-bulog.jpeg')}}" style="height: 40px;"></td>
		<td></td>
		<td  style="width: 25%">
			<img src="{{public_path('assets/images/logo-yat.jpeg')}}" style="height: 50px; margin-right: 0">
		</td>
	</tr>
	<tr>
		<td></td>
		<td style="font-size: 7pt; width: 50%;">
			<h3>BERITA ACARA SERAH TERIMA (BAST)<br>PENERIMA BANTUAN PANGAN - CBP <?=date('Y')?></h3>
			<table>
				<tr><td>Nomor</td><td>:</td><td></td></tr>
				<tr><td>Alokasi Bulan</td><td>:</td><td><span style="text-transform: uppercase;"><?=$bulan[(int)date('m')]?></span> <?=date('Y')?></td></tr>
			</table>
		</td>
		<td></td>
	</tr>
</table>

<table style="width: 100%; font-size: 8pt;">
	<!-- <tr>
		<td style="width: 15%">Provinsi</td> <td style="width: 55%">: JAWA TIMUR</td> 
		<td style="width: 10%; display: none;">KCU / KC</td> <td style="width:20%; display: none;">: {{$kprk}}</td>
	</tr> -->
	<tr>
		<td style="width: 20%">PROVINSI</td> <td style="width: 2.5%;">:</td><td> {{$provinsi}}</td>
	</tr>
	<tr>
		<td>KECAMATAN</td> <td>:</td><td> {{$kecamatan}}</td>
	</tr>
	<tr>
		<td>KABUPATEN</td> <td>:</td><td> {{$kabupaten}}</td> 
	</tr>
	<tr>
		<td>KELURAHAN/DESA</td> <td>:</td><td> {{$kelurahan}}</td>
	</tr>
	
</table>

<p style="margin-top: 10px; margin-bottom: 10px;">
	Kami yang bertanda tangan pada daftar dibawah ini :
Menyatakan dengan sebenar-benarnya bahwa telah menerima 10 KG beras bantuan pangan – CBP 2023 dengan kualitas baik :

</p>
</htmlpageheader>
<div>
	<?php $counter = 0;?>
	@foreach($list as $k => $lis)
<table style="width: 100%; padding-top: 400px; border: solid 1px #000; border-collapse: collapse;" class="list">
	<tr class="t-head"><td style="width: 5%;text-align: center;">NO</td> <td style="width: 25%;text-align: center;">NAMA</td> <td style="width: 55%;text-align: center;">ALAMAT</td> 
		<!-- <td colspan="2" style="width: 10%; text-align: center;">NOMOR BARCODE</td>  -->
		 <td style="text-align: center;">TANDA TANGAN PBP</td> 
		<!-- <td style="width: 10%;text-align: center;">TGL SERAH</td> -->
	</tr>
	<?php $odd = true; $show = false;?>
	@foreach($lis as $l)
	<?php $counter++; 
		$qr = $l->prefik.sprintf("%04s", $counter);
	?>
	<tr>
		<td style="text-align: center">{{$counter}}</td>
		<td style="padding: 10px; font-size: 10pt;">{{$l->nama}}</td>
		<td style="padding: 10px; font-size: 7pt;">{{$l->alamat}}, RT {{$l->rt}}, RW {{$l->rw}}</td>
		@if($show)
				@if($odd)
				<td style="text-align: center; padding: 5px 0;"><img src="http://chart.googleapis.com/chart?cht=qr&chs=42x42&choe=UTF-8&chld=L|0&chl={{$qr}}"></td>
				<td></td>
					<?php $odd = false;?>
				@else
				<td></td>
				<td style="text-align: center; padding: 5px 0;"><img src="http://chart.googleapis.com/chart?cht=qr&chs=42x42&choe=UTF-8&chld=L|0&chl={{$qr}}"></td>
					<?php $odd = true;?>
				@endif
		@endif
		
		<td></td>
	</tr>
	@endforeach
	
</table>
@if($k == (sizeof($list)-1))
<br>
<p style="text-align: right; width: 100%;">..................,..............<?=date('Y')?></p>
<table style="width: 100%">
	<tr>
		<td style="text-align: center;">
			Mengetahui,<br>
			<b>Aparat Setempat*</b>
			<br><br><br><br><br><br><br>

			..............................................<br>
			<p>(Nama Jelas, TTD dan Stempel)</p>
		</td>
		<td style="text-align: center;">
			Yang Menyerahkan,<br>	
			Transporter
			<br><br><br><br><br><br><br>
			...............................................<br>
			(Nama Jelas)
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<p>Keterangan :<br>
* Aparat Setempat adalah Pengurus RT/RW atau sebutan nama lainnya atau aparat kelurahan/desa atau perwakilan penerima bantuan pangan sasaran.
</p>
</td>
	</tr>
</table>
@endif
<!-- <footer>
<p style="text-align: right;"><span>Halaman {PAGENO} | {nb}</span></p>
</footer> -->
<htmlpagefooter name="page-footer" style="margin-bottom: 50px;">
	<p style="text-align: right; float: right; "> 
	Halaman {PAGENO} | {nbpg} {{$kelurahan}}
</p><br>
</htmlpagefooter>
@if($k != (sizeof($list)-1))
<html-separator/>
<pagebreak>
@endif
@endforeach
</div>






</body>
</html>