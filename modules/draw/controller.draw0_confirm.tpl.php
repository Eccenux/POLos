<h2>Przesłane dane</h2>

<ul>
	<li>Liczba profili: <?=ModuleTemplate::formatNumber($tplData['profile-total'][0]['profiles'])?></li>
	<li>Liczba zaproszeń: <?=ModuleTemplate::formatNumber($tplData['profile-total'][0]['invites'])?></li>
	<li>Liczba osób: <?=ModuleTemplate::formatNumber($tplData['personal-total'][0]['people'])?></li>
</ul>

<h2>Potwierdzenie</h2>
<p>Na początku nastąpi reset danych losowania i dopasowanie profili. To może trochę potrwać. Czy możemy kontynuować?</p>
<form action="" method="GET">
	<input type="hidden" name="mod" value="draw">
	<input type="hidden" name="a" value="draw">
	<input type="hidden" name="stage" value="1">
	<input type="submit" name="confirm" value="Tak, rozpocznij">
</form>
