@ECHO OFF
@chcp 1250
cls

rem Test encoding
rem echo Za¿ó³æ gêœl¹ jaŸñ!
rem PAUSE
rem GOTO :EOF

rem
rem Settings
rem
rem Warning! Do NOT change this file directly.
rem Create `__settings.bat` instead.
rem
SET tmpset_INSTALL_DIR=C:\Program Files (x86)\Apache Software Foundation\Apache2.2\htdocs\polos
SET tmpset_GIT_REPO=https://github.com/Eccenux/POLos
SET tmpset_POLOS_URL=http://localhost/polos/
rem Should be ISO-formatted.
SET tmpset_TODAY=%date%

rem user settings
IF EXIST "__settings.bat" (
	CALL __settings.bat
)

echo.
echo To jest instalator (i aktualizator) skryptów POLos.
echo Uwaga! Nie wykonuj aktualizacji jeœli nie musisz.
echo.
echo Aby przerwaæ instalacjê zamknij to okno.
echo.
echo Instalator bêdzie operowa³ na plikach w poni¿szym folderze.
echo Musisz mieæ uprawnienia do modyfikacji tego folderu!
echo %tmpset_INSTALL_DIR%
echo.
PAUSE

rem
rem Initial check
rem
echo.
echo Sprawdzenie narzêdzi.
git --version
IF [%ERRORLEVEL%] NEQ [0] (
	echo.
	echo B³¹d! Nie uda³o siê uruchomiæ git. Musisz zainstalowaæ Git dla Windows.
	echo.
	PAUSE
	GOTO :EOF
)
echo OK.
echo.

rem
rem Installation type
rem
IF EXIST "%tmpset_INSTALL_DIR%" (
	goto update
) else (
	goto fresh_install
)
goto :EOF

:fresh_install
rem
rem Directories
rem
mkdir "%tmpset_INSTALL_DIR%"
IF [%ERRORLEVEL%] NEQ [0] (
	echo.
	echo B³¹d! Nie uda³o utworzyæ folderu instalacyjnego. Spróbuj uruchomiæ skrypt z uprawnieniami administratora.
	echo.
	PAUSE
	GOTO :EOF
)
cd "%tmpset_INSTALL_DIR%"

rem full rights to Users group
icacls "%tmpset_INSTALL_DIR%" /grant *S-1-5-32-545:(OI)(CI)F

rem
rem Download changes
rem
git clone %tmpset_GIT_REPO% .
IF [%ERRORLEVEL%] NEQ [0] (
	echo.
	echo B³¹d! Nie uda³o siê pobraæ danych. Upewnij siê, ¿e masz po³¹czenie z Internetem i spróbuj ponownie.
	echo.
	PAUSE
	GOTO :EOF
)

rem temp dir
mkdir .\temp

echo.
echo Wygl¹da na to, ¿e instalacja skryptów POLos powiod³a siê.
echo Uwaga! Pamiêtaj, ¿e oprócz skryptów POLos potrzebujesz tak¿e oprogramowania serwerowego (tj. Apache, PHP, MySQL).
echo.
echo Kontynuuj aby otworzyæ instrukcjê instalacji oprogramowania serwerowego.
echo.
PAUSE
start "" "https://github.com/Eccenux/POLos/blob/master/INSTALL.md"
GOTO :EOF


:update
cd "%tmpset_INSTALL_DIR%"

rem Check if temp dir exists
IF NOT EXIST .\temp (
	mkdir .\temp
)

rem Test access rights
mkdir .\temp--test
IF NOT EXIST .\temp--test (
	echo.
	echo B³¹d! Nie masz uprawnieñ do modyfikacji folderu instalacyjnego. Spróbuj uruchomiæ skrypt z uprawnieniami administratora.
	echo.
	PAUSE
	GOTO :EOF
)
rmdir .\temp--test

rem Check if the dir is already controlled by Git
IF EXIST "%tmpset_INSTALL_DIR%\.git" (
	goto update_with_git
) else (
	goto update_no_git
)
goto :EOF

:update_with_git
echo.
echo Znaleziono poprzednie pliki. Nast¹pi próba aktualizacji.
echo.
PAUSE

rem
rem Download changes
rem
git fetch
IF [%ERRORLEVEL%] NEQ [0] (
	echo.
	echo B³¹d! Nie uda³o siê pobraæ danych. Upewnij siê, ¿e masz po³¹czenie z Internetem i spróbuj ponownie.
	echo.
	PAUSE
	GOTO :EOF
)

rem
rem Check for local modifications
rem

rem error log
git status | findstr modified | findstr ".err.log" | find /v /c "" > temp\%tmpset_TODAY%_locally_modifiedlog.count.txt
set /p tmpset_MODIFIEDLOG=<temp\%tmpset_TODAY%_locally_modifiedlog.count.txt
IF [%tmpset_MODIFIEDLOG%] NEQ [0] (
	move .err.log .err--%tmpset_TODAY%.log
	echo.
	echo Uwaga! Znaleziono zmieniony log b³êdów. Jego nazwa zosta³a zmieniona na .err--%tmpset_TODAY%.log
	echo.
)

rem other modifications
git status | findstr modified > temp\%tmpset_TODAY%_locally_modified.txt
git status | findstr deleted > temp\%tmpset_TODAY%_locally_deleted.txt
type temp\%tmpset_TODAY%_locally_modified.txt | find /v /c "" > temp\%tmpset_TODAY%_locally_modified.count.txt
type temp\%tmpset_TODAY%_locally_deleted.txt | find /v /c "" > temp\%tmpset_TODAY%_locally_deleted.count.txt
set /p tmpset_MODIFIED=<temp\%tmpset_TODAY%_locally_modified.count.txt
set /p tmpset_DELETED=<temp\%tmpset_TODAY%_locally_deleted.count.txt
IF [%tmpset_MODIFIED%] NEQ [0] (
	echo.
	echo Uwaga! Znaleziono zmienione pliki. Poni¿sze pliki zostan¹ nadpisane.
	echo Aby dokoñczyæ aktualizacjê zalecane jest nadpisanie plików.
	type temp\%tmpset_TODAY%_locally_modified.txt
	echo.
)
IF [%tmpset_DELETED%] NEQ [0] (
	echo.
	echo Uwaga! Znaleziono usuniête pliki. Poni¿sze pliki zostan¹ przywrócone.
	echo Aby dokoñczyæ aktualizacjê zalecane jest przywrócenie plików.
	type temp\%tmpset_TODAY%_locally_deleted.txt
	echo.
)

rem 
rem Reset files (use github files instead of local files)
rem 
rem Note! Files that are not in the repo will NOT be removed.
rem Ignored files will NOT be removed.
IF [%tmpset_MODIFIED%%tmpset_DELETED%] NEQ [00] (
	echo.
	echo Kontynuuj jeœli mo¿na nadpisaæ/przywróciæ pliki.
	echo.
	PAUSE
	git reset --hard origin/master
)

GOTO update_end

:update_no_git
echo.
echo Znaleziono pliki, które nie by³y aktualizowane. Niektóre pliki mog¹ zostaæ nadpisane.
echo Aby wykonaæ aktualizacjê konieczne jest nadpisanie plików.
echo.
PAUSE

git init
git remote add origin %tmpset_GIT_REPO%
git fetch
IF [%ERRORLEVEL%] NEQ [0] (
	echo.
	echo B³¹d! Nie uda³o siê pobraæ danych. Upewnij siê, ¿e masz po³¹czenie z Internetem i spróbuj ponownie.
	echo.
	PAUSE
	GOTO :EOF
)

git reset --hard origin/master
git branch -u origin/master
GOTO update_end

:update_end
echo.
echo Wygl¹da na to, ¿e aktualizacja skryptów POLos powiod³a siê.
echo.
echo Kontynuuj aby uruchomiæ POLos.
echo.
echo Uwaga! Jeœli otworzy siê strona z b³êdem, to upewnij siê, ¿e oprogramowanie serwerowe jest prawid³owo zainstalowane.
echo Jeœli oprogramowanie jest zainstalowane, to sprawdŸ logi.
echo.
PAUSE
start "" "%tmpset_POLOS_URL%"
GOTO :EOF