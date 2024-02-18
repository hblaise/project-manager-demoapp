# Projekt manager demo app

## Használat

Az alkalmazás alapvetően Docker segítségével futtatható a legkönnyebben, ehhez a repository-ban található egy docker-compose.yml fájl.
Futtatáshoz az alkalmazás gyökérkönyvtárába kell navigálni parancssorban, majd futtatni a `docker-compose up -d` parancsot.
Miután a konténerek elindultak, adjunk legalább 10-20 másodpercet a konténereknek, hogy működőképes állapotba kerüljenek, különben `502 Bad Gateway` hibát fogunk kapni.
Első alkalommal történő indítást követően be kell lépni adatbázis manager alkalmazással az adatbázisba.
Az adatbázis szervert a `localhost:3306` címen érjük el, az adatbázis szerverhez tartozó felhasználó és jelszó pedig az `app/config/database.json-ban`, iletve a gyökérkönyvtárban található `docker-compose.yml` fájlban található, amelyek tetszés szerint módosíthatóak.
A kapcsolódáshoz JDBC Driver használata esetén szükséges az `allowPublicKeyRetrieval = true` kapcsolati paraméter beállítása.
Adatbázis szerverhez sikeresen kapcsolódva, először hozzuk létre a `database.json` és `docker-compose.yml` konfigurációs fájlban meghatározott adatbázist (alapesetben ez a welove_test).
Amennyiben szeretnénk más adatbázis nevet megadni, mind a két konfigurációs fájlban tegyük meg a változtatást.
Ezt követően az alkalmazás gyökérkönyvtárában található init.sql fájlt futtatnunk kell adatbázis kezelőben, amely létre fogja hozni az adatbázis szerkezetét, illetve beilleszti az alapadatokat.
Az alkalmazást a <http://localhost> url-en lehet elérni.
Az alkalmazás használatának befejeztével a konténerek leállításához navigáljunk újra az alkalmazás gyökérkönyvtárába parancssorban és futtassuk a `docker-compose down` parancsot.

Amennyiben az alkalmazást Docker nélkül szeretnénk futtatni, úgy a futtató gépen szükség lesz a következőkre (illetve ezeknek a megfelelő konfigurációjára):

- Composer
- Nginx
- PHP 8.3 (szükséges kiegészítők: pdo_mysql)

Mindenből a legfrisebb verziót érdemes telepíteni. Az alkalmazás belépő pontja az `app/public/index.php` fájl.
