<?php
error_reporting(E_ERROR);
include('config.php');
?>

<html lang="en" data-theme="<?php echo $theme ?>">
<!-- Author: Dmitri Popov, dmpop@linux.com
					License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt -->

<head>
	<meta charset="utf-8">
	<title><?php echo $title ?></title>
	<meta charset="utf-8">
	<link rel="shortcut icon" href="favicon.png" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/classless.css" />
	<link rel="stylesheet" href="css/themes.css" />
	<style>
		textarea {
			font-size: 15px;
			width: 100%;
			height: 55%;
			line-height: 1.9;
			margin-top: 2em;
		}
	</style>
</head>

<body>
	<div class="card text-center">
	<img style="height: 3em; margin-bottom: 1em;" src="favicon.svg" alt="logo" />
		<h1 style="margin-top: 0em; margin-bottom: .5em; letter-spacing: 3px; color: #cc6600;"><?php echo $title; ?></h1>
		<button onclick="location.href='index.php'">Back</button>
		<?php
		function Read()
		{
			$f = "note.md";
			echo file_get_contents($f);
		}
		function Write()
		{
			$f = "note.md";
			$fp = fopen($f, "w");
			$data = $_POST["text"];
			fwrite($fp, $data);
			fclose($fp);
		}
		if (isset($_POST["save"])) {
			if ($_POST['pwd'] != $password) {
				print '<p>Wrong password</p>';
				exit();
			}
			Write();
		};
		?>
		<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
			<textarea name="text"><?php Read(); ?></textarea><br /><br />
			<input type="password" name="pwd">
			<button type="submit" name="save">Save</button>
		</form>
		<hr />
		<p><?php echo $footer; ?></p>
	</div>
</body>

</html>