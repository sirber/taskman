TaakMan
(c) St�pahne B�rub� <sirber@hotmail.com>

Bas� sur: 
* framework: slim
* database: medoo
* template: twig
* package manager: composer

R�pertoires:
* public: acc�s client (/)
* private: l'application
* vendor: les d�pendances

Installation:
* # composer install
* Pointer apache vers le r�pertoire /public
* Activer "AllowOverride all"
* Cr�er le fichier de configuration (private/settings.php)
* Donner acc�s �criture sur private/logs et private/upload

#######
Todo

/user