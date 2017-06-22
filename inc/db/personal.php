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
	protected $pv_defaultOrderSql = 'ORDER BY profile_id, pesel';

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

		'row_state' => 'row_state',
		'csv_row' => 'csv_row',
		'csv_file' => 'csv_file',
		
		'profile_id' => 'profile_id',
		'draw_id' => 'draw_id',
		'user_code' => 'user_code',
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
	protected $pv_intColumnsByAlias = array('age', 'row_state', 'csv_file', 'profile_id', 'draw_id');

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

	/**
	 * Set/match profile to people.
	 * @param array $profile
	 * @return int
	 */
	public function setProfile($profile)
	{
		$pv_record = array(
			'profile_id' => $profile['id']
		);

		//
		// Pre-preparation
		//
		$this->pf_setRecordExtraParse($pv_record);

		//
		// Prepare
		//
		$pv_set_sql = $this->pf_getSetSQL($pv_record);

		//
		// Execute
		//
		$sql = "UPDATE {$this->pv_tableName}
			$pv_set_sql
			WHERE
				profile_id IS NULL
				AND
				row_state = 0
				AND
				age >= {$profile['age_min']}
				AND
				".($profile['age_max']>0 ? "age <= {$profile['age_max']}": '(1)')."
				AND
				sex = '".substr($profile['sex'], 0, 1)."'
				AND
				region = '{$profile['region']}'
		"
		;
		$pv_affected_rows = $this->pf_runModificationSQL($sql);

		//
		// Return
		//
		if ($pv_affected_rows===false)
		{
			$this->msg = 'DB error while updating record(s)!';
			return 0;
		}

		return 1;	// OlKul :)
	}

	/**
	 * High speed row insertion.
	 *
	 * @note This is might be very MySQL specifc. Not recomended for normal usage.
	 * @note We assume `pf_insRecordExtraParse`.
	 *
	 * @param array $pv_records Records to be inserted.
	 * @return int 0 upon error
	 * @throws Exception If \a pv_tableName was not set.
	 */
	public function pf_insRecords(&$pv_records, $fileId)
	{
		if (empty($this->pv_tableName))
		{
			throw new Exception("Tabel name is empty");
		}

		// static values
		$pv_staticRecord = array();
		$pv_staticRecord['csv_file'] = $fileId;
		$this->pf_insRecordExtraParse($pv_staticRecord);	// Note! Assuming it only adds values.
		$pv_staticValues = "";
		foreach ($pv_staticRecord as $pv_val)
		{
			$pv_staticValues .= "'".mysql_real_escape_string($pv_val)."',";
		}
		$pv_staticValues = rtrim($pv_staticValues, ",");

		// Prepare "header" of the INSERT statement
		$pv_headerRecord = array_merge($pv_records[0], $pv_staticRecord);
		$pv_ins_sql_arr = $this->pf_getInsSQLArrays($pv_headerRecord);
		$sql = "INSERT INTO {$this->pv_tableName} {$pv_ins_sql_arr['']['keys']}
			VALUES"
		;

		// Build SQL
		foreach($pv_records as &$pv_record) {
			$values = "";
			foreach ($pv_record as $pv_val)
			{
				$values .= "'".mysql_real_escape_string($pv_val)."',";
			}
			$sql .= "\n($values $pv_staticValues),";
		}
		$sql = rtrim($sql, ",");

		// Run
		$pv_result = mysql_query($sql);
		if ($pv_result==false)
		{
			$dumpPath = "./temp/last.personal.sql";
			file_put_contents($dumpPath, $sql);
			$this->msg = mysql_error();
			trigger_error("\nSQL error: {$this->msg}\nSQL:{$dumpPath}\n", E_USER_ERROR);
			return 0;
		}
		return 1;	// OK :)
	}
}
