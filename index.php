<?php
require_once( "classes/class.siteform.php" );
require_once( "classes/class.lidmaatschap.php" );
require_once( "classes/class.leden.php" );
/*
 *
 * -------------------------------------------------------
 * FILENAME: index
 * FILE:     index.php
 * TABLES :  leden,lidmaatschap
 * MYSQL DB: dbloi
 * -------------------------------------------------------
 * Created by : D.M.Keteldijk
 * project : LOI-inzendopdracht051R6
 * -------------------------------------------------------
 *
 */
@session_start();

//init (naw) registratie
$lid = new leden;
$lidmaatschap = new lidmaatschap("lidmaatschap");

//form generator
$regform = new siteform("registratie");

//process form
if (isset($_POST['thisform'])) {
    $form = $_POST['thisform'];
    
if ($regform->processrequest($_POST)) {

        //is valid form 
        if ($form == "registratie") {
            if (!isset($_SESSION['aspirantlid']))
                $_SESSION['aspirantlid'] = $regform->formfields;				
				
            //nawgegevens ok, nu naar lidmaatshap form 
            $regform = new siteform("lidmaatschap");
            $regform->formfields = $lidmaatschap->formfields();
            $regform->initForm();
        }
        if ($form == "lidmaatschap") {
		
		 if(!isset($_SESSION['aspirantlid'])) //terug naar af
		  header("location:index.php");
		   
		   //data to object			
            $lidmaatschap->update($_POST);
            
            //save all
            $lid->save($_SESSION['aspirantlid']);
            $lidmaatschap->lidnummer = $lid->id;
            if ($lidmaatschap->save()){
                $mail_verzonden = $lid->sendemail($lidmaatschap->data());				
			}
        }
    } //errors displayed in formHTML 
}// form posted
else {
    //standaard eerste scherm naw-registratie
    $regform->formfields = $lid->formfields();
    $regform->initForm();
}

if (isset($mail_verzonden)){
    $regform->formHTML = $mail_verzonden;
	unset($_SESSION['aspirantlid']);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
            <link href="style.css" media="all" rel="stylesheet" type="text/css" />
    
    </head>
    <body>
        <div class="container">
            <?php echo $regform->formHTML; ?>
        </div>
    </body>
</html>
