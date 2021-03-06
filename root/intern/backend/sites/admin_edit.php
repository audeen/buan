<?php

//////////////////////////////////////////////////
//  BUAN-Projekt                                //
//  Dateiname:   admin_edit.php                 //
//  Fachbereich Medien FH-Kiel - 5. Semester    //
//  Beschreibung : Edit-Page für Admins         //
//  Ersteller    : Jannik Sievert               //
//  Stand        :                              //
//  Version      : 1.0                          //
//////////////////////////////////////////////////
session_start();
?>

<!-- Sicherheitesabfrage einbinden -->
  <?php include ('../../backend/functions/authentification.php'); ?>
<!-- html-head einbinden -->
  <?php include ('../../backend/navigation/html_head.php'); ?>
  
<body>
    <!-- backend-navigation einbinden -->
  <?php include('../../backend/navigation/html_nav_be.php'); ?>
  <?php include('../../../config/config.php');?>
  <?php include($lang_admin_edit); ?>

<div class="container">

  <div class="alert alert-warning mt-3" role="alert">
    <?php echo $lang_adminedit[$_SESSION['language']][0];?>
  </div>

<?php

include ('../../backend/admin/admin_pw_reset.php');
include ('../../backend/admin/admin_update.php');

// Datenbankverbindung herstellen
$pdo;

// Wenn keine ID übermittelt, zeige alle an
if (empty($_POST['id_a'])) {
  echo "<meta http-equiv=\"refresh\" content=\"0;url=admin_show.php\">";
}

// Ausgabe von Cards je admin
$sql = "SELECT * FROM admins WHERE id_a=\"".$_POST['id_a']."\"";
echo "<div class=\"row\">\n";
  foreach ($pdo->query($sql) as $row) {

    echo "<div class=\"col-md-4\">\n";
      echo "<div class=\"card mb-3\">\n";
        echo "<form action=\"#\" method=\"POST\">";
          echo "<h3 class=\"card-header\">ID: ".$row['id_a']."</h3>\n";
          echo "<div class=\"card-body\">\n";
            echo "<p>Name</p>";
            echo "<input class=\"form-control mb-2\" id=\"exampleFormControlTextarea1\" rows=\"3\" name=\"a_name\" value=\"".$row['a_name']."\">";
            echo "<p>Mail</p>";
            echo "<input class=\"form-control mb-2\" id=\"exampleFormControlTextarea1\" rows=\"3\" name=\"a_mail\" value=\"".$row['a_mail']."\">";
        echo "</div>\n";
        echo "<ul class=\"list-group list-group-flush\">\n";
          echo "<li class=\"list-group-item\">";            
            // Radio-Button-Belegung abfragen
            if ($row['a_blocked'] == 0) {
              $blocked = "";
              $active ="checked";
            }
            else{
              $blocked = "checked";
              $active ="";
            }
            //Abfragen, ob aktiver Admin seinen Status ändern möchte
            if($_SESSION['user_id_a'] == $_POST['id_a']) {
              echo $lang_adminedit[$_SESSION['language']][9];
            }
            else {
              echo "<div class=\"form-check mb-2\">\n";
                echo "<h5>Status:</h5>";
                echo "<input class=\"form-check-input\" type=\"radio\" name=\"a_blocked\" id=\"exampleRadios1\" value=\"0\"".$active."\n";
                echo "<label class=\"form-check-label\" for=\"exampleRadios1\">\n";
                  echo $lang_adminedit[$_SESSION['language']][1];
                echo "</label>\n";
              echo "</div>";
              echo "<div class=\"form-check mb-2\">\n";
                echo "<input class=\"form-check-input\" type=\"radio\" name=\"a_blocked\" id=\"exampleRadios1\" value=\"1\"".$blocked."\n";
                echo "<label class=\"form-check-label\" for=\"exampleRadios1\">\n";
                  echo $lang_adminedit[$_SESSION['language']][2];
                echo "</label>\n";
              echo "</div>";
            }
            echo "</li>\n";
          echo "</ul>\n";  
          echo "<ul class=\"list-group list-group-flush\">\n";
          echo "<li class=\"list-group-item\">";
            // Abfrage, ob angemeldeter Admin sein Passwort zurücksetzen möchte
            if($_SESSION['user_id_a'] == $_POST['id_a']) {
              echo $lang_adminedit[$_SESSION['language']][7];
              $_POST['a_blocked'] = "0";
            }
            else {
              echo "<button type=\"submit\" class=\"btn btn-primary mb-2 mt-2\" name=\"pw\">".$lang_adminedit[$_SESSION['language']][3]."</button>";
            }
            // Ist der Vorgang gestartet, schalte Button für E-Mail-Versand frei
            if (isset($_POST['pw'])) {
              $linkToSend = urlencode($linkToSend);
              echo "<br>";
              echo "<a class=\"btn btn-outline-success\" href=\"mailto:".$_POST['a_mail']."?subject=".$lang_adminedit[$_SESSION['language']][3]."&body=".$linkToSend."\">".$lang_adminedit[$_SESSION['language']][10]."</a>";
            }
          echo "</li>\n";
          echo "</ul>\n";
   
          echo "<div class=\"card-body\">\n";
            echo "<button type=\"submit\" class=\"btn btn-outline-success mb-2 mt-2 mr-2\" name=\"update\">".$lang_adminedit[$_SESSION['language']][5]."</button>";
            echo "<input type=\"reset\" class=\"btn btn-outline-warning mb-2 mt-2 mr-2\" value=".$lang_adminedit[$_SESSION['language']][11]." formnovalidate>";
            echo "<a type=\"button\" href=\"../../backend/sites/admin_show.php\" class=\"btn btn-outline-danger mb-2 mt-2 mr-2 float-left\">".$lang_adminedit[$_SESSION['language']][6]."</a>";
            echo "<input type=\"hidden\" name=\"id_a\" value=\"".$row['id_a']."\">";
            echo "<input type=\"hidden\" name=\"a_saved\" value=\"".time()."\">";
          echo "</div>\n";
        echo "</form>";
        echo "<div class=\"card-footer text-muted\">\n";
          echo $lang_adminedit[$_SESSION['language']][8].(date("d.m.Y, H:i:s",$row['a_saved']));
        echo "</div>\n";
      echo "</div>\n";
    echo "</div>\n";
  }
echo "</div>";



?>

</div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="../../js/jquery-3.4.1.min.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <?php /* include ("../../control/control.php") */;?>
  </body>


