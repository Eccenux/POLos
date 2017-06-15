<?php

// <editor-fold defaultstate="collapsed" desc="generic test preparation">
date_default_timezone_set('Europe/Paris');
// include tested class
$testedClass = preg_replace(
	array(
		'#\\.tests\\\\#',
		'#Test(?=.php$)#'
	)
	, ""
	, __FILE__
);
include $testedClass;
// </editor-fold>

/**
 * Generated by PHPUnit_SkeletonGenerator on 2017-06-15 at 00:31:09.
 */
class CsvParserTest extends PHPUnit_Framework_TestCase
{
	private $profileCsv;
	private $profileOrder;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		echo "\nTest setUp"
			."\n-------------\n"
		;
		$this->profileCsv = __DIR__.'\profile.csv';
		$this->profileOrder = explode(',',  "-,group_name,sex,age_min_max,region,-,invites_no");
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	/**
	 * Check columns ignore.
	 * @covers CsvParser::parse
	 */
	public function testParse_profile_ignores()
	{
		$expectedColumnsCount = count($this->profileOrder) - 2;
		$parser = new CsvParser($this->profileCsv, $this->profileOrder);
		$parser->parse();
		var_export($parser->rows);
		$firstRow = $parser->rows[CsvRowState::OK][0];
		$this->assertEquals($expectedColumnsCount, count($firstRow));
	}
	
	/**
	 * Basic parseRow test.
	 * @covers CsvParser::parseRow
	 */
	public function testParseRow()
	{
		$parser = new CsvParser(__FILE__, array('name', 'age'));
		$data = array('test', '1');
		$row = $parser->parseRow($data);
		var_export($row);
		$this->assertEquals($row['columns']['name'], 'test');
		$this->assertEquals($row['columns']['age'], '1');
		$this->assertEquals($row['state'], CsvRowState::OK);
	}
	/**
	 * Custom column functions test.
	 * @covers CsvParser::parseRow
	 */
	public function testParseRow_CustomFunction()
	{
		$parser = new CsvParser(__FILE__, array('name', 'age'));
		$parser->columnParsers['age'] = function($name, $value){
			return CsvParser::parseColumnInteger($name, $value);
		};
		// OK
		$data = array('test', '1');
		$row = $parser->parseRow($data);
		var_export($row);
		$this->assertEquals($row['columns']['name'], 'test');
		$this->assertSame($row['columns']['age'], 1);
		$this->assertEquals($row['state'], CsvRowState::OK);

		// invalid
		$data = array('test', 'abc');
		$row = $parser->parseRow($data);
		var_export($row);
		$this->assertEquals($row['columns']['name'], 'test');
		$this->assertEquals($row['state'], CsvRowState::INVALID);
	}

	/**
	 * State combination test.
	 * @covers CsvParser::parseRow
	 */
	public function testParseRow_StateCombination()
	{
		$columnParsers['ok'] = function($name, $value){
			return array(
				'state' => CsvRowState::OK,
				'columns' => array(
					$name => $value,
				)
			);
		};
		$columnParsers['invalid'] = function($name, $value){
			return array(
				'state' => CsvRowState::INVALID,
				'columns' => array(
					$name => $value,
				)
			);
		};
		$columnParsers['warning'] = function($name, $value){
			return array(
				'state' => CsvRowState::WARNING,
				'columns' => array(
					$name => $value,
				)
			);
		};

		// all states
		$parser = new CsvParser(__FILE__, array('ok', 'invalid', 'warning'));
		$parser->columnParsers = $columnParsers;
		$data = array(1,2,3);
		$row = $parser->parseRow($data);
		echo "\n"; var_export($row);
		$this->assertSame($row['state'], CsvRowState::OK | CsvRowState::INVALID | CsvRowState::WARNING);

		// non-ok
		$parser = new CsvParser(__FILE__, array('invalid', 'warning'));
		$parser->columnParsers = $columnParsers;
		$data = array(1,2,3);
		$row = $parser->parseRow($data);
		echo "\n"; var_export($row);
		$this->assertSame($row['state'], CsvRowState::INVALID | CsvRowState::WARNING);
		$this->assertNotEquals($row['state'], CsvRowState::OK);
		$this->assertNotEquals($row['state'], CsvRowState::INVALID);
		$this->assertNotEquals($row['state'], CsvRowState::WARNING);
	}
}
