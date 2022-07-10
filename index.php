<?php
include('config.php');
include 'inc/parsedown.php';
if (!file_exists($photo_dir)) {
	mkdir($dir, 0755, true);
}
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
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<div>
	<div style="text-align: center;">
		<img style="display: inline; height: 2.5em; vertical-align: middle;" src="favicon.svg" alt="logo" />
		<h1 style="display: inline; margin-left: 0.19em; vertical-align: middle; margin-top: 0em; letter-spacing: 3px; color: #cc6600;"><?php echo $title; ?></h1>
		<?php
		$uptime = shell_exec('uptime -p');
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
			<h4 style="margin-top: 0.5em; letter-spacing: 3px;">
				<?php
				$ip = shell_exec("hostname -i");
				if (isset($ip)) {
					echo $ip . " ";
				} ?>
			</h4>
			<hr>
			<div class="row">
				<div class="col-4">
					<?php
					if (!empty($temp)) {
						echo '<h4 style="margin-top: 1em; letter-spacing: 3px;">TEMPERATURE</h4>';
						echo "<progress max='100' value='" . $temp . "'></progress>" . $temp . "°C";
					} else {
						echo '<h4 style="margin-top: 1em; letter-spacing: 3px;">UPTIME</h4>';
						echo "<hr style='margin-top:1em;'>";
						echo trim($uptime, "up ");
					}
					?>
				</div>
				<div class="col-4">
					<h4 style="margin-top: 1em; letter-spacing: 3px;">RAM</h4>
					<progress max="100" value="<?php echo $mem; ?>"></progress> <?php echo $mem; ?>% used
				</div>
				<div class="col-4">
					<h4 style="margin-top: 1em; letter-spacing: 3px;">STORAGE</h4>
					<progress max="100" value="<?php echo $storage_used_p ?>"></progress> <?php echo $storage_used_p ?>% used
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
				<label for="potd">🖼️ Random photo</label>
				<div class="tab">
					<div style="margin-top: .5em;"></div>
					<img style='border-radius: 5px;' src=<?php $photo_dir;
								$photos = glob($photo_dir . DIRECTORY_SEPARATOR . '*');
								echo $photos[array_rand($photos)]; ?> />
				</div>
				<?php
				setcookie("posLat", "", time() - 3600);
				setcookie("posLon", "", time() - 3600);
				$lat = $_COOKIE['posLat'];
				$lon = $_COOKIE['posLon'];
				if (empty($lat) && empty($lon)) {
					$lat = $default_lat;
					$lon = $default_lon;
				}
				echo '<input type="radio" name="tabs" id="sun">';
				echo '<label for="sun">☀️ Sun</label>';
				echo '<div class="tab">';
				echo "<h3><a href='http://www.openstreetmap.org/index.html?mlat=" . $lat . "&mlon=" . $lon . "' target='_blank'>Location</a></h3>";
				echo "<table style='margin-top: 1.5em;'>";
				for ($i = 0; $i <= 6; $i++) {
					$sun_info = date_sun_info(strtotime("today + $i day"), $lat, $lon);
					echo "<tr>";
					echo "<td>";
					echo ' <span style="color: gray;">' . date("D", strtotime("+ $i day")) . ':</span> ';
					echo "</td>";
					echo "<td style='text-align: left'>";
					echo "🌅 <span style='color: #ff9800; letter-spacing: 3px;'>" . date("H:i", $sun_info["sunrise"]) . "</span>";
					echo "</td>";
					echo "<td style='text-align: left'>";
					echo "🌇 <span style='color: #0099ff; letter-spacing: 3px;'>" . date("H:i", $sun_info["sunset"]) . "</span>";
					echo "</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo '</div>';
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