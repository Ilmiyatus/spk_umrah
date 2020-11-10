<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1, 2)); ?>

<?php
$ada_error = false;
$result = '';

$id_biro_umroh = (isset($_GET['id'])) ? trim($_GET['id']) : '';

if(!$id_biro_umroh) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = $pdo->prepare('SELECT id_biro_umroh FROM biro_umroh WHERE id_biro_umroh = :id_biro_umroh');
	$query->execute(array('id_biro_umroh' => $id_biro_umroh));
	$result = $query->fetch();
	
	if(empty($result)) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	} else {
		
		$handle = $pdo->prepare('DELETE FROM nilai_biro_umroh WHERE id_biro_umroh = :id_biro_umroh');				
		$handle->execute(array(
			'id_biro_umroh' => $result['id_biro_umroh']
		));
		$handle = $pdo->prepare('DELETE FROM biro_umroh WHERE id_biro_umroh = :id_biro_umroh');				
		$handle->execute(array(
			'id_biro_umroh' => $result['id_biro_umroh']
		));
		redirect_to('list-biro-umrah.php?status=sukses-hapus');
		
	}
}
?>

<?php
$judul_page = 'Hapus Biro Umrah';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-biro-umrah.php'); ?>
	
		<div class="main-content the-content">
			<h1><?php echo $judul_page; ?></h1>
			
			<?php if($ada_error): ?>
			
				<?php echo '<p>'.$ada_error.'</p>'; ?>	
			
			<?php endif; ?>
			
		</div>
	
	</div>
	</div>


<?php
require_once('template-parts/footer.php');