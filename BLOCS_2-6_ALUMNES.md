# DespesesGestió — blocs 2 a 6 (què fem a cada pas)

Aquest document explica, **bloc a bloc**, **què s’espera que aprengueu i implementeu**. No és una llista per copiar sense entendre: és el mapa del projecte, amb **fitxers**, **idees de disseny** i **commits de referència** del mateix repositori.

---

## Branques i commits (aquest repositori)

Les branques `feature/bloc*` són **punts de control**: cada una apunta al **darrer commit** del treball d’aquest bloc. La història és **lineal** (sense merges visibles entre blocs): cada branca següent conté tots els commits anteriors.

| Branca (punter `git`) | Últim commit (`git log -1`) | Què afegeix aquest bloc (commits nous respecte l’anterior)                                                                                                                                                                                                                             |
| --------------------- | --------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `main`                | `18b7d24`                   | Esquelet: landing, `login.php` sense BD, `.gitignore`.                                                                                                                                                                                                                                 |
| `feature/bloc2`       | `3265bef`                   | `d0a060f` (Connexió + Usuari) i `3265bef` (sessió, `logout`, retocs `login` / `index`).                                                                                                                                                                                                |
| `feature/bloc3`       | `a28866c`                   | `a28866c` — `Autenticacio`, esquelet `gestiomoviments` / `crearmoviments`, retocs `login`.                                                                                                                                                                                             |
| `feature/bloc4`       | `dff4c06`                   | `dff4c06` — `inclou/header.php`, `inclou/footer.php`, refactor de les dues pàgines de moviments.                                                                                                                                                                                       |
| `feature/bloc5`       | `16ac157`                   | Després de `dff4c06`, en **ordre cronològic de commits**: primer **categories** (tres commits, contingut del bloc 6 pedagògic) i després **moviments** (dos commits, bloc 5 pedagògic). **No existeix** `feature/bloc6` en aquest remot: tot això és la mateixa línia `feature/bloc5`. |

Ordre **real** dels commits (del més antic al més nou, després de `dff4c06`):  
`8129533` → `8c8a5d7` → `bae6802` → `6da2c38` → `16ac157`.

- **Categories (bloc 6 pedagògic) ja consta al repositori abans que cap commit de moviments:** `8129533`, `8c8a5d7`, `bae6802`.
- **Moviments (bloc 5 pedagògic) venen després:** `6da2c38`, `16ac157`.

**Verificació ràpida amb el git:** `8129533` és ancestre de `6da2c38` (categories primer). Al commit `bae6802` encara **no** existeixen `public/objectes/Moviment.php` ni `public/inclou/llistatMoviments.php`; apareixen a partir de `6da2c38`. En canvi, `Categoria.php` i `gestioCategories.php` ja hi són des de `8129533`.

Això encaixa amb la base de dades: el JOIN i el `<select>` de moviments depenen de la taula **categories** i del codi que la gestiona.

---

## Com llegir aquest document

- **Punt de partida (`main`):** només l’esquelet (`18b7d24`). La solució completa dels blocs 2–6 la trobareu seguint la branca **`feature/bloc5`** (o comprovant cada `feature/blocN` fins on arribi el punter).
- **Commits entre parèntesis** al cos del document: mateix repositori. Si el vostre diff no coincideix al 100 %, és acceptable sempre que el **comportament** i els **conceptes** siguin els mateixos.
- La **numeració docent** (sovint «tema 5 = moviments», «tema 6 = categories») pot ser l’inversa de l’**ordre dels commits** en aquest repo: aquí **sempre** es van commitar categories abans que moviments. Si feu el curs en ordre 5 → 6 igualment, necessitareu dades o codi de categories (dump, etc.) abans de poder provar moviments; en canvi, si cloneu i seguiu la història commit a commit, ja tindreu categories quan arribeu a `6da2c38`.

---

## Bloc 2 — `feature/bloc2`

### Què fem en aquest bloc

Posem **persistència i seguretat bàsica**: la contrasenya no es compara en clar contra la base de dades, sinó amb **hash** (`password_verify`). La sessió recorda **qui ha entrat** i el **rol**. Totes les redireccions van seguides de **`exit`**.

### Al final del bloc hauríeu de poder

- Obrir una connexió **PDO** a MySQL amb errors en excepció i fetch associatiu per defecte.
- Fer **login** llegint l’usuari per nom, comprovant el hash, i guardant `id`, `nom`, `rol` a `$_SESSION`.
- Fer **logout** i tornar a l’índex; a `index.php` veure benvinguda o enllaç a login segons la sessió.

### Pas 1 — commit `d0a060f` (Connexió i usuari)

| Què               | Detall                                                                                                                                                                                                                                                                                                                                                                                                                                                               |
| ----------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Fitxers nous**  | `public/objectes/Connexio.php`, `public/objectes/Usuari.php`                                                                                                                                                                                                                                                                                                                                                                                                         |
| **Connexio**      | Mètode tipus `connectar(): PDO`: DSN `mysql:host=...;dbname=...;charset=utf8mb4`, credencials (p. ex. `getenv('DB_HOST')` amb valors per defecte), `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`, `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`.                                                                                                                                                                                                                |
| **Usuari**        | `require_once` de `Connexio.php`. Patró: classe que **estén** `Connexio` o rep el PDO (com vulgueu). Propietats: `id`, `nom`, `contrasenya`, `rol`. Constructor amb `nom` i `contrasenya` (i opcionalment `rol`). `login(): bool` amb `prepare` / `WHERE nom = :nom`, `fetch`, i si hi ha fila `password_verify($contrasenya_plana, $row['contrasenya'])` → omplir dades, posar `contrasenya` a `null`, retornar `true`. Getters: `getId()`, `getNom()`, `getRol()`. |
| **Base de dades** | Taula `usuaris` (`id`, `nom`, `contrasenya` en bcrypt, `rol`, etc.).                                                                                                                                                                                                                                                                                                                                                                                                 |
| **`.gitignore`**  | Al projecte de referència s’exclou `public/objectes/InicialitzacioBD.php` i fitxers d’entorn (`.env`), per no pujar credencials.                                                                                                                                                                                                                                                                                                                                     |

**Fitxers creats o tocats:** `Connexio.php`, `Usuari.php`, possible retoc a `.gitignore`.

### Pas 2 — commit `3265bef` (Sessió, logout, redireccions)

| Què                    | Detall                                                                                                                                                                                                                                                                   |
| ---------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| **Fitxer nou**         | `public/logout.php` — `session_start()`, `session_destroy()`, `header('Location: index.php')`, `exit`.                                                                                                                                                                   |
| **`public/login.php`** | `session_start()`, `require_once` `Usuari.php`. Si ja hi ha `$_SESSION['id']`, redirigir segons rol (admin → `gestiomoviments.php`, altres → `crearmoviments.php`) i `exit`. En POST vàlid: omplir sessió i redirigir igual; si falla, `$loginError` i alerta Bootstrap. |
| **`public/index.php`** | `session_start()`; si hi ha sessió, benvinguda i enllaç a **Sortir**; si no, enllaç a **login**.                                                                                                                                                                         |

**Fitxers creats o tocats:** `logout.php`; modificacions a `login.php` i `index.php`.

---

## Bloc 3 — `feature/bloc3`

### Què fem en aquest bloc

Centralitzem **qui pot veure què**: una classe **`Autenticacio`** amb mètodes estàtics per comprovar sessió i rol, i **dues pàgines** (encara sense lògica de negoci de moviments) que només obren si les condicions es compleixen.

### Al final del bloc hauríeu de poder

- Evitar duplicar `if (!isset($_SESSION['id'])) { header... }` a cada fitxer: una sola crida tipus `requerirLogin()`.
- Separar rutes **d’administrador** (`gestiomoviments.php`) de les de **usuari** (`crearmoviments.php`), i redirigir amb missatge si un no-admin intenta la zona admin.

### Pas — commit `a28866c` (Autenticació i esquelet)

| Què                    | Detall                                                                                                                                                                                                                |
| ---------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Fitxer nou**         | `public/objectes/Autenticacio.php` — `sessioIniciada()`, `esAdmin()`, `requerirLogin()` (sense sessió → `login.php` + `exit`), `requerirAdmin()` (sense admin → p. ex. `crearmoviments.php?error=no_admin` + `exit`). |
| **Fitxers nous**       | `public/gestiomoviments.php` — `requerirAdmin()`, títol tipus «Gestió de moviments», secció buida o text placeholder.                                                                                                 |
|                        | `public/crearmoviments.php` — `requerirLogin()`, tractar `?error=no_admin` amb alerta Bootstrap.                                                                                                                      |
| **`public/login.php`** | Després del login correcte: admin → `gestiomoviments.php`, la resta → `crearmoviments.php`.                                                                                                                           |

**Fitxers creats o tocats:** `Autenticacio.php`, `gestiomoviments.php`, `crearmoviments.php`, `login.php`.

---

## Bloc 4 — `feature/bloc4`

### Què fem en aquest bloc

**Un sol lloc** per al HTML repetit (capçalera HTML, Bootstrap, navbar, peu): **`include`** amb variables (`$titolPagina`, `$plantillaSeccio`). Les pàgines només defineixen el contingut del mig.

### Al final del bloc hauríeu de poder

- Afegir una pàgina nova sense copiar tot el `<!DOCTYPE>` ni els enllaços del menú: només `require` del header, cos propi, `require` del footer.

### Pas — commit `dff4c06` (Plantilla compartida)

| Què              | Detall                                                                                                                                                                                                                                                                                                                                                                                             |
| ---------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Fitxers nous** | `public/inclou/header.php` — document fins a `<main>`; `<?= htmlspecialchars($titolPagina) ?>`; CDN Bootstrap CSS; navbar amb nom d’usuari, enllaços segons rol, **Sortir** → `logout.php`. Variable `$plantillaSeccio` (`'moviments' \| 'categories' \| null`) per remarcar la pestanya activa (classes Bootstrap). El header pot fer `require_once` d’`Autenticacio.php` per saber si sou admin. |
|                  | `public/inclou/footer.php` — tancar `</main>`, peu amb any `date('Y')`, script Bootstrap bundle, `</body></html>`.                                                                                                                                                                                                                                                                                 |
| **Modificar**    | `public/gestiomoviments.php` i `public/crearmoviments.php` — definir `$titolPagina` i `$plantillaSeccio`, `require` header, contingut dins `container`, `require` footer.                                                                                                                                                                                                                          |

**Fitxers creats o tocats:** `inclou/header.php`, `inclou/footer.php`; refactor de `gestiomoviments.php` i `crearmoviments.php`.

---

## Bloc 5 — `feature/bloc5` (moviments: dues fases a la solució)

> **Ordre al repositori:** els commits d’aquest apartat (`6da2c38`, `16ac157`) van **després** dels tres commits de **categories** (`8129533` … `bae6802`). A la secció «Bloc 6» més avall es descriu el que ja ha passat abans en temps de git.

### Què fem en aquest bloc (visió global)

1. **Llistar** moviments des de la BD amb **PDO** i una classe **`Moviment`**, amb **filtre per rol** (admin ve tot; usuari només `id_usuari` = sessió). **JOIN** amb `categories` per mostrar el nom de la categoria. **Eliminar** només des de la zona admin, amb POST segur.
2. **Ampliar** amb **alta, edició i baixa** des de formularis: validació, separació d’includes (`formulariMoviment`, processament POST, alertes), i regles diferents per admin (pot triar usuari del moviment) i usuari (els seus moviments).

### Al final del bloc hauríeu de poder

- Reutilitzar el mateix **llistat** a `gestiomoviments.php` i `crearmoviments.php` amb un **`require`** comú.
- Entendre per què l’**eliminació** i certes accions només es processen a la pàgina **admin**.

### Pas 1 — commit `6da2c38` (model, llistat i esborrat admin)

| Què                       | Detall                                                                                                                                                                                                                                                        |
| ------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Base de dades**         | Taula `moviments` (`id`, `id_usuari`, `id_categoria`, `concepte`, `import`, `data`) amb FK a `usuaris` i `categories`. Calen **categories** existents per al JOIN.                                                                                            |
| **Fitxer nou**            | `public/objectes/Moviment.php` — `llistarAmbCategoria(?int $nomesIdUsuari)` amb JOIN; `eliminar(int $id)`.                                                                                                                                                    |
| **Fitxer nou**            | `public/inclou/llistatMoviments.php` — instanciar model, cridar `llistarAmbCategoria($esAdmin ? null : (int) $_SESSION['id'])`, taula Bootstrap, **htmlspecialchars**; si admin, formulari POST `accio=eliminar` cap a `gestiomoviments.php` amb `confirm()`. |
| **`gestiomoviments.php`** | `requerirAdmin()`, instanciar `Moviment`, processar POST `eliminar`, missatge `$alerta`, `require` del llistat.                                                                                                                                               |
| **`crearmoviments.php`**  | `requerirLogin()`, missatge opcional `no_admin`, mateix `require` del llistat (sense processar esborrat aquí).                                                                                                                                                |

**Fitxers creats o tocats:** `Moviment.php`, `llistatMoviments.php`, `gestiomoviments.php`, `crearmoviments.php`.

### Pas 2 — commit `16ac157` (formulari crear / editar / eliminar i model ampliat)

| Què                | Detall                                                                                                                                                                                                           |
| ------------------ | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **`Moviment.php`** | P. ex. `validarEntrada`, `importPerGuardar`, `crear`, `obtenirPerId`, `actualitzar`, `llistarUsuarisPerSelect` (per admin).                                                                                      |
| **Includes nous**  | `inclou/processarMovimentsAccions.php` — POST `crear` / `actualitzar` / `eliminar` segons mode `admin` o `usuari`.                                                                                               |
|                    | `inclou/formulariMoviment.php`, `prepararVariablesFormulariMoviment.php`, `alertaMoviments.php`, `carregarMovimentEditarGet.php` — separar vista, preparació de valors del formulari i GET `?editar=id` (admin). |
| **Pàgines**        | `gestiomoviments.php` i `crearmoviments.php` carreguen `Categoria` (llista per `<select>`), integren el formulari i els includes anteriors; el llistat pot afegir enllaç **Editar** (admin).                     |

**Fitxers creats o tocats:** `Moviment.php` (ampliat), `gestiomoviments.php`, `crearmoviments.php`, `llistatMoviments.php`, nous fitxers sota `public/inclou/` esmentats.

---

## Bloc 6 (categories, només admin) — commits `8129533` … `bae6802` a la branca `feature/bloc5`

> **Ordre al repositori:** aquest treball es va commitar **abans** que `Moviment.php` i el llistat/CRUD de moviments (`6da2c38`, `16ac157`). És el que explica la secció «Bloc 5» més amunt en temps de git, tot i que aquí mantenim la numeració 5 = moviments i 6 = categories.

### Què fem en aquest bloc

**CRUD de categories** (a la BD el camp sol dir-se `nom`; a la interfície es pot mostrar com a **Descripció**). Només **admin**. Es treballa validació, **UNIQUE**, comprovació abans d’esborrar si hi ha **moviments** que usen la categoria, i un **mateix formulari** per crear i editar.

### Al final del bloc hauríeu de poder

- Llistar, crear, editar i eliminar categories amb missatges clars (èxit, error, duplicat, categoria en ús).
- Enllaçar la gestió de categories des del menú d’admin.

### Pas 1 — commit `8129533` (pàgina de llistat i eliminació; model complet al repositori)

| Què                            | Detall                                                                                                                                                                                                                                                                                                                |
| ------------------------------ | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **`Categoria.php`**            | A la solució de referència aquest commit ja inclou `llistarTotes`, `obtenirPerId`, `crear`, `actualitzar` i `eliminar` (aquest últim comprova moviments dependents abans del `DELETE`). Si ho feu per micro-passos, podeu començar només amb `llistarTotes` + `eliminar` al model i afegir la resta als passos 2 i 3. |
| **`gestioCategories.php`**     | Només admin: taula amb ID i descripció, POST `accio=eliminar` amb `confirm()`, missatges d’èxit o error si la categoria té moviments.                                                                                                                                                                                 |
| **`public/inclou/header.php`** | Per a admin: enllaços a **`gestiomoviments.php`** i **`gestioCategories.php`**, amb estil actiu segons `$plantillaSeccio === 'moviments'` o `'categories'` (no és només un enllaç a categories: són les dues pestanyes de gestió).                                                                                    |

**Fitxers creats o tocats:** `Categoria.php`, `gestioCategories.php`, `inclou/header.php`.

### Pas 2 — commit `8c8a5d7` (alta amb validació i UNIQUE)

| Què                        | Detall                                                                                                                                                                     |
| -------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **`gestioCategories.php`** | Card «Afegir categoria», POST `accio=crear`, camp `nom`: `trim`, buit o massa curt → alerta; `try/catch` amb `PDOException` i `errorInfo[0] === '23000'` per nom duplicat. |
| **Base de dades**          | Índex **UNIQUE** sobre `categories.nom` (o el camp que useu).                                                                                                              |

**Fitxers creats o tocats:** sobretot `gestioCategories.php` i, si cal, migració / SQL de l’índex UNIQUE.

### Pas 3 — commit `bae6802` (edició i un sol formulari crear/editar)

| Què                        | Detall                                                                                                                                          |
| -------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------- |
| **`Categoria.php`**        | (Si encara no hi són al vostre pas 1) `obtenirPerId`, `actualitzar`.                                                                            |
| **`gestioCategories.php`** | GET `?editar=id`, mateixa card amb mode alta o edició, `accio=actualitzar`, botó Cancel·lar; mateixa validació i tractament UNIQUE que a crear. |

**Fitxers creats o tocats:** `Categoria.php` (si cal), `gestioCategories.php`.

---

## Taula resum (docent / alumne)

| Bloc | Punter de branca (aquest repo)                             | Què s’aconsegueix                                        | Fitxers o carpetes clau                                                       | Commits                                                               |
| ---- | ---------------------------------------------------------- | -------------------------------------------------------- | ----------------------------------------------------------------------------- | --------------------------------------------------------------------- |
| 2    | `feature/bloc2` → `3265bef`                                | PDO, usuari amb hash, sessió, logout                     | `objectes/Connexio.php`, `Usuari.php`, `logout.php`, `login.php`, `index.php` | `d0a060f`, `3265bef`                                                  |
| 3    | `feature/bloc3` → `a28866c`                                | Protecció de rutes i rols                                | `Autenticacio.php`, `gestiomoviments.php`, `crearmoviments.php`               | `a28866c`                                                             |
| 4    | `feature/bloc4` → `dff4c06`                                | Plantilla HTML + Bootstrap                               | `inclou/header.php`, `inclou/footer.php`                                      | `dff4c06`                                                             |
| 5    | `feature/bloc5` → `16ac157` (darrers commits de la branca) | Moviments: llistat, rol, JOIN; després CRUD amb includes | `Moviment.php`, `inclou/llistatMoviments.php`, includes de formulari/accions  | `6da2c38`, `16ac157` (**després** de la fila «6» en temps de commit)  |
| 6    | mateixa branca `feature/bloc5` (sense `feature/bloc6`)     | CRUD categories (admin)                                  | `Categoria.php`, `gestioCategories.php`, `header.php`                         | `8129533`, `8c8a5d7`, `bae6802` (**abans** dels commits de moviments) |

Convé **tancar i provar** cada bloc (o cada pas dins del bloc 5 i 6) abans de continuar.
