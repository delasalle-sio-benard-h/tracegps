<?php
// Projet TraceGPS
// fichier : modele/Utilisateur.class.php
// Rôle : la classe Utilisateur représente les utilisateurs de l'application
// Dernière mise à jour : 9/9/2019 par JM CARTRON

include_once ('Outils.class.php');

class Utilisateur
{
    // ------------------------------------------------------------------------------------------------------
    // ---------------------------------- Attributs privés de la classe -------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    private $id;	// identifiant de l'utilisateur (numéro automatique dans la BDD)
    private $pseudo;	// pseudo de l'utilisateur
    private $mdpSha1;	// mot de passe de l'utilisateur (hashé en SHA1)
    private $adrMail;	// adresse mail de l'utilisateur
    private $numTel;	// numéro de téléphone de l'utilisateur
    private $niveau;	// niveau d'accès : 1 = utilisateur (pratiquant ou proche)    2 = administrateur
    private $dateCreation;	// date de création du compte
    private $nbTraces;	// nombre de traces stockées actuellement
    private $dateDerniereTrace;	// date de début de la dernière trace
    // ------------------------------------------------------------------------------------------------------
    // ----------------------------------------- Constructeur -----------------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    public function __construct($unId, $unPseudo, $unMdpSha1, $uneAdrMail, $unNumTel, $unNiveau,
        $uneDateCreation, $unNbTraces, $uneDateDerniereTrace) {
            // A VOUS DE TROUVER LE CODE  MANQUANT
            // Utilisez la classe Outils pour améliorer la forme du numéro de téléphone
            $this->id = $unId;
            $this->pseudo = $unPseudo;
            $this->mdpSha1 = $unMdpSha1;
            $this->adrMail = $uneAdrMail;
            $this->numTel = $unNumTel;
            $this->niveau=$unNiveau;
            $this->dateCreation=$uneDateCreation;
            $this->nbTraces=$unNbTraces;
            $this->dateDerniereTrace=$uneDateDerniereTrace;
    }
    // ------------------------------------------------------------------------------------------------------
    // ---------------------------------------- Getters et Setters ------------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    // A VOUS DE TROUVER LE CODE  MANQUANT
    // Utilisez la page de test fournie pour retrouver les noms des getters et des setters
    public function getId() {return $this->id;}
    public function setId($unId) {$this->id = $unId;}
    
    public function getPseudo() {return $this->pseudo;}
    public function setPseudo($unPseudo) {$this->pseudo = $unPseudo;}
    
    public function getMdpSha1() {return $this->adrMail;}
    public function setMdpSha1($unMdpSha1) {$this->adrMail= $unMdpSha1;}
    
    public function getNumTel() {return $this->numTel;}
    public function setNumTel($unNumTel) {$this->numTel = $unNumTel;}
    
    public function getNiveau() {return $this->niveau;}
    public function setNiveau($unNiveau) {$this->niveau = $unNiveau;}
    
    public function getDateCreation() {return $this->DateCreation;}
    public function setDateCreation($uneDateCreation) {$this->DateCreation = $uneDateCreation;}
    
    public function getNbTraces() {return $this->nbTraces;}
    public function setNbTraces($unNbTraces) {$this->nbTraces = $unNbTraces;}
    
    public function getDateDerniereTrace() {return $this->dateDerniereTrace;}
    public function setDateDerniereTrace($uneDateDerniereTrace) {$this->dateDerniereTrace= $uneDateDerniereTrace;}
    

    
    // ------------------------------------------------------------------------------------------------------
    // -------------------------------------- Méthodes d'instances ------------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    public function toString() {
        $msg = 'id : ' . $this->id . '<br>';
        $msg .= 'pseudo : ' . $this->pseudo . '<br>';
        $msg .= 'mdpSha1 : ' . $this->mdpSha1 . '<br>';
        $msg .= 'adrMail : ' . $this->adrMail . '<br>';
        $msg .= 'numTel : ' . $this->numTel . '<br>';
        $msg .= 'niveau : ' . $this->niveau . '<br>';
        $msg .= 'dateCreation : ' . $this->dateCreation . '<br>';
        $msg .= 'nbTraces : ' . $this->nbTraces . '<br>';
        $msg .= 'dateDerniereTrace : ' . $this->dateDerniereTrace . '<br>';
        return $msg;
    }
    
} // fin de la classe Utilisateur

// ATTENTION : on ne met pas de balise de fin de script pour ne pas prendre le risque
// d'enregistrer d'espaces après la balise de fin de script !!!!!!!!!!!!
