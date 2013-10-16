<?php

require_once( 'Connections/connection.php' );

class leden { // class : begin
    // **********************
    // ATTRIBUTE DECLARATION
    // **********************

    var $id;   // (normal Attribute)
    var $voornaam;   // (normal Attribute)
    var $tussenvoegsel;   // (normal Attribute)
    var $achternaam;   // (normal Attribute)
    var $geboortedatum;   // (normal Attribute)
    var $geslacht;   // (normal Attribute)
    var $email;   // (normal Attribute)
    var $straat;   // (normal Attribute)
    var $huisnummer;   // (normal Attribute)
    var $postcode;   // (normal Attribute)
    var $woonplaats;   // (normal Attribute)


    public function __construct() {

    }

    public function formfields() {
        return get_object_vars($this);
    }

    public function save($data) {
        foreach ($data as $key => $val)
            $this->{$key} = $val;
        return $this->insert();
    }

    private function insert() {
        
        //format 
        $ymd=explode("/",$this->geboortedatum);
        $geboortedatum=$ymd[2].$ymd[1].$ymd[0];
        $insertSQL = "INSERT INTO leden ( voornaam,tussenvoegsel,achternaam,straat,huisnummer,postcode,woonplaats,email,geboortedatum,geslacht,gewijzigd) 
             VALUES ('".ucfirst($this->voornaam)."','".$this->tussenvoegsel."','".ucfirst($this->achternaam)."','".
                ucfirst($this->straat)."','".ucfirst($this->huisnummer)."','".ucfirst($this->postcode)."','".ucfirst($this->woonplaats)."','".strtolower($this->email)."','".$geboortedatum."','".$this->geslacht."','".date('Ymd')."')";
       return($this->queryExecute($insertSQL));
            
    }

    public function sendemail($data) {
       return $this->sendmail($data);
               //==true ? 'Success':'Mail Failed!');exit;
    }

    private function sendmail($data) {
       // echo "in de mail";
       $subj = "Uw registratie";    
       $from = "loiInzendopdracht@loi.nl";
       $headers = "From:" . $from;
       $body = str_replace('#lidmaatschap', $data, $this->mailtext());
       $result= mail($this->email, $subj, $body,$headers); 
       
        return $this->bedankt($result);       
    }
private function bedankt($success){
    if ($success)
    $result = <<<BEDANKT
                    <div class='msgcontainer'>
            <span class='titel'>PROFICIAT EN BEDANKT!</span>
                        <p>Uw registratie is geslaagd!<br>
            Controleer uw mailbox op de ontvangbevestiging</p>
                    </div>
BEDANKT;
    else{
     $result =  <<<FAILED
                    <div class='msgcontainer'>
            <span class='titel'>HELAAS!</span>
                        <p?Uw registratie kon niet worden verzonden!
            Controleer uw internet verbinden of <a href="{$_SERVER['PHP_SELF']}"> probeer later opnieuw</a>!</p>
                    </div>
FAILED;
    }
return $result;
}
    
    private function mailtext() {
        $aanhef = ($this->geslacht == 'M' ? 'meneer' : 'mevrouw');
        $tekst = <<<REGMAIL
                    Beste $aanhef {$this->voornaam} {$this->tussenvoegsel} {$this->achternaam},<br>
                    <p>Uw registratie is geslaagd! Hieronder vindt u de gegevens die wij van u hebben ontangen</p>
                    <p>Persoonsgegevens: {$this->voornaam} {$this->tussenvoegsel} {$this->achternaam}<br>                    
                       Adresgegevens:  {$this->straat} {$this->postcode} {$this->woonplaats}<br>
                       Contactgegevens: {$this->email}<br>
                        <b>Lidmaatschap:</b><br>
                       #data
                           </p>
                    <p>Wij wensen u veel sport plezier!</p>
                    Mvgr,
                    Het bestuur.
REGMAIL;
        return $tekst;
    }

   
    private function queryExecute($query) {
        
        global $database_connection;
        global $connection;
        mysql_select_db($database_connection, $connection);
        $result = mysql_query($query, $connection) or die("verbinding met de database werd verbroken:" . mysql_error());

        if ($result === TRUE)
            $this->id = mysql_insert_id();
        return $result;
    }

}

// class : end	
?>