<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1, 2)); ?>

<?php
$errors = array();
$sukses = false;

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

	$id_biro_umroh = (isset($result['id_biro_umroh'])) ? trim($result['id_biro_umroh']) : '';
	$nama = (isset($result['nama'])) ? trim($result['nama']) : '';
	$alamat = (isset($result['alamat'])) ? trim($result['alamat']) : '';
	$tanggal_input = (isset($result['tanggal_input'])) ? trim($result['tanggal_input']) : '';
}

if(isset($_POST['submit'])):	
	
	$nama = (isset($_POST['nama'])) ? trim($_POST['nama']) : '';
	$alamat = (isset($_POST['alamat'])) ? trim($_POST['alamat']) : '';
	$tanggal_input = (isset($_POST['tanggal_input'])) ? trim($_POST['tanggal_input']) : '';
	$kriteria = (isset($_POST['kriteria'])) ? $_POST['kriteria'] : array();
	
	if(!$id_biro_umroh) {
		$errors[] = 'ID biro umrah tidak ada';
	}

	if(!$nama) {
		$errors[] = 'Nama biro umrah tidak boleh kosong';
	}
	if(!$tanggal_input) {
		$errors[] = 'Tanggal input tidak boleh kosong';
	}
	
	if(empty($errors)):
		
		$prepare_query = 'UPDATE biro_umroh SET nama = :nama, alamat = :alamat, tanggal_input = :tanggal_input WHERE id_biro_umroh = :id_biro_umroh';
		$data = array(
			'nama' => $nama,
			'alamat' => $alamat,
			'tanggal_input' => $tanggal_input,
			'id_biro_umroh' => $id_biro_umroh,
		);		
		$handle = $pdo->prepare($prepare_query);		
		$sukses = $handle->execute($data);
		
		if(!empty($kriteria)):
			foreach($kriteria as $id_kriteria => $nilai):
				$handle = $pdo->prepare('INSERT INTO nilai_biro_umroh (id_biro_umroh, id_kriteria, nilai) 
				VALUES (:id_biro_umroh, :id_kriteria, :nilai)
				ON DUPLICATE KEY UPDATE nilai = :nilai');
				$handle->execute( array(
					'id_biro_umroh' => $id_biro_umroh,
					'id_kriteria' => $id_kriteria,
					'nilai' =>$nilai
				) );
			endforeach;
		endif;
		
		redirect_to('list-biro-umrah.php?status=sukses-edit');
	
	endif;

endif;
?>

<?php
$judul_page = 'Edit Biro Umrah';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-biro-umrah.php'); ?>
	
		<div class="main-content the-content">
			<h1>Edit Biro Umrah</h1>
			
			<?php if(!empty($errors)): ?>
			
				<div class="msg-box warning-box">
					<p><strong>Error:</strong></p>
					<ul>
						<?php foreach($errors as $error): ?>
							<li><?php echo $error; ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				
			<?php endif; ?>
			
			<?php if($sukses): ?>
			
				<div class="msg-box">
					<p>Data berhasil disimpan</p>
				</div>	
				
			<?php elseif($ada_error): ?>
				
				<p><?php echo $ada_error; ?></p>
			
			<?php else: ?>				
				
				<form action="edit-biro-umrah.php?id=<?php echo $id_biro_umroh; ?>" method="post">
					<div class="field-wrap clearfix">					
						<label>Nama Biro Umrah <span class="red">*</span></label>
						<input type="text" name="nama" value="<?php echo $nama; ?>">
					</div>					
					<div class="field-wrap clearfix">					
						<label>Kantor Pusat <span class="red">*</span></label>
						<select name="alamat" value="<?php echo $alamat; ?>" selected>
							<option value="0">-- Pilih Kantor Pusat --</option>
							<option value="Mojokerto, Jawa Timur" <?php if ($alamat=="Mojokerto, Jawa Timur") { echo "selected=\"selected\""; } ?>>Mojokerto, Jawa Timur</option>
							<option value="Sidoarjo, Jawa Timur" <?php if ($alamat=="Sidoarjo, Jawa Timur") { echo "selected=\"selected\""; } ?>>Sidoarjo, Jawa Timur</option>
							<option value="Palembang, Sumatera Selatan" <?php if ($alamat=="Palembang, Sumatera Selatan") { echo "selected=\"selected\""; } ?>>Palembang, Sumatera Selatan</option>
							<option value="Bandung, Jawa Barat" <?php if ($alamat=="Bandung, Jawa Barat") { echo "selected=\"selected\""; } ?>>Bandung, Jawa Barat</option>
							<option value="Samarinda, Kalimantan Timur" <?php if ($alamat=="Samarinda, Kalimantan Timur") { echo "selected=\"selected\""; } ?>>Samarinda, Kalimantan Timur</option>
							<option value="Tebet Barat, Jakarta Selatan" <?php if ($alamat=="Tebet Barat, Jakarta Selatan") { echo "selected=\"selected\""; } ?>>Tebet Barat, Jakarta Selatan</option>
							<?php echo $alamat; ?></select>
					</div>
					<div class="field-wrap clearfix">					
						<label>Tanggal Input <span class="red">*</span></label>
						<input type="text" name="tanggal_input" value="<?php echo $tanggal_input; ?>" class="datepicker">
					</div>	
					
					<h3>Nilai Kriteria</h3>
					<?php
					$query2 = $pdo->prepare('SELECT nilai_biro_umroh.nilai AS nilai, kriteria.nama AS nama, kriteria.id_kriteria AS id_kriteria, kriteria.ada_pilihan AS jenis_nilai 
					FROM kriteria LEFT JOIN nilai_biro_umroh 
					ON nilai_biro_umroh.id_kriteria = kriteria.id_kriteria 
					AND nilai_biro_umroh.id_biro_umroh = :id_biro_umroh 
					ORDER BY kriteria.urutan_order ASC');
					$query2->execute(array(
						'id_biro_umroh' => $id_biro_umroh
					));
					$query2->setFetchMode(PDO::FETCH_ASSOC);
					
					if($query2->rowCount() > 0):
					
						while($kriteria = $query2->fetch()):
						?>
							<div class="field-wrap clearfix">					
								<label><?php echo $kriteria['nama']; ?></label>
								<?php if(!$kriteria['jenis_nilai']): ?>
									<input type="radio" name="kriteria[<?php echo $kriteria['id_kriteria'];  ?>]" value="1" <?php if($kriteria['nilai']==0.2){echo 'checked';}?>> 1
									<input type="radio" name="kriteria[<?php echo $kriteria['id_kriteria'];  ?>]" value="2" <?php if($kriteria['nilai']==0.4){echo 'checked';}?>> 2	
									<input type="radio" name="kriteria[<?php echo $kriteria['id_kriteria'];  ?>]" value="3" <?php if($kriteria['nilai']==0.6){echo 'checked';}?>> 3
									<input type="radio" name="kriteria[<?php echo $kriteria['id_kriteria'];  ?>]" value="4" <?php if($kriteria['nilai']==0.8){echo 'checked';}?>> 4
									<input type="radio" name="kriteria[<?php echo $kriteria['id_kriteria'];  ?>]" value="5" <?php if($kriteria['nilai']==1){echo 'checked';}?>> 5	
								<?php else: ?>
									<select name="kriteria[<?php echo $kriteria['id_kriteria']; ?>]">
										<option value="0">-- Pilih Variabel --</option>
										<?php
										$query3 = $pdo->prepare('SELECT * FROM pilihan_kriteria WHERE id_kriteria = :id_kriteria ORDER BY urutan_order ASC');			
										$query3->execute(array(
											'id_kriteria' => $kriteria['id_kriteria']
										));
										$query3->setFetchMode(PDO::FETCH_ASSOC);
										if($query3->rowCount() > 0): while($hasl = $query3->fetch()):
										?>
											<option value="<?php echo $hasl['nilai']; ?>" <?php selected($kriteria['nilai'], $hasl['nilai']); ?>><?php echo $hasl['nama']; ?></option>
										<?php
										endwhile; endif;
										?>
									</select>
								<?php endif; ?>
							</div>		
						<?php
						endwhile;
						
					else:					
						echo '<p>Kriteria masih kosong.</p>';						
					endif;
					?>
					
					<div class="field-wrap clearfix">
						<button type="submit" name="submit" value="submit" class="button">Simpan Biro Umrah</button>
					</div>
				</form>
				
			<?php endif; ?>			
			
		</div>
	
	</div>
	</div>


<?php
require_once('template-parts/footer.php');