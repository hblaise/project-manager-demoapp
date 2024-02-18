# Projekt manager demo app

## Futtatás Docker segítségével

Az alkalmazás alapvetően Docker segítségével futtatható a legkönnyebben, ehhez a repository-ban található egy docker-compose.yml fájl.
Futtatáshoz az alkalmazás gyökérkönyvtárába kell navigálni parancssorban, majd futtatni a `docker-compose up -d` parancsot.
Miután a konténerek elindultak, adjunk legalább 10-20 másodpercet a konténereknek, hogy működőképes állapotba kerüljenek, különben `502 Bad Gateway` hibát fogunk kapni.
Az adatbázis automatikusan elkészül, majd az init.sql lefuttatásra kerül, ezzel létre jön az adatbázis szerkezete, majd feltöltésre kerül alapadatokkal.
Az adatbázis szervert a `localhost:3306` címen érjük el, az adatbázis szerverhez tartozó felhasználó és jelszó pedig az `app/config/database.json-ban`, iletve a gyökérkönyvtárban található `docker-compose.yml` fájlban található, amelyek tetszés szerint módosíthatóak.
Adatbázis kezelővel való kapcsolódáshoz, JDBC Driver használata esetén szükséges az `allowPublicKeyRetrieval = true` kapcsolati paraméter beállítása.
Az alkalmazást a <http://localhost> url-en lehet elérni.
Az alkalmazás használatának befejeztével a konténerek leállításához navigáljunk újra az alkalmazás gyökérkönyvtárába parancssorban és futtassuk a `docker-compose down` parancsot.

Az alkalmazás futtatásához a futtató környezetben szükséges, hogy a következő portok szabadok legyenek:

- 80-as port a webszerver számára
- 3306 a MySQL számára

Abban az esetben, ha ezek a portok nem szabadok, úgy a gyökérkönyvtárban található `docker-compose.yml` fájlban módosítani kell a külső portot/portokat szabad portokat használva.

## Futtatás Docker nélkül

Amennyiben az alkalmazást Docker nélkül szeretnénk futtatni, úgy a futtató gépen szükség lesz a következőkre (illetve ezeknek a megfelelő konfigurációjára):

- Composer
- Nginx
- PHP 8.3 (szükséges kiegészítők: pdo_mysql)
- MySQL (adatbázis: welove_test)

Létre kell hozni a `app/config/database.json` konfigurációs fájlban meghatározott adatbázist (alapesetben ez a welove_test) vagy szükség szerint módosítani kell a konfigurációs fájlt, ha másik adatbázist szeretnénk használni.
Ezt követően az alkalmazás gyökérkönyvtárában található init.sql fájlt futtatnunk kell az adatbázis szerveren a megfelelő adatbázisban, amely létre fogja hozni az adatbázis szerkezetét, illetve beilleszti az alapadatokat.
Mindenből a legfrisebb verziót érdemes telepíteni. Az alkalmazás belépő pontja az `app/public/index.php` fájl.
