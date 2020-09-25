<?php 

//on rend disponible tous nos packages installés par Composer 
//en une seule de code yeah.

//ce fichier va aussi nous permettre d'auto-charger nos propres classe !!! 
//plus besoin de faire des include
//voir le fichier composer.json à la racine du projet pour la configuration
//après avoir configuré composer.json, on doit exécuter dans le terminal : 
// composer dump-autoload

require("../vendor/autoload.php");


//dump($_SERVER);

//dans le contrôleur frontal
//front controller
//ce fichier reçoit toutes les requêtes faite à mon site

//on récupère le paramètre d'URL page et on le stocke dans une belle petite variable
//opérateur ternaire, équivalent du code commenté ci-dessous ! 
//short & sweet
$page = (!empty($_GET['page'])) ? $_GET['page'] : "home";

//ne marche que grâce au vardumper ! 
//pour débugger et continuer l'exécution du code
//dump($page);

//pour débugger puis faire un die()
//dd($page);

/*
//exactement équivalent à ces 6 lignes
if(!empty($_GET['page'])){
    $page = $_GET['page'];
}
else {
    $page = "home";
}
*/


//routing avec altorouter

//crée une instance de la classe téléchargée
$router = new AltoRouter();

//demande à AltoRouter d'ignorer les sous-dossiers présents dans l'URL
//je veux qu'il compare mes routes avec la fin de l'URL seulement
//le BASE_URI a été créé par le .htaccess ! 
$router->setBasePath( $_SERVER['BASE_URI'] );
//$router->setBasePath( "/dungeons/s05-projet-oshop-gsylvestre/public");


//créer nos routes !!!
//accueil
$router->map("GET", "/", ["MainController", "home"]);

//mentions légales
$router->map("GET", "/legal-mentions", ["MainController", "legal"]);

//la partie [i:id] est dynamique, variable ! Elle doit contenir un nombre entier
//altorouter va nous passer la valeur de cette partie de l'url sous le nom "id"
$router->map("GET", "/catalog/category/[i:id]", ["CatalogController", "showCategoryProducts"]);

//idem mais par type de produit
$router->map("GET", "/catalog/type/[i:id]", ["CatalogController", "showTypeProducts"]);

//idem mais par marque de produit
$router->map("GET", "/catalog/brand/[i:id]", ["CatalogController", "showBrandProducts"]);

//page de détails 'un produit
$router->map("GET", "/catalog/product/[i:id]", ["CatalogController", "showProduct"]);

/*
//version avec un seul appel de méthode
$router->addRoutes([
    ["GET", "/", ["MainController", "home"]],
    [],
]);
*/

//on demande à altorouter de comparer maintenant l'URL de la requête avec nos routes
$match = $router->match();


//si altorouter n'a pas trouvé l'URL demandée dans la liste de nos routes
if ($match === false){
    //alors page 404
    //instancie notre classe MainController
    $controller = new Oshop\Controller\MainController();
    $controller->fourofour();
}
else {
    //dans quel contrôleur se trouve la méthode à appeler ?
    $controllerToUse = "Oshop\Controller\\" . $match["target"][0];
    //quelle est la méthode à appeler ?
    $methodToCall = $match["target"][1];

    //truc méga chelou : on crée une instance à partir du nom du contrôleur qui est contenu
    //sous forme de chaîne dans notre variable
    $controller = new $controllerToUse();

    //même logique... on connait le nom de la méthode à appeler, il est dans notre variable... 
    //donc on se permet de l'appeler comme ça : 
    $controller->$methodToCall( $match["params"] );
}