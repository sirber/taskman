WebApp StyloRouge
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
* Pointer apache vers le r�pertoire public
* Activer "AllowOverride all"
* V�rifier le fichier de configuration (private/settings.php)
* Donner acc�s �criture sur private/logs, private/cache et private/upload



#######
Todo

task/view/js : rewrite to move the tfoot/tr into the body and clone a new one in the tfoot. might fix the input file issues