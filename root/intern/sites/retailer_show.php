<!-- Include Security-File -->
<?php include ('../../config/security.php'); ?>

<!DOCTYPE html>
<html lang="de">
  <!-- html-head einbinden -->
  <?php include ('../../config/html_head.html'); ?>
  <body>
    <!-- backend-navigation einbinden -->
    <?php include ('../../config/html_nav_be.php'); ?>

    <div class="container">
      <div class="alert alert-primary mt-3" role="alert">
        Alle H&auml;ndler
      </div>
      <?php include ('../../config/retailer_query.php'); ?>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="../js/jquery-3.4.1.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <?php include ("../control/control.php");?>
  </body>
</html>