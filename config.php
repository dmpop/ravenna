<?php
$theme = "dark";
$title = "Ravenna";
$password = "secret";
$intro = "This is <a href='https://github.com/dmpop/ravenna'>Ravenna</a>";
$key = "";
$feed_cache = __DIR__ . '/feed_cache.html';
$feed_cache_expire = 900; // 15 minutes

$links = array(
	array('https://dmpop.gumroad.com/l/digikamrecipes', 'https://tokyoma.de/bookcovers/digikam-recipes.jpg', 'digiKam Recipes'),
	array('https://dmpop.gumroad.com/l/linux-photography', 'https://tokyoma.de/bookcovers/linux-photography.jpg', 'Linux Photography'),
	array('https://dmpop.gumroad.com/l/from-draft-to-epub', 'https://tokyoma.de/bookcovers/from-draft-to-epub.jpg', 'From Draft to EPUB'),
);

$feeds = array(
	"https://tokyoma.de/rss.xml",
	"http://feeds.kottke.org/main",
);

$footer = "I really 🧡 <a href='https://www.paypal.com/paypalme/dmpop'>coffee</a>";
