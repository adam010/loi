<?php
/*
 *
 * -------------------------------------------------------
 * CLASSNAME:        lidmaatschap
 * CLASS FILE:       class.lidmaatschap.php
 * FOR MYSQL TABLE:  lidmaatschap
 * MYSQL DB:         dbloi
 * -------------------------------------------------------
 * Created by : D.M.Keteldijk
 * project : LOI-inzendopdracht051R6
 * -------------------------------------------------------
 *
 */
class lidmaatschap
{
   var $id; 
   var $lidnummer; 
   var $datumingang; 
   var $datumeinde; 
   var $sportonderdeel; 
   var $lesdag; 
   
   public function __construct()
   {
   }
   public function update( $data )
   {
      foreach ( $data as $key=>$val )
         $this->{$key} = $val;
      return true;
   }
   public function formfields()
   { //return alle class properties
      return get_object_vars( $this );
   }
   public function save()
   { //opslaan
      return $this->insert();
   }
      public function data()
   {
      $data = "<table>
	  <tr><td>Sportonderdeel :</td><td><b>{$this->sportonderdeel}</b><br></td></tr>
        <tr><td>Ingangsdatum :</td><td>{$this->datumingang}</td></tr>
        <tr><td>Einddatum :</td><td>{$this->datumeinde}<br></td></tr>
 
        <tr><td>Lesdag :</td><td>{$this->lesdag}</td></tr>
                
      </table>";
      return $data;
   }
   private function insert()
   {
      $this->id    = ""; //autoinc.
      $datumingang = $this->datumingang;
      $datumeinde  = $this->datumeinde;
      $datumingang = $this->sqldate( $datumingang );
      $datumeinde  = $this->sqldate( $datumeinde );
      $insertSQL   = "INSERT INTO lidmaatschap ( id,lidnummer,datumingang,datumeinde,sportonderdeel,lesdag ) VALUES ( '$this->id','$this->lidnummer','$datumingang','$datumeinde','$this->sportonderdeel','$this->lesdag' )";
      $result      = $this->queryExecute( $insertSQL );
      if ( $result )
         $this->id = mysql_insert_id();
      return $result;
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
   private function sqldate( $dmy )
   {
      $ymd      = explode( "/", $dmy );
      $sqldatum = $ymd[2] . $ymd[1] . $ymd[0];
      return $sqldatum;
   }
} // class : end	
?>