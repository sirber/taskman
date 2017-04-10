TaakMan
(c) Stépahne Bérubé <sirber@hotmail.com>

Basé sur: 
* framework: slim
* database: medoo
* template: twig
* package manager: composer

Répertoires:
* public: accès client (/)
* private: l'application
* vendor: les dépendances

Installation:
* # composer install
* Pointer apache vers le répertoire /public
* Activer "AllowOverride all"
* Créer le fichier de configuration (private/settings.php)
* Donner accès écriture sur private/logs et private/upload

#######
Todo

/user