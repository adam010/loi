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
 * project : LOI-inzendopdracht051R5
 * -------------------------------------------------------
 *
 */
@session_start();

//init (naw) registratie
$lid = new leden;
$lidmaatschap = new lidmaatschap();

//form generator
$regform = new siteform("registratie");

//process form
if (isset($_POST['thisform'])) {
    $form = $_POST['thisform'];
    
    //sanitize post  
    array_shift($_POST);//remove thisform elem.
    array_pop($_POST); //remove html button elem.

    if ($regform->processrequest($_POST)) {
        //is valid form 
        if ($form == "registratie") {
            if (!isset($_SESSION['aspirantlid']))
                $_SESSION['aspirantlid'] = $regform->formfields;

            //naw ok, nu naar lidmaatshap form 
            $regform = new siteform("lidmaatschap");
            $regform->formfields = $lidmaatschap->formfields();
            $regform->initForm();
        }
        if ($form == "lidmaatschap") {
            //data to object
            $lidmaatschap->update($_POST);
            
            //save all
            $lid->save($_SESSION['aspirantlid']);
            $lidmaatschap->lidnummer = $lid->id;
            if ($lidmaatschap->save())
                $mail_verzonden = $lid->sendemail($lidmaatschap->data());
        }
    } //errors displayed in formHTML 
}// form posted
else {
    //standaard eerste scherm naw-registratie
    $regform->formfields = $lid->formfields();
    $regform->initForm();
}

if (isset($mail_verzonden))
    $regform->formHTML = $mail_verzonden;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php echo $regform->formHTML ?>
    </body>
</html>
