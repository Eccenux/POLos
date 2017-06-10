@ECHO OFF
set MYSQLcmd=c:\Programy\Serwerowate\MySQL\bin\mysql -uroot -p123 --database=polos_db

echo INSERT
rem %MYSQLcmd% < ids.sql
%MYSQLcmd% < personal.sql
%MYSQLcmd% < profile.sql

echo Koniec
rem %MYSQLcmd% -e "(select count(*) as licznik, 'profile' as tabela from profile) UNION (select count(*), 'ids' from ids) UNION (select count(*), 'personal' from personal)"
%MYSQLcmd% -e "(select count(*) as licznik, 'profile' as tabela from profile) UNION (select count(*), 'personal' from personal)"

PAUSE