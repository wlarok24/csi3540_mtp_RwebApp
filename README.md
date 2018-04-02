*********************************************
**University of Ottawa - CSI3540**

**Projet de session**

Auteur : [William LaRocque](https://github.com/wlarok24)

Numéro étudiant : 8397424
*********************************************
## Idée pour le projet
Mon idée pour le projet est de créer un web app pour permettre de gérer notre inventaire de certains items dans la maison pour que le client puisse savoir quand ils doivent en acheter d'autres pour ne pas en manquer. Ce genre de service serait utile pour des items qu’on utilise régulièrement, mais pas assez pour être conscient de notre inventaire, comme des oignons, patates, papier de toilette, etc.

## Fonctionalités principales
* Coté Client
  * Navigation
    * Options pour usager logged-in ou logged out
  * Hub
    * Tableau des items
    * Ajouter/Supprimer items
    * Modification de l’inventaire
    * Usage quotidien de vos items
    * Graphique de prédiction de l'inventaire
    * Graphique sur la consommation d'un item
  * Sign up
    * Création de nouveau compte
  * My Settings
    * Changer le mot de passe
* Côté serveur
  * Certification HTTPS avec Let’s Encrypt (optionnel)
  * Base de donnée MySQL
  * API PHP
    * Usager
    * Item
    * Usage d'items
    * Sécurité mot de passe (Salt and hash) et tokens de session
    * Communiquent avec le serveur R (OpenCPU)
  * R
    * Script pour modèles statistiques

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
* R (Côté serveur et client)
  * [OpenCPU](https://www.opencpu.org/)
  
## Structure de l'archive (Repo)
![Alt text](/docs/RepoHierarchy.png "Strucure de l'archive")
![Alt text](/docs/RPkgHierarchy.png "Strucure du package R")

## Installation et Utilisation
### Installation serveur LAMP
1. [Installer serveur LAMP](https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-16-04).
2. Transférer les fichiers du repo sur le serveur (si nécessaire).
3. Modifier le fichier **deploy/rwebapp.ca.conf**.
  * Modifier le DocumentRoot vers le répertoire **www** du repo
  * Modifier le nom du serveur (ServerName) si vous voulez
  * Modifier l'alias du serveur (ServerAlias). Si vous utiliser un nom de domaine, assurer vous de modifier le fichier hôtes (hosts) de votre ordinateur.
4. Déplacer **deploy/rwebapp.ca.conf** dans le répertoire **/etc/apache2/sites-available**.
5. Si le répertoire **csi3540_mtp_RwebApp** ne se trouve pas dans **/var/www/**, le document **/etc/apache2/apache2.conf** pour donner accès à Apache au répertoire **csi3540_mtp_RwebApp/www**.
  * Ajouter le code suivant avec les autres éléments **Directory** :
  ```
  <Directory path_to_repo/csi3540_mtp_RwebApp/www>
   Options Indexes FollowSymLinks
   AllowOverride None
   Require all granted
  </Directory>
  ```
6. Partir *MySQL* avec une des commandes suivantes :
  > sudo service mysql start **ou** /etc/init.d/mysql start
7. Exécuter le fichier **deploy/dbcreate.sh** qui va créer la base de données et les *credentials* avec la commande suivante:
  > bash dbcreate.sh
8. **Important** Déplacer les fichiers *credentials* dans les bons répertoires.
  * **apiCredentials.php** va dans le répertoire **/www/** du repo.
  * **RDBCredentials.csv** va dans le répertoire **csi3540RwebApp/data** du repo.
9. *(Optionnel)* Populer la base de donnée avec un usager de test ("Test user") avec la commande suivante :
    > php deploy/dbpopulate.php
  * Le courriel de l'usager est "test_user@example.com" et son mot de passe est "password"
10. Tester le serveur.
  * Partir le serveur apache avec :
    > sudo service apache2 start
  * Accéder au site avec l'addresse que vous avez choisi.
  * Aller sur la page de "Sign up" et tenter de créer un compte.
  * Si vous réussissez, tenter de vous connecter ("Log in").
  * **Si vous ne réussissez pas les étapes c ou d, aller voir l'error log avec la commande suivante :**
    > cat /var/log/apache2/error.log
11. Fermer les serveurs avec les commandes suivantes :
 > sudo service mysql stop **ou** /etc/init.d/mysql stop  
 > sudo service apache2 stop

### Installation du serveur R
#### Installer OpenCPU sur un serveur Ubuntu 16.04 ou plus récent (Production)
![Alt text](/docs/LiveArchitecture.png "Architecture de production")
1. Télécharger et installer OpenCPU server en suivant [les instructions d'OpenCPU](https://www.opencpu.org/download.html).
2. **Référez-vous aux [instructions du serveur OpenCPU](https://opencpu.github.io/server-manual/opencpu-server.pdf) pour faire les instructions suivantes**
3. Ouvrir le site du serveur OpenCPU avec les commandes suivantes :
    ```
    sudo a2ensite opencpu
    sudo apachectl restart
    ```
4. Installer la librairie **RMySQL** avec les commandes suivantes :
    ```
    sudo -i R
    install.packages("RMySQL")
    ```
5. S'assurer que **RDBCredentials.csv** est dans le répertoire **csi3540RwebApp/data**.
6. Rebâtir la librairie du projet en exéxutant la commande :
    > sudo R CMD build csi3540RwebApp
7. Si la commande précédente a été exécutée avec succès, installer la librairie avec la commande :
    > sudo R INSTALL build csi3540RwebApp
8. Ajouter **csi3540RwebApp** dans l'attribut **preload** dans la configuration du serveur OpenCPU **/etc/opencpu/server.conf**.
9. Repartir le serveur Apache avec la commande :
   > sudo apachectl restart 

#### Serveur local R sur Windows (Développement)
![Alt text](/docs/DevArchitecture.png "Architecture de développement")
1. Installer [R](https://www.r-project.org/).
2. Installer [RTools](https://cran.r-project.org/bin/windows/Rtools/).
3. Ajouter R et RTools dans les variables d'environnement (*Path*).
4. Ouvrir R.
5. Installer les *packages* **opencpu, RMySQL** avec la commande :
   > install.package(<package name>)
6. Chercher le répertoire **csi3540RwebApp** du LAMP server.
7. Rebâtir le *R package* pour le projet en exécutant la commande :
   > R CMD build csi3540RwebApp
8. Installer **csi3540RwebApp** avec le fichier tar.gz et la commande **Install package(s) from local files** du menu **Packages**.

### Utilisation du site
1. Ouvrir le serveur Linux.
2. Partir le serveur Apache et MySQL :
 > sudo service mysql start **ou** /etc/init.d/mysql start 
 > sudo service apache2 start
3. Partir le serveur R sur Windows en utilisant le script **deploy/startRserver.R**.
4. Utiliser le site.
5. Fermer le serveur R en suivant les instructions à l'écran.
6. Fermer les serveurs Apache et MySQL avec les commandes suivantes :
 > sudo service mysql stop **ou** /etc/init.d/mysql stop  
 > sudo service apache2 stop
