<?
	if ( !defined('NOT_HACKING_RILI') )
	{
		die("No hacking allowded ;).");
	}
	
	require_once ('./inc/numericEncoder.php');
	require_once ('./inc/db/personal.php');
	$dbPersonal = new dbPersonal();

	$tplData = array();
	$tplData['file'] = 'export.invites.csv';

	// get
	$dbPersonal->pf_getRecords($rows, array(
		'profile_id' => array('IS NOT', 'NULL'),
		'draw_id' => array('IS NOT', 'NULL'),
		'row_state' => 0,
	), array(
		'id',
		'pesel',
		'name',
		'surname',
		'city',
		'street',
		'building_no',
		'flat_no',
		'zip_code',
	));

	//
	// create file
	$fp = fopen($tplData['file'], 'w');
	// header
	fputcsv($fp, array(
		'kod',
		'imię',
		'naziwsko',
		'miasto',
		'ulica',
		'nr budynku',
		'nr lokalu',
		'kod pocztowy',
	));
	// rows
	$encoder = new NumericEncoder();
	foreach ($rows as $row) {
		$row['id'] = $encoder->encode(1000 * $row['id'] + intval(substr($row['pesel'], 0, 3)));
		unset($row['pesel']);
		fputcsv($fp, $row);
	}
	fclose($fp);

	// tpl
	$tplData['count'] = count($rows);

	// prepare data for render
	$pv_controller->tpl->data = $tplData;
	$pv_controller->tpl->file = 'controller.invitations.tpl.php';
?>