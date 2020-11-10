<?php
require_once('includes/init.php');
$judul_page = 'Perankingan Menggunakan Metode TOPSIS';
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
		
		<h3>Step 1: Matriks Keputusan (X)</h3>
		<table class="pure-table pure-table-striped">
			<thead>
				<tr class="super-top">
					<th rowspan="2" class="super-top-left">Nama Biro Umrah</th>
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
					<th rowspan="2" class="super-top-left">Nama Biro Umrah</th>
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
		
		
		<h3>Step 4: Matriks Y</h3>			
		<table class="pure-table pure-table-striped">
			<thead>
				<tr class="super-top">
					<th rowspan="2" class="super-top-left">Nama Biro Umrah</th>
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
							echo round($matriks_y[$id_kriteria][$id_biro_umroh], $digit);
							echo '</td>';
						endforeach;
						?>
					</tr>
				<?php endforeach; ?>	
			</tbody>
		</table>	
		
		
		<h3>Step 5.1: Solusi Ideal Positif (A<sup>+</sup>)</h3>			
		<table class="pure-table pure-table-striped">
			<thead>					
				<tr>
					<?php foreach($kriterias as $kriteria ): ?>
						<th><?php echo $kriteria['nama']; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<?php foreach($kriterias as $kriteria ): ?>
						<td>
							<?php
							$id_kriteria = $kriteria['id_kriteria'];							
							echo round($solusi_ideal_positif[$id_kriteria], $digit);
							?>
						</td>
					<?php endforeach; ?>
				</tr>					
			</tbody>
		</table>
		
		<h3>Step 5.2: Solusi Ideal Negatif (A<sup>-</sup>)</h3>			
		<table class="pure-table pure-table-striped">
			<thead>					
				<tr>
					<?php foreach($kriterias as $kriteria ): ?>
						<th><?php echo $kriteria['nama']; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<?php foreach($kriterias as $kriteria ): ?>
						<td>
							<?php
							$id_kriteria = $kriteria['id_kriteria'];							
							echo round($solusi_ideal_negatif[$id_kriteria], $digit);
							?>
						</td>
					<?php endforeach; ?>
				</tr>					
			</tbody>
		</table>		
		
		<h3>Step 6.1: Jarak Ideal Positif (S<sub>i</sub>+)</h3>			
		<table class="pure-table pure-table-striped">
			<thead>					
				<tr>
					<th class="super-top-left">Nama Biro Umrah</th>
					<th>Jarak Ideal Positif</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($biros as $biro ): ?>
					<tr>
						<td><?php echo $biro['nama']; ?></td>
						<td>
							<?php								
							$id_biro_umroh = $biro['id_biro_umroh'];
							echo round($jarak_ideal_positif[$id_biro_umroh], $digit);
							?>
						</td>						
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<h3>Step 6.2: Jarak Ideal Negatif (S<sub>i</sub>-)</h3>			
		<table class="pure-table pure-table-striped">
			<thead>					
				<tr>
					<th class="super-top-left">Nama Biro Umrah</th>
					<th>Jarak Ideal Negatif</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($biros as $biro ): ?>
					<tr>
						<td><?php echo $biro['nama']; ?></td>
						<td>
							<?php								
							$id_biro_umroh = $biro['id_biro_umroh'];
							echo round($jarak_ideal_negatif[$id_biro_umroh], $digit);
							?>
						</td>						
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		
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
		<h3>Step 7: Perangkingan (V)</h3>			
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
require_once('template-parts/footer.php');