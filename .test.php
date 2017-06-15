<?php
/**
 *	@file Temporarary test debugging helper.
 */
define('NOT_HACKING_RILI', true);
date_default_timezone_set('Europe/Paris');

require_once './modules/import/CsvParser.php';

// basic profile parsing
/**
$dir = 'D:\_Varia\OkoloWWW\_moje - varia\PHP\POLos\.tests\modules\import';
$profileCsv = $dir.'\profile.csv';

$profileOrder = explode(',',  "-,group_name,sex,age_min_max,region,-,invites_no");
$parser = new CsvParser($profileCsv, $profileOrder);
$parser->parse();
var_export($parser->rows);
$firstRow = $parser->rows[CsvRowState::OK][0];
$parsedColumnsCount = count($firstRow);
$expectedColumnsCount = count($profileOrder) - 2;
var_dump($parsedColumnsCount, $expectedColumnsCount);
/**/

// range parsing
/**
$values = array(
	'123-345' => array('min'=>'123', 'max'=>'345'),
	'123,345' => array('min'=>'123', 'max'=>'345'),
	'123, 345' => array('min'=>'123', 'max'=>'345'),
	'345+' => array('min'=>'345'),
);
foreach ($values as $value => $expected)
{
	$matches = array();
	preg_match("#(?P<min>\\d+)(?:[^\\d]*\\+|[^\\d]+(?P<max>\\d+))#", $value, $matches);
	//preg_match("#(?P<min>\\d+)[^\\d]+(?P<max>\\d+)#", $value, $matches);
	unset($matches[0], $matches[1], $matches[2]);
	echo "\n";
	var_export($matches);
}
/**/
