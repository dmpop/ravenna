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

$footer = "Read the <a href='https://dmpop.gumroad.com/l/php-right-away'>PHP Right Away</a> book";
