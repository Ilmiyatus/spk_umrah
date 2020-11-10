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
			
		<h3>Step 1: Matriks Keputusan (X)</h3>
		<table class="pure-table pure-table-striped">
			<thead>
				<tr class="super-top">
					<th rowspan="2">Nama Biro Umrah</th>
					<th colspan="<?php echo count($kriterias); ?>">Kriteria</th>
				</tr>
				<tr>
					<?php foreach($kriterias as $kriteria ): ?>
						<th><?php echo $kriteria['nama']; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach($biros as $biro): ?>
					<tr>
						<td><?php echo $biro['nama']; ?></td>
						<?php						
						foreach($kriterias as $kriteria):
							$id_biro_umroh = $biro['id_biro_umroh'];
							$id_kriteria = $kriteria['id_kriteria'];
							echo '<td>';
							echo $matriks_x[$id_kriteria][$id_biro_umroh];
							echo '</td>';
						endforeach;
						?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<h3>Step 2: Bobot Preferensi (W)</h3>			
		<table class="pure-table pure-table-striped">
			<thead>
				<tr>
					<th>Nama Kriteria</th>
					<th>Type</th>
					<th>Bobot (W)</th>						
				</tr>
			</thead>
			<tbody>
				<?php foreach($kriterias as $hasil): ?>
					<tr>
						<td><?php echo $hasil['nama']; ?></td>
						<td>
						<?php
						if($hasil['type'] == 'benefit') {
							echo 'Benefit';
						} elseif($hasil['type'] == 'cost') {
							echo 'Cost';
						}							
						?>
						</td>
						<td><?php echo $hasil['bobot']; ?></td>							
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<h3>Step 3: Matriks Ternormalisasi (R)</h3>			
		<table class="pure-table pure-table-striped">
			<thead>
				<tr class="super-top">
					<th rowspan="2">Nama Biro Umrah</th>
					<th colspan="<?php echo count($kriterias); ?>">Kriteria</th>
				</tr>
				<tr>
					<?php foreach($kriterias as $kriteria ): ?>
						<th><?php echo $kriteria['nama']; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach($biros as $biro): ?>
					<tr>
						<td><?php echo $biro['nama']; ?></td>
						<?php						
						foreach($kriterias as $kriteria):
							$id_biro_umroh = $biro['id_biro_umroh'];
							$id_kriteria = $kriteria['id_kriteria'];
							echo '<td>';
							echo round($matriks_r[$id_kriteria][$id_biro_umroh], $digit);
							echo '</td>';
						endforeach;
						?>
					</tr>
				<?php endforeach; ?>				
			</tbody>
		</table>		
		
		
		<?php		
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
		<h3>Step 4: Perangkingan (V)</h3>			
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