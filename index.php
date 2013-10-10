<?php
require_once( "classes/class.siteform.php" );
require_once( "classes/class.lidmaatschap.php" );
require_once( "classes/class.leden.php" );
@session_start();

$lid=new leden;
$lidmaatschap=new lidmaatschap();
//
    $regform=new siteform("registratie");
    
//init (naw) registratie


//process form
if(isset($_POST['thisform'])){
  $form= $_POST['thisform'];
  //sanitize post  
  array_shift($_POST);
  array_pop($_POST);
  
  if($regform->processrequest($_POST))      {
      //is valid form 
     if($form=="registratie"){ 
         if(!isset($_SESSION['aspirantlid']))
            $_SESSION['aspirantlid']=$regform->formfields;                
            
        //naw ok, nu naar lidmaatshap form 
        $regform=new siteform("lidmaatschap");
        
        $regform->formfields=$lidmaatschap->formfields();      
     }
     
     if($form=="lidmaatschap" ){
       //data to object
       $lidmaatschap->update($_POST);
       //save all
       $lid->save($_SESSION['aspirantlid']);
       $lidmaatschap->lidnummer=$lid->id; 
       if ($lidmaatschap->save())
           $mail_verzonden=$lid->sendemail($lidmaatschap->data());
      
     }
      
  }        
  else{$fields=$_POST;}  

   }// form posted
else{    
$regform->formfields=$lid->formfields();
}
$regform->initForm();       
if(isset($mail_verzonden))    
    $regform->formHTML=$mail_verzonden;



   
//     else if($_POST['thisform']=="lidmaatschap"){     
//      $lidmaatschapDetails=$regform->formfields;   
//      $lidnummer=$lid->register($_SESSION['aspirantlid']);
//      $lidmaatschapDetails->lidnummer=$lidnummer;
//      $lidmaatschap->submit(litmaatschapDetails);
//     }
   


//$regform->formfields=$fields;
//$regform->formHTML=$regform->getform($fields)
;?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php        
        echo $regform->formHTML
        ?>
    </body>
</html>
