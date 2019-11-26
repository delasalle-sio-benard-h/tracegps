<?php

// connexion du serveur web à la base MySQL
include_once ('../modele/DAO.class.php');
$dao = new DAO();
// Récupération des données transmises
// la fonction $_GET récupère une donnée passée en paramètre dans l'URL par la méthode GET
// la fonction $_POST récupère une donnée envoyées par la méthode POST
// la fonction $_REQUEST récupère par défaut le contenu des variables $_GET, $_POST, $_COOKIE
if ( empty ($_REQUEST ["pseudo"]))  $pseudo = "";  else   $pseudo = $_REQUEST ["pseudo"];
if ( empty ($_REQUEST ["mdp"]))  $mdpSha1 = "";  else   $mdpSha1 = $_REQUEST ["mdp"];
if ( empty ($_REQUEST ["lang"])) $lang = "";  else $lang = strtolower($_REQUEST ["lang"]);
if ( empty ($_REQUEST ["pseudoConsulte"])) $pseudoConsulte = "";  else $pseudoConsulte = strtolower($_REQUEST ["pseudoConsulte"]);
// "xml" par défaut si le paramètre lang est absent ou incorrect
if ($lang != "json") $lang = "xml";
// initialisation du nombre de réponses

$lesTraces = array();
// Contrôle de la présence des paramètres
if ( $pseudo == "" || $mdpSha1 == "" || $pseudoConsulte == "" )
{	$msg = "Erreur : données incomplètes.";
}
else
{	if ( $dao->getNiveauConnexion($pseudo, $mdpSha1) == 0 )
    $msg = "Erreur : authentification incorrecte.";
    else
    {	$lesTraces = $dao->getLesTraces( $dao->getUnUtilisateur($pseudoConsulte)->getId());
        
        // mémorisation du nombre d'utilisateurs
        $nbReponses = sizeof($lesTraces);
        
        if ($nbReponses == 0) {
            $msg = "Aucune trace trouvées pour ".$pseudoConsulte;
        }
        else {
            $msg = $nbReponses . " trace(s) pour l'utilisateur ".$pseudoConsulte;
        }
    }
}
// ferme la connexion à MySQL
unset($dao);
// création du flux en sortie
if ($lang == "xml") {
    creerFluxXML($msg, $lesTraces);
}
else {
    creerFluxJSON($msg, $lesTraces);
}
// fin du programme (pour ne pas enchainer sur la fonction qui suit)
exit;
// création du flux XML en sortie
function creerFluxXML($msg, $lesTraces)
{
    
    // crée une instance de DOMdocument (DOM : Document Object Model)
    $doc = new DOMDocument();
    
    // specifie la version et le type d'encodage
    $doc->version = '1.0';
    $doc->encoding = 'UTF-8';
    
    // crée un commentaire et l'encode en UTF-8
    $elt_commentaire = $doc->createComment('Service web GetLesParcoursDunUtilisateur - BTS SIO - Lycée De La Salle - Rennes');
    // place ce commentaire à la racine du document XML
    $doc->appendChild($elt_commentaire);
    
    // crée l'élément 'data' à la racine du document XML
    $elt_data = $doc->createElement('data');
    $doc->appendChild($elt_data);
    
    // place l'élément 'reponse' dans l'élément 'data'
    $elt_reponse = $doc->createElement('reponse', $msg);
    $elt_data->appendChild($elt_reponse);
    
    // traitement des utilisateurs
    if (sizeof($lesTraces) > 0) {
        // place l'élément 'donnees' dans l'élément 'data'
        $elt_donnees = $doc->createElement('donnees');
        $elt_data->appendChild($elt_donnees);
        
        // place l'élément 'lesUtilisateurs' dans l'élément 'donnees'
        $elt_lesTraces = $doc->createElement('lesTraces');
        $elt_donnees->appendChild($elt_lesTraces);
        
        foreach ($lesTraces as $uneTrace)
        {
            // crée un élément vide 'utilisateur'
            $elt_trace = $doc->createElement('trace');
            // place l'élément 'utilisateur' dans l'élément 'lesUtilisateurs'
            $elt_lesTraces->appendChild($elt_trace);
            
            // crée les éléments enfants de l'élément 'utilisateur'
            $elt_id         = $doc->createElement('id', $uneTrace->getId());
            $elt_trace->appendChild($elt_id);
            
            $elt_heureDebut     = $doc->createElement('dateHeureDebut', $uneTrace->getDateHeureDebut());
            $elt_trace->appendChild($elt_heureDebut);
            
            $elt_terminee    = $doc->createElement('terminee', $uneTrace->getTerminee());
            $elt_trace->appendChild($elt_terminee);
            
            if($uneTrace->getTerminee()==1){
                $elt_heureFin     = $doc->createElement('dateHeureFin', $uneTrace->getDateHeureFin());
                $elt_trace->appendChild($elt_heureFin);
            }
            
            $elt_distance     = $doc->createElement('distance', $uneTrace->getDistanceTotale());
            $elt_trace->appendChild($elt_distance);
            
            $elt_idUtilisateur = $doc->createElement('idUtilisateur', $uneTrace->getIdUtilisateur());
            $elt_trace->appendChild($elt_idUtilisateur);
        }
    }
    
    // Mise en forme finale
    $doc->formatOutput = true;
    
    // renvoie le contenu XML
    echo $doc->saveXML();
    return;
}
// création du flux JSON en sortie
function creerFluxJSON($msg, $lesTraces)
{
    
    if (sizeof($lesTraces) == 0) {
        // construction de l'élément "data"
        $elt_data = ["reponse" => $msg];
    }
    else {
        // construction d'un tableau contenant les utilisateurs
        $lesObjetsDuTableau = array();
        foreach ($lesTraces as $uneTrace)
        {	// crée une ligne dans le tableau
            $unObjetUtilisateur = array();
            $unObjetUtilisateur["id"] = $uneTrace->getId();
            $unObjetUtilisateur["dateHeureDebut"] = $uneTrace->getDateHeureDebut();
            $unObjetUtilisateur["terminee"] = $uneTrace->getTerminee();
            if($uneTrace->getTerminee()==1){
                $unObjetUtilisateur["dateHeureFin"] = $uneTrace->getDateHeureFin();
            }
            $unObjetUtilisateur["distance"] = $uneTrace->getDistanceTotale();
            $unObjetUtilisateur["idUtilisateur"] = $uneTrace->getIdUtilisateur();
            $lesObjetsDuTableau[] = $unObjetUtilisateur;
        }
        // construction de l'élément "lesUtilisateurs"
        $elt_trace = ["lesTraces" => $lesObjetsDuTableau];
        
        // construction de l'élément "data"
        $elt_data = ["reponse" => $msg, "donnees" => $elt_trace];
    }
    
    // construction de la racine
    $elt_racine = ["data" => $elt_data];
    
    // retourne le contenu JSON (l'option JSON_PRETTY_PRINT gère les sauts de ligne et l'indentation)
    echo json_encode($elt_racine, JSON_PRETTY_PRINT);
    return;
}
?>