<?php
// Projet TraceGPS
// fichier : modele/Trace.class.php
// Rôle : la classe Trace représente une trace ou un parcours
// Dernière mise à jour : 9/9/2019 par JM CARTRON

include_once ('PointDeTrace.class.php');

class Trace
{
    // ------------------------------------------------------------------------------------------------------
    // ---------------------------------- Attributs privés de la classe -------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    private $id;				// identifiant de la trace
    private $dateHeureDebut;		// date et heure de début
    private $dateHeureFin;		// date et heure de fin
    private $terminee;			// true si la trace est terminée, false sinon
    private $idUtilisateur;		// identifiant de l'utilisateur ayant créé la trace
    private $lesPointsDeTrace;		// la collection (array) des objets PointDeTrace formant la trace
    // ------------------------------------------------------------------------------------------------------
    // ----------------------------------------- Constructeur -----------------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    public function __construct($unId, $uneDateHeureDebut, $uneDateHeureFin, $terminee, $unIdUtilisateur) {
        // A VOUS DE TROUVER LE CODE  MANQUANT
        $this->id = $unId;
        $this->dateHeureDebut = $uneDateHeureDebut;
        $this->dateHeureFin = $uneDateHeureFin;        
        $this->terminee = $terminee;
        $this->idUtilisateur = $unIdUtilisateur;
        $this->lesPointsDeTrace = Array();
        
    }
    // ------------------------------------------------------------------------------------------------------
    // ---------------------------------------- Getters et Setters ------------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    public function getId() {return $this->id;}
    public function setId($unId) {$this->id = $unId;}
    
    public function getDateHeureDebut() {return $this->dateHeureDebut;}
    public function setDateHeureDebut($uneDateHeureDebut) {$this->dateHeureDebut = $uneDateHeureDebut;}
    
    public function getDateHeureFin() {return $this->dateHeureFin;}
    public function setDateHeureFin($uneDateHeureFin) {$this->dateHeureFin= $uneDateHeureFin;}
    
    public function getTerminee() {return $this->terminee;}
    public function setTerminee($terminee) {$this->terminee = $terminee;}
    
    public function getIdUtilisateur() {return $this->idUtilisateur;}
    public function setIdUtilisateur($unIdUtilisateur) {$this->idUtilisateur = $unIdUtilisateur;}
    
    public function getLesPointsDeTrace() {return $this->lesPointsDeTrace;}
    public function setLesPointsDeTrace($lesPointsDeTrace) {$this->lesPointsDeTrace = $lesPointsDeTrace;}
    
    // Fournit une chaine contenant toutes les données de l'objet
    public function toString() {
        $msg = "Id : " . $this->getId() . "<br>";
        $msg .= "Utilisateur : " . $this->getIdUtilisateur() . "<br>";
        if ($this->getDateHeureDebut() != null) {
            $msg .= "Heure de début : " . $this->getDateHeureDebut() . "<br>";
        }
        if ($this->getTerminee()) {
            $msg .= "Terminée : Oui  <br>";
        }
        else {
            $msg .= "Terminée : Non  <br>";
        }
        $msg .= "Nombre de points : " . $this->getNombrePoints() . "<br>";
        if ($this->getNombrePoints() > 0) {
            if ($this->getDateHeureFin() != null) {
                $msg .= "Heure de fin : " . $this->getDateHeureFin() . "<br>";
            }
            $msg .= "Durée en secondes : " . $this->getDureeEnSecondes() . "<br>";
            $msg .= "Durée totale : " . $this->getDureeTotale() . "<br>";
            $msg .= "Distance totale en Km : " . $this->getDistanceTotale() . "<br>";
            $msg .= "Dénivelé en m : " . $this->getDenivele() . "<br>";
            $msg .= "Dénivelé positif en m : " . $this->getDenivelePositif() . "<br>";
            $msg .= "Dénivelé négatif en m : " . $this->getDeniveleNegatif() . "<br>";
            $msg .= "Vitesse moyenne en Km/h : " . $this->getVitesseMoyenne() . "<br>";
            $msg .= "Centre du parcours : " . "<br>";
            $msg .= "   - Latitude : " . $this->getCentre()->getLatitude() . "<br>";
            $msg .= "   - Longitude : "  . $this->getCentre()->getLongitude() . "<br>";
            $msg .= "   - Altitude : " . $this->getCentre()->getAltitude() . "<br>";
        }
        return $msg;
    }
    
    public function getNombrePoints() {return sizeof($this->lesPointsDeTrace);}
    
    public function getCentre() 
    {
        if($this->getNombrePoints() == 0) { return NULL;}
        $unPoint = $this->lesPointsDeTrace[0];
        $latMin = $unPoint->getLatitude();
        $latMax = $unPoint->getLatitude();
        $lonMin = $unPoint->getLongitude();
        $lonMax = $unPoint->getLongitude();
        
        $centre = new Point(0,0,0);
        
        for ($i = 0; $i< $this->getNombrePoints(); $i++){
            $lePoint = $this->lesPointsDeTrace[$i];
            if ($lePoint->getLatitude()>$latMax) {$latMax = $lePoint->getLatitude();}
            if ($lePoint->getLatitude()<$latMin) {$latMin = $lePoint->getLatitude();}
            if ($lePoint->getLongitude()>$lonMax) {$lonMax = $lePoint->getLongitude();}
            if ($lePoint->getLongitude()<$lonMin) {$lonMin = $lePoint->getLongitude();}
        }
        $centre->setLatitude(($latMin + $latMax)/2);
        $centre->setLongitude(($lonMin + $lonMax)/2);
        return $centre;
            
    }
    
    public function getDenivele() 
    {
        $unPoint = $this->lesPointsDeTrace[0];
        $denMin = $unPoint->getAltitude();
        $denMax = 0;
        for ($i = 0; $i< $this->getNombrePoints(); $i++){
            $lePoint = $this->lesPointsDeTrace[$i];
            if ($lePoint->getAltitude()>$denMax) {$denMax = $lePoint->getAltitude();}
            if ($lePoint->getAltitude()<$denMax) {$denMin = $lePoint->getAltitude();}
            
        }
        
        return $denMax - $denMin;
    }
    
    public function getDureeEnSecondes()
    {
        if($this->getNombrePoints() == 0) { return 0;}
        
        $unPoint = $this->lesPointsDeTrace[0];
        $tempsD = strtotime($unPoint->getDateHeure());
        $unPoint = $this->lesPointsDeTrace[$this->getNombrePoints()-1];
        $tempsF = strtotime($unPoint->getDateHeure());
        
        
        return $tempsF - $tempsD;
       
    }
    public function getDureeTotale()
    {
        $temps = $this->getDureeEnSecondes();
        
        $heures = $temps / 3600;
        $minutes = ($temps % 3600) / 60;
        $secondes = ($temps % 3600) % 60;
        return sprintf("%02d",$heures) . ":" . sprintf("%02d",$minutes) . ":" . sprintf("%02d",$secondes);
        
    }
    public function getDistanceTotale()
    {
        if($this->getNombrePoints() == 0) { return 0;}
        
        return $this->lesPointsDeTrace[$this->getNombrePoints()-1]->getDistanceCumulee();
    }
    public function getDenivelePositif()
    {
        if($this->getNombrePoints() == 0) { return 0;}
        $ancienPoint = $this->lesPointsDeTrace[0]->getAltitude();
        $denivele = 0;
        for ($i = 0; $i< $this->getNombrePoints(); $i++){
            $lePoint = $this->lesPointsDeTrace[$i];
            if ($lePoint->getAltitude()>$ancienPoint) {$denivele += ($lePoint->getAltitude()- $ancienPoint);}
            $ancienPoint = $lePoint->getAltitude();
        }
        return $denivele;

    }
    
    public function getDeniveleNegatif()
    {
        if($this->getNombrePoints() == 0) { return 0;}
        $ancienPoint = $this->lesPointsDeTrace[0]->getAltitude();
        $denivele = 0;
        for ($i = 0; $i< $this->getNombrePoints(); $i++){
            $lePoint = $this->lesPointsDeTrace[$i];
            if ($lePoint->getAltitude()<$ancienPoint) {$denivele -= ($lePoint->getAltitude()- $ancienPoint);}
            $ancienPoint = $lePoint->getAltitude();
        }
        return $denivele;
    }
    
    public function getVitesseMoyenne()
    {
        if($this->getNombrePoints() == 0) { return 0;}
        return ($this->getDistanceTotale())/($this->getDureeEnSecondes()/3600);
    }
    
    public function AjouterPoint(PointDeTrace $unPoint)
    {
        if($this->getNombrePoints() == 0) { 
            $unPoint->setTempsCumule(0);
            $unPoint->setDistanceCumulee(0);
            $unPoint->setVitesse(0);
        }
        else
        {
            $dernierPoint = $this->lesPointsDeTrace[$this->getNombrePoints() - 1];
        
            $duree = strtotime($unPoint->getDateHeure()) - strtotime($dernierPoint->getDateHeure());
            $unPoint->setDistanceCumulee($dernierPoint->getTempsCumule() + $duree);
            
            $distance = Point::getDistance($dernierPoint, $unPoint);
            $unPoint->setDistanceCumulee($dernierPoint->getDistanceCumulee() + $distance);
            
            if ($duree > 0) {
                $vitesse = ($distance/$duree)*3600;
                $unPoint->setVitesse($vitesse);
            }
            else {
                $vitesse = 0;
            }
            $unPoint->setVitesse($vitesse);
         }
         $this->lesPointsDeTrace[] = $unPoint;
    }
    
    public function ViderPoint()
    {
        $this->lesPointsDeTrace = Array();
    }
    
} // fin de la classe Trace

// ATTENTION : on ne met pas de balise de fin de script pour ne pas prendre le risque
// d'enregistrer d'espaces après la balise de fin de script !!!!!!!!!!!!
