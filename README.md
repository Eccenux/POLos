POLos
=====

POLos, to system pomocniczy do pierwszego etapu wyłaniania panelu obywatelskiego -- losowania zaproszeń dla wyborców.

POLos jest podzielony na 3 etapy:
1. **Import danych** -- przesłanie danych osób oraz wprowadzenie kryteriów losowania (profili).
2. **Losowanie** -- losowanie za pomocą usługi Random.org.
3. **Eksport danych** -- eksport danych wylosowanych osób wraz ze specjalnym identyfikatorem, który powinien zostać dołączony do zaproszenia.

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
3. Wyświetlenie liczby przetworzonych i liczby nieprzetworzonych (np. nieznana dzielnica).

Import odbywa się z użyciem plików CSV, które można wyeksportować z bazy danych lub z arkusza Excel czy Open Office.

Ważne! **Pliki XLS (Excel) mają ograniczenie do niecałych 66 tys**. wierszy, a w starszych wersjach Excel jeszcze mniej. Nie można w nich zatem przechowywać spisu wyborców dużego miasta. Należy unikać tych plików przy przetwarzaniu dużej ilości danych.

Można jednak podzielić dane na części. POLos obsługuje przesyłanie paru plików z danymi osobowymi.

### Losowanie ###

1. Dla każdego profilu:
	1. Ustalana jest lista osób pasujących do profilu zaproszenia i sortowana np. wg PESEL.
	2. Z tej listy (a nie z całego rejestru) losowane są zaproszenia dla danej grupy.
	3. Dane do weryfikacji losowania są zapisywane.
	4. Wylosowanym osobom nadawane są identyfikatory.
2. Po losowaniu można sprawdzić jego przebieg i zweryfikować, że losowanie zostało wykonane za pomocą Random.org (losowania mają podpis za pomocą certyfikatu pochodzącego z Random.org).

Przy generowaniu identyfikatorów pomijane są mylące znaki np. „I” (jak Igor), „l” (jak lampa).

### Eksport danych ###

1. Z programu eksportowane są dane adresowe wraz z identyfikatorem służące później do wydruku zaproszeń.
2. Osobny eksport jest wykonywany dla systemu ankietowego. W tym eksporcie nie ma pełnych danych osobowych, tylko identyfikator oraz imię. Dodatkowo eksportowana jest dzielnica.

Wydruk zaproszeń **nie** jest częścią POLos.

Wymagania techniczne
--------------------

Ze względu na przetwarzanie dużej ilości danych program działa w środowisku serwerowym.

Za serwer może posłużyć w miarę dowolny, współczesny komputer. Zalecane parametry:
* CPU: 3 GHz, quad, 64-bit.
* RAM: 8 GB.
* Dysk: 20 GB wolnego miejsca.

Wymagane programy:
* Apache 2.2 lub nowszy.
* PHP 5.3 lub nowszy.
* MySQL 5.0 lub nowszy.