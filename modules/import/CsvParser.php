<?php

/**
 * Parsing state enum.
 * @note all values should be 2^n.
 */
abstract class CsvParserState
{
	const UNINTIALIZED = -1;	// Not parsed yet
	const OK = 0;				// All rows are OK
	const GENERAL_ERROR = 1;	// Failed to load file or all rows are invalid
	const ROW_ERRORS = 2;		// Some row(s) are invalid
	const ROW_WARNINGS = 4;		// Some row(s) are potentially invalid (e.g. too long values)
}

/**
 * States for rows (and columns).
 * @note all values should be 2^n.
 */
abstract class CsvRowState
{
	const OK = 0;				// All rows are OK
	const INVALID = 1;			// failed to load file or all rows are invalid
	const WARNING = 2;			// failed to load file or all rows are invalid
}

/**
 * Helper class for parsing CSV.
 *
 * @author Maciej Nux Jaros
 */
class CsvParser
{
	/**
	 * Must be larger then longest line (also including new line character)
	 * @var int
	 */
	public $maxPerLine = 2000;
	/**
	 * Name of column to be ignored.
	 * @var string
	 */
	public $ignoreName = "-";
	/**
	 * Array of functions to parse columns.
	 *
	 * See \a parseColumn() for a basic function.
	 * 
	 * @var array
	 */
	public $columnParsers = array();
	/**
	 * Messages with user-readable errors.
	 * @var array
	 */
	public $messages = array();

	/**
	 * Parsed rows.
	 * @var array
	 */
	public $rows = array();
	/**
	 * File handle.
	 * @var resource
	 */
	private $file = null;
	/**
	 * Order array with ignored columns set to "-".
	 * @var array
	 */
	public $order = array();
	/**
	 * Parsing state.
	 * @var CsvParserState
	 */
	private $state = null;

	public function __construct($path, $order)
	{
		$this->order = $order;
		if (($this->file = fopen($path, "r")) === FALSE) {
			$this->messages[] = "Nie udało się otworzyć pliku. Błąd dostępu lub plik nie istnieje.";
			$this->state = CsvParserState::GENERAL_ERROR;
		}
	}

	/**
	 * Parse file.
	 * 
	 * If an error occurs see \a messages for details.
	 *
	 * @return CsvParserState.
	 */
	public function parse()
	{
		if ($this->state === CsvParserState::GENERAL_ERROR) {
			return $this->state;
		}
		$handle = $this->file;
		$totalRows = 0;
		$validRows = 0;
		$state = CsvParserState::OK;
		while (($data = fgetcsv($handle, $this->maxPerLine)) !== FALSE) {
			$row = $this->parseRow($data);
			$this->rows[$row['state']][] = $row['columns'];
			$totalRows++;
			switch ($row['state'])
			{
				case CsvRowState::INVALID:
					$state |= CsvParserState::ROW_ERRORS;
				break;
				case CsvRowState::WARNING:
					$state |= CsvParserState::ROW_WARNINGS;
				break;
				default:
					$validRows++;
				break;
			}
		}
		if (empty($validRows)) {
			$this->messages[] = "Cały plik jest niepoprawny! Żaden odczytany wiersz nie był poprawny.";
			$state = CsvParserState::GENERAL_ERROR;
		}
		fclose($handle);
		$this->state = $state;
		return $this->state;
	}

	/**
	 * Parse a row of CSV data.
	 * @param array $data
	 * @return array
	 */
	public function parseRow($data)
	{
		$count = count($data);
		if ($count > count($this->order)) {
			$count = count($this->order);
		}

		$row_state = CsvRowState::OK;
		$row_columns = array();
		for ($c=0; $c < $count; $c++) {
			$name = $this->order[$c];
			if ($name === $this->ignoreName) {
				continue;
			}
			$value = $data[$c];
			if (isset($this->columnParsers[$name]) && is_callable($this->columnParsers[$name])) {
				$parsed = $this->columnParsers[$name]($name, $value);
			} else {
				$parsed = $this->parseColumn($name, $value);
			}
			$row_columns = array_merge($row_columns, $parsed['columns']);
			$row_state |= $parsed['state'];
        }
		
		return array(
			'state' => $row_state,
			'columns' => $row_columns
		);
	}

	/**
	 * Default column parser.
	 *
	 * @param String $name Column name (like in the $this->order array).
	 * @param String $value Value read from CSV.
	 * @return array
	 */
	public function parseColumn($name, $value)
	{
		return array(
			'state' => CsvRowState::OK,
			'columns' => array(
				$name => $value,
			)
		);
	}
	/**
	 * Required (non-empty string) column parser.
	 *
	 * @param String $name
	 * @param String $value
	 * @return array
	 */
	public static function parseColumnRequired($name, $value)
	{
		return array(
			'state' => !empty($value) ? CsvRowState::OK : CsvRowState::INVALID,
			'columns' => array(
				$name => $value,
			)
		);
	}
	/**
	 * Integer column parser.
	 *
	 * @param String $name
	 * @param String $value
	 * @return array
	 */
	public static function parseColumnInteger($name, $value)
	{
		return array(
			'state' => is_numeric($value) ? CsvRowState::OK : CsvRowState::INVALID,
			'columns' => array(
				$name => intval($value, 10),
			)
		);
	}
	/**
	 * Range column parser.
	 *
	 * Accepted formats:
	 * `123-345`
	 * `123,345`
	 * `123, 345`
	 * `345+` (means 345 or above)
	 *
	 * @param String $prefix Column prefix.
	 * @param String $value
	 * @return array
	 */
	public static function parseColumnRange($prefix, $value)
	{
		$matches = array();
		preg_match(
			"#(?P<min>\\d+)(?:"
			."[^\\d]*\\+"	// no top boundary
			."|"
			."[^\\d]+(?P<max>\\d+)"
			.")#"
			, $value, $matches
		);
		$ret = array(
			'state' => CsvRowState::INVALID,
			'columns' => array(),
		);
		if (!empty($matches) && isset($matches['min'])) {
			$ret['state'] = CsvRowState::OK;
			$ret['columns'][$prefix.'min'] = intval($matches['min'], 10);
			if (isset($matches['max'])) {
				$ret['columns'][$prefix.'max'] = intval($matches['max'], 10);
			}
		}
		return $ret;
	}
}