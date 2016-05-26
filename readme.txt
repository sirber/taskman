WebApp StyloRouge
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
* Pointer apache vers le répertoire public
* Activer "AllowOverride all"
* Vérifier le fichier de configuration (private/settings.php)
* Donner accès écriture sur private/logs, private/cache et private/upload



#######
Todo

task/view/js : rewrite to move the tfoot/tr into the body and clone a new one in the tfoot. might fix the input file issues