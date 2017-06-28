<?php

require_once 'Parsedown.php';

$parsedown = new Parsedown();

$files = glob("../*.md");
foreach($files as $file) {
	$html = $parsedown->text(file_get_contents($file));

	// md links
	$html = preg_replace('#<a href="([^:/>."]+).md">#', '<a class="md-link" href="?mod=_main&amp;a=$1">', $html);
	// source files links
	$html = preg_replace('#<a href="(?!\w*:)#', 
			'<a target="_blank" href="https://github.com/Eccenux/POLos/blob/master/', $html);
	// other (probably external) links
	$html = preg_replace('#<a href=#', '<a target="_blank" href=', $html);

	// save
	$fileName = basename($file, ".md");
	file_put_contents("../modules/_main/$fileName.html", $html);
}