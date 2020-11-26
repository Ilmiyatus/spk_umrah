<!DOCTYPE html>
<head>
	<meta http-equiv="x-ua-compatible" content="ie=edge" />
	<meta charset="UTF-8" />
	<title><?php
		if(isset($judul_page)) {
			echo $judul_page;
		}
	?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico" />
	<link rel="stylesheet" href="stylesheets/style.css">
	<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>	
	<script type="text/javascript" src="js/superfish.min.js"></script>	
	<script type="text/javascript" src="js/main.js"></script>	
</head>
<body>
	<div id="page">
	
	<header id="header">
		<div class="container clearfix">
			<div id="logo-wrap">
				<h1 id="logo"><img src="" alt=""></a></h1>
			</div>
			
			<div id="header-content" class="clearfix">
				<nav id="nav">
					<ul class="sf-menu">
						<?php $user_role = get_role(); ?>
						<?php if($user_role == 'admin'): ?>
							<li><a href="list-kriteria.php">Kriteria</a></li>
							<li><a href="list-biro-umrah.php">Biro Umrah</a></li>
							<li><a href="ranking-topsis.php">Rank TOPSIS</a>
							</li>
						<?php endif; ?>
						<?php if($user_role == 'pengunjung'): ?>
							<li><a href="ranking.php" style="color:transparent">Rangking</a>
						<?php endif; ?>
					</ul>
				</nav>
				
				<div id="header-right">
					<?php if(isset($_SESSION['user_id'])): ?>
						<a href="logout.php" class="button">Log Out</a>
					<?php else: ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</header>
	
	<div id="main">