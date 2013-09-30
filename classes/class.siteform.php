<?php

require_once('../Connections/connection.php');
/*
 *
 * -------------------------------------------------------
 * CLASSNAME:        siteform
 * CLASS FILE:       siteform.class.php
 * FOR MYSQL TABLE:  users
 * MYSQL DB:         dbloi
 * -------------------------------------------------------
 * Created by : D.M.Keteldijk
 * project : LOI-inzendopdracht051R5
 * -------------------------------------------------------
 *
 */

class siteform
{
    
    public $titel = "INLOGGEN";
    public $omschrijving = "Vul uw login gegevens in om in te loggen";
    public $formHTML = "";
    public $loginid = 0;
    public $mode = "normal";
    public $formfields = array();
    public $formtype = "";
    public $formpost = "";
    public $accesform = false;

    private $register_fields = array("id" => "", "naam" => "", "username" => "", "email" => "", "password" => "");
    private $listofsports= array("Zwemmen","Tennis","Basketbal","Voetbal","Atletiek","Schaatsen");
        private $allowed_postcodepattern ="/^[1-9][0-9]{3}[\s]?[A-Za-z]{2}$/i";
            //"/^\W*[1-9]{1}[0-9]{3}\W*[a-zA-Z]{2}\W*$/";
            //"/^([0-9]){4}([a-z][A-Z]){2}?$/";
    private $allowed_stringpattern = "/[a-zA-Z]*[^\s][a-zA-Z]{2,}+$/";
    private $allowed_passwordpattern = "/^[a-zA-Z]{8,}$/";
    private $allowed_emailpattern = "/^[^0-9][a-zA-Z]{2,}@[a-zA-Z]{2,}\.[a-zA-Z]{2,3}$/";
    private $allowed_datumpattern ="%^(0[1-9]{1}|[12]{1}[0-9]{1}|3[01]{1})\/(0[1-9]{1}|1[0-2]{1})\/([12]{1}[0-9]{3})$%";
            ///"/^(0[1-9]{1}|[12]{1}[0-9]{1}|3[01]{1})\/(0[1-9]{1}|1[0-2]{1})\/([12]{1}[0-9]{3})$/";
    
    
    private $errors = array();
    
    public function __construct($param)
    {
        //param source kan zijn Qry-string of form $_POST
        if (is_string($param))
            $this->formtype = $param; //inlog / registratie verzoek
        
        else if (is_array($param)) { //posted form
            $this->formpost = $param;
            $this->formtype = $param['thisform'];
        }
        $this->initForm($this->formtype); //form velden init.
        $this->formHTML = $this->formcreate(); //leeg form genereren
    }
    
    public function initForm() //juiste velden toewijzen aan formtype
    {
        $this->mode   = (isset($this->formpost['submitUpdate']) ? "update" : "normal");
        $formtype     = $this->formtype;
        $this->errors = array();
        
        switch ($formtype) {
            case "registratie":
                $this->formfields   = $this->register_fields;
                $this->titel        = "REGISTRATIE";
                $this->omschrijving = "Vul onderstaand formulier in om te registreren";
                $this->accesform    = true;
                break;
            case "update":
                $this->formfields   = $this->register_fields;
                $this->titel        = "GEGEVENS WIJZIGEN";
                $this->omschrijving = "";
                $this->accesform    = false;
                break;
                $this->accesform  = true;
        }
        return true;
    }
    
    //posted formulier verwerken
    public function processrequest($fields)
    {
        //overeenkomende $key/$value paar in form post,
        //mappen naar formfield array
        $this->formfields = $fields;
        
        
        //form invoer valideren
        if (!$this->validateForm($this->formfields)) {          
            $this->formHTML = $this->formcreate(); //retourneren posted form
            return false;
        }
        
        
        
        if ($this->formtype == "registratie" || $this->formtype == "update") {
            //check als email bestaat bij registratie             
            if ($this->IsEmailRegistered($this->formfields['email'])) {               
                $this->formHTML = $this->formcreate(); 
                return false;
            }
        }
        
        $this->formHTML = $this->formHTML = $this->formcreate();
        return true;
    }
    
    public function validateForm($data)
    {
        //per veld de data toetsen en fouten 
        //opvangen in de array-error object, leeg betekent geen fouten        
        $this->errors = array(); //start fresh
        foreach ($data as $key => $value)
            $this->errors[$key] = $this->validate($key, $value);
        $formvalid = array_filter($this->errors);
        var_dump($this->errors);
        return empty($formvalid);
    }
    
    public function userRegistered()
    {
        if ($this->loginid > 0)
            return true;
        else
            return false;
    }
    
    public function getform($fields)
    {
        //fill form fields with values
        $this->formfields = $fields;
        return $this->formcreate();
    }
    
    // data validatie per veld 
    private function validate($fieldname, $fieldvalue)
    {
       
    if($fieldname=="id" || $fieldname=="tussenvoegsel") return false;
          
        if (empty($fieldvalue))
             return $this->valueError($fieldname); 
       if ($fieldname == 'email')
            return $this->validateEmail($fieldvalue);
       else if ( ($fieldname == 'voornaam' ||               
             $fieldname == 'achternaam' ||
             $fieldname == 'straat' ||
             $fieldname == 'woonplaats') ){
              if (preg_match($this->allowed_stringpattern, $fieldvalue) != 1)
            return "<span>Fout :</span>
                <ul class=\"foutlst\">
			<li>$fieldname is te kort,min. 3 tekens </li>
			<li>Alleen letters toegestaan</li>
			<li>Geen speciale teken toegestaan</li>
                </ul>";
        }  
               
        else if ($fieldname == 'geslacht' && $fieldvalue =='-1' ) 
                                               
                  return $this->valueError($fieldname);
        else if ($fieldname == 'huisnummer' && (!is_numeric ($fieldvalue))) 
                  return $this->valueError($fieldname);
        else if($fieldname == 'geboortedatum' && 
                         (preg_match($this->allowed_datumpattern, $fieldvalue) != 1))               
                      return $this->valueError($fieldname);                
        
       else if ($fieldname == 'postcode'){
             if (preg_match($this->allowed_postcodepattern, $fieldvalue) != 1)
                     return "<span>Fout :</span>
                <ul class=\"foutlst\">                        
			<li>Alleen letters en cijfers toegestaan</li>
			<li>Geen spaties of speciale tekens gebruiken</li>
                </ul>";
       }   
       
   
        return false; //no validation errors
    }
    private function valueError($field){
        return "<span>Fout :</span><span>Ongeldig waarde<br> voor $field</span>";
    }
    private function validateEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) //php built-in standaard email-validatie
            return "<span>Fout :</span>Ongeldig emailadres!";
        if (preg_match($this->allowed_emailpattern, $email) != 1) // verificatie regex. expressie
            return "<span>Fout :</span><bR>
			<ul class=\"foutlst\">
			<li>Alleen .nl emailadres toegestaan</li>
			<li>Alleen uit cijfers!</li>
			<li>Geen speciale tekens!</li>
			</ul>";
        if (($this->formtype == "registratie" || $this->formtype == "update")) { //emailadres moet vrij zijn bij registratie
            if ($this->IsEmailRegistered($email))
                return "<span>Fout :</span>Dit emailadres is reeds in gebruik!";
        }
        
        return false;
    }
    
    private function IsEmailRegistered($email)
    {
        $query  = "Select id from users where email='" . $email . "'";
        $result = $this->queryExecute($query);
        $row    = mysql_fetch_assoc($result);
        
        if ($row && $this->accesform)
            return true;
        else if ($this->formtype == "update" && $row['id'] == $this->loginid)
            return false;
        else if ($row)
            return true;
        return false;
    }
    
    private function isRegistered($username, $password)
    {
        //controle op user-wachw.combinatie	
        //check of username bestaat 
        
        $query = "Select id from users where username='" . $username . "'";
        
        if ($this->formtype == "inloggen") {
            $pass = MD5($password);
            $query .= " AND `password`= '" . $pass . "'";
        }
        $result = $this->queryExecute($query);
        $row    = mysql_fetch_assoc($result);
        
        if ($this->formtype == "inloggen") {
            
            if ($row)
                $this->loginid = $row['id']; // user_id beschikbaar voor inloggen	
            else
                return $this->incorrectdata($this->formtype);
        } else if ($this->formtype == "registratie") {
            if ($row) {
                if ($row['id'] == $this->loginid && $this->mode == "update") //current record 
                    return true;
            }
            //else return $this->incorrectdata($this->formtype);
            if ($row) //username taken
                return $this->incorrectdata($this->formtype);
        }
        return true;
    }
    
    private function incorrectdata($frm)
    {
        
        switch ($frm) {
            case "inloggen":
                $this->titel        = "Login mislukt";
                $this->omschrijving = "De combinatie van email en wachtwoord is onbekend!<br>U kunt zich hier <a href=\"" . $_SERVER['PHP_SELF'] . "?f=registratie\">Registreren</a>";
                
                break;
            case "registratie":
                $this->titel        = "Usernaam in gebruik";
                $this->omschrijving = "Usernaam is reeds in gebruikt!";
                break;
            default:
                $this->titel        = "INLOGGEN";
                $this->omschrijving = "Vul uw login gegevens in om in te loggen";
        }
        return false;
    }
    
    //Maak  formulier
    //dynamische form op basis van formtype en velden
    private function formcreate()
    {
        $fields     = $this->formfields;
        $dateformat = "";
        $submittype = ($this->accesform ? "submit" : "submitUpdate");
        $formdef    = sprintf("<div class=\"%s\"><span class=\"formtitle\"></span>",$this->formtype,$this->titel);
        $formdef .= sprintf("<form  name=\"%s\" method=\"post\" action=\"%s\">", $this->formtype, $_SERVER['PHP_SELF']);
        $formdef .= sprintf("<input type=\"hidden\" name=\"thisform\" id=\"thisform\" value=\"%s\"/>", $this->formtype);
        $fieldstoskip = array("id","gewijzigd","laatstgewijzigd","thuisteam","uitteam");
                      
        while (list($field, $val) = each($fields)) {
            
            $datumformat = ($field == "geboortedatum" ? "<sup>(dd/mm/yyyy)</sup>" : "");
            if ($field == "id") {
                $formdef .= sprintf("<input type=\"hidden\" name=\"id\" id=\"id\" value=\"%s\"/>", $val);
                continue;
            }
            
            if (in_array($field, $fieldstoskip))
                continue;
            $fieldtype = ($field == "password" ? "password" : "text");
            
            //setup error diplay
            $errormsg = (empty($this->errors[$field]) ? "" : $this->errors[$field]);
            if ($field == "geslacht")                 
                $formdef .= sprintf("<div class=\"formfield\"><label><strong>%s</strong>
                    <br>{$this->selectmenu(array("Man","Vrouw"),$field)}</label></div>
                        <div class=\"err\"><span>%s</span></div></div>"
                    ,ucfirst($field),$errormsg);
        
            else {                
                $formdef .= sprintf("<div class=\"formfield\"><label><strong><span class=\"label_$field[0]\">%s</span></strong>$datumformat<br>", ucfirst($field));
                $formdef .= sprintf(" <input type=\"%s\" name=\"%s\" value=\"%s\"/></label>", $fieldtype, $field, $val);                
                $formdef .= sprintf("<div class=\"err\"><span>%s</span></div></div>", $errormsg);
            }
        } //while
        $formdef .= sprintf("<div class=\"button\"><label><button name=\"$submittype\">Submit</button></label><label><button name=\"cancel\">Annuleer</button></label></div>");
        $formdef .= "</form></div>";
        
        return $formdef;
    }
private function selectmenu($listofitems,$menuname){
   sort($listofitems);
  $select="<select name=\"$menuname\" >";  
  $options="<option value=\"99\">Selecteer optie</option>";
  foreach ($listofitems as $option)
      $options .="<option value=\"$option\">$option</option>";
      
  $select .="$options </select>";
  
  return $select;
}
    // utility  voor query execute
    private function queryExecute($query)
    {
        global $database_connection;
        global $connection;
        mysql_select_db($database_connection, $connection);
        $result = mysql_query($query, $connection) or die("verbinding met de database werd verbroken:" . mysql_error()); //
        if ($result === TRUE) //insert success
            $this->uid = mysql_insert_id();
        
        return $result;
    }
    
}

//end class
?>