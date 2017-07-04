Przygotowanie do szybkiej instalacji
-------------------------------------

Żeby móc wykonywać szybkie instalacje (i aktualizacje) potrzebne jest dodatkowe oprogramowanie.

Instalacja nie jest skomplikowana, ale musi być wykonana w sposób uważny. Dlatego przygotowałem filmy instruktażowe, które pokazują jak zainstalować wymagane oprogramowanie (Git) i wykonać pierwszą, automatyczną instalację skryptów → **[playlista "POLos" na Youtube](https://www.youtube.com/watch?v=LwT5XqH1_AE&list=PLEy8lmAN1vwS0brKQ0NFzTTKWTdJy4Uvz)**.


Jeśli plik `_install.bat` nie uruchamia się, tylko otwiera się notatnik, to za pewne jest źle zapisany. Niektóre wersje Windows ukrywają prawdziwe rozszerzenia plików, co może powodować takie problemy.

### Pokazanie prawdziwego rozszerzenia w Windows 10 ###

Żeby zobaczyć prawdziwe rozszerzenia trzeba zmienić ustawienia wyświetlania (widoku) Windows Explorera.

| Ukryte rozszerzenie | Prawdziwe rozszerzenie |
|---------------------------|-------------------------|
| <a href="https://raw.github.com/Eccenux/POLos/master/.doc/images/install-bat-extension-see-1.png" target="_blank"><img style="width:300px" src="https://raw.github.com/Eccenux/POLos/master/.doc/images/install-bat-extension-see-1.png" alt="Ukryte rozszerzenie"></a> | <a href="https://raw.github.com/Eccenux/POLos/master/.doc/images/install-bat-extension-see-2.png" target="_blank"><img style="width:300px" src="https://raw.github.com/Eccenux/POLos/master/.doc/images/install-bat-extension-see-2.png" alt="Prawdziwe rozszerzenie"></a> |

### Zmiana rozszerzenia ###

Rozszerzenie zmienia się tak jak zmienia się nazwę. Wystarczy zaznaczyć plik i wcisnąć F2. 

| Potwierdzenie zmiany rozszerzenia | Poprawne rozszerzenie |
|---------------------------|-------------------------|
| <a href="https://raw.github.com/Eccenux/POLos/master/.doc/images/install-bat-extension-change-1.png" target="_blank"><img style="width:300px" src="https://raw.github.com/Eccenux/POLos/master/.doc/images/install-bat-extension-change-1.png" alt="Ukryte rozszerzenie"></a> | <a href="https://raw.github.com/Eccenux/POLos/master/.doc/images/install-bat-extension-change-2.png" target="_blank"><img style="width:300px" src="https://raw.github.com/Eccenux/POLos/master/.doc/images/install-bat-extension-change-2.png" alt="Prawdziwe rozszerzenie"></a> |


Pierwsza instalacja
-------------------

Zalecane jest by początkową instalację przeprowadził informatyk. Szczegółowy opis niezbędnych komponentów serwerowych i konfiguracji znajduje się w [INSTALL.md](INSTALL.md).

Po instalacji wymaganego oprogramowania i wstępnej konfiguracji można skorzystać ze skryptu `_install.bat`. Skopiuje on najnowsze pliki POLos z zewnętrznego serwera (z Github). Ułatwi także przyszłe aktualizacje.

Aktualizacje skryptów
---------------------

### Kiedy robić aktualizację? ###

Późniejsze aktualizacje można próbować wykonać samodzielnie. Ważne jest jednak, żeby nie wykonywać aktualizacji bez potrzeby. Wykonywanie aktualizacji nie jest zalecane jeśli wszystko działa jak trzeba.

### Archiwizacja przed aktualizacją ###

**W trakcie aktualizacji mogą zostać nadpisane niektóre pliki**. Jeśli wprowadziłeś(-aś) jakieś własne zmiany w skryptach, to musisz wykonać ich kopię. Jeśli nie masz pewności, to najlepiej wykonaj archiwizację folderu `polos`.

W celu wykonania archiwizacji wystarczy wejść do folderu instalacyjnego (domyślny folder poniżej), i w menu pod prawoklikiem wybrać "Wyślij do" → "Folder skompresowany (zip)".   
```
C:\Program Files (x86)\Apache Software Foundation\Apache2.2\htdocs\polos 
```

### Aktualizacja ###

Sama aktualizacja jest bardzo prosta jeśli wszystko jest już skonfigurowane. Jeśli masz już Git, to wystarczy uruchomić `_install.bat`.

Jeśli nie masz jeszcze Git (albo nie wiesz co to jest), to obejrzyj filmy instruktażowe na **[playliście "POLos" na Youtube](https://www.youtube.com/watch?v=LwT5XqH1_AE&list=PLEy8lmAN1vwS0brKQ0NFzTTKWTdJy4Uvz)**.
