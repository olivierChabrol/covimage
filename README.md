# covimage
## Base de données

Pour installer la base de donnée du projet, il faut préalablement avoir installé *Composer* : https://getcomposer.org/download/

Afin de **pouvoir** créer la base de données, saisir dans un terminal :

* composer require symfony/orm-pack
* composer require --dev symfony/maker-bundle

En prenant connaissance du code, modifier le fichier Covimage/.env, ligne 32 colonne 22, avec nomUtilisateur:motDePasse d'utilisateur sql ayant tous les droits sur votre machine.

Afin de **créer** la base de données, saisir dans le terminal :

* php bin/console doctrine:database:create

Cette action va créer sur PHPMyAdmin la base de données "DB_Test". Cependant elle est vide.

Afin de **récupérer les attributs** et tables du code :

* php bin/console make:migration
* php bin/console doctrine:migrations:migrate

On obtient alors la structure de la table qui est décrite dans le code. Il manque toujours les éléments contenus à l'intérieur.

Afin de **charger** dans la base de données ce qui est dans le code php :

* php bin/console doctrine:fixtures:load