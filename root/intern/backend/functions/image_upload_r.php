<?php

//////////////////////////////////////////////////
//  BUAN-Projekt                                //
//  Dateiname:   image_upload_r.php             //
//  Fachbereich Medien FH-Kiel - 5. Semester    //
//  Beschreibung : Bilderupload Händler         //
//  Ersteller    : Jannik Sievert               //
//  Stand        :                              //
//  Version      : 1.0                          //
//////////////////////////////////////////////////

// Orientiert an: 
// https://www.codingcage.com/2014/12/file-upload-and-view-with-php-and-mysql.html

// Wert aus POST in Variable schreiben
if (isset($_POST['id_r'])){
    $id_r =  $_POST['id_r'];
}

//Update gesetzt?
 if(isset($_POST['update'])) 
 {
  $pdo; 
  $imgFile = $_FILES['image']['name'];
  $tmp_dir = $_FILES['image']['tmp_name'];
  $imgSize = $_FILES['image']['size'];
  $image = $_FILES['image']['name'];
  $imgExt = strtolower(pathinfo($image,PATHINFO_EXTENSION));

   //Uploadverzeichnis wählen
   $upload_dir = '../../../images/retailer/'; 

    // Bilddaten beziehen und Erweiterung auslesen, Zufällige Zahl als Namen festlegen, um Dopplungen zu vermeiden

    $image = rand(1000,1000000).".".$imgExt;
    
   // Zugelassene Dateiendungen wählen
   $valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
      
   // Prüfe Dateiendung gegen zugelassene Dateiendungen
   if(in_array($imgExt, $valid_extensions)){   
    // Dateigröße prüfen
    if($imgSize < 5000000)    {
     move_uploaded_file($tmp_dir,$upload_dir.$image);
    }
    else{
        //ÜBERSETZEN
     $errMSG = "Sorry, your file is too large.";
    }
   }
   else{
    $errMSG = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";  
   }
    
   //Kein Fehler? Dann weiter
   if(!isset($errMSG) && (isset($_POST['id_r'])))
   {
       $query = "UPDATE 
                     `retailer`
                 SET 
                     `r_img` = :r_img
                 WHERE 
                     `id_r` =:id_r";
   
   $pdoResult = $pdo->prepare($query);

   $pdoExec = $pdoResult->execute(array(
      ":r_img"=>$image,
      ":id_r"=>$id_r));

  }
}