@ECHO OFF
@chcp 1250
cls

rem Test encoding
rem echo Za��� g�l� ja��!
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
echo To jest instalator (i aktualizator) skrypt�w POLos.
echo Uwaga! Nie wykonuj aktualizacji je�li nie musisz.
echo.
echo Aby przerwa� instalacj� zamknij to okno.
echo.
echo Instalator b�dzie operowa� na plikach w poni�szym folderze.
echo Musisz mie� uprawnienia do modyfikacji tego folderu!
echo %tmpset_INSTALL_DIR%
echo.
PAUSE

rem
rem Initial check
rem
echo.
echo Sprawdzenie narz�dzi.
git --version
IF [%ERRORLEVEL%] NEQ [0] (
	echo.
	echo B��d! Nie uda�o si� uruchomi� git. Musisz zainstalowa� Git dla Windows.
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
	echo B��d! Nie uda�o utworzy� folderu instalacyjnego. Spr�buj uruchomi� skrypt z uprawnieniami administratora.
	echo.
	PAUSE
	GOTO :EOF
)
cd "%tmpset_INSTALL_DIR%"

rem temp dir
mkdir .\temp

rem
rem Download changes
rem
git clone %tmpset_GIT_REPO%
IF [%ERRORLEVEL%] NEQ [0] (
	echo.
	echo B��d! Nie uda�o si� pobra� danych. Upewnij si�, �e masz po��czenie z Internetem i spr�buj ponownie.
	echo.
	PAUSE
	GOTO :EOF
)
echo.
echo Wygl�da na to, �e instalacja skrypt�w POLos powiod�a si�.
echo Uwaga! Pami�taj, �e opr�cz skrypt�w POLos potrzebujesz tak�e oprogramowania serwerowego (tj. Apache, PHP, MySQL).
echo Zobacz instrukcj� na:
echo https://github.com/Eccenux/POLos/blob/master/INSTALL.md
echo.
PAUSE
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
	echo B��d! Nie masz uprawnie� do modyfikacji folderu instalacyjnego. Spr�buj uruchomi� skrypt z uprawnieniami administratora.
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
echo Znaleziono poprzednie pliki. Nast�pi pr�ba aktualizacji.
echo.
PAUSE

rem
rem Download changes
rem
git fetch
IF [%ERRORLEVEL%] NEQ [0] (
	echo.
	echo B��d! Nie uda�o si� pobra� danych. Upewnij si�, �e masz po��czenie z Internetem i spr�buj ponownie.
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
	echo Uwaga! Znaleziono zmieniony log b��d�w. Jego nazwa zosta�a zmieniona na .err--%tmpset_TODAY%.log
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
	echo Uwaga! Znaleziono zmienione pliki. Poni�sze pliki zostan� nadpisane.
	echo Aby doko�czy� aktualizacj� zalecane jest nadpisanie plik�w.
	type temp\%tmpset_TODAY%_locally_modified.txt
	echo.
)
IF [%tmpset_DELETED%] NEQ [0] (
	echo.
	echo Uwaga! Znaleziono usuni�te pliki. Poni�sze pliki zostan� przywr�cone.
	echo Aby doko�czy� aktualizacj� zalecane jest przywr�cenie plik�w.
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
	echo Kontynuuj je�li mo�na nadpisa�/przywr�ci� pliki.
	echo.
	PAUSE
	git reset --hard origin/master
)

GOTO update_end

:update_no_git
echo.
echo Znaleziono pliki, kt�re nie by�y aktualizowane. Niekt�re pliki mog� zosta� nadpisane.
echo Aby wykona� aktualizacj� konieczne jest nadpisanie plik�w.
echo.
PAUSE

git init
git remote add origin %tmpset_GIT_REPO%
git fetch
IF [%ERRORLEVEL%] NEQ [0] (
	echo.
	echo B��d! Nie uda�o si� pobra� danych. Upewnij si�, �e masz po��czenie z Internetem i spr�buj ponownie.
	echo.
	PAUSE
	GOTO :EOF
)

git reset --hard origin/master
git branch -u origin/master
GOTO update_end

:update_end
echo.
echo Wygl�da na to, �e aktualizacja skrypt�w POLos powiod�a si�.
echo.
echo Kontynuuj aby uruchomi� POLos.
echo.
echo Uwaga! Je�li otworzy si� strona z b��dem, to upewnij si�, �e oprogramowanie serwerowe jest prawid�owo zainstalowane.
echo Je�li oprogramowanie jest zainstalowane, to sprawd� logi.
echo.
PAUSE
start "" "%tmpset_POLOS_URL%"
GOTO :EOF