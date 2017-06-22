<?php
/**
 * Base class for other database manipulation classes.
 *
 * @warning It's assumed that database connection is estabilished before any read/write methods are used.
 *
 * @note In a descendant class in most cases you just need to setup some attributes/properties and use high level operations.
 * @note Override "High level SQL operations" if need more operations be performed on records before building final SQL statement.
 *
 * @note If you need to use this class for a different DBMS you may want to split the class and re-implement "Basic SQL operations" for each DBMS.
 *
 * Format of common parametrs of methods:
 * <pre>
 * 	$tabel = array (
 * 		0 => $record0,
 * 		1 => $record1,
 * 		...
 * 	);
 * 	$record = array ()
 * </pre>
 *
 * Author's note...
 * Yes, the library is double licensed and free (see below).
 * In various forms it's with me almost from the start of my career, even abandoned quite a few times...
 * I'm actually kind of sentimental about it :-). Still this haven't been released to public before 2014.
 *
 * @copyright (c) 2005-2014, Maciej Nux Jaros
 * @license http://creativecommons.org/licenses/by/4.0/ CC-BY
 * @license http://opensource.org/licenses/MIT MIT
 */
abstract class dbBaseClass
{
	// <editor-fold defaultstate="collapsed" desc="attributes">

	/**
	 * Last SQL error message.
	 *
	 * Might be an array if there were more messages from a single function.
	 *
	 * @var string|array
	 */
	public $msg='';
	/**
	 * Last SQL statement after which an error occured (for debugging).
	 * @var string
	 */
	public $sql='';

	/**
	 * Table name for which SQL operations will be performed.
	 *
	 * @warning MUST be defined in descendant class if high level operations are not overridden in the descendant class.
	 * 
	 * @var string
	 */
	protected $pv_tableName;

	/**
	 * Aliased names of columns that are to be parsed as integers.
	 *
	 * @note All other columns are parsed as string/binary so this is purely optional.
	 *
	 * @var array
	 */
	protected $pv_intColumnsByAlias = array();

	/**
	 * Alised names of columns that are to be excluded when inserting records.
	 *
	 * @note For tables that have automatically incremented ids you should add the name of this id column here.
	 *
	 * @see pf_insRecord()
	 * @var array
	 */
	protected $pv_insertExcludedCols = array();

	/**
	 * Default SQL fragment for ordering records in pf_getRecords
	 *
	 * @warning This SHOULD be set in a descendant class, if pf_getRecords is not overriden.
	 * @var string
	 */
	protected $pv_defaultOrderSql;

	/**
	 * Array for tranlsating column aliases to actual names
	 *
	 * @warning This MUST be initalized in a descendat class.
	 * @var array
	 */
	protected $pv_aliasNames2colNames = array();
	/**
	 * Array for backward translation of aliases.
	 *
	 * Automatically created based on \a $pv_aliasNames2colNames.
	 *
	 * @var array
	 */
	protected $pv_colNames2aliasNames;

	/**
	 * This can be used in values of records that are inserted in the DB.
	 * this will later be replaced with {@link pf_replacePseudoIDWithReal()}.
	 *
	 * @note Security? If this is inserted in some other filed the id might be exposed...
	 *	IDs are usually not secret, so should not be a problem.
	 * 
	 * @var string
	 */
	protected $pv_last_insert_id_pseudo_val = '__LAST_INSERT_ID__';

	/**
	 * "Templates" for running complicated (e.g. statistical) queries
	 *
	 * @see pf_getStats()
	 * @var array
	 */
	protected $pv_sqlStatsTpls = array();

	/**
	 * Left keyword quote character used for this DB engine
	 * @var string
	 */
	private $pvc_left_keyword_quote = '`';
	/**
	 * Right keyword quote character used for this DB engine
	 * @var string
	 */
	private $pvc_right_keyword_quote = '`';

	// </editor-fold>
	
	// <editor-fold defaultstate="collapsed" desc="construct">
	/**
	 * @throws Exception
	 */
	public function __construct()
	{
		// aliases
		if (empty($this->pv_aliasNames2colNames))
		{
			throw new Exception('pv_aliasNames2colNames is empty');
		}
		$this->pv_colNames2aliasNames = array_flip($this->pv_aliasNames2colNames);
	}
	// </editor-fold>
	
	// <editor-fold defaultstate="collapsed" desc="alias helpers">
	/**
	 * Returns a name of a column based on its alias.
	 *
	 * @param string $pv_alias Column alias.
	 * @return string Actual column name (and possibly standard table alias used in queries)
	 */
	private function pf_getColumnNameFromAlias($pv_alias)
	{
		if (isset($this->pv_aliasNames2colNames[$pv_alias]))
		{
			return $this->pv_aliasNames2colNames[$pv_alias];
		}
		return $pv_alias;
		//return strtr($pv_alias, $this->pv_aliasNames2colNames);
	}

	/**
	 * Returns an alias of a column based on its actual name.
	 *
	 * @param string $pv_column Actual column name (and possibly standard table alias used in queries)
	 * @return string Columns alias.
	 */
	private function pf_getAliasFromColumnName($pv_column)
	{
		if (isset($this->pv_colNames2aliasNames[$pv_column]))
		{
			return $this->pv_colNames2aliasNames[$pv_column];
		}
		return $pv_column;
		//return strtr($pv_column, $this->pv_colNames2aliasNames);
	}

	/**
	 * Similat to {@link pf_getColumnNameFromAlias()} but with array in return.
	 *
	 * @param string $pv_alias Column alias
	 * @return array (
	 * 	'tbl'=>'table alias'
	 * 	'col'=>'column name' // (without table alias)
	 * )
	 */
	private function pf_getTblColumnArrayFromAlias($pv_alias)
	{
		$pv_tbl = '';
		$pv_column = $this->pf_getColumnNameFromAlias($pv_alias);
		$pv_ret = array();
		if (preg_match('#(.+?)\.(.+)#', $pv_column, $pv_ret))
		{
			$pv_tbl = $pv_ret[1];
			$pv_column = $pv_ret[2];
		}
		return array(
			'tbl'	 => $pv_tbl,
			'col'	 => $pv_column
		);
	}

	/**
	 * Transforms alias=>value array into and array that can be used for inserting data.
	 *
	 * @param array $pv_alias_val_array alias=>value array
	 * @param array $pv_excludedColumns [optional] An array of columns (aliased) to be excluded in the resulting array.
	 * @return array One or more arrays (rows) in the following format:
	 * 	'table alias' => array (
	 *		column=>value,
	 *		...
	 * 	)
	 *	where value is escaped for inserting in SQL
	 *	and column name is without the table alias.
	 * Number of arrays (rows) depend on the number of actual SQL tables that certain columns are in.
	 */
	protected function pf_prepareInsertArrays ($pv_alias_val_array, $pv_excludedColumns=array())
	{
		$pv_ret_arr = array();
		if (!empty($pv_alias_val_array))
		{
			foreach ($pv_alias_val_array as $pv_key => &$pv_val)
			{
				// excluded?
				if (!empty($pv_excludedColumns) && in_array($pv_key, $pv_excludedColumns))
				{
					continue;
				}
				// tr
				$pv_val = $this->pf_prepareValue ($pv_key, $pv_val);
				$pv_tbl_col_arr = $this->pf_getTblColumnArrayFromAlias($pv_key);
				$pv_tbl = $pv_tbl_col_arr['tbl'];
				$pv_col = $pv_tbl_col_arr['col'];
				unset($pv_tbl_col_arr);
				// set
				if (!isset($pv_ret_arr[$pv_tbl]))
				{
					$pv_ret_arr[$pv_tbl] = array();
				}
				$pv_ret_arr[$pv_tbl][$pv_col] = $pv_val;
			}
		}
		return $pv_ret_arr;
	}
	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="SQL fragments builders">
	/**
	 * Transforms alias=>value array into SQL fragments ready for WHERE clause.
	 *
	 * @note Values are escaped for inserting in SQL
	 *
	 * Empty values are NOT excluded. If you need to exclude some, use \a $pv_excludedColumns parameter.
	 *
	 * @param array $pv_alias_val_array An alias=>value array or an 'alias'=>array('operator', 'value')
	 *  <pre>
	 *  NOTE! If the value is an array then it must be in the follwing format:
	 *	array('>', 10) is understood as: `column > 10`
	 *	array('<=', '10') is understood as: `column <= '10'`
	 *	etc...
	 *  </pre>
	 *	For the 'IN' operator the value will be treated as a simple CSV (separates made of a comma only) or an array.
	 *  <pre>
	 *  Example (both have same effect):
	 *	array('IN', '1,2,3')
	 *	array('IN', array(1,2,3))
	 *  NOTE! In both cases the values are preparaed acording to standard rules (i.e. escaped or cast to int).
	 *  </pre>
	 *	Characters allowed for operators are: [<>=!] and [RLIKE], IN, NOT IN, IS, IS NOT (last to will ignore value and assume it to be NULL)
	 * @param array $pv_excludedColumns [optional] An array of columns (aliased) to be excluded in the results.
	 * @param bool $pv_areConstraintsPrecise [optional] Use instead os adding LIKE operator for every value.
	 *	When false it's like setting "LIKE" as a default operator (otherwise equality is default).
	 * @return array An array of SQL fragments for comparissions.
	 */
	protected function pf_getColumnValueArray ($pv_alias_val_array, $pv_excludedColumns=array(), $pv_areConstraintsPrecise=true)
	{
		$pv_ret_arr = array();
		if (!empty($pv_alias_val_array))
		{
			foreach ($pv_alias_val_array as $pv_key => &$pv_val)
			{
				// excluded?
				if (!empty($pv_excludedColumns) && in_array($pv_key, $pv_excludedColumns))
				{
					continue;
				}
				// array?
				$pv_sign = '=';
				if (is_array($pv_val))
				{
					// skip if incorrect
					if (!isset($pv_val[0]) || !isset($pv_val[1]) || !preg_match('#^([<>=!RLIKE ]+|IN|NOT IN|IS|IS NOT)$#', $pv_val[0]))
					{
						continue;
					}
					$pv_sign = $pv_val[0];
					$pv_val = $pv_val[1];
				}
				// prepare
				if ($pv_sign == 'IN' || $pv_sign == 'NOT IN')
				{
					$pv_val = $this->pf_prepareArrayValue ($pv_key, $pv_val);
				}
				else
				{
					$pv_val = $this->pf_prepareValue ($pv_key, $pv_val);
				}
				$pv_column = $this->pf_getColumnNameFromAlias($pv_key);
				// set
				if ($pv_areConstraintsPrecise)
				{
					if ($pv_sign == 'IN' || $pv_sign == 'NOT IN')
					{
						$pv_ret_arr[] = $pv_column.' '.$pv_sign.' ('.$pv_val.')';
					}
					elseif ($pv_sign == 'IS' || $pv_sign == 'IS NOT')
					{
						$pv_ret_arr[] = $pv_column.' '.$pv_sign.' NULL';	// assuming val is NULL
					}
					elseif (is_string($pv_val))
					{
						$pv_ret_arr[] = $pv_column.' '.$pv_sign.' \''.$pv_val.'\'';
					}
					elseif (is_null($pv_val))
					{
						$pv_ret_arr[] = $pv_column.' '.$pv_sign.' NULL';
					}
					else
					{
						$pv_ret_arr[] = $pv_column.' '.$pv_sign.' '.$pv_val.'';
					}
				}
				else
				{
					$pv_ret_arr[] = $pv_column.' LIKE \''.$pv_val.'\'';
				}
			}
		}
		return $pv_ret_arr;
	}

	/**
	 * Transforms alias=>value array into SQL fragment ready for WHERE clause.
	 *
	 * @note Values are escaped for inserting in SQL.
	 *
	 * @param array $pv_constraints See description in {@link pf_getColumnValueArray()}
	 * @param array $pv_excludedColumns [optional] An array of columns (aliased) to be excluded in the results.
	 * @param bool $pv_areConstraintsPrecise [optional] Use instead os adding LIKE operator for every value.
	 *	When false it's like setting "LIKE" as a default operator (otherwise equality is default).
	 * @return string SQL fragment in format: 'WHERE (column=value) AND ...' or an empty string.
	 */
	protected function pf_getWhereSQL ($pv_constraints, $pv_excludedColumns=array(), $pv_areConstraintsPrecise=true)
	{
		$pv_where_arr = $this->pf_getColumnValueArray ($pv_constraints, $pv_excludedColumns, $pv_areConstraintsPrecise);
		if (!empty($pv_where_arr))
		{
			$pv_where_sql='WHERE (' . implode(') AND (', $pv_where_arr) .')';
		}
		else
		{
			$pv_where_sql='';
		}
		return $pv_where_sql;
	}

	/**
	 * Builds a SET fragment for an UPDATE statement.
	 *
	 * @note Values are escaped for inserting in SQL.
	 *
	 * @param array $pv_record Record with values to be transformed.
	 * @param array $pv_excludedColumns [optional] An array of columns (aliased) to be excluded in the results.
	 * @return string SQL fragment or an empty string.
	 */
	protected function pf_getSetSQL ($pv_record, $pv_excludedColumns=array())
	{
		$pv_set_arr = $this->pf_getColumnValueArray ($pv_record, $pv_excludedColumns);
		if (!empty($pv_set_arr))
		{
			$pv_set_sql='SET ' . implode(', ', $pv_set_arr);
		}
		else
		{
			$pv_set_sql='';
		}
		return $pv_set_sql;
	}

	/**
	 * Builds separate SQL fragments for a list of columns and for a list of values for the INSERT statement.
	 *
	 * @note Values are escaped for inserting in SQL.
	 *
	 * @param array $pv_record Record with values to be transformed.
	 * @param array $pv_excludedColumns [optional] An array of columns (aliased) to be excluded in the results.
	 * @return array (
	 *			'table alias' => array (
	 *				'keys' => "(`name`, `count`)" - sql with keys of the array (column names)
	 *				'vals' => "VALUES('abc', 123)" - sql with values
	 *			)
	 *		)
	 */
	protected function pf_getInsSQLArrays ($pv_record, $pv_excludedColumns=array())
	{
		$pv_ins_arr = $this->pf_prepareInsertArrays ($pv_record, $pv_excludedColumns);
		$pv_ins_sql_arr = array();
		if (!empty($pv_ins_arr))
		{
			foreach ($pv_ins_arr as $pv_tbl=>$pv_arr)
			{
				$pv_ins_sql_arr[$pv_tbl] = array(
					'keys'=>array(),
					'vals'=>array(),
				);
				foreach ($pv_arr as $pv_key=>$pv_val)
				{
					$pv_ins_sql_arr[$pv_tbl]['keys'][] = $pv_key;
					$pv_ins_sql_arr[$pv_tbl]['vals'][] = $pv_val;
				}
				$pv_ins_sql_arr[$pv_tbl]['vals'] = "VALUES ('". implode("', '", $pv_ins_sql_arr[$pv_tbl]['vals']) ."')";
				$pv_ins_sql_arr[$pv_tbl]['keys'] = '('.$this->pvc_left_keyword_quote
						. implode($this->pvc_right_keyword_quote.', '.$this->pvc_left_keyword_quote, $pv_ins_sql_arr[$pv_tbl]['keys'])
						. $this->pvc_right_keyword_quote.')';
			}
		}
		return $pv_ins_sql_arr;
	}

	/**
	 * Builds a columns list for the SELECT statement.
	 *
	 * @param array $pv_columns Alias list.
	 * @param array $pv_excludedColumns [optional] An array of columns (aliased) to be excluded.
	 *
	 * @return string SQL fragment with real names and aliases.
	 */
	protected function pf_getColumnsSQLFromAliasList ($pv_columns, $pv_excludedColumns=array())
	{
		$pv_sql_list = array();
		foreach ($pv_columns as $pv_alias)
		{
			// excluded?
			if (!empty($pv_excludedColumns) && in_array($pv_alias, $pv_excludedColumns))
			{
				continue;
			}
			// as
			if (isset($this->pv_aliasNames2colNames[$pv_alias]))
			{
				$pv_column = $this->pv_aliasNames2colNames[$pv_alias];
				$pv_sql_list[] = "$pv_column as '$pv_alias'";
			}
		}
		if (empty($pv_sql_list))
		{
			$pv_sql_list = '';
		}
		else
		{
			$pv_sql_list = implode(', ', $pv_sql_list);
		}
		return $pv_sql_list;
	}
	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="SQL operations helpers">
	/**
	 * Prepares a value before putting into an SQL.
	 *
	 * @param string $pv_alias Column alias.
	 * @param mixed $pv_val Value to be prepared.
	 * @return mixed Prepared value.
	 */
	protected function pf_prepareValue ($pv_alias, $pv_val)
	{
		if (!is_integer($pv_val) && !is_null($pv_val))
		{
			if (!empty($this->pv_intColumnsByAlias) && array_search($pv_alias, $this->pv_intColumnsByAlias)!==false)
			{
				$pv_val = intval($pv_val);
			}
			else
			{
				$pv_val = mysql_real_escape_string($pv_val);
			}
		}
		return $pv_val;
	}

	/**
	 * Prepare composed value (array or CSV) for safe injection in SQL.
	 *
	 * @param string $pv_alias Column alias.
	 * @param mixed $pv_val Value to be prepared (array or simple CSV - comma only).
	 * @return mixed Prepared value (to be used e.g. inside in: 'IN (...)').
	 */
	private function pf_prepareArrayValue ($pv_alias, $pv_val)
	{
		if (!is_array($pv_val))
		{
			$pv_val = explode(',', $pv_val);
		}
		$pv_new_val = array();
		foreach ($pv_val as $v)
		{
			$v = $this->pf_prepareValue ($pv_alias, $v);
			if (is_string($v))
			{
				$v = '\''.$v.'\'';
			}
			$pv_new_val[] = $v;
		}
		return implode(',', $pv_new_val);
	}


	/**
	 * Replaces pseudo-ID (pv_last_insert_id_pseudo_val) with an actual ID.
	 *
	 * @param string $pv_valuesSql
	 * @param int|string $pv_realId
	 */
	protected function pf_replacePseudoIDWithReal (&$pv_valuesSql, $pv_realId)
	{
		$pv_valuesSql = strtr($pv_valuesSql, array($this->pv_last_insert_id_pseudo_val => $pv_realId));
	}

	/**
	 * Triggers an error after an SQL error failure.
	 *
	 * @param string $pv_sql Statement that couldn't be executed.
	 */
	private function pf_throwSQLError($pv_sql)
	{
		$this->sql = $pv_sql;
		$this->msg = mysql_error();
		trigger_error("\nSQL error: {$this->msg}\nSQL:{$this->sql}\n", E_USER_ERROR);
	}
	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="Basic SQL operations">
	/**
	 * Simply gets an array of records from the DB.
	 *
	 * @warning Statement in \a $pv_sql is NOT secured in any way here (all values should be properly escaped before).
	 *
	 * @param array $pv_array Return array.
	 * @param string $pv_sql The SQL to be sent directly(!) to the DB.
	 * @param boolean $pv_noValueParsing If true then value parsing will not be performed.
	 *	Normally values are parsed based on their type. Specifically Integers are parsed from strings to int type.
	 * @return boolean true if everything is fine.
	 */
	protected function pf_getArrayOfRecords (&$pv_array, $pv_sql, $pv_noValueParsing = false)
	{
		$pv_result = mysql_query($pv_sql);
		if ($pv_result==false)
		{
			$this->pf_throwSQLError($pv_sql);
			return false;
		}
		if (empty($this->pv_intColumnsByAlias) || $pv_noValueParsing)
		{
			while ($row = mysql_fetch_array($pv_result, MYSQL_ASSOC))
			{
				$pv_array[] = $row;
			}
		}
		else
		{
			while ($row = mysql_fetch_array($pv_result, MYSQL_ASSOC))
			{
				foreach ($row as $pv_alias=>&$val)
				{
					if (array_search($pv_alias, $this->pv_intColumnsByAlias)!==false)
					{
						$row[$pv_alias] = intval($row[$pv_alias]);
					}
				}
				$pv_array[] = $row;
			}
		}
		mysql_free_result ($pv_result);
		return true;
	}
	
	/**
	 * Run a insert, update or delete statement.
	 *
	 * @param string $pv_sql
	 * @return boolean|int <ul>
	 *	<li>false upon error
	 *  <li>number of affected rows (upon success)
	 * <ul>
	 */
	protected function pf_runModificationSQL ($pv_sql)
	{
		$pv_result = mysql_query($pv_sql);
		if ($pv_result==false)
		{
			$this->pf_throwSQLError($pv_sql);
			return false;
		}
		return mysql_affected_rows();
	}

	/**
	 * Returns ID form last insert.
	 *
	 * Works for AUTO-incremented ID.
	 *
	 * @return int
	 */
	protected function pf_getLastInsertId ()
	{
		return mysql_insert_id();
	}
	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="High level SQL operations">
	/**
	 * Extra operations on a record to be run in `pf_insRecord`.
	 *
	 * It can be overriden by a descendant class to avoid re-implementing whole `pf_insRecord`.
	 * 
	 * @param array $pv_record The record.
	 */
	protected function pf_insRecordExtraParse(&$pv_record)
	{
	}
	/**
	 * Extra operations on a record to be run in `pf_setRecords`.
	 *
	 * It can be overriden by a descendant class to avoid re-implementing whole `pf_setRecords`.
	 *
	 * @param array $pv_record The record.
	 */
	protected function pf_setRecordExtraParse(&$pv_record)
	{
	}

	/**
	 * A convenience function to get many stats in one function call.
	 *
	 * Same sa {@link pf_getStats()} except that it gets multiple stats into an asoc. array.
	 *
	 * Note! For now passes same constrains for each stats.
	 */
	public function pf_getStatsMany(&$pv_array, $pv_tplnames, $pv_constraints=array())
	{
		foreach ($pv_tplnames as $pv_tplname) {
			$pv_array[$pv_tplname] = array();
			$this->pf_getStats($pv_array[$pv_tplname], $pv_tplname, $pv_constraints);
		}
	}

	/**
	 * Returns records in format based on templates that are defined in {@link $pv_sqlStatsTpls}
	 *
	 * @note This is just a simplification that allows to quickly get and dump values in a varity of formats.
	 *
	 * @param array $pv_array Result array of records (rows).
	 * @param string $pv_tplname Name of the stats template (vide {@link $pv_sqlStatsTpls})
	 * @param array $pv_constraints Constraints for one or more fields that are prepared and injected in the SQL. See description in {@link pf_getColumnValueArray()}
	 *		Note! Constraints can be used only if a marker is added in the template:
	 *		"{pv_constraints}"
	 *		or
	 *		"{pv_constraints|default_constraint=1}"
	 *		Typically the marker would appear in the WHERE clause.
	 *		Example marker:
	 *		"SELECT * FROM jobs WHERE {pv_constraints|active=1}"
	 *		So by default this results in:
	 *		"SELECT * FROM jobs WHERE active=1"
	 *		With constraints like so: array('type'=>'builder'); it would become:
	 *		"SELECT * FROM jobs WHERE type='builder'"
	 * @return int 1 - OK, 0 - error or an empty result set
	 */
	public function pf_getStats(&$pv_array, $pv_tplname, $pv_constraints=array())
	{
		$pv_array = array();
		
		//
		// SQL tpl check
		//
		if (empty($this->pv_sqlStatsTpls) || !isset($this->pv_sqlStatsTpls[$pv_tplname]))
		{
			$this->msg = 'Unknown template';
			return 0;
		}
		$pv_sql = $this->pv_sqlStatsTpls[$pv_tplname];
		
		//
		// Constraints
		//
		$pv_constraints_pattern = '#\{(?:pv_constraints|pv_ograniczenia)(\|([\s\S]+?))?\}#';
		$pv_matched = array();
		if (preg_match($pv_constraints_pattern, $pv_sql, $pv_matched))
		{
			$pv_where_sql = $this->pf_getWhereSQL($pv_constraints);
			// creating replacement for the marker
			$pv_constraints_replace = '';
			if (!empty($pv_where_sql))
			{
				$pv_constraints_replace = preg_replace('#^WHERE\s+(.+)$#', '($1)', $pv_where_sql);	// removing WHERE added by `pf_getWhereSQL`
			}
			// no constraints given but there is a default value
			else if (count($pv_matched)>2)
			{
				$pv_constraints_replace = $pv_matched[2];
			}
			// replacement
			$pv_sql = preg_replace($pv_constraints_pattern, $pv_constraints_replace, $pv_sql);
		}
		
		//
		// Retrive data
		//
		$this->pf_getArrayOfRecords($pv_array, $pv_sql);

		//
		// Wrap up
		//
		if (!empty($pv_array))
		{
			return 1;	// OK :)
		}
		else
		{
			return 0;	// error or an empty result set
		}
	}

	/**
	 * Standard record (row) insertion.
	 *
	 * @note For now a single table is supported.
	 *
	 * @param array $pv_record Record to be inserted.
	 * @param bool $pv_returnId If true then ID will be returned uppon success.
	 * @return int 0 upon error
	 * @throws Exception If \a pv_tableName was not set.
	 */
	public function pf_insRecord($pv_record, $pv_returnId = false)
	{
		if (empty($this->pv_tableName))
		{
			throw new Exception("Tabel name is empty");
		}

		//
		// Pre-preparation
		//
		$this->pf_insRecordExtraParse($pv_record);

		//
		// Prepare record
		//
		$pv_ins_sql_arr = $this->pf_getInsSQLArrays($pv_record, $this->pv_insertExcludedCols);

		//
		// Insert
		//
		$sql = "INSERT INTO {$this->pv_tableName} {$pv_ins_sql_arr['']['keys']}
			{$pv_ins_sql_arr['']['vals']}";
		$pv_affected_rows = $this->pf_runModificationSQL($sql);
		if ($pv_affected_rows==0)
		{
			$this->msg = 'DB error while inserting record!';
			return 0;
		}

		if ($pv_returnId) {
			return $this->pf_getLastInsertId();
		}
		return 1;	// OK :)
	}

	/**
	 * Standard deletion of records (rows) based on given constraints.
	 *
	 * @param array $pv_constraints See description in {@link pf_getColumnValueArray()}
	 * @param bool $pv_areConstraintsPrecise [optional] Use instead of adding LIKE operator for every value.
	 * @return int 0 upon error or no records matched.
	 * @throws Exception If \a pv_tableName was not set.
	 */
	public function pf_delRecords($pv_constraints=array(), $pv_areConstraintsPrecise=true)
	{
		if (empty($this->pv_tableName))
		{
			throw new Exception("Table name is empty");
		}

		//
		// Prepare
		//
		$pv_where_sql = $this->pf_getWhereSQL($pv_constraints, array(), $pv_areConstraintsPrecise);

		//
		// Execute
		//
		$sql = "DELETE
			FROM {$this->pv_tableName}
			$pv_where_sql"
		;
		$pv_affected_rows = $this->pf_runModificationSQL($sql);

		//
		// Return
		//
		if ($pv_affected_rows===false)
		{
			$this->msg = 'DB error while deleting record(s)!';
			return 0;
		}

		return 1;	// OK :)
	}

	/**
	 * Standard update method for existing records (rows) based on given constraints.
	 *
	 * @param array $pv_record New (modified) values.
	 * 		Note! Record SHOULD contain only the values (columns) that:
	 * 		a) can be modified
	 * 		b) are to be modified
	 * 		If you must you can also exclude columns using \a $pv_excludedColumns.
	 * @param array $pv_constraints See description in {@link pf_getColumnValueArray()}
	 * @param array $pv_excludedColumns [optional] An array of columns (aliased) to be excluded in the update.
	 * @param bool $pv_areConstraintsPrecise [optional] Use instead of adding LIKE operator for every value.
	 * @return int 0 upon error or no records matched.
	 * @throws Exception If \a pv_tableName was not set.
	 */
	public function pf_setRecords($pv_record, $pv_constraints=array(), $pv_excludedColumns=array(), $pv_areConstraintsPrecise=true)
	{
		if (empty($this->pv_tableName))
		{
			throw new Exception("Table name is empty");
		}

		//
		// Pre-preparation
		//
		$this->pf_setRecordExtraParse($pv_record);

		//
		// Prepare
		//
		$pv_where_sql = $this->pf_getWhereSQL($pv_constraints, array(), $pv_areConstraintsPrecise);
		$pv_set_sql = $this->pf_getSetSQL($pv_record, $pv_excludedColumns);

		//
		// Execute
		//
		$sql = "UPDATE {$this->pv_tableName}
			$pv_set_sql
			$pv_where_sql"
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
	 * Standard get method for existing records (rows) based on given constraints.
	 *
	 * @param array $pv_array Return array with records (rows).
	 * @param array $pv_constraints See description in {@link pf_getColumnValueArray()}
	 * @param array $pv_columns [optional] An array of columns (aliased) to be retrived.
	 *	Only those defined for the record are available.
	 *	If empty all of them are retrived.
	 * @param bool $pv_areConstraintsPrecise [optional] Use instead of adding LIKE operator for every value.
	 * @return int 0 upon error or no records matched.
	 * @throws Exception If \a pv_tableName was not set.
	 */
	public function pf_getRecords(&$pv_array, $pv_constraints=array(), $pv_columns=array(), $pv_areConstraintsPrecise=true)
	{
		if (empty($this->pv_tableName))
		{
			throw new Exception("Table name is empty");
		}
		$pv_array = array();

		//
		// Prepare
		//
		$pv_where_sql = $this->pf_getWhereSQL($pv_constraints, array(), $pv_areConstraintsPrecise);
		$pv_order_sql = empty($this->pv_defaultOrderSql) ? "" : $this->pv_defaultOrderSql;
		if (empty($pv_columns))
		{
			$pv_columns = array_keys($this->pv_aliasNames2colNames);
		}
		$pv_columns_sql = $this->pf_getColumnsSQLFromAliasList($pv_columns);

		//
		// Execute
		//
		$sql = "SELECT $pv_columns_sql
			FROM {$this->pv_tableName}
			$pv_where_sql
			$pv_order_sql"
		;
		//trigger_error ($sql, E_USER_NOTICE);
		$this->pf_getArrayOfRecords($pv_array, $sql);

		//
		// Return
		//
		return !empty($pv_array) ? 1 : 0;
	}
	// </editor-fold>

}

?>