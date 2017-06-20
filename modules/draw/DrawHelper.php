<?php
/**
 * Draw Helper
 *
 * @author Maciej Nux Jaros
 */
class DrawHelper
{
	/**
	 * Prepare extra columns for profile-persons match.
	 * @param array $profiles
	 */
	public static function prepareProfilePerson(&$profiles)
	{
		foreach ($profiles as &$row)
		{
			$percentage = $row['persons'] / $row['invites_no'] * 100;
			if ($percentage < 1) {
				$row['percentage'] = "< 1%";
			} else {
				$row['percentage'] = strtr(sprintf("%.0f", $percentage), '.', ',') . "%";
			}
			if ($percentage > 200) {
				$row['row_class'] = 'draw-fair';
			} else if ($percentage > 100) {
				$row['row_class'] = 'draw-possible';
			} else if ($percentage == 100) {
				$row['row_class'] = 'draw-minimum';
			} else if ($percentage < 100) {
				$row['row_class'] = 'draw-not-possible';
			}
		}
	}

	/**
	 * Transform PESEL to be less identifiable.
	 *
	 * E.g. this PESEL:
	 *	70061614472
	 * would become:
	 *	..0616144..
	 *
	 * @param string $pesel
	 * @return string
	 */
	public static function safePesel($pesel) {
	 return '..'.substr($pesel, 2, 7).'..';
	}
}
