<?php
$currentRoot = dirname(__FILE__);
require_once $currentRoot.'/dbbase.class.php';

/**
 * Presonal data class
 *
 * @note Connection need to be established before methods of this class are used.
 */
class dbPersonal extends dbBaseClass
{
	/**
	 * @see dbBaseClass
	 * @var string
	 */
	protected $pv_tableName = 'personal';

	/**
	 * @see dbBaseClass
	 * @var string
	 */
	protected $pv_defaultOrderSql = 'ORDER BY pesel';

	/**
	 * @see dbBaseClass
	 * @var array
	 */
	protected $pv_aliasNames2colNames = array (
		'id' => 'id',
		
		'dt_create' => 'dt_create',
		'dt_change' => 'dt_change',

		'sex' => 'sex',
		'age' => 'age',
		'region' => 'region',

		'pesel' => 'pesel',
		
		'name' => 'name',
		'surname' => 'surname',
		'city' => 'city',
		'street' => 'street',
		'building_no' => 'building_no',
		'flat_no' => 'flat_no',
		'zip_code' => 'zip_code',

		'csv_row' => 'csv_row',
		'csv_file' => 'csv_file',
	);

	/**
	 * Stats "templates" and aggregation queries.
	 *
	 * @see dbBaseClass->pf_getStats
	 * @var array
	 */
	protected $pv_sqlStatsTpls = array (
		// liczba rekordów
		'total' =>
			'SELECT count(*) as people
			FROM personal
			WHERE {pv_constraints|(1)}',
		// liczba zaproszeń per region (dzielnica)
		'region-counts' =>
			'SELECT region, count(*) as people
			FROM personal
			WHERE {pv_constraints|(1)}
			GROUP BY region
			ORDER BY 1 DESC',
	);

	/**
	 * Aliased names of columns that are to be parsed as integers.
	 *
	 * @note All other columns are parsed as string/binary so this is purely optional.
	 *
	 * @var array
	 */
	protected $pv_intColumnsByAlias = array('age');

	/**
	 * Alised names of columns that are to be excluded when inserting records.
	 *
	 * @note For tables that have automatically incremented ids you should add the name of this id column here.
	 *
	 * @see pf_insRecord()
	 * @var array
	 */
	protected $pv_insertExcludedCols = array('id');

	/**
	 * Extra operations on a record to be run in `pf_insRecord`.
	 *
	 * @param array $pv_record The record.
	 */
	protected function pf_insRecordExtraParse(&$pv_record)
	{
		$now = date('Y-m-d H:i:s');
		$pv_record['dt_create'] = $now;
		$pv_record['dt_change'] = $now;
	}
	/**
	 * Extra operations on a record to be run in `pf_setRecords`.
	 *
	 * @param array $pv_record The record.
	 */
	protected function pf_setRecordExtraParse(&$pv_record)
	{
		$now = date('Y-m-d H:i:s');
		$pv_record['dt_change'] = $now;
	}
}

?>