<!DOCTYPE html>
<html>
<head>
    <title>Sys test</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" media="screen" href="./css/main.css" />
</head>
<body lang="en">
<div id='content'>
<table border="1">
<tr><th>ini key</th><th>ini value</th><th>expected</th></tr>
<?php
$phpIniChecks = array(
	'short_open_tag'      => 'On',
	'display_errors'      => 'Off',
	'upload_max_filesize' => '>=30M',
	'memory_limit'        => '>=256M',
	'memory_limit,post_max_size,upload_max_filesize' => 'x>y>z',
);

foreach ($phpIniChecks as $iniKey => $expected) {
	if (strpos($iniKey, ',')!==false) {
		$keys = explode(',', $iniKey);
		$inis = array();
		foreach($keys as $key) {
			$inis[] = ini_get($key);
		}
		$ini = implode(", ", $inis);
	} else {
		$ini = ini_get($iniKey);
	}
	
	echo "\n<tr><td>$iniKey</td><td>$ini</td><td>$expected</td></tr>";
}
?>
</table>
</div>
</body>
</html>
