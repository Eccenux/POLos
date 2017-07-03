POLos
=====

POLos, to system pomocniczy do pierwszego etapu wyłaniania panelu obywatelskiego -- losowania zaproszeń dla wyborców.

POLos jest podzielony na 3 etapy:
1. **Import danych** -- przesłanie danych osób oraz wprowadzenie kryteriów losowania (profili).
2. **Losowanie** -- losowanie za pomocą usługi Random.org.
3. **Eksport danych** -- eksport danych wylosowanych osób wraz ze specjalnym identyfikatorem, który powinien zostać dołączony do zaproszenia.

Instrukcje dodatkowe:
* Informacje o wstępnej instalacji serwera i konfiguracji serwera → [INSTALL.md](INSTALL.md).
* Informacje o instalacji skryptów i aktualizacji POLos → [UPDATE.md](UPDATE.md).
* Informacje o rozwiązywaniu problemów → [HELP.md](HELP.md).

Poniżej natomiast znajdziesz opis przebiegu działania programu oraz szczegółowy opis wymagań.

Licencja
---------

**Program został sfinansowany ze środków Gminy Miasta Gdańsk** i udostępniony na otwartej licencji CC-BY 4.0. Patrz: [LICENSE.md](LICENSE.md).

Copyright © 2017 Maciej "Nux" Jaros, Gmina Miasta Gdańsk.

Założenia
---------

* Dane są losowane z dużego zbioru danych osobowych (np. listy wyborców).
* Profile są złożone z danych, które można ustalić z danych osobowych -- płeć, wiek, dzielnica.
* Do każdego profilu podana jest liczba zaproszeń do wylosowania.  

Przebieg programu
-----------------

### Import danych ###

1. Przetworzenie listy profili zaproszenia, w tym liczby zaproszeń do wylosowania.
2. Przetworzenie rejestru w celu ustalenia płci, wieku, dzielnicy oraz danych adresowych.
3. Wyświetlenie liczby przetworzonych i liczby nieprzetworzonych (np. nieprawidłowy PESEL).

Import odbywa się z użyciem plików CSV, które można wyeksportować z bazy danych lub z arkusza Excel czy Open Office.

Ważne! **Pliki XLS (Excel) mają ograniczenie do niecałych 66 tys**. wierszy, a w starszych wersjach Excel jeszcze mniej. Nie można w nich zatem przechowywać spisu wyborców dużego miasta. Należy unikać tych plików przy przetwarzaniu dużej ilości danych.

Można jednak podzielić dane na części. POLos obsługuje przesyłanie paru plików z danymi osobowymi.

Mimo wszystko zalecane jest korzystanie z Open Office, ponieważ lepiej obsługuje pliki CSV. Zwłaszcza otwieranie wyeksportowanych plików CSV.

### Losowanie ###

1. Dla każdego profilu:
	1. Ustalana jest lista osób pasujących do profilu zaproszenia i sortowana np. wg PESEL.
	2. Z tej listy (a nie z całego rejestru) losowane są zaproszenia dla danej grupy.
	3. Dane do weryfikacji losowania są zapisywane.
2. Po losowaniu można sprawdzić jego przebieg i zweryfikować, że losowanie zostało wykonane za pomocą Random.org (losowania mają podpis za pomocą certyfikatu pochodzącego z Random.org).

### Eksport danych ###

1. Z programu eksportowane są dane adresowe wraz z identyfikatorem służące później do wydruku zaproszeń.
2. Osobny eksport jest wykonywany dla systemu ankietowego. W tym eksporcie nie ma pełnych danych osobowych, tylko identyfikator oraz imię. Dodatkowo eksportowana jest dzielnica.
3. Wylosowanym osobom nadawane są identyfikatory.

Przy generowaniu identyfikatorów pomijane są mylące znaki np. „I” (jak Igor), „l” (jak lampa).

Wydruk zaproszeń **nie** jest częścią POLos.

Wymagania techniczne
--------------------

Ze względu na przetwarzanie dużej ilości danych program działa w środowisku serwerowym.

### Sprzęt ###

Za serwer może posłużyć w miarę dowolny, współczesny komputer. Zalecane parametry:
* CPU: 3 GHz, quad, 64-bit.
* RAM: 8 GB.
* Dysk: 20 GB wolnego miejsca. Uwaga! Zalecana jest instalacja na dysku typu SSD!

### Oprogramowanie ###

Wymagane programy:
* Apache 2.2 lub nowszy.
* PHP 5.3 lub nowszy. Wymagane rozszerzenie `mysql`.
* MySQL 5.1 lub nowszy. Uwaga! Musi być skonfigurowany na obsługę UTF-8.
* Appache Open Office lub Libre Office do obsługi plików CSV. MS Excel może sobie nie poradzić z kodowaniem UTF-8.

### Uwagi do instalacji ###

Na dysku SSD (lub innym szybkim dysku) powinny być przede wszystkim:
* Folder tymczasowy PHP (określony przez `upload_tmp_dir` w pliku `php.ini`).
* Folder danych MySQL (określony przez `datadir` w pliku `my.ini`).

Należy pamiętać, że MySQL musi być skonfigurowany tak, żeby obsługiwać kodowanie UTF-8. Inaczej może być problem z wyświetlaniem polskich znaków.

Na stronie `sys-test.php` znajdują się wymagania co do niektórych parametrów PHP oraz MySQL. Zalecane wartości tam podane są przewidziane na przetwarzanie plików CSV między 20-30 MB. W razie wątpliwości lepiej jest wybrać większe wartości.

Więcej informacji o instalacji oraz informacje o typowych problemach znajdują się w [INSTALL.md](INSTALL.md).