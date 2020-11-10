<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1, 2)); ?>

<?php
$judul_page = 'List Biro Umrah';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-biro-umrah.php'); ?>
	
		<div class="main-content the-content">
			
			<?php
			$status = isset($_GET['status']) ? $_GET['status'] : '';
			$msg = '';
			switch($status):
				case 'sukses-baru':
					$msg = 'Data biro umrah baru berhasil ditambahkan';
					break;
				case 'sukses-hapus':
					$msg = 'Data biro umrah berhasil dihapus';
					break;
				case 'sukses-edit':
					$msg = 'Data biro umrah berhasil diedit';
					break;
			endswitch;
			
			if($msg):
				echo '<div class="msg-box msg-box-full">';
				echo '<p><span class="fa fa-bullhorn"></span> &nbsp; '.$msg.'</p>';
				echo '</div>';
			endif;
			?>
		
			<h1>List Biro Umrah </h1>
			
			<?php
			$query = $pdo->prepare('SELECT * FROM biro_umroh');			
			$query->execute();
			$query->setFetchMode(PDO::FETCH_ASSOC);
			
			if($query->rowCount() > 0):
			?>
			
			<table class="pure-table pure-table-striped">
				<thead>
					<tr>
						<th>Nama Biro Umrah</th>
						<th>Kantor Pusat</th>
						<th>Detail</th>						
						<th>Edit</th>
						<th>Hapus</th>
					</tr>
				</thead>
				<tbody>
					<?php while($hasil = $query->fetch()): ?>
						<tr>
							<td><?php echo $hasil['nama']; ?></td>							
							<td><?php echo $hasil['alamat']; ?></td>							
							<td><a href="single-biro-umrah.php?id=<?php echo $hasil['id_biro_umroh']; ?>"><span class="fa fa-eye"></span> Detail</a></td>
							<td><a href="edit-biro-umrah.php?id=<?php echo $hasil['id_biro_umroh']; ?>"><span class="fa fa-pencil"></span> Edit</a></td>
							<td><a href="hapus-biro-umrah.php?id=<?php echo $hasil['id_biro_umroh']; ?>" class="red yakin-hapus"><span class="fa fa-times"></span> Hapus</a></td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
			
			
			<?php
			$query = $pdo->prepare('SELECT id_kriteria, nama, type, bobot FROM kriteria
				ORDER BY urutan_order ASC');
			$query->execute();			
			$kriterias = $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);
			
			$query2 = $pdo->prepare('SELECT id_biro_umroh, nama FROM biro_umroh');
			$query2->execute();			
			$query2->setFetchMode(PDO::FETCH_ASSOC);
			$biros = $query2->fetchAll();			
			?>
			
			<h3>Matriks Keputusan (X)</h3>
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
							$query3 = $pdo->prepare('SELECT id_kriteria, nilai FROM nilai_biro_umroh
								WHERE id_biro_umroh = :id_biro_umroh');
							$query3->execute(array(
								'id_biro_umroh' => $biro['id_biro_umroh']
							));			
							$query3->setFetchMode(PDO::FETCH_ASSOC);
							$nilais = $query3->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);
							
							foreach($kriterias as $id_kriteria => $values):
								echo '<td>';
								if(isset($nilais[$id_kriteria])) {
									echo $nilais[$id_kriteria]['nilai'];
									$kriterias[$id_kriteria]['nilai'][$biro['id_biro_umroh']] = $nilais[$id_kriteria]['nilai'];
								} else {
									echo 0;
									$kriterias[$id_kriteria]['nilai'][$biro['id_biro_umroh']] = 0;
								}
								
								if(isset($kriterias[$id_kriteria]['tn_kuadrat'])){
									$kriterias[$id_kriteria]['tn_kuadrat'] += pow($kriterias[$id_kriteria]['nilai'][$biro['id_biro_umroh']], 2);
								} else {
									$kriterias[$id_kriteria]['tn_kuadrat'] = pow($kriterias[$id_kriteria]['nilai'][$biro['id_biro_umroh']], 2);
								}
								echo '</td>';
							endforeach;
							?>
							</pre>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<?php else: ?>
				<p>Maaf, belum ada data untuk biro umrah.</p>
			<?php endif; ?>
		</div>
	
	</div>
	</div>

<?php
require_once('template-parts/footer.php');