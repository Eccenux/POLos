<?php
	date_default_timezone_set('Europe/Paris');
	function formatBytes($size, $precision = 2)
	{
		$size = intval($size);
		$base = log($size, 1024);
		$suffixes = array('', 'K', 'M', 'G', 'T');

		return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
	}
?>
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

<section>
<h2>PHP</h2>
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
</section>

<section>
	<h2>MySQL</h2>
	<table border="1">
	<tr><th>variable name</th><th>ini value</th><th>expected</th></tr>
	<?php
		require_once ('./inc/dbConnect.php');
		$pv_result = mysql_query("show variables like 'max_allowed_packet'");
		$sqlIniChecks = array(
			'max_allowed_packet' => '>=50 MB'
		);
		while ($row = mysql_fetch_array($pv_result, MYSQL_ASSOC))
		{
			$expected = $sqlIniChecks[$row['Variable_name']];
			echo "\n<tr><td>{$row['Variable_name']}</td><td>".formatBytes($row['Value'])."B</td><td>$expected</td></tr>";
		}
	?>
	</table>
</section>
</div>
</body>
</html>
