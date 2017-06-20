<?php
$currentRoot = dirname(__FILE__);
require_once $currentRoot.'/dbbase.class.php';

/**
 * Profile data class
 *
 * @note Connection need to be established before methods of this class are used.
 */
class dbProfile extends dbBaseClass
{
	/**
	 * @see dbBaseClass
	 * @var string
	 */
	protected $pv_tableName = 'profile';

	/**
	 * @see dbBaseClass
	 * @var string
	 */
	protected $pv_defaultOrderSql = 'ORDER BY id';

	/**
	 * @see dbBaseClass
	 * @var array
	 */
	protected $pv_aliasNames2colNames = array (
		'id' => 'id',
		
		'dt_create' => 'dt_create',
		'dt_change' => 'dt_change',

		'group_name' => 'group_name',
		'invites_no' => 'invites_no',

		'sex' => 'sex',
		'age_min' => 'age_min',
		'age_max' => 'age_max',
		'region' => 'region',

		'row_state' => 'row_state',
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
		// liczba profili i zaproszeń
		'total' =>
			'SELECT count(*) as profiles, sum(invites_no) as invites
			FROM profile
			WHERE {pv_constraints|(1)}',
		// liczba zaproszeń per region (dzielnica)
		'region-invites' =>
			'SELECT region, sum(invites_no) as invites
			FROM profile
			WHERE {pv_constraints|(1)}
			GROUP BY region
			ORDER BY 1 DESC',
		// scalanie profili
		'profile-duplicates' => "
			SELECT
				sex, age_min, age_max, region, sum(invites_no) as invites_no,
				GROUP_CONCAT(DISTINCT id SEPARATOR ',') AS ids
			FROM profile
			GROUP BY sex, age_min, age_max, region
			HAVING count(id) > 1
		",
		// liczby osób dopasowanych do profili -- wstępne dopasowanie
		'profile-persons' => '
			SELECT
				i.group_name, i.sex, i.age_min, i.age_max, i.region, i.invites_no,
				count(p.id) as persons
			FROM profile i, personal p
			WHERE
				i.row_state = 0 AND p.row_state = 0
				AND
				p.age >= i.age_min
				AND
				(i.age_max IS NULL OR p.age <= i.age_max)
				AND
				p.sex = LEFT(i.sex, 1)
				AND
				p.region = i.region
				AND
				{pv_constraints|(1)}
			GROUP BY i.id
			ORDER BY i.id DESC
		',
		// liczby osób dopasowanych do profili
		'profile-persons-matched' => '
			SELECT
				i.id, i.group_name, i.sex, i.age_min, i.age_max, i.region, i.invites_no,
				count(p.id) as persons
			FROM profile i LEFT JOIN personal p ON i.id=profile_id
			WHERE
				i.row_state = 0
				AND
				{pv_constraints|(1)}
			GROUP BY i.id
			ORDER BY i.id DESC
		',
		// liczby osób dopasowanych do profili
		'profiles-results' => '
			SELECT
				i.group_name, i.sex, i.age_min, i.age_max, i.region, i.invites_no,
				d.dt_create as draw_time, d.verification
			FROM profile i LEFT JOIN draw d ON i.id=profile_id
			WHERE
				i.row_state = 0
				AND d.id =
				(
				   SELECT MAX(dsub.id)
				   FROM draw dsub
				   WHERE dsub.profile_id = i.id
				)
			GROUP BY i.id
			ORDER BY i.id DESC, d.id DESC
',
	);

	/**
	 * Aliased names of columns that are to be parsed as integers.
	 *
	 * @note All other columns are parsed as string/binary so this is purely optional.
	 *
	 * @var array
	 */
	protected $pv_intColumnsByAlias = array('id', 'invites_no', 'age_min', 'age_max', 'row_state', 'csv_file');

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