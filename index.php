<?php
include('config.php');
include 'inc/parsedown.php';
?>

<html lang="en" data-theme="<?php echo $theme; ?>">
<!-- Author: Dmitri Popov, dmpop@linux.com
         License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt -->

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<title><?php echo $title; ?></title>
	<link rel="shortcut icon" href="favicon.png" />
	<link rel="stylesheet" href="css/classless.css">
	<link rel="stylesheet" href="css/tabbox.css">
	<link rel="stylesheet" href="css/styles.scss">
	<link rel="stylesheet" href="css/themes.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<div>
	<div style="text-align: center;">
		<img style="height: 3em; margin-bottom: 1em;" src="favicon.svg" alt="logo" />
		<h1 style="margin-top: 0em; letter-spacing: 3px; color: #cc6600;"><?php echo $title; ?></h1>
		<p>
			<?php echo $intro; ?>
		</p>
		<?php
		$uname = shell_exec("uname -mnr");
		$cpuusage = 100 - shell_exec("vmstat | tail -1 | awk '{print $15}'");
		$mem = shell_exec("free | grep Mem | awk '{print $3/$2 * 100.0}'");
		$mem = round($mem, 1);
		if (isset($uname)) {
			echo $uname . " ";
		}
		if (isset($cpuusage) && is_numeric($cpuusage)) {
			echo '<span style="color: gray;">CPU load:</span> <strong>' . $cpuusage . '%</strong> ';
		}
		if (isset($mem) && is_numeric($mem)) {
			echo '<span style="color: gray;">Memory:</span> <strong>' . $mem . '%</strong>';
		}
		?>
	</div>
	<p id="geolocation"></p>
	<script>
		window.onload = getLocation();
		var x = document.getElementById("geolocation");

		function getLocation() {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(currentPosition);
			} else {
				x.innerHTML = "Geolocation is not supported.";
			}
		}

		function currentPosition(position) {
			document.cookie = "posLat = ; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
			document.cookie = "posLon = ; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
			document.cookie = "posLat = " + position.coords.latitude;
			document.cookie = "posLon = " + position.coords.longitude;
		}
	</script>

	<div class="tabs">
		<input type="radio" name="tabs" id="potd" checked="checked">
		<label for="potd">🖼️ Photo of the day</label>
		<div class="tab">
			<div style="margin-top: .5em;"></div>
			<?php
			$request = "http://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1";
			$response = file_get_contents($request);
			$data = json_decode($response, true);
			echo "<img style='border-radius: 5px;' src='https://bing.com" . $data['images'][0]['url'] . "' />";
			?>
		</div>

		<?php
		setcookie("posLat", "", time() - 3600);
		setcookie("posLon", "", time() - 3600);
		$lat = $_COOKIE['posLat'];
		$lon = $_COOKIE['posLon'];
		if (!empty($lat) && !empty($lon) && !empty($key)) {
			echo '<input type="radio" name="tabs" id="weather">';
			echo '<label for="weather">🌤️ Weather</label>';
			echo '<div class="tab">';
			$request = "https://api.openweathermap.org/data/2.5/forecast/daily?lat=$lat&lon=$lon&units=metric&cnt=7&lang=en&units=metric&cnt=7&appid=$key";
			$response = file_get_contents($request);
			$data = json_decode($response, true);
			echo "<h3>" . $data['city']['name'] . "</h3>";
			echo "<hr>";
			echo "<table style='margin-top: 1.5em;'>";
			for ($i = 0; $i <= 6; $i++) {
				echo "<tr>";
				echo "<td>";
				echo ' <span style="color: gray;">' . date("D", strtotime("+ $i day")) . ':</span> ';
				echo "</td>";
				echo "<td style='text-align: left'>";
				echo "<span style='color: #03a9f4;'>" . round($data['list'][$i]['temp']['day'], 0) . "°C</span> ";
				echo $data['list'][$i]['weather'][0]['description'] . " ";
				echo "<span style='color: #26a69a;'>" . $data['list'][$i]['speed'] . " m/s</span> ";
				echo "<span style='color: #ff9800;'>&#8593;" . date("H:i", $data['list'][$i]['sunrise']) . " ";
				echo "&#8595;" . date("H:i", $data['list'][$i]['sunset']) . "</span>";
				echo "</td>";
				echo "</tr>";
			}
			echo "</tr>";
			echo "</table>";
			echo '</div>';
		}
		?>
		<input type="radio" name="tabs" id="links">
		<label for="links">🔗 Links</label>
		<div class="tab">
			<div class="grid" style="margin-top: 1em; margin-bottom: 1em;">
				<?php
				foreach ($links as $link) {
					echo '<div><figure class="text-center">';
					echo '<a href="' . $link[0] . '"><img src="' . $link[1] . '" alt="' . $link[2] . '" height=64></a>';
					echo '<figcaption>'  . $link[2] . '</figcaption>';
					echo '</figure></div>';
				}
				?>
			</div>
		</div>
		<input type="radio" name="tabs" id="rss">
		<label for="rss">🔥 RSS feeds</label>
		<div class="tab">
			<div style="margin-bottom: 1.5em;"></div>
			<?php
			$array_length = count($feeds);
			for ($i = 0; $i < $array_length; $i++) {
				echo "<details>";
				$rss = simplexml_load_file($feeds[$i]);
				echo '<summary>' . $rss->channel->title . '</summary>';
				echo "<ul>";
				foreach ($rss->channel->item as $item) {
					echo '<li style="font-size: 85%"><a href="' . $item->link . '">' . $item->title . "</a></li>";
				}
				echo "</ul>";
				echo "</details>";
			}
			?>
		</div>
		<?php
		if (!is_file('note.md')) {
			$text = "Notes go here. [Markdown](https://www.markdownguide.org/) **is** _supported_.";
			file_put_contents('note.md', $text);
		}
		echo '<input type="radio" name="tabs" id="notes">';
		echo '<label for="notes">🗒️ Notes</label>';
		echo '<div class="tab">';
		$note = file_get_contents('note.md');
		$Parsedown = new Parsedown();
		echo $Parsedown->text($note);
		echo "<div class='text-center'><button onclick=\"location.href='edit.php'\">Edit</button></div>";
		echo '</div>';
		?>
		<!-- Custom tab template START
	<input type="radio" name="tabs" id="custom_tab">
	<label for="custom_tab">☕ Custom tab</label>
	<div class="tab">
		{CONTENT}
	</div>
Custom tab template END -->
	</div>
	<p class="text-center"><?php echo $footer ?></p>
	</body>

</html>