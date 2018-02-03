*********************************************
	University of Ottawa - CSI3540
	Projet de session
	Nom : William LaRocque
	Numéro étudiant : 8397424
*********************************************
## Idée pour le projet
Pour le projet du cours CSI3540, je voulais combiner les connaissances que j’ai acquises lors de mon cours d'analyse de régression et celles que je vais apprendre dans ce cours. Ainsi, j’ai tenter de trouver une technologie pour l'analyser des données avec les outils statistiques que je connais qui fonctionne avec celles du web.

Après un peu de recherche, j’ai trouvé la technologie OpenCPU (https://www.opencpu.org) qui permet de rouler du code R sur un serveur Linux. Le language R est le language que j’ai appris et qui permet de faire de l’analyse statistique. Ainsi, je pensais ajouter OpenCPU sur un serveur LAMP (Linux, Apache, MySQL, PHP) pour implémenter mon projet.

Mon idée pour le projet, créer un web app pour permettre de gérer notre inventaire de certains items dans la maison pour que le client puisse savoir quand ils doivent en acheter pour ne pas en manquer. Ce genre de service serait utile pour des items qu’on utilise régulièrement, mais pas assez pour être conscient de notre inventaire, comme des oignons, patates, papier de toilette, etc.
*********************************************
## Structure des fichiers
*********************************************
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
