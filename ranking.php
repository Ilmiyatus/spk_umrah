<?php
require_once('includes/init.php');
$judul_page = 'Perankingan Menggunakan Metode TOPSIS dan SAW';
require_once('template-parts/header.php');
$digit = 6;
$query = $pdo->prepare('SELECT id_kriteria, nama, type, bobot
	FROM kriteria ORDER BY urutan_order ASC');
$query->execute();
$query->setFetchMode(PDO::FETCH_ASSOC);
$kriterias = $query->fetchAll();
$query2 = $pdo->prepare('SELECT id_biro_umroh, nama FROM biro_umroh');
$query2->execute();			
$query2->setFetchMode(PDO::FETCH_ASSOC);
$biros = $query2->fetchAll();


$matriks_x = array();
foreach($kriterias as $kriteria):
	foreach($biros as $biro):
		
		$id_biro_umroh = $biro['id_biro_umroh'];
		$id_kriteria = $kriteria['id_kriteria'];
		
		$query3 = $pdo->prepare('SELECT nilai FROM nilai_biro_umroh
			WHERE id_biro_umroh = :id_biro_umroh AND id_kriteria = :id_kriteria');
		$query3->execute(array(
			'id_biro_umroh' => $id_biro_umroh,
			'id_kriteria' => $id_kriteria,
		));			
		$query3->setFetchMode(PDO::FETCH_ASSOC);
		if($nilai_biro_umroh = $query3->fetch()) {
			$matriks_x[$id_kriteria][$id_biro_umroh] = $nilai_biro_umroh['nilai'];
		} else {			
			$matriks_x[$id_kriteria][$id_biro_umroh] = 0;
		}

	endforeach;
endforeach;

$matriks_r = array();
foreach($matriks_x as $id_kriteria => $nilai_biro_umrohs):
	
	$jumlah_kuadrat = 0;
	foreach($nilai_biro_umrohs as $nilai_biro_umroh):
		$jumlah_kuadrat += pow($nilai_biro_umroh, 2);
	endforeach;
	$akar_kuadrat = sqrt($jumlah_kuadrat);
	
	foreach($nilai_biro_umrohs as $id_biro_umroh => $nilai_biro_umroh):
		$matriks_r[$id_kriteria][$id_biro_umroh] = $nilai_biro_umroh / $akar_kuadrat;
	endforeach;
	
endforeach;

$matriks_y = array();
foreach($kriterias as $kriteria):
	foreach($biros as $biro):
		
		$bobot = $kriteria['bobot'];
		$id_biro_umroh = $biro['id_biro_umroh'];
		$id_kriteria = $kriteria['id_kriteria'];
		
		$nilai_r = $matriks_r[$id_kriteria][$id_biro_umroh];
		$matriks_y[$id_kriteria][$id_biro_umroh] = $bobot * $nilai_r;

	endforeach;
endforeach;

$solusi_ideal_positif = array();
$solusi_ideal_negatif = array();
foreach($kriterias as $kriteria):

	$id_kriteria = $kriteria['id_kriteria'];
	$type_kriteria = $kriteria['type'];
	
	$nilai_max = max($matriks_y[$id_kriteria]);
	$nilai_min = min($matriks_y[$id_kriteria]);
	
	if($type_kriteria == 'benefit'):
		$s_i_p = $nilai_max;
		$s_i_n = $nilai_min;
	elseif($type_kriteria == 'cost'):
		$s_i_p = $nilai_min;
		$s_i_n = $nilai_max;
	endif;
	
	$solusi_ideal_positif[$id_kriteria] = $s_i_p;
	$solusi_ideal_negatif[$id_kriteria] = $s_i_n;

endforeach;


$jarak_ideal_positif = array();
$jarak_ideal_negatif = array();
foreach($biros as $biro):

	$id_biro_umroh = $biro['id_biro_umroh'];		
	$jumlah_kuadrat_jip = 0;
	$jumlah_kuadrat_jin = 0;
	
	foreach($matriks_y as $id_kriteria => $nilai_biro_umrohs):
		
		$hsl_pengurangan_jip = $nilai_biro_umrohs[$id_biro_umroh] - $solusi_ideal_positif[$id_kriteria];
		$hsl_pengurangan_jin = $nilai_biro_umrohs[$id_biro_umroh] - $solusi_ideal_negatif[$id_kriteria];
		
		$jumlah_kuadrat_jip += pow($hsl_pengurangan_jip, 2);
		$jumlah_kuadrat_jin += pow($hsl_pengurangan_jin, 2);
	
	endforeach;
	
	$akar_kuadrat_jip = sqrt($jumlah_kuadrat_jip);
	$akar_kuadrat_jin = sqrt($jumlah_kuadrat_jin);
	
	$jarak_ideal_positif[$id_biro_umroh] = $akar_kuadrat_jip;
	$jarak_ideal_negatif[$id_biro_umroh] = $akar_kuadrat_jin;
	
endforeach;
$ranks = array();
foreach($biros as $biro):

	$s_negatif = $jarak_ideal_negatif[$biro['id_biro_umroh']];
	$s_positif = $jarak_ideal_positif[$biro['id_biro_umroh']];	
	
	$nilai_v = $s_negatif / ($s_positif + $s_negatif);
	
	$ranks[$biro['id_biro_umroh']]['id_biro_umroh'] = $biro['id_biro_umroh'];
	$ranks[$biro['id_biro_umroh']]['nama'] = $biro['nama'];
	$ranks[$biro['id_biro_umroh']]['nilai'] = $nilai_v;
	
endforeach;
 
?>

<div class="main-content-row">
<div class="container clearfix">	

	<div class="main-content main-content-full the-content">
		
		<h1><?php echo $judul_page; ?></h1>
		
		
		<?php		
		$sorted_ranks = $ranks;	
		
		if(function_exists('array_multisort')):
			foreach ($sorted_ranks as $key => $row) {
				$nama[$key]  = $row['nama'];
				$nilai[$key] = $row['nilai'];
			}
			array_multisort($nilai, SORT_DESC, $nama, SORT_ASC, $sorted_ranks);
		endif;
		?>		
		<h3>Perangkingan</h3>			
		<table class="pure-table pure-table-striped">
			<thead>					
				<tr>
					<th width="100px">Ranking</th>
					<th class="super-top-left">Nama Biro Umrah</th>
					<th>Nilai</th>
				</tr>
			</thead>
			<tbody
			><?php $a = 1;
				foreach($sorted_ranks as $biro ): ?>
					<tr>
					<td><?php echo $a++; ?></td>
						<td><?php echo $biro['nama']; ?></td>
						<td><?php echo round($biro['nilai'], $digit); ?></td>											
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>			
		
	</div>

</div>
</div>
<?php
require_once('includes/init.php');
$judul_page = 'Perankingan Menggunakan Metode SAW';
require_once('template-parts/header.php');
$digit = 6;
$query = $pdo->prepare('SELECT id_kriteria, nama, type, bobot
	FROM kriteria ORDER BY urutan_order ASC');
$query->execute();
$query->setFetchMode(PDO::FETCH_ASSOC);
$kriterias = $query->fetchAll();
$query2 = $pdo->prepare('SELECT id_biro_umroh, nama FROM biro_umroh');
$query2->execute();			
$query2->setFetchMode(PDO::FETCH_ASSOC);
$biros = $query2->fetchAll();


$matriks_x = array();
$list_kriteria = array();
foreach($kriterias as $kriteria):
	$list_kriteria[$kriteria['id_kriteria']] = $kriteria;
	foreach($biros as $biro):
		
		$id_biro_umroh = $biro['id_biro_umroh'];
		$id_kriteria = $kriteria['id_kriteria'];
		
		$query3 = $pdo->prepare('SELECT nilai FROM nilai_biro_umroh
			WHERE id_biro_umroh = :id_biro_umroh AND id_kriteria = :id_kriteria');
		$query3->execute(array(
			'id_biro_umroh' => $id_biro_umroh,
			'id_kriteria' => $id_kriteria,
		));			
		$query3->setFetchMode(PDO::FETCH_ASSOC);
		if($nilai_biro_umroh = $query3->fetch()) {
			$matriks_x[$id_kriteria][$id_biro_umroh] = $nilai_biro_umroh['nilai'];
		} else {			
			$matriks_x[$id_kriteria][$id_biro_umroh] = 0;
		}

	endforeach;
endforeach;

$matriks_r = array();
foreach($matriks_x as $id_kriteria => $nilai_biro_umrohs):
	
	$tipe = $list_kriteria[$id_kriteria]['type'];
	foreach($nilai_biro_umrohs as $id_alternatif => $nilai) {
		if($tipe == 'benefit') {
			$nilai_normal = $nilai / max($nilai_biro_umrohs);
		} elseif($tipe == 'cost') {
			$nilai_normal = min($nilai_biro_umrohs) / $nilai;
		}
		
		$matriks_r[$id_kriteria][$id_alternatif] = $nilai_normal;
	}
	
endforeach;
$ranks = array();
foreach($biros as $biro):

	$total_nilai = 0;
	foreach($list_kriteria as $kriteria) {
	
		$bobot = $kriteria['bobot'];
		$id_biro_umroh = $biro['id_biro_umroh'];
		$id_kriteria = $kriteria['id_kriteria'];
		
		$nilai_r = $matriks_r[$id_kriteria][$id_biro_umroh];
		$total_nilai = $total_nilai + ($bobot * $nilai_r);

	}
	
	$ranks[$biro['id_biro_umroh']]['id_biro_umroh'] = $biro['id_biro_umroh'];
	$ranks[$biro['id_biro_umroh']]['nama'] = $biro['nama'];
	$ranks[$biro['id_biro_umroh']]['nilai'] = $total_nilai;
	
endforeach;
 
?>

<div class="main-content-row">
<div class="container clearfix">	

	<div class="main-content main-content-full the-content">
		
		<h1><?php echo $judul_page; ?></h1>
				
		<?php count($kriterias);
				
					foreach($kriterias as $kriteria ):
					$kriteria['nama'];
					endforeach;
				foreach($biros as $biro):
					$biro['nama'];	
						foreach($kriterias as $kriteria):
							$id_biro_umroh = $biro['id_biro_umroh'];
							$id_kriteria = $kriteria['id_kriteria'];
							$matriks_x[$id_kriteria][$id_biro_umroh];
						endforeach;
				endforeach;
		
		foreach($kriterias as $hasil):
		$hasil['nama']; 
						if($hasil['type'] == 'benefit') {
							'Benefit';
						} elseif($hasil['type'] == 'cost') {
							'Cost';
						}							
						$hasil['bobot']; 
				endforeach;
			foreach($kriterias as $kriteria ):
						$kriteria['nama'];
					endforeach;
			foreach($biros as $biro): 
			$biro['nama']; 					
						foreach($kriterias as $kriteria):
							$id_biro_umroh = $biro['id_biro_umroh'];
							$id_kriteria = $kriteria['id_kriteria'];
							 round($matriks_r[$id_kriteria][$id_biro_umroh], $digit);
						endforeach;
						endforeach;
		$sorted_ranks = $ranks;		
		if(function_exists('array_multisort')):
			$nama = array();
			$nilai = array();
			foreach ($sorted_ranks as $key => $row) {
				$nama[$key]  = $row['nama'];
				$nilai[$key] = $row['nilai'];
			}
			array_multisort($nilai, SORT_DESC, $nama, SORT_ASC, $sorted_ranks);
		endif;
		?>		
		<h3>Perangkingan</h3>			
		<table class="pure-table pure-table-striped">
			<thead>					
				<tr>
					<th width="100px">Ranking</th>
					<th>Nama Biro Umrah</th>
					<th>Skor</th>
				</tr>
			</thead>
			<tbody>
			<?php $a = 1;
				foreach($sorted_ranks as $biro ): ?>
					<tr>
						<td><?php echo $a++; ?></td>
						<td><?php echo $biro['nama']; ?></td>
						<td><?php echo round($biro['nilai'], $digit); ?></td>											
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>			
		
	</div>

</div>
</div>

<?php
require_once('template-parts/footer.php');