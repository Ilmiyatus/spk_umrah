<?php require_once('includes/init.php'); ?>

<?php
$ada_error = false;
$result = '';

$id_biro_umroh = (isset($_GET['id'])) ? trim($_GET['id']) : '';

if(!$id_biro_umroh) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = $pdo->prepare('SELECT * FROM biro_umroh WHERE id_biro_umroh = :id_biro_umroh');
	$query->execute(array('id_biro_umroh' => $id_biro_umroh));
	$result = $query->fetch();
	
	if(empty($result)) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	}
}
?>

<?php
$judul_page = 'Detail Biro Umrah';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-biro-umrah.php'); ?>
	
		<div class="main-content the-content">
			<h1><?php echo $judul_page; ?></h1>
			
			<?php if($ada_error): ?>
			
				<?php echo '<p>'.$ada_error.'</p>'; ?>
				
			<?php elseif(!empty($result)): ?>
			
				<h4>Nama Biro Umrah</h4>
				<p><?php echo $result['nama']; ?></p>
				
				<h4>Alamat</h4>
				<p><?php echo nl2br($result['alamat']); ?></p>
				
				<h4>Tanggal Input</h4>
				<p><?php
					$tgl = strtotime($result['tanggal_input']);
					echo date('j F Y', $tgl);
				?></p>
				
				<?php
				$query2 = $pdo->prepare('SELECT nilai_biro_umroh.nilai AS nilai, kriteria.nama AS nama FROM kriteria 
				LEFT JOIN nilai_biro_umroh ON nilai_biro_umroh.id_kriteria = kriteria.id_kriteria 
				AND nilai_biro_umroh.id_biro_umroh = :id_biro_umroh ORDER BY kriteria.urutan_order ASC');
				$query2->execute(array(
					'id_biro_umroh' => $id_biro_umroh
				));
				$query2->setFetchMode(PDO::FETCH_ASSOC);
				$kriterias = $query2->fetchAll();
				if(!empty($kriterias)):
				?>
					<h3>Nilai Kriteria</h3>
					<table class="pure-table">
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
									<th><?php echo ($kriteria['nilai']) ? $kriteria['nilai'] : 0; ?></th>
								<?php endforeach; ?>
							</tr>
						</tbody>
					</table>
				<?php
				endif;
				?>

				<p><a href="edit-biro-umrah.php?id=<?php echo $id_biro_umroh; ?>" class="button"><span class="fa fa-pencil"></span> Edit</a> &nbsp; <a href="hapus-biro-umrah.php?id=<?php echo $id_biro_umroh; ?>" class="button button-red yakin-hapus"><span class="fa fa-times"></span> Hapus</a></p>
			
			<?php endif; ?>			
			
		</div>
	
	</div>
	</div>


<?php
require_once('template-parts/footer.php');