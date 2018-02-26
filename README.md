*********************************************
**University of Ottawa - CSI3540**

**Projet de session**

Auteur : [William LaRocque](https://github.com/wlarok24)

Numéro étudiant : 8397424
*********************************************
## Idée pour le projet
Mon idée pour le projet est de créer un web app pour permettre de gérer notre inventaire de certains items dans la maison pour que le client puisse savoir quand ils doivent en acheter d'autres pour ne pas en manquer. Ce genre de service serait utile pour des items qu’on utilise régulièrement, mais pas assez pour être conscient de notre inventaire, comme des oignons, patates, papier de toilette, etc.

## Installation et Utilisation
### Installation serveur LAMP
1. [Installer serveur LAMP](https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-16-04))
2. Transférer les fichiers du repo sur le serveur (si nécessaire)
3. Modifier le fichier **deploy/rwebapp.ca.conf**
  * Modifier le DocumentRoot vers le répertoire **www** du repo
  * Modifier le nom du serveur (ServerName) si vous voulez
  * Modifier l'alias du serveur (ServerAlias). Si vous utiliser un nom de domaine, assurer vous de modifier le fichier hôtes (hosts) de votre ordinateur.
4. Déplacer **deploy/rwebapp.ca.conf** dans le répertoire **/etc/apache2/sites-available**
5. Exécuter le fichier **deploy/dbcreate.sh** qui va créer la base de données et les *credentials*

### Installation Single Use Server R server on Windows
1. Installer [R](https://www.r-project.org/)
2. Installer [RTools](https://cran.r-project.org/bin/windows/Rtools/)
3. Ajouter R et RTools dans les variables d'environnement (*Path*)
4. Chercher le répertoire **csi3540RwebApp** du LAMP server
5. Rebâtir le *R package* pour le projet
  * Exécuter la commande **R CMD build csi3540RwebApp**
6. Ouvrir R
7. Installer les *packages* **opencpu, RMySQL, car, MASS** avec la commande **install.package(<package name>)**
8. Installer **csi3540RwebApp** avec le fichier tar.gz et la commande **Install package(s) from local files** du menu **Packages**

### Fonctionalités principales
* Coté Client
  * Navigation
    * Options pour usager logged-in ou logged out
  * Hub
    * Tableau des items
    * Graphique de prédiction de l'inventaire
    * Graphique sur la consommation d'un item
    * Connexion avec la base de données
  * Sign up
    * Connexion avec la base de données
  * My Settings
    * Changer le mot de passe
    * Connexion avec la base de données
* Côté serveur
  * Base de donnée MySQL
  * API PHP
    * Usager
    * Item
    * Usage d'items
  * R
    * Script pour modèles statistiques

## Architecture de développement
![Alt text](/docs/DevArchitecture.png "Architecture de développement")

## Strucure de l'archive
![Alt text](/docs/RepoHierarchy.png "Strucure de l'archive")

## Technologies utilisés
* HTML5
  * [Bootstrap 4](https://getbootstrap.com/)
* CSS
  * [Bootstrap 4](https://getbootstrap.com/)
* Javascript
  * [jQuery 3.2.1](https://jquery.com/)
  * [SweetAlert2](https://sweetalert2.github.io/) (Pour changer le style des alertes)
  * [Flot Charts](www.flotcharts.org/) (Pour dessiner des graphiques)
* Serveur LAMP ([Instructions d'installation](https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-16-04))
  * Linux (Ubuntu 16.04)
  * Apache 2
  * MySQL
  * PHP7
* R (Côté serveur et client (si nécessaire))
  * [OpenCPU](https://www.opencpu.org/)
