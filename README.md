```
 /$$     /$$                                     /$$$$$$$  /$$       /$$$$$$$
|  $$   /$$/                                    | $$__  $$| $$      | $$__  $$
 \  $$ /$$//$$$$$$$   /$$$$$$  /$$    /$$       | $$  \ $$| $$$$$$$ | $$  \ $$
  \  $$$$/| $$__  $$ /$$__  $$|  $$  /$$//$$$$$$| $$$$$$$/| $$__  $$| $$$$$$$/
   \  $$/ | $$  \ $$| $$  \ $$ \  $$/$$/|______/| $$____/ | $$  \ $$| $$____/
    | $$  | $$  | $$| $$  | $$  \  $$$/         | $$      | $$  | $$| $$
    | $$  | $$  | $$|  $$$$$$/   \  $/          | $$      | $$  | $$| $$
    |__/  |__/  |__/ \______/     \_/           |__/      |__/  |__/|__/
```

**Prérequis**

1. Avoir git sur son ordinateur;
2. Avoir Docker Dekstop d'installé

# Pour démarer le Projet : 
1. Clonner le Projet a l'aide de la commande:

```bash
    git clone https://github.com/Kyrozn/Ynov-PHP.git
```

2. Se déplacer a travers les dossier (jusqu'au dossier docker)
```bash
    cd Ynov-PHP/Docker
```
3. Intaller dompdf :
```bash
composer require dompdf/dompdf
```
4. Build le docker avec cette commande :
```bash
    docker compose up
```

5. Accéder a La page initial sur cet url :

    localhost:5050

6. Acceder a la bdd :

    localhost:8080

## Arret :

Si vous souhaitez arréter le docker. 2 possibilité 
- Sois vous arréter le Container sur docker desktop
- Sois vous utiliser la commande :
```bash
docker compose down
```

(Pourriez vous prendre en compte le dernier Push,celui a 00:02, le push a mit beaucoup de temps a s'effectuer sans aucune raison)
