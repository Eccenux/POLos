<? if (empty($pv_mainMenu)) { ?>
	<span class="empty">Menu jest puste</span>
<? } else { ?>
	<ul>
		<? foreach ($pv_mainMenu as $m) { ?>
			<? if (empty($m->submenu)) { ?>
				<li><a href="<?=$m->url?>"><?=htmlspecialchars($m->title)?></a></li>
			<? } else { ?>
				<li><a href="<?=$m->url?>"><?=htmlspecialchars($m->title)?></a>
					<ul>
						<? foreach ($m->submenu as $sm) { ?>
							<li><a href="<?=$sm['url']?>"><?=htmlspecialchars($sm['title'])?></a></li>
						<? } ?>
					</ul>
				</li>
			<? } ?>
		<? } ?>
	</ul>
<? } ?>