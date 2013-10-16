<?php
require_once( 'Connections/connection.php' );
/*
 *
 * -------------------------------------------------------
 * CLASSNAME:        siteform, genereren formulieren
 * CLASS FILE:       siteform.class.php
 * FOR MYSQL TABLE:  leden,lidmaatschap
 * MYSQL DB:         dbloi
 * -------------------------------------------------------
 * Created by : D.M.Keteldijk
 * project : LOI-inzendopdracht051R6
 * -------------------------------------------------------
 *
 */
class siteform
{
   public $titel = "REGISTRATIE";
   public $omschrijving = "Vul uw login gegevens in om in te loggen";
   public $formHTML = "";
   public $loginid = 0;
   public $mode = "normal";
   public $formfields = array();
   public $formtype = "";
   public $formpost = "";
   public $accesform = false;
   private $listofsports = array( "Zwemmen"=>"Zwemmen", "Tennis"=>"Tennis", "Basketbal"=>"Basketbal", "Voetbal"=>"Voetbal", "Atletiek"=>"Atletiek", "Schaatsen"=>"Schaatsen","Hockey"=>"Hockey" );
   private $lesdagen = array( "Maandag" =>" Maandag", "Dinsdag"=>"Dinsdag", 
       "Woensdag"=>"Woensdag", "Donderdag"=>"Donderdag", 
       "Vrijdag"=>"Vrijdag", "Zaterdag"=>"Zaterdag" );
  
   private $allowed_postcodepattern = "/^[1-9][0-9]{3}[\s]?[A-Za-z]{2}$/i";
   private $allowed_stringpattern = "/[a-zA-Z]*[^\s][a-zA-Z]{2,}+$/";
   private $allowed_passwordpattern = "/^[a-zA-Z]{8,}$/";
   private $allowed_emailpattern = "/^[^0-9][a-zA-Z0-9]{2,}@[a-zA-Z]{2,}\.[a-zA-Z]{2,3}$/";
   private $allowed_datumpattern = "%^(0[1-9]{1}|[12]{1}[0-9]{1}|3[01]{1})\/(0[1-9]{1}|1[0-2]{1})\/([12]{1}[0-9]{3})$%";
   private $errors = array();
  
   
   public function __construct( $param )
   {
      //param source kan zijn Qry-string of form $_POST
      if ( is_string( $param ) )
         $this->formtype = $param; //inlog / registratie verzoek
      else if ( is_array( $param ) ) { //posted form
         $this->formpost = $param;
         $this->formtype = $param['thisform'];
      } //is_array( $param )
   }
   public function initForm()
   {
      //init. formulier creatie
      $this->errors = array();
      switch ( $this->formtype ) {
         case "registratie":
            //$this->formfields   = $this->register_fields;
            $this->titel        = "REGISTRATIE";
            $this->omschrijving = "Vul onderstaand formulier in om te registreren";
            $this->accesform    = true;
            break;
         case "lidmaatschap":
            $this->titel        = "SPORT LIDMAATSCHAP";
            $this->omschrijving = "Ingangsdatum en sportonderdeel";
            $this->accesform    = true;
      } 
      $this->formHTML = $this->formcreate();
      return true;
   }
   public function processrequest( $post )
   {
      ////posted formulier verwerken
      
	  $this->formtype=$post['thisform'];
	  
	  //sanitize post  
		array_shift($post);//remove thisform elem.
		array_pop($post); //remove html button elem.
	  
	  $this->formfields = $post;
	 	  
      //form invoer valideren
      if ( !$this->validateForm( $this->formfields ) ) {
         $this->formHTML = $this->formcreate(); //retourneren posted form
         return false;
      } //!$this->validateForm( $this->formfields )
		  
      return true;
   }  
   public function getform( $fields )
   {
      //fill form fields with values
      $this->formfields = $fields;
      return $this->formcreate();
   }
   
    private function validateForm( $data )
   {
      //per veld de data toetsen en fouten 
      //opvangen in de array-error object, leeg betekent geen fouten        
      $this->errors = array(); //start fresh
	  
      foreach ( $data as $key=>$value )
         $this->errors[$key] = $this->validate( $key, $value );
      $formvalid = array_filter( $this->errors );
      //var_dump($this->errors);
      return empty( $formvalid );
   }
   
   // data validatie per veld 
   private function validate( $fieldname, $fieldvalue )
   {
      if ( $fieldname == "id" || $fieldname == "tussenvoegsel" )
         return false;
      if ( empty( $fieldvalue ) )
         return $this->valueError( $fieldname );
      if ( $fieldname == 'email' )
         return $this->validateEmail( $fieldvalue );
      else if ( ( $fieldname == 'voornaam' || $fieldname == 'achternaam' || $fieldname == 'woonplaats' ) ) {
         if ( preg_match( $this->allowed_stringpattern, $fieldvalue ) != 1 )
            return "<span>Fout :</span>
                <ul class=\"foutlst\">
			<li>$fieldname is te kort,min. 3 tekens </li>
			<li>Alleen letters toegestaan</li>
			<li>Geen speciale teken toegestaan</li>
                </ul>";
      } //( $fieldname == 'voornaam' || $fieldname == 'achternaam' || $fieldname == 'woonplaats' )
      else if ( ( $fieldname == 'geslacht' || $fieldname == 'sportonderdeel' || $fieldname == 'lesdag' ) && empty( $fieldvalue ) )
         return $this->valueError( $fieldname );
      else if ( $fieldname == 'huisnummer' && ( !is_numeric( $fieldvalue ) ) )
         return $this->valueError( $fieldname );
      else if ( ($fieldname == 'geboortedatum' || $fieldname == 'datumingang' || $fieldname == 'datumeinde')&& ( preg_match( $this->allowed_datumpattern, $fieldvalue ) != 1 ) )
         return $this->valueError( $fieldname );
	  else if (($fieldname=='datumingang' || $fieldname=='datumeinde')&& !$this->looptijdvalid($fieldname))
	   return $this->valueError( $fieldname );
      else if ( $fieldname == 'postcode' ) {
         if ( preg_match( $this->allowed_postcodepattern, $fieldvalue ) != 1 )
            return "<span>Fout :</span>
                <ul class=\"foutlst\">                        
			<li>Alleen letters en cijfers toegestaan</li>
			<li>Geen spaties of speciale tekens gebruiken</li>
                </ul>";
      } //$fieldname == 'postcode'
      return false; //no validation errors
   }
   private function valueError( $field )
   {
     if($field=='datumingang')
	 return "<span>Fout :</span><span>$field kan niet later zijn dan datumeinde. </span>";
	 else
      return "<span>Fout :</span><span>Ongeldig waarde voor $field</span>";
	  
   }
   private function validateEmail( $email )
   {
      if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) //php built-in standaard email-validatie
         return "<span>Fout :</span>Ongeldig emailadres!";
      if ( preg_match( $this->allowed_emailpattern, $email ) != 1 ) // verificatie regex. expressie
         return "<span>Fout :</span><bR>
			<ul class=\"foutlst\">			
			<li>Alleen uit cijfers!</li>
			<li>Geen speciale tekens!</li>
			</ul>";
      return false;
   }
   private  function looptijdvalid($fieldname){
    //begindatum / einddatum check

    return(DateTime::createFromFormat('d/m/Y', $this->formfields['datumeinde']) 
	> DateTime::createFromFormat('d/m/Y', $this->formfields['datumingang']));
  }
  
   //dynamische form op basis van formtype en velden
   private function formcreate()
   {
      $fields     = $this->formfields;
      $submittype = ( $this->accesform ? "submit" : "submitUpdate" );
      
      $formdef = "<div class=\"form {$this->formtype}\"><div class=\"formheader\"><span>{$this->titel}</span><br>{$this->omschrijving}</div>";
      $formdef .= sprintf( "<form  name=\"%s\" method=\"post\" action=\"%s\">", $this->formtype, $_SERVER['PHP_SELF'] );
      $formdef .= sprintf( "<input type=\"hidden\" name=\"thisform\" id=\"thisform\" value=\"%s\"/>", $this->formtype );
      $fieldstoskip = array(
          "id",
         "gewijzigd",
         "laatstgewijzigd",
         "lidnummer" 
      );
      ksort( $this->listofsports );
      while ( list( $field, $val ) = each( $fields ) ) {
         $datumformat = ( $field == "geboortedatum" || $field == "datumingang" || $field == "datumeinde" ? "<sup>(dd/mm/yyyy)</sup>" : "" );
         $fieldtype = ( $field == "password" ? "password" : "text" );
         
         if ( $field == "id" ) {
            $formdef .= sprintf( "<input type=\"hidden\" name=\"id\" id=\"id\" value=\"%s\"/>", $val );
            continue;
         } //$field == "id"
         if ( in_array( $field, $fieldstoskip ) )
            continue;
         
         
//setup error diplay
         $errormsg  = ( empty( $this->errors[$field] ) ? "" : $this->errors[$field] );

         if ( $field == "geslacht" )
            $formdef .= sprintf( "<div class=\"formfield\"><label><strong><span>%s</span></strong>
                    <br>{$this->selectmenu(array('M'=>'Ik ben van het mannelijk geslacht', 'V'=>'Ik ben vrouw van het vrouwlijk gelacht'), $field)}</label>
                        <div class=\"err\"><span>%s</span></div></div>", ucfirst( $field ), $errormsg );
         else if ( $field == "sportonderdeel" )
            $formdef .= sprintf( "<div class=\"formfield\"><label><strong><span>%s</span></strong>
                    <br>{$this->selectmenu($this->listofsports, $field)}</label>
                        <div class=\"err\"><span>%s</span></div></div>", ucfirst( $field ), $errormsg );
         else if ( $field == "lesdag" )
            $formdef .= sprintf( "<div class=\"formfield\"><label><strong><span>%s</span></strong>
                    <br>{$this->selectmenu($this->lesdagen, $field)}</label>
                        <div class=\"err\"><span>%s</span></div></div>", ucfirst( $field ), $errormsg );
         else {
            $formdef .= sprintf( "<div class=\"formfield\"><label><strong><span class=\"label_$field[0]\">%s</span></strong>$datumformat<br>", ucfirst( $field ) );
            $formdef .= sprintf( " <input type=\"%s\" name=\"%s\" value=\"%s\"/></label>", $fieldtype, $field, $val );
            $formdef .= sprintf( "<div class=\"err\"><span>%s</span></div></div>", $errormsg );
         }
      } //while
      
	  //form navigatie
      $formdef .= sprintf( "<div class=\"button\"><label><button name=\"cancel\" type=".
	  ($this->formtype=="registratie"? "reset":"button").">".($this->formtype=="lidmaatschap"? "<div class=\"prevbtn\" onclick=\"history.back()\"></div><span>Vorig</span>":"<span>Annuleer</span>")."</button></label><label><button name=\"$submittype\">". ($this->formtype=='registratie'? '<span>Vervolg</span><div class="nextbtn"></div>':'<span>Verzenden<s/span>')."</button></label></div>" );
	  
      $formdef .= "</form></div>";
      
      return $formdef;
   }
   private function selectmenu( $listofitems, $menuname )
   { 
      $select  = "<select name=\"$menuname\" >";
      $options = "<option value=\"\">Selecteer optie</option>";
      
      foreach ( $listofitems as $option=>$value )
         $options .= "<option value=\"$option\">$value</option>";
      
      $select .= "$options </select>";
      return $select;
   }
   // utility  voor query execute
   private function queryExecute( $query )
   {
      global $database_connection;
      global $connection;
      
      mysql_select_db( $database_connection, $connection );
      $result = mysql_query( $query, $connection ) or die( "verbinding met de database werd verbroken:" . mysql_error() ); //
      if ( $result === TRUE ) //insert success
         $this->uid = mysql_insert_id();
      
      return $result;
   }
}
//end class
?>