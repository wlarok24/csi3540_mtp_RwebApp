*********************************************
**University of Ottawa - CSI3540**

**Projet de session**

Auteur : [William LaRocque](https://github.com/wlarok24)

Numéro étudiant : 8397424
*********************************************
## Idée pour le projet
Mon idée pour le projet est de créer un web app pour permettre de gérer notre inventaire de certains items dans la maison pour que le client puisse savoir quand ils doivent en acheter d'autres pour ne pas en manquer. Ce genre de service serait utile pour des items qu’on utilise régulièrement, mais pas assez pour être conscient de notre inventaire, comme des oignons, patates, papier de toilette, etc.

## Installation et Utilisation
### Mockups
Pour voir les mockups HTML et CSS, simplement ouvrir les fichiers avec un navigateur web.
Veuiller noter que de cette manière les fonctionalités de "log in", "log out" ne fonctionne pas.
De plus, vous ne pourrez pas tester les fonctionnalités de l'API PHP qui sont déjà programmée.

### Fonctionalités principales
* Coté Client
  * Navigation
    * Options pour usager logged-in ou logged out **(en développement)**
  * Hub
    * Tableau des items (Mock data)
    * Graphique de prédiction de l'inventaire (Mock data)
    * Graphique sur la consommation d'un item **(en développement)**
    * Connexion avec la base de données **(en développement)**
  * Sign up
    * Connexion avec la base de données **(en développement)**
  * My Settings
    * Changer le mot de passe **(en développement)**
    * Connexion avec la base de données **(en développement)**
* Côté serveur **(en développement)**
  * Base de donnée MySQL
  * API PHP
    * Usager
    * Item
    * Usage d'items
  * R
    * Script pour modèles statistiques

### Installation du projet sur un serveur
À venir

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
