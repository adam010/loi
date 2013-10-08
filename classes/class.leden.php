<?php
require_once( 'Connections/connection.php' );

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
	
public function nawformfields(){
    return get_object_vars($this);
}	

	
    public function save($data){   
        
        foreach($data as $key=> $val)
            $this->{$key}=$val;
        
        $this->id= $this->insert();
    }
    
    private function insert(){     
	$this->id= ""; 	
	$insertSQL = "INSERT INTO leden ( id,voornaam,tussenvoegsel,achternaam,straat,huisnummer,postcode,woonplaats,email,geboortedatum,geslacht ) VALUES ( '$this->id','$this->voornaam','$this->tussenvoegsel','$this->achternaam','$this->straat','$this->huisnummer','$this->postcode','$this->woonplaats','$this->email','$this->geboortedatum','$this->geslacht' )";
	$result = $this->queryExecute( $insertSQL );
        if ( $result )
            return  mysql_insert_id();
        return false;
	
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