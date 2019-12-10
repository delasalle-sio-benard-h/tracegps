<<<<<<< HEAD
<?php
    // Projet TraceGPS - services web
    // fichier : services/EnvoyerPosition.php
    // Dernière mise à jour : 05/11/2018 par Gurak
    
    // Les paramètres peuvent être passés par la méthode GET (pratique pour les tests, mais à éviter en exploitation) :
    //     http://<hébergeur>/SupprimerUnUtilisateur.php?pseudo=admin&mdpSha1=ff9fff929a1292db1c00e3142139b22ee4925177&pseudoAsupprimer=oxygen&lang=xml
    
    // Les paramètres peuvent être passés par la méthode POST (à privilégier en exploitation pour la confidentialité des données) :
    //     http://<hébergeur>/SupprimerUnUtilisateur.php 
    
    // connexion du serveur web à la base MySQL
    include_once ('../modele/DAO.class.php');
    $dao = new DAO();
    
    // Récupération des données transmises
    
    if ( empty ($_REQUEST ["pseudo"]) == true)  $pseudo = "";  else   $pseudo = $_REQUEST ["pseudo"];
    if ( empty ($_REQUEST ["mdp"]) == true)  $mdpSha1 = "";  else   $mdpSha1 = $_REQUEST ["mdp"];
    if ( empty ($_REQUEST ["idTrace"]) == true)  $idTrace = "";  else   $idTrace = $_REQUEST ["idTrace"];
    if ( empty ($_REQUEST ["lang"]) == true) $lang = "";  else $lang = strtolower($_REQUEST ["lang"]);
    
    if ($lang != "json") $lang = "xml";
    // Contrôle de la présence des paramètres
    if ( $pseudo == "" || $mdpSha1 == "" || $idTrace == "" || $lang == "")
    {	$msg = "Erreur : données incomplètes.";
    }
     
    else
    {	// il faut être utilisateur pour supprimer un parcours
        if ( $dao->getNiveauConnexion($pseudo, $mdpSha1) == 0 )
        {   $msg = "Erreur : authentification incorrecte.";
        }   //
        else
        {	// contrôle d'existence de idTrace
            $uneTrace = $dao->getUneTrace($idTrace);
            $unUtilisateur = $dao->getUnUtilisateur($pseudo);
            if ($uneTrace == null)
            {  $msg = "Erreur : parcours inexistant.";
            }
            else
            {   // si cet utilisateur possède encore des traces, sa suppression est refusée
                if ( $uneTrace->getIdUtilisateur() != $unUtilisateur->getId() ) {
                    $msg = "Erreur : vous n'êtes pas le propriétaire de ce parcours.";
                }
                else {
                    // suppression du parcours dans la BDD
                    $uneTrace = $dao->getUneTrace($idTrace);
                    if ( $uneTrace ) {
                        $msg = "Données de la trace demandée.";
                    }
                    else {
                        // tout a fonctionné
                        $msg = "Erreur : parcours inexistant.";
                    }
                }
            }
        }
    }

    
    // ferme la connexion à MySQL
    unset($dao);
    
    // création du flux en sortie
    if ($lang == "xml") {
        creerFluxXML($msg,$uneTrace);
    }
    else {
        creerFluxJSON($msg,$uneTrace);
    }
    
    // fin du programme (pour ne pas enchainer sur la fonction qui suit)
    exit;
    
    // ================================================================================================
    
    // création du flux XML en sortie
    function creerFluxXML($msg, $uneTrace)
    {

        
        // crée une instance de DOMdocument (DOM : Document Object Model)
        $doc = new DOMDocument();
        
        // specifie la version et le type d'encodage
        $doc->version = '1.0';
        $doc->encoding = 'UTF-8';
        
        // crée un commentaire et l'encode en UTF-8
        $elt_commentaire = $doc->createComment('Service web GetUnParcoursEtSesPoints - BTS SIO - Lycée De La Salle - Rennes');
        // place ce commentaire à la racine du document XML
        $doc->appendChild($elt_commentaire);
        
        // crée l'élément 'data' à la racine du document XML
        $elt_data = $doc->createElement('data');
        $doc->appendChild($elt_data);
        
        // place l'élément 'reponse' dans l'élément 'data'
        $elt_reponse = $doc->createElement('reponse', $msg);
        $elt_data->appendChild($elt_reponse);
        
        // traitement des utilisateurs
        if ($uneTrace->getNombrePoints() > 0) {
            // place l'élément 'donnees' dans l'élément 'data'
            $elt_donnees = $doc->createElement('donnees');
            $elt_data->appendChild($elt_donnees);
            
            // place l'élément 'lesUtilisateurs' dans l'élément 'donnees'
            $elt_lesParcoursDunUtilisateur = $doc->createElement('t');
            //$elt_donnees->appendChild($elt_lesParcoursDunUtilisateur);
            
            // crée un élément vide 'utilisateur'
            $elt_ParcourDunUtilisateur = $doc->createElement('trace');
            // place l'élément 'utilisateur' dans l'élément 'lesUtilisateurs'
            $elt_donnees->appendChild($elt_ParcourDunUtilisateur);
            
            // crée les éléments enfants de l'élément 'utilisateur'
            $elt_id         = $doc->createElement('id', $uneTrace->getId());
            $elt_ParcourDunUtilisateur->appendChild($elt_id);
            
            $elt_dateHeureDebut    = $doc->createElement('dateHeureDebut', $uneTrace->getDateHeureDebut());
            $elt_ParcourDunUtilisateur->appendChild($elt_dateHeureDebut);
            
            $elt_terminee    = $doc->createElement('terminee', $uneTrace->getTerminee());
            $elt_ParcourDunUtilisateur->appendChild($elt_terminee);
            if ($uneTrace->getDateHeureFin() != null)
            {
                $elt_dateHeureFin     = $doc->createElement('dateHeureFin', $uneTrace->getDateHeureFin());
                $elt_ParcourDunUtilisateur->appendChild($elt_dateHeureFin);
            }
            
            $elt_idUtilisateur = $doc->createElement('idUtilisateur', $uneTrace->getIdUtilisateur());
            $elt_ParcourDunUtilisateur->appendChild($elt_idUtilisateur);
            
            $elt_PointUneTrace = $doc->createElement('lesPoints');
            $elt_donnees->appendChild($elt_PointUneTrace);
            
            foreach ($uneTrace->getLesPointsDeTrace() as $unParcourDunUtilisateur)
            {
                // crée un élément vide 'utilisateur'
                $elt_ParcourDunUtilisateur = $doc->createElement('point');
                $elt_PointUneTrace->appendChild($elt_ParcourDunUtilisateur);
                
                
                
                // crée les éléments enfants de l'élément 'utilisateur'
                $elt_id         = $doc->createElement('id', $unParcourDunUtilisateur->getId());
                $elt_ParcourDunUtilisateur->appendChild($elt_id);
                
                $elt_latitude   = $doc->createElement('latitude', $unParcourDunUtilisateur->getLatitude());
                $elt_ParcourDunUtilisateur->appendChild($elt_latitude);
                
                $elt_longitude    = $doc->createElement('longitude', $unParcourDunUtilisateur->getLongitude());
                $elt_ParcourDunUtilisateur->appendChild($elt_longitude);
                
                $elt_altitude     = $doc->createElement('altitude', $unParcourDunUtilisateur->getAltitude());
                $elt_ParcourDunUtilisateur->appendChild($elt_altitude);
                
                $elt_dateHeure     = $doc->createElement('dateHeure', $unParcourDunUtilisateur->getDateHeure());
                $elt_ParcourDunUtilisateur->appendChild($elt_dateHeure);
                
                $elt_rythmeCardio = $doc->createElement('rythmeCardio', $unParcourDunUtilisateur->getRythmeCardio());
                $elt_ParcourDunUtilisateur->appendChild($elt_rythmeCardio);
            }
        }
        // Mise en forme finale
        $doc->formatOutput = true;
        
        // renvoie le contenu XML
        echo $doc->saveXML();
        return;
    }
    
    // ================================================================================================
    
    // création du flux JSON en sortie
    function creerFluxJSON($msg, $uneTrace)
    {
      
        
        if ($uneTrace->getNombrePoints() == 0) {
            // construction de l'élément "data"
            $elt_data = ["reponse" => $msg];
        }
        else {
            // construction d'un tableau contenant les utilisateurs
            $lesObjetsDuTableau = array();
            $unObjetTrace = array();
            $lesObjetsDuTableauTrace = array();
            $unObjetParcourDunUtilisateur = array();
            $unObjetTrace["id"] = $uneTrace->getId();
            $unObjetTrace["dateHeureDebut"] = $uneTrace->getDateHeureDebut();
            $unObjetTrace["terminee"] = $uneTrace->getTerminee();
            if ($uneTrace->getDateHeureFin() != null)
                $unObjetTrace["dateHeureFin"] = $uneTrace->getDateHeureFin();
                
                $unObjetTrace["idUtilisateur"] = $uneTrace->getIdUtilisateur();
                $lesObjetsDuTableauTrace[] = $unObjetTrace;
                foreach ($uneTrace->getLesPointsDeTrace() as $unParcourDunUtilisateur)
                {	// crée une ligne dans le tableau
                    
                    $unObjetParcourDunUtilisateur = array();
                    $unObjetParcourDunUtilisateur["id"] = $unParcourDunUtilisateur->getId();
                    $unObjetParcourDunUtilisateur["latitude"] = $unParcourDunUtilisateur->getLatitude();
                    $unObjetParcourDunUtilisateur["longitude"] = $unParcourDunUtilisateur->getLongitude();
                    $unObjetParcourDunUtilisateur["altitude"] = $unParcourDunUtilisateur->getAltitude();
                    
                    $unObjetParcourDunUtilisateur["dateHeure"] = $unParcourDunUtilisateur->getDateHeure();
                    $unObjetParcourDunUtilisateur["rythmeCardio"] = $unParcourDunUtilisateur->getRythmeCardio();
                    $lesObjetsDuTableau[] = $unObjetParcourDunUtilisateur;
                }
                // construction de l'élément "lesTraces"
                
                
                $elt_ParcoursDunUtilisateur = ["trace" => $lesObjetsDuTableauTrace, "lesPoints" => $lesObjetsDuTableau];
                
                
                // construction de l'élément "data"
                $elt_data = ["reponse" => $msg, "donnees" => $elt_ParcoursDunUtilisateur];
        }
        
        // construction de la racine
        $elt_racine = ["data" => $elt_data];
        
        // retourne le contenu JSON (l'option JSON_PRETTY_PRINT gère les sauts de ligne et l'indentation)
        echo json_encode($elt_racine, JSON_PRETTY_PRINT);
        return;
    }
    
    // ================================================================================================
    ?>
    
=======
<?php
// Projet TraceGPS - services web
// fichier : api/services/GetTousLesUtilisateurs.php
// Dernière mise à jour : 3/7/2019 par Jim
// Rôle : ce service permet à un utilisateur authentifié d'obtenir la liste de tous les utilisateurs (de niveau 1)
// Le service web doit recevoir 3 paramètres :
//•	pseudo : le pseudo de l'utilisateur
//•	mdp : le mot de passe de l'utilisateur hashé en sha1
//•	idTrace : l'id de la trace à consulter
//•	lang : le langage utilisé pour le flux de données ("xml" ou "json")
// Le service retourne un flux de données XML ou JSON contenant un compte-rendu d'exécution
// Les paramètres doivent être passés par la méthode GET :
//     http://<hébergeur>/tracegps/api/GetTousLesUtilisateurs?pseudo=callisto&mdp=13e3668bbee30b004380052b086457b014504b3e&lang=xml
// connexion du serveur web à la base MySQL
$dao = new DAO();
// Récupération des données transmises
$pseudo = ( empty($this->request['pseudo'])) ? "" : $this->request['pseudo'];
$mdpSha1 = ( empty($this->request['mdp'])) ? "" : $this->request['mdp'];
$idTrace = ( empty($this->request['idTrace'])) ? "" : $this->request['idTrace'];
$lang = ( empty($this->request['lang'])) ? "" : $this->request['lang'];
// "xml" par défaut si le paramètre lang est absent ou incorrect
if ($lang != "json") $lang = "xml";
$leParcours = null;
// Les paramètres doivent être présents
if ( $pseudo == "" || $mdpSha1 == "" || $idTrace == "" )
{	$msg = "Erreur : données incomplètes.";
$code_reponse = 400;
}
else
{	if ( $dao->getNiveauConnexion($pseudo, $mdpSha1) == 0 )
{   $msg = "Erreur : authentification incorrecte.";
$code_reponse = 401;
}
else
{
    //verification de l'existance du parcours
    $leParcours = $dao->getUneTrace($idTrace);
    if ($leParcours == null)
    {
        $msg = "Erreur : parcours inexistant.";
        $code_reponse = 404;
    }
    else
    {
        //Vérification de l'existance d'une éventuelle autorisation
        $idUser = $dao->getUnUtilisateur($pseudo)->getId();
        $idProprio = $leParcours->getIdUtilisateur();
        if ($idProprio != $idUser)
        {
            if ($dao->autoriseAConsulter($idProprio, $idUser) == false)
            {
                $leParcours = null;
                $msg = "Erreur : vous n'êtes pas autorisé par le propriétaire du parcours.";
                $code_reponse = 403;
            }
            else
            {
                $msg = "Données de la trace demandée.";
                $code_reponse = 200;
            }
        }
        else
        {
            $msg = "Données de la trace demandée.";
            $code_reponse = 200;
        }
    }
}
}
// ferme la connexion à MySQL :
unset($dao);
// création du flux en sortie
if ($lang == "xml") {
    $content_type = "application/xml; charset=utf-8";      // indique le format XML pour la réponse
    $donnees = creerFluxXML($msg, $leParcours);
}
else {
    $content_type = "application/json; charset=utf-8";      // indique le format Json pour la réponse
    $donnees = creerFluxJSON($msg, $leParcours);
}
// envoi de la réponse HTTP
$this->envoyerReponse($code_reponse, $content_type, $donnees);
// fin du programme (pour ne pas enchainer sur les 2 fonctions qui suivent)
exit;
// ================================================================================================
// création du flux XML en sortie
function creerFluxXML($msg,$leParcours)
{
    /* Exemple de code XML
     <?xml version="1.0" encoding="UTF-8"?>
     <!--Service web ChangerDeMdp - BTS SIO - Lycée De La Salle - Rennes-->
     <data>
     <reponse>Erreur : authentification incorrecte.</reponse>
     </data>
     */
    
    // crée une instance de DOMdocument (DOM : Document Object Model)
    $doc = new DOMDocument();
    
    // specifie la version et le type d'encodage
    $doc->version = '1.0';
    $doc->encoding = 'UTF-8';
    
    // crée un commentaire et l'encode en UTF-8
    $elt_commentaire = $doc->createComment('Service web getUnParcoursEtSesPoints - BTS SIO - Lycée De La Salle - Rennes');
    // place ce commentaire à la racine du document XML
    $doc->appendChild($elt_commentaire);
    
    // crée l'élément 'data' à la racine du document XML
    $elt_data = $doc->createElement('data');
    $doc->appendChild($elt_data);
    
    // place l'élément 'reponse' dans l'élément 'data'
    $elt_reponse = $doc->createElement('reponse', $msg);
    $elt_data->appendChild($elt_reponse);
    
    if ($leParcours != null){
        // traitement des utilisateurs
        
        // place l'élément 'donnees' dans l'élément 'data'
        $elt_donnees = $doc->createElement('donnees');
        $elt_data->appendChild($elt_donnees);
        
        
        // place l'élément 'lesUtilisateurs' dans l'élément 'donnees'
        $elt_Parcours = $doc->createElement('trace');
        $elt_donnees->appendChild($elt_Parcours);
        
        
        // crée les éléments enfants de l'élément 'Trace'
        $elt_id = $doc->createElement('id', $leParcours->getId());
        $elt_Parcours->appendChild($elt_id);
        
        $elt_dateDebut     = $doc->createElement('dateHeureDebut', $leParcours->getDateHeureDebut());
        $elt_Parcours->appendChild($elt_dateDebut);
        
        $elt_terminee     = $doc->createElement('terminee', $leParcours->getTerminee());
        $elt_Parcours->appendChild($elt_terminee);
        
        $dateHeure    = $doc->createElement('dateHeureFin', $leParcours->getDateHeureFin());
        $elt_Parcours->appendChild($dateHeure);
        
        $elt_idUser     = $doc->createElement('iduUser', $leParcours->getIdUtilisateur());
        $elt_Parcours->appendChild($elt_idUser);
        
        
        if (sizeof($leParcours->getLesPointsDeTrace()) > 0)
        {
            
            foreach ($leParcours->getLesPointsDeTrace() as $unPoint)
            {
                $elt_Points = $doc->createElement('point');
                $elt_donnees->appendChild($elt_Points);
                
                $elt_idPoint         = $doc->createElement('idPoint', $unPoint->getId());
                $elt_Points->appendChild($elt_idPoint);
                
                $elt_latitude     = $doc->createElement('latitude', $unPoint->getLatitude());
                $elt_Points->appendChild($elt_latitude);
                
                $elt_longitude    = $doc->createElement('longitude', $unPoint->getLongitude());
                $elt_Points->appendChild($elt_longitude);
                
                $elt_altitude  = $doc->createElement('altitude', $unPoint->getAltitude());
                $elt_Points->appendChild($elt_altitude);
                
                $elt_dateHeure     = $doc->createElement('dateHeure', $unPoint->getDateHeure());
                $elt_Points->appendChild($elt_dateHeure);
                
                $elt_rythmeCardiaque    = $doc->createElement('rythmeCardiaque', $unPoint->getRythmeCardio());
                $elt_Points->appendChild($elt_rythmeCardiaque);
            }
        }
    }
    
    
    
    // Mise en forme finale
    $doc->formatOutput = true;
    
    // renvoie le contenu XML
    return $doc->saveXML();
}
// ================================================================================================
// création du flux JSON en sortie
function creerFluxJSON($msg,$leParcours)
{
    /* Exemple de code JSON
     {
     "data": {
     "reponse": "Erreur : authentification incorrecte."
     }
     }
     */
    
    // construction de l'élément "data"
    $elt_data = ["reponse" => $msg];
    
    // construction de la racine
    $elt_racine = ["data" => $elt_data];
    if($leParcours != null){
        
        $LeSeulParcours = array();
        $lesObjetsDuTableau = array();
        $unObjetParcours = array();
        $unObjetParcours["id"] = $leParcours->getId();
        $unObjetParcours["dateHeureDebut"] = $leParcours->getDateheureDebut();
        $unObjetParcours["terminee"] = $leParcours->getTerminee();
        $unObjetParcours["dateHeureFin"] = $leParcours->getdateHeureFin();
        $unObjetParcours["idUtilisateur"] = $leParcours->getIdUtilisateur();
        
        $LeSeulParcours[] = $unObjetParcours;
        if($leParcours->getLesPointsDeTrace() >0){
            foreach ($leParcours->getLesPointsDeTrace() as $unPoint)
            {
                
                $unObjetPoint = array();
                $unObjetPoint["id"] = $unPoint->getId();
                $unObjetPoint["latitude"] = $unPoint->getlatitude();
                $unObjetPoint["longitude"] = $unPoint->getLongitude();
                $unObjetPoint["altitude"] = $unPoint->getAltitude();
                $unObjetPoint["dateHeure"] = $unPoint->getDateHeure();
                $unObjetPoint["rythmeCardiaque"] = $unPoint->getRythmeCardio();
                $lesObjetsDuTableau[] = $unObjetPoint;
            }
        }
        
        
        
        $elt_LeParcours = ["trace" => $LeSeulParcours, "lesPoints" => $lesObjetsDuTableau];
        // construction de l'élément "data"
        $elt_data = ["reponse" => $msg, "donnees" => $elt_LeParcours];
    }
    $elt_racine = ["data" => $elt_data];
    // retourne le contenu JSON (l'option JSON_PRETTY_PRINT gère les sauts de ligne et l'indentation)
    return json_encode($elt_racine, JSON_PRETTY_PRINT);
}
// ================================================================================================
?>
>>>>>>> branch 'master' of https://github.com/delasalle-sio-benard-h/tracegps.git
