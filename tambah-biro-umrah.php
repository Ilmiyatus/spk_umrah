<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1, 2)); ?>

<?php
$errors = array();
$sukses = false;

$nama = (isset($_POST['nama'])) ? trim($_POST['nama']) : '';
$alamat = (isset($_POST['alamat'])) ? trim($_POST['alamat']) : '';
$kriteria = (isset($_POST['kriteria'])) ? $_POST['kriteria'] : array();


if(isset($_POST['submit'])):	
	
	if(!$nama) {
		$errors[] = 'Nama biro umrah tidak boleh kosong';
	}	
	
	if(empty($errors)):
		
		$handle = $pdo->prepare('INSERT INTO biro_umroh (nama, alamat, tanggal_input) VALUES (:nama, :alamat, :tanggal_input)');
		$handle->execute( array(
			'nama' => $nama,
			'alamat' => $alamat,
			'tanggal_input' => date('Y-m-d')
		) );
		$sukses = "Biro umrah <strong>{$nama}</strong> berhasil dimasukkan.";
		$id_biro_umroh = $pdo->lastInsertId();
		
		if(!empty($kriteria)):
			foreach($kriteria as $id_kriteria => $nilai):
				$handle = $pdo->prepare('INSERT INTO nilai_biro_umroh (id_biro_umroh, id_kriteria, nilai) VALUES (:id_biro_umroh, :id_kriteria, :nilai)');
				$handle->execute( array(
					'id_biro_umroh' => $id_biro_umroh,
					'id_kriteria' => $id_kriteria,
					'nilai' =>$nilai
				) );
			endforeach;
		endif;
		
		redirect_to('list-biro-umrah.php?status=sukses-baru');		
		
	endif;

endif;
?>

<?php
$judul_page = 'Tambah Biro Umrah';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-biro-umrah.php'); ?>
	
		<div class="main-content the-content">
			<h1>Tambah Biro Umrah</h1>
			
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
			
			
				<form action="tambah-biro-umrah.php" method="post">
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
					
					<h3>Nilai Kriteria</h3>
					<?php
					$query = $pdo->prepare('SELECT id_kriteria, nama, ada_pilihan FROM kriteria ORDER BY urutan_order ASC');			
					$query->execute();
					$query->setFetchMode(PDO::FETCH_ASSOC);
					
					if($query->rowCount() > 0):
					
						while($kriteria = $query->fetch()):							
						?>
						
							<div class="field-wrap clearfix">					
								<label><?php echo $kriteria['nama']; ?></label>
								<?php if(!$kriteria['ada_pilihan']): ?>
									<input type="radio" name="kriteria[<?php echo $kriteria['id_kriteria'];  ?>]" value="1"> 1
									<input type="radio" name="kriteria[<?php echo $kriteria['id_kriteria'];  ?>]" value="2"> 2		
									<input type="radio" name="kriteria[<?php echo $kriteria['id_kriteria'];  ?>]" value="3"> 3
									<input type="radio" name="kriteria[<?php echo $kriteria['id_kriteria'];  ?>]" value="4"> 4
									<input type="radio" name="kriteria[<?php echo $kriteria['id_kriteria'];  ?>]" value="5"> 5	
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
											<option value="<?php echo $hasl['nilai']; ?>"><?php echo $hasl['nama']; ?></option>
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
						<button type="submit" name="submit" value="submit" class="button">Tambah Biro Umrah</button>
					</div>
				</form>
					
			
		</div>
	
	</div>
	</div>


<?php
require_once('template-parts/footer.php');