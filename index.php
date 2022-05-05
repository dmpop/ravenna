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
	<link rel="stylesheet" href="css/styles.css">
	<link rel="stylesheet" href="css/themes.css">
	<script src="js/justgage.js"></script>
	<script src="js/raphael.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<div>
	<div style="text-align: center;">
		<img style="display: inline; height: 2.5em; vertical-align: middle;" src="favicon.svg" alt="logo" />
		<h1 style="display: inline; margin-left: 0.19em; vertical-align: middle; margin-top: 0em; letter-spacing: 3px; color: #cc6600;"><?php echo $title; ?></h1>
		<?php
		$temp = shell_exec('cat /sys/class/thermal/thermal_zone*/temp');
		$temp = round($temp / 1000, 1);
		$mem = shell_exec("free | grep Mem | awk '{print $3/$2 * 100.0}'");
		$mem = round($mem, 1);
		$dir = '/';
		$storage_free = disk_free_space($dir);
		$storage_total = disk_total_space($dir);
		$storage_used = $storage_total - $storage_free;
		$storage_used_p = sprintf('%.2f', ($storage_used / $storage_total) * 100);
		?>
		<div class="card" style="margin-top: 2em;">
			<h4 style="margin-top: 0.5em;">
				<?php
				$uname = shell_exec("uname -mnr");
				if (isset($uname)) {
					echo $uname . " ";
				} ?>
			</h4>
			<hr>
			<div class="row">
				<div class="col-4">
					<?php
					if (isset($temp)) { ?>
						<div id="temp"></div>
						<script>
							var t = new JustGage({
								id: "temp",
								value: <?php echo $temp; ?>,
								min: 0,
								max: 100,
								title: "TEMP",
								label: "°C",
								valueFontColor: "#cc6600",
								valueFontFamily: "Barlow"
							});
						</script>
					<?php } ?>
				</div>
				<div class="col-4">
					<?php if (isset($mem) && is_numeric($mem)) { ?>
						<div id="memgauge"></div>
						<script>
							var u = new JustGage({
								id: "memgauge",
								value: <?php echo $mem; ?>,
								min: 0,
								max: 100,
								title: "RAM",
								label: "%",
								valueFontColor: "#cc6600",
								valueFontFamily: "Barlow"
							});
						</script>
					<?php } ?>
				</div>
				<div class="col-4">
					<?php if (isset($storage_used_p) && is_numeric($storage_used_p)) { ?>
						<div id="storage_used"></div>
						<script>
							var u = new JustGage({
								id: "storage_used",
								value: <?php echo $storage_used_p; ?>,
								min: 0,
								max: 100,
								title: "STORAGE",
								label: "%",
								valueFontColor: "#cc6600",
								valueFontFamily: "Barlow"
							});
						</script>

					<?php } ?>
				</div>
			</div>
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
				<div class="grid" style="margin-top: 2em;">
					<?php
					foreach ($links as $link) {
						echo '<div><figure class="text-center">';
						echo '<a href="' . $link[0] . '" target="_blank"><img src="' . $link[1] . '" alt="' . $link[2] . '" height=64></a>';
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
				$refresh = time() - $feed_cache_expire;
				$array_length = count($feeds);
				if (!file_exists($feed_cache) || filemtime($feed_cache) < $refresh) {
					for ($i = 0; $i < $array_length; $i++) {
						$content .= "<details>";
						$xml = simplexml_load_file(str_replace(PHP_EOL, "", $feeds[$i]));
						$root_element_name = $xml->getName();
						if ($root_element_name  == 'rss') {
							$content .= '<summary>' . htmlspecialchars($xml->channel->title) . '</summary>';
							$content .= "<ul>";
							foreach ($xml->channel->item as $item) {
								$content .= '<li style="font-size: 85%"><a href="' . htmlspecialchars($item->link) . '" target="_blank">' . htmlspecialchars($item->title) . "</a></li>";
							}
						} else if ($root_element_name  == 'feed') {
							$content .= '<summary>' . htmlspecialchars($xml->title) . '</summary>';
							$content .= "<ul>";
							foreach ($xml->entry as $entry) {
								$content .= '<li style="font-size: 85%"><a href="' . htmlspecialchars($entry->link['href']) . '" target="_blank">' . htmlspecialchars($entry->title) . "</a></li>";
							}
						}
						$content .= "</ul>";
						$content .= "</details>";
						file_put_contents($feed_cache, $content);
					}
					echo $content;
				} else {
					echo file_get_contents($feed_cache);
				}
				?>
			</div>
			<?php
			if (!is_file('note.md')) {
				$text = "Notes go here. [Markdown](https://www.markdownguide.org/) **is** _supported_.";
				file_put_contents('note.md', $text);
			}
			?>
			<input type="radio" name="tabs" id="notes">
			<label for="notes">🗒️ Notes</label>
			<div class="tab">
				<?php
				$note = file_get_contents('note.md');
				$Parsedown = new Parsedown();
				echo $Parsedown->text($note);
				?>
				<div class='text-center'>
					<button onclick="location.href='edit.php'">Edit</button>
				</div>
			</div>
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