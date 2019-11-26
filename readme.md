# Dépendances :

* Symfony 4.3
* Php 7.3
* Composer
* Yarn
* MySQL

*(Vous retrouverez les procédures d'installation ci-dessous)*

# Procédure de build :

### Installation de php

Installation de php nécessaire (Version recommandée : 7.3)
```bash
sudo apt install php7.3
```
### Installation des dépendances de *php*

#### Pour *Linux*

```bash
sudo apt install php7.3-curl php7.3-gd php7.3-intl php7.3-json php7.3-mbstring php7.3-xml php7.3-zip
```

#### Pour *MacOS*

```bash
brew install php7.3-curl php7.3-gd php7.3-intl php7.3-json php7.3-mbstring php7.3-xml php7.3-zip
```

### Installation de **composer**

#### Pour *Linux*

```bash
sudo apt-get install composer
```

#### Pour *MacOs*

```bash
brew install composer
```

### Installation de **yarn**

#### Pour *Linux*

```bash
sudo apt-get install yarn
```

#### Pour *MacOs*

```bash
brew install yarn
```

## Installation des modules du projet Symfony

```bash
make install
```

## Base de données

Le projet fonctionne avec une base de données MySQL.

### Installation de MySQL
```bash
sudo apt-get install mysql-server libqt5-sql-mysql
```

### Configuration de la base de donnée :

Modifiez ou ajoutez cette ligne dans le fichier *.env* à la racine du projet:
```bash
DATABASE_URL=mysql://nom_utilisateur:mdp_de_votre_database@127.0.0.1:3306/quiz
```

Création de la base de donnée et importation des fixtures:
```bash
bin/console doctrine:database:create
bin/console doctrine:schema:create
bin/console doctrine:fixtures:load
```

## Pour compiler les assets 

```bash
make encore
```

Pour que les changements dans les assets soient compilés automatiquement :

```bash
make encore-watch
```

## Pour démarrer le serveur

```bash
make server
```

## Pour nettoyer le cache de symfony

```bash
make cc
```

# Lien vers la VM

Depuis le réseau de la fac ou à l'aide du VPN :