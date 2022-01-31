<?php
$theme = "dark";
$title = "Ravenna";
$password = "monkey";
$intro = "This is <a href='https://github.com/dmpop/ravenna'>Ravenna</a>";
$key = "";
$feed_cache = __DIR__ . '/feed_cache.html';
$feed_cache_expire = 900; // 15 minutes

$links = array(
	array('https://tokyoma.de/', 'https://tokyoma.de/favicon.svg', 'Tokyo Made'),
	array('https://tinyvps.xyz/tim', 'https://tokyoma.de/bookcovers/digikam-recipes.jpg', 'digiKam Recipes'),
	array('https://tinyvps.xyz/tim', 'https://tokyoma.de/bookcovers/linux-photography.jpg', 'Linux Photography'),
);

$feeds = array(
	"https://tokyoma.de/rss.xml",
	"http://feeds.kottke.org/main",
);

$footer = "I really 🧡 <a href='https://www.paypal.com/paypalme/dmpop'>coffee</a>";
