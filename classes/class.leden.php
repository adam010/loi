<?php
require_once( '../Connections/connection.php' );

	class leden
	{ // class : begin
	
	
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
	
	
	
	
	
	// **********************
	// CONSTRUCTOR METHOD
	// **********************
	
	 public function __construct(  ){
	
	
	
	}
	
	
	// **********************
	// GETTER METHODS
	// **********************
        public function register($lid){          
           foreach($this as $key => $value) 
               $this->{$key}=$lid[$key];       
          $this->save();      
        }
	
	function getid()
	{
	return $this->id;
	}
	
	function getvoornaam()
	{
	return $this->voornaam;
	}
	
	function gettussenvoegsel()
	{
	return $this->tussenvoegsel;
	}
	
	function getachternaam()
	{
	return $this->achternaam;
	}
	
	function getstraat()
	{
	return $this->straat;
	}
	
	function gethuisnummer()
	{
	return $this->huisnummer;
	}
	
	function getpostcode()
	{
	return $this->postcode;
	}
	
	function getwoonplaats()
	{
	return $this->woonplaats;
	}
	
	function getemail()
	{
	return $this->email;
	}
	
	function getgeboortedatum()
	{
	return $this->geboortedatum;
	}
	
	function getgeslacht()
	{
	return $this->geslacht;
	}
	
	// **********************
	// SETTER METHODS
	// **********************
	
	
	function setid($val)
	{
	$this->id =  $val;
	}
	
	function setvoornaam($val)
	{
	$this->voornaam =  $val;
	}
	
	function settussenvoegsel($val)
	{
	$this->tussenvoegsel =  $val;
	}
	
	function setachternaam($val)
	{
	$this->achternaam =  $val;
	}
	
	function setstraat($val)
	{
	$this->straat =  $val;
	}
	
	function sethuisnummer($val)
	{
	$this->huisnummer =  $val;
	}
	
	function setpostcode($val)
	{
	$this->postcode =  $val;
	}
	
	function setwoonplaats($val)
	{
	$this->woonplaats =  $val;
	}
	
	function setemail($val)
	{
	$this->email =  $val;
	}
	
	function setgeboortedatum($val)
	{
	$this->geboortedatum =  $val;
	}
	
	function setgeslacht($val)
	{
	$this->geslacht =  $val;
	}
	
	// **********************
	// SELECT METHOD / LOAD
	// **********************
	
	function select($id)
	{
	
	$sql =  "SELECT * FROM leden WHERE  = $id;";
	$result =  $this->database->query($sql);
	$result = $this->database->result;
	$row = mysql_fetch_object($result);
	if($row){
	
	$this->id = $row->id;
	
	$this->voornaam = $row->voornaam;
	
	$this->tussenvoegsel = $row->tussenvoegsel;
	
	$this->achternaam = $row->achternaam;
	
	$this->straat = $row->straat;
	
	$this->huisnummer = $row->huisnummer;
	
	$this->postcode = $row->postcode;
	
	$this->woonplaats = $row->woonplaats;
	
	$this->email = $row->email;
	
	$this->geboortedatum = $row->geboortedatum;
	
	$this->geslacht = $row->geslacht;
	}
        else return false;
	}
	
	// **********************
	// DELETE
	// **********************
	
	function delete($id)
	{
	$sql = "DELETE FROM leden WHERE  = $id;";
	$result = $this->database->query($sql);
	
	}
	
	// **********************
	// INSERT
	// **********************
	
	function save()
	{
	$this->id= ""; 
	
	$insertSQL = "INSERT INTO leden ( id,voornaam,tussenvoegsel,achternaam,straat,huisnummer,postcode,woonplaats,email,geboortedatum,geslacht ) VALUES ( '$this->id','$this->voornaam','$this->tussenvoegsel','$this->achternaam','$this->straat','$this->huisnummer','$this->postcode','$this->woonplaats','$this->email','$this->geboortedatum','$this->geslacht' )";
	$result = $this->queryExecute( $insertSQL );
        if ( $result )
            return  mysql_insert_id();
        return false;
	
	}
	
	// **********************
	// UPDATE
	// **********************	
	function update($id)
	{
	$sql = " UPDATE leden SET  id = '$this->id',voornaam = '$this->voornaam',tussenvoegsel = '$this->tussenvoegsel',achternaam = '$this->achternaam',straat = '$this->straat',huisnummer = '$this->huisnummer',postcode = '$this->postcode',woonplaats = '$this->woonplaats',email = '$this->email',geboortedatum = '$this->geboortedatum',geslacht = '$this->geslacht' WHERE  = $id ";
	$result = $this->database->query($sql);
	
	}
          private function queryExecute( $query )
    {
        global $database_connection;
        global $connection;
        mysql_select_db( $database_connection, $connection );
        $result = mysql_query( $query, $connection ) or die( "verbinding met de database werd verbroken:" . mysql_error() );
        
        if ( $result === TRUE )
            $this->uid = mysql_insert_id();
        
        return $result;
    }
	} // class : end	
	?>