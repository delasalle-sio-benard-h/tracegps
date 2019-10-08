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
     
    public function getNombrePoints () {
        return sizeof($this->lesPointsDeTrace);
    }

    public function getCentre(){
    
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            return 0;
        }
    
        $colec = $this->lesPointsDeTrace;
        $latMax=$this->lesPointsDeTrace[0]->getLatitude();
        $lonMax=$this->lesPointsDeTrace[0]->getLongitude();
        $latMin=$this->lesPointsDeTrace[0]->getLatitude();
        $lonMin=$this->lesPointsDeTrace[0]->getLongitude();
        
        foreach ($colec as $lePoint){
            
            if ($lePoint->getLongitude()> $lonMax){
                $lonMax = $lePoint->getLongitude();
            }
            
            if ($lePoint->getLongitude() < $lonMin)
            {
                $lonMin = $lePoint->getLongitude();
            }
            
            if ($lePoint->getLatitude() > $latMax)
            {
                $latMax = $lePoint->getLatitude();
            }
            
            if ($lePoint->getLatitude() < $latMin)
            {
                $latMin = $lePoint->getLatitude();
            }
            
        }
        
        $latCentre = ($latMin + $latMax)/2;
        $lonCentre = ($lonMin + $lonMax)/2;
        $pointCentre = new Point($latCentre,$lonCentre,0);
        return  $pointCentre;
    }
    
    public function getDenivele(){
        
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            return 0;
        }
        $p = $this->getLesPointsDeTrace()[0];
        
        $altMin = $p->getAltitude();
        $altMax = $p->getAltitude();
        
        foreach ($this->getLesPointsDeTrace() as $lePoint)
        {       
            
            if ($lePoint->getAltitude() > $altMax)
            {
                $altMax = $lePoint->getAltitude();
            }
            
            if ($lePoint->getAltitude() < $altMin)
            {
                $altMin = $lePoint->getAltitude();
            }
            
        }
        
        $denivele = $altMax - $altMin;
        return $denivele;
        
    }

    public function getDureeEnSecondes () {
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            return 0;
        }
       
        return strtotime($this->getDateHeureFin())- strtotime($this->getDateHeureDebut());
    }
    
    public function getTempsCumuleEnChaine()
    {
        
        $temps = $this->getDureeEnSecondes();
        $heures = (int)$temps/3600;
        $minutes = $temps%60%60;
        $secondes = ($temps%3600)%60;
        
        return sprintf("%02d",$heures) . ":" . sprintf("%02d",$minutes) . ":" . sprintf("%02d",$secondes);
    }
    
    public function getDureeTotale () {
        $temps = $this->getDureeEnSecondes();
        $heures = $temps/3600;
        $minutes = ($temps%3600)/60;
        $secondes = ($temps%3600)%60;
        
        return sprintf("%02d",$heures) . ":" . sprintf("%02d",$minutes) . ":" . sprintf("%02d",$secondes);
    }
    
    public function getDistanceTotale () {
        
        if (sizeof($this->lesPointsDeTrace)== 0){
            return 0;
        }
            
            return $this->lesPointsDeTrace[$this->getNombrePoints()-1]->getDistanceCumulee();
        }

    public function getDenivelePositif () {
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            return 0;
        }
            
        else
        {
                
            $denivele = 0;
                
            for ($i = 0; $i < sizeof($this->getLesPointsDeTrace())-1; $i++)
            {
                $point1 = $this->getLesPointsDeTrace()[$i];
                    
                $point2 = $this->getLesPointsDeTrace()[$i + 1];
                    
                if ($point1->getAltitude() < $point2->getAltitude())
                {
                    $denivele += ($point2->getAltitude() - $point1->getAltitude());
                }
            }
                
            return $denivele;
        }
 
        
} // fin de la classe Trace

    public function getDeniveleNegatif () {
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            return 0;
        }
        
        else
        {
            
            $denivele = 0;
            
            for ($i = 0; $i < sizeof($this->getLesPointsDeTrace()) - 1; $i++)
            {
                $point1 = $this->getLesPointsDeTrace()[$i];
                
                $point2 = $this->getLesPointsDeTrace()[$i + 1];
                
                if ($point1->getAltitude() > $point2->getAltitude())
                {
                    $denivele += ($point1->getAltitude() - $point2->getAltitude());
                }
            }
            
            return $denivele;
        }
    }

    public function getVitesseMoyenne () {
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            return 0;
        }
        
        
        return $this->getDistanceTotale() / ($this->getDureeEnSecondes() / 3600);
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

    public function viderListePoints() {
        $this->lesPointsDeTrace = Array();
    }
    
}
// ATTENTION : on ne met pas de balise de fin de script pour ne pas prendre le risque
// d'enregistrer d'espaces après la balise de fin de script !!!!!!!!!!!!
