Instalacja POLos
================

Ze względu na przetwarzanie dużej ilości danych program działa w środowisku serwerowym (Apache, PHP, MySQL). Nie jest wymagany konkretny system, ale testy były wykonywane na Windows (konkretnie na Windows 10).

Wymagania sprzętowe
-------------------

Za serwer może posłużyć w miarę dowolny, współczesny komputer. Zalecane parametry:
* CPU: 3 GHz, quad, 64-bit.
* RAM: 8 GB.
* Dysk: 20 GB wolnego miejsca. Uwaga! Zalecana jest instalacja na dysku typu SSD!

Zamiast dysku SSD można ew. użyć dwóch dysków HDD, ale program będzie działał zauważalnie wolniej (nawet 10 razy wolniej).

Można też spróbować użyć dwóch wolniejszych komputerów. Jeden na serwer SQL, a drugi na serwer Apache+PHP. Ważne! Ze na wydajność oraz **ze względów bezpieczeństwa** zalecane jest korzystanie wyłącznie z sieci lokalnej.

Instalacja oprogramowania
-------------------------

### Klient ###
Dostęp do programu odbywa się przez przeglądarkę (najlepiej Firefox lub Chrome). W starych IE (starszym niż ten z Windows 7), wygląd niektórych elementów może być dziwny -- zwłaszcza losowania.

Zalecany jest dostęp lokalny, czyli z tego samego komputera, na którym jest Apache. Wówczas niepotrzebne jest szyfrowanie połączenie i konfigurowanie uwierzytelniania (logowania).

Do obsługi plików CSV najlepiej zainstalować [Apache Open Office](https://www.openoffice.org/pl/download/index.html) lub ew. Libre Office . MS Excel może wystarczyć do eksportu plików CSV, ale nie poradzi sobie z kodowaniem UTF-8 przy eksporcie plików CSV.

### Serwer ###

Na komputerze muszą być zainstalowane i skonfigurowane następujące programy serwerowe: 
* Apache 2.2 lub nowszy.
* PHP 5.3 lub nowszy. Wymagane rozszerzenie `mysql`.
* MySQL 5.1 lub nowszy. Uwaga! Musi być skonfigurowany na obsługę UTF-8.

Poza tym, że używane jest rozszerzenie `mysql`, to nie ma konkretnych wymagań do wersji PHP. Można próbować instalować POLos na zintegrowanych pakietach serwerowych typu XAMPP, czy WampServer.

Zalecane wersje to:
* httpd-2.2.25-win32-x86-openssl-0.9.8y.msi
* mysql-5.1.50-win32 (full).msi
* php-5.3.5-Win32-VC6-x86.msi

### Instalacja serwera ###

Na serwerze należy zainstalować Apache, PHP i MySQL.

**Ważne! Niektóre rzeczy powinny być skonfigurowane podczas instalacji**! Szczegóły poniżej.

1. Instalacja Apache.
2. Instalacja PHP. Ważne etapy:
	1. Przy instalacji zaznacz moduł `mysql` jeśli nie jest domyślnie zaznaczony.
	2. Wskaż folder `conf` z zainstalowanego Apache. Bez tego PHP nie będzie działał poprawnie.
3. Instalacja MySQL.
	1. Najlepiej wybrać wstępną konfigurację, która będzie w stanie obsłużyć dużą ilość danych (serwerową).
	2. Musisz wybrać obsługę kodowanie UTF-8.

**Uwaga!** Instalator PHP 5.3 może mieć problem z ustawieniem ścieżki w konfiguracji Apache. Po instalacji upewnij się, że na końcu `httpd.conf` znajduje się coś w rodzaju:

```ini
#BEGIN PHP INSTALLER EDITS - REMOVE ONLY ON UNINSTALL
PHPIniDir "c:/Program Files (x86)/PHP/"
LoadModule php5_module "c:/Program Files (x86)/PHP/php5apache2_2.dll"
#END PHP INSTALLER EDITS - REMOVE ONLY ON UNINSTALL
```

### Konfiguracja serwera ###

#### Apache ####
W pliku `httpd.conf` znajduje się główna konfiguracja Apache. 

1. Należy zmienić konfigurację głównego folderu ze skryptami. W wypadku typowej instalacji Windows to `<Directory "C:/Program Files (x86)/Apache Software Foundation/Apache2.2/htdocs">`. Należy zmienić w niej `AllowOverride None` na `AllowOverride All`.
2. W dyrektywę `DirectoryIndex` należy dodać `index.php`. Czyli zmienić ją na: `DirectoryIndex index.php index.html`.

Dodatkowo w wypadku użycia poza siecią lokalną należy włączyć moduły:
1. `mod_rewrite` -- do przekierowań.
2. `mod_ssl` -- do szyfrowania (HTTPS).

Należy pamiętać, że **po zmianie konfiguracji konieczny jest restart usługi Apache**.

#### PHP ####
W pliku `php.ini` znajduje się główna konfiguracja PHP.

Należy ustawić:
1. `short_open_tag` na `On`.
2. `display_errors` na `Off`.
3. `upload_max_filesize` na `30M` (lub więcej - ogranicza wielkość przesyłanych plików).
4. `memory_limit` na `512M` (lub więcej).
5. `post_max_size` musi być większe `upload_max_filesize`, ale mniejsze od `memory_limit`. Zalecane jest ustawienie przynajmniej o 10M większe od `upload_max_filesize`.

Upewnij się także, że `upload_tmp_dir`, `session.save_path` oraz `error_log` wskazują na istniejący folder.

Należy pamiętać, że **po zmianie konfiguracji konieczny jest restart usługi Apache**.

#### MySQL ####

O ile pamiętało się, żeby podczas instalacji MySQL skonfigurować przez kreator usługę, to większość domyślnych ustawień powinna wystarczyć.

Należy jedynie `my.ini` należy powiększyć `max_allowed_packet` na około 2 razy więcej niż plik CSV. Jeśli w pliku `my.ini` nie ma wpisu "max_allowed_packet", to należy go dodać w sekcji `[mysqld]`. Powinno to wyglądać tak:
```ini
max_allowed_packet = 100M 
```

#### Baza danych ####

Na początek należy utworzyć bazę danych. Można to zrobić w konsoli MySQL lub przez [phpMyAdmin](https://www.phpmyadmin.net/downloads/).

Tworzenie bazy i użytkownika:
```sql
CREATE DATABASE polos_db;
GRANT ALL ON polos_db.* 
	TO polos_user@localhost IDENTIFIED BY '...jakieś hasło do bazy...';
```

Tworzenie struktury:
```bash
mysql -uroot -pHasloRoot --database=polos_db < __TABLES.sql
```
Można też zaimportować strukturę w phpMyAdmin. Plik `__TABLES.sql` znajduje się w folderze `.sql`.

**Uwaga! Wykonanie pliku `__TABLES.sql` kasuje bieżące dane**.

#### Skrypty ####

1. Najpierw skopiuj pliki na serwer Apache np. do folderu `htdocs\polos`.
2. Utwórz plik `polos\inc\dbConnect.php` i wpisz w nim polecenia połączenia z bazą MySQL. Przykładowa zawartość pliku `dbConnect.php` znajduje się w [dbConnect.example.php](inc/dbConnect.example.php). Oczywiście należy w nim wpisać właściwe hasło.
3. Utwórz plik `polos\js\random-org\key.js` i wpisz w nim klucz API uzyskany z Random.org. Przykładowa zawartość pliku `key.js` znajduje się w [key.example.js](js/random-org/key.example.js).

Zamiast ręcznego kopiowania plików można skorzystać ze skryptu `_install.bat`. Skopiuje on najnowsze pliki POLos z zewnętrznego serwera (z Github). Ułatwi także przyszłe aktualizacje.

Więcej informacji w [UPDATE.md](UPDATE.md).