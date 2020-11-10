<?php require_once('includes/init.php'); ?>

<?php
$errors = array();
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['username']) ? trim($_POST['password']) : '';

if(isset($_POST['submit'])):
	
	if(!$username) {
		$errors[] = 'Username tidak boleh kosong';
	}
	if(!$password) {
		$errors[] = 'Password tidak boleh kosong';
	}
	
	if(empty($errors)):
		
		$query = $pdo->prepare('SELECT * FROM user WHERE username = :username');
		$query->execute( array(
			'username' => $username
		) );
		$query->setFetchMode(PDO::FETCH_ASSOC);
		$user = $query->fetch();
		
		if($user) {
			$hashed_password = $password;
			if($user['password'] === $hashed_password) {
				$_SESSION["user_id"] = $user["id_user"];
				$_SESSION["username"] = $user["username"];
				$_SESSION["role"] = $user["role"];
				$user_role = get_role();
				if($user_role == 'admin')
					redirect_to("list-kriteria.php");
				if($user_role == 'pengunjung')
					redirect_to("ranking.php");
			} else {
				$errors[] = 'Maaf, anda salah memasukkan username / password';
			}
		} else {
			$errors[] = 'Maaf, anda salah memasukkan username / password';
		}
		
	endif;

endif;	
?>

<?php
$judul_page = 'Log in';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">	
	<br>
	<br>
	<center>
	<h1><b><font size="4">SISTEM PENDUKUNG KEPUTUSAN PEMILIHAN BIRO PERJALANAN UMROH</font></h1>
	<h1><b><font size="4">MENGGUNAKAN METODE TOPSIS DAN SAW<font></h1>
	</center>
		<div class="main-content the-content">
			<h1>Log in</h1>
			
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
			
			<form action="login.php" method="post">
				<div class="field-wrap clearfix">					
					<label>Username:</label>
					<input type="text" name="username" value="<?php echo htmlentities($username); ?>">
				</div>
				<div class="field-wrap clearfix">					
					<label>Password:</label>
					<input type="password" name="password">
				</div>
				<div class="field-wrap clearfix">
					<button type="submit" name="submit" value="submit" class="button">Log in</button>
				</div>
			</form>
		</div>
	
	</div>
	</div>

<?php
require_once('template-parts/footer.php');