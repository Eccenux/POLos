Typowe problemy
---------------

### Apache nie uruchamia się ###

1. Upewnij się, że ścieżka do pliku `php5apache2_2.dll` jest prawidłowa.
2. Upewnij się, że folder podany w dyrektywie `DocumentRoot` w `httpd.conf` istnieje.
3. Upewnij się, że port podany w dyrektywie `Listen` w `httpd.conf` jest wolny. Można to zrobić poleceniem `netstat -aon | findstr 80` (zakładając, że `80` to numer portu). 

### PHP nie uruchamia się ###
1. Upewnij się, że w `conf\mime.types` znajdują się wpisy:
```
application/x-httpd-php	php
application/x-httpd-php-source	phps
```
2. Upewnij się także, że w pliku `httpd.conf` znajdują się polecenia `PHPIniDir` oraz `LoadModule php5_module` podane powyżej.
3. Upewnij się też, że plik `php5apache2_2.dll` jest w folderze PHP.

### Import nie działa ###

Spróbuj najpierw z importu mniejszej ilości danych (np. pierwsze 1000 wierszy). POLos powinien wyświetlić komunikat z informacją co jest nie tak z danymi. 

Jeśli nie działa import dużych plików:
1. W `php.ini` powiększ `upload_max_filesize` tak by był większy niż plik CSV.
2. W `php.ini` powiększ `post_max_size` tak by był przynajmniej o 10M większy od `upload_max_filesize`.
3. W `php.ini` powiększ `memory_limit` (zwłaszcza jeśli w logu PHP pojawia się komunikat o problemie przydziału pamięci).
4. W `my.ini` powiększ `max_allowed_packet` na około 2 razy więcej niż plik CSV. 

### Import działa zbyt wolno ###

Import danych osobowych dla dużego miasta może trwać bardzo długo na powolnym lub silnie obciążonym komputerze. To znaczy import 100 tysięcy może nawet trwać godzinę, podczas gdy na szybkim, dobrze skonfigurowanym komputerze potrwa minutę. Dlatego zalecane jest przetwarzanie danych na dysku SSD. 
 
Na dysku SSD (lub innym szybkim dysku) powinny być przede wszystkim:
* Folder tymczasowy PHP (określony przez `upload_tmp_dir` w pliku `php.ini`).
* Folder danych MySQL (określony przez `datadir` w pliku `my.ini`).

Można także spróbować dostosować konfigurację MySQL (w pliku `my.ini`) tak, żeby używała więcej pamięci RAM. Można w tym celu skorzystać z pliku `my-huge.ini` (z folderu MySQL). Ale można go wykorzystać jedynie jako wzorzec (wskazówki), bo może zawierać inne ścieżki niż używany plik `my.ini`.

### W nazwach dzielnic nie ma polskich znaków ###

1. Sprawdź kodowanie plików CSV. Powinny być zapisanie jako pliku UTF-8. Najlepiej użyć Apache Open Office do tworzenia plików CSV.
2. Należy pamiętać, że MySQL musi być skonfigurowany tak, żeby obsługiwać kodowanie UTF-8. Najlepiej to wybrać podczas instalacji.

| <a href="https://raw.github.com/Eccenux/POLos/master/images/screen-oo-save-as-csv.png" target="_blank"><img style="width:300px" src="https://raw.github.com/Eccenux/POLos/master/images/screen-oo-save-as-csv.png" alt="Zapisz jako CSV"></a> | <a href="https://raw.github.com/Eccenux/POLos/master/images/screen-oo-save-as-csv-utf8.png" target="_blank"><img style="width:300px" src="https://raw.github.com/Eccenux/POLos/master/images/screen-oo-save-as-csv-utf8.png" alt="Zapisz z kodowaniem UTF-8"></a> |
|---------------------------|-------------------------|
| Zapisz jako plik typu CSV | Wybierz kodowanie UTF-8 |

### Nie udaje się import niektórych wierszy ###

Podczas importu wykonywana jest walidacja danych. Odrzucane są m.in. wiersze z nieprawidłowym zakresem wieku (w wypadku profili), czy z nieprawidłowym PESEL (w wypadku danych osobowych).

Błędne dane można sprawdzić w bazie danych. Do wykonywania zapytań SQL można np. skorzystać z [phpMyAdmin](https://www.phpmyadmin.net/downloads/).

Zapytanie do sprawdzania błędnych rekordów profili:
```sql
SELECT *
FROM profile
WHERE row_state <> 0
```

Sprawdzenie błędnych rekordów osobowych w ten sposób jest obecnie niemożliwe, ponieważ import błędnych wierszy został wyłączony ze względów wydajnościowych. Jeśli przy wstawianiu danych osobowych do bazy pojawi się błąd, to zrzut zapytania SQL powinien pojawić się w `polos\temp\last.personal.sql`.

Żeby spróbować sprawdzić, które rekordy osobowe nie zostały zaimportowane można spróbować zrobić pełny eksport danych osobowych za pomocą poniższego zapytania. Wyniki zapytania można wyeksportować do pliku CSV i porównać z importowanym plikiem.
```sql
SELECT `region`, `pesel`, `name`, `surname`, `city`, `street`, `building_no`, `flat_no`, `zip_code` 
FROM personal
ORDER BY id ASC
```

Żeby wykonać eksport wyników zapytania w phpMyAdmin, musisz wykonać zapytanie, a potem **pod tabelką wyników** wybrać operację eksportu.

Inne problemy i diagnostyka
---------------------------
1. Na stronie `sys-test.php` znajdują się wymagania co do niektórych parametrów PHP oraz MySQL. Sprawdź czy wszystko się zgadza. Pamiętaj, że zalecane wartości tam podane są przewidziane na przetwarzanie plików CSV między 20-30 MB. W razie wątpliwości lepiej jest wybrać większe wartości. 
2. Sprawdź zawartość pliku z błędami POLos (w wypadku złapanych błędów w folderze polos utworzy się `.err.log`).
3. Sprawdź zawartość pliku z błędami PHP (systemowe błędy PHP powinny pojawić się w pliku wskazanym przez opcję `error_log`; zazwyczaj nazywa się `php-errors.log` lub `php_errors.log`).
4. Sprawdź zawartość pliku z błędami Apache (błędy uruchamia i funkcjonowania Apache powinny pojawić się w pliku `logs\error.log`, w folderze Apache).

