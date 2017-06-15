<?php
/**
 * Import forms helper.
 */
class ImportHelper
{
	public $parser = null;

	/**
	 * Init.
	 * @param string $order Order snet by user.
	 * @param array $columnParsers Array of column parser functions.
	 * @param string $type Type of file ('profile' / 'personal').
	 */
	function __construct($order, $columnParsers, $type)
	{
		$this->order = $order;
		$this->columnParsers = $columnParsers;
		$this->type = $type;
	}

	/**
	 * Parse and save uploaded file.
	 *
	 * Note! The file will not be save to DB if parser was not able to parse the file.
	 * That is when its state is `CsvParserState::GENERAL_ERROR`.
	 * 
	 * @param array $file Uploaded file specs from `$_FILES`.
	 * @return \CsvParser
	 */
	function parse($file) {
		// parse file
		$csvPath = $file["tmp_name"];
		$csvOrder = explode(",", $this->order);
		$parser = new CsvParser($csvPath, $csvOrder);
		$parser->columnParsers = $this->columnParsers;
		$state = $parser->parse(true);
		//echo "<pre>".var_export($parser->rows, true)."</pre>";

		if ($state !== CsvParserState::GENERAL_ERROR) {
			// save file
			require_once ('./inc/db/file.php');
			$dbFile = new dbFile();
			$dbFile->pf_insRecord(array(
				'type' => $this->type,
				'column_map' => $this->order,
				'name' => $file['name'],
				'contents' => file_get_contents($csvPath),
			));
		}

		return $parser;
	}

}