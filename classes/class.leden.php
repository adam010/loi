<?php
require_once( 'Connections/connection.php' );
/*
 *
 * -------------------------------------------------------
 * CLASSNAME:        leden
 * CLASS FILE:       class.leden.php
 * FOR MYSQL TABLE:  leden
 * MYSQL DB:         dbloi
 * -------------------------------------------------------
 * Created by : D.M.Keteldijk
 * project : LOI-inzendopdracht051R6
 * -------------------------------------------------------
 *
 */
class leden
{
   var $id;
   var $voornaam;
   var $tussenvoegsel;
   var $achternaam;
   var $geboortedatum;
   var $geslacht;
   var $email;
   var $straat;
   var $huisnummer;
   var $postcode;
   var $woonplaats;
   
   
   public function __construct()
   {
   }
   public function formfields()
   { //return alle class properties
      return get_object_vars( $this );
   }
   public function save( $data )
   { 
      foreach ( $data as $key=>$val )
         $this->{$key} = $val;
      return $this->insert();
   }
   public function sendemail( $data )
   {
      return $this->sendmail( $data );
   }
   private function insert()
   {
      //format geb.datum
	  $geboortedatum= DateTime::createFromFormat('d/m/Y', $this->geboortedatum)->format('Ymd');
      //$ymd           = explode( "/", $this->geboortedatum );
      //$geboortedatum = $ymd[2] . $ymd[1] . $ymd[0];
      $insertSQL     = "INSERT INTO leden ( voornaam,tussenvoegsel,achternaam,straat,huisnummer,postcode,woonplaats,email,geboortedatum,geslacht,gewijzigd) 
             VALUES ('" . ucfirst( $this->voornaam ) . "','" . $this->tussenvoegsel . "','" . ucfirst( $this->achternaam ) . "','" . ucfirst( $this->straat ) . "','" . ucfirst( $this->huisnummer ) . "','" . ucfirst( $this->postcode ) . "','" . ucfirst( $this->woonplaats ) . "','" . strtolower( $this->email ) . "','" . $geboortedatum . "','" . $this->geslacht . "','" . date( 'Ymd' ) . "')";
      return ( $this->queryExecute( $insertSQL ) );
   }
 
   private function sendmail( $lidmaatschapdata )
   {
      $subj    = "Uw registratie";
      //$from    = "Inzendopdracht@loi.nl";      
      $headers = "MIME-Version: 1.0\r\n";
      $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	  //$headers .= "From:" . $from;
	  $gegevens =$this->lidgegevens( $lidmaatschapdata ) ;
      $body     = $this->mailtext( $gegevens ) ;
	  $adminbody= $this->adminmailtext( $gegevens ) ;
	  $adminmailadres= ini_get('sendmail_from');	  
	  if (!mail( "$adminmailadres", "Nieuwe registratie", $adminbody, $headers ))
		return $this->bedankt( false );
      $result  = mail( "{$this->email}", $subj, $body, $headers );
      return $this->bedankt( $result );
   }
   private function bedankt( $success )
   {
      if ( $success )
         $result = <<<BEDANKT
                    <div class='msgcontainer'>
            <span class='titel'>PROFICIAT EN BEDANKT!</span>
                        <p>Uw registratie is geslaagd!<br>
            Controleer uw mailbox voor de ontvangstbevestiging</p>
			<p><a href="index.php"> HOME</a></p>
                    </div>
BEDANKT;
      else {
         $result = <<<FAILED
                    <div class='msgcontainer'>
            <span class='titel'>HELAAS!</span>
                        <p>Uw registratie kon niet worden verzonden!<br>
            Controleer uw internet verbinden of <a href="{$_SERVER['PHP_SELF']}"> probeer later opnieuw</a>!</p>
                    </div>
FAILED;
      }
      return $result;
   }
   private function lidgegevens($data){
    $gegevens= "<p><b>Lidnummer : $this->id</b> <br>Persoonsgegevens : {$this->voornaam} {$this->tussenvoegsel} {$this->achternaam}<br> 
                       Adresgegevens :  {$this->straat} {$this->postcode} {$this->woonplaats}<br>
                       Contactgegevens : {$this->email}<br><br></p>
                       <p> <b>Lidmaatschap:</b><br>
                       $data
					   </p>";
	return $gegevens;
   }
   private function mailtext($data)
   { 
      $aanhef = ( $this->geslacht == 'M' ? 'meneer' : 'mevrouw' );
      $tekst  = <<<REGMAIL
                    Beste $aanhef {$this->voornaam} {$this->tussenvoegsel} {$this->achternaam},<br>
                    <p>Uw registratie is geslaagd! <br>Hieronder vindt u de gegevens die wij van u hebben geregistreerd.</p>
					$data                           
                    <p>Wij wensen u veel sport plezier!</p>
                    Mvgr,<br>
                    Het bestuur.
REGMAIL;
      return $tekst;
   }
  private function adminmailtext($data)
   { 
      $aanhef = ( $this->geslacht == 'M' ? 'Meneer' : 'Mevrouw' );
      $tekst  = <<<REGMAIL
                    Beste Administrator,<br>
                    <p>Er is nieuwe inschrijving ontvangen. <br>
					Hieronder vindt u de gegevens die zijn geregistreerd.</p>
					$data                           
                    <p>Dit bericht is automatisch verzonden!</p>                    
REGMAIL;
      return $tekst;
   }
   private function queryExecute( $query )
   {
      global $database_connection;
      global $connection;
      mysql_select_db( $database_connection, $connection );
      $result = mysql_query( $query, $connection ) or die( "verbinding met de database werd verbroken:" . mysql_error() );
      if ( $result === TRUE )
         $this->id = mysql_insert_id();
      return $result;
   }
}
// class : end	
?>