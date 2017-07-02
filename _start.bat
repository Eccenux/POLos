rem settings
SET tmpset_POLOS_URL=http://localhost/polos/

rem user settings
IF EXIST "__settings.bat" (
	CALL __settings.bat
)

rem start
start "" "%tmpset_POLOS_URL%"
