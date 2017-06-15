<?php
/**
 *	@file Temporarary test debugging helper.
 */
define('NOT_HACKING_RILI', true);
date_default_timezone_set('Europe/Paris');

require_once './modules/import/CsvParser.php';

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