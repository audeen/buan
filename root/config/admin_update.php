<?php
include ('config.php');
// php update data in mysql database using PDO

if(isset($_POST['update']))
{
    try {
        $pdo;
    } catch (PDOException $exc) {
        echo $exc->getMessage();
        exit();
    }
    
    // get values form input text and number
    
    $id_a = $_POST['id_a'];
    $a_name = $_POST['a_name'];
    $a_blocked = $_POST['a_blocked'];
    
    $query = "UPDATE `admins` SET `a_name`=:a_name,`a_blocked`=:a_blocked WHERE `id_a` =:id_a";
    
    $pdoResult = $pdo->prepare($query);
    
    $pdoExec = $pdoResult->execute(array(":a_name"=>$a_name,":a_blocked"=>$a_blocked,":id_a"=>$id_a));
    
    if($pdoExec)
    {
        echo 'Data Updated';
        header("Refresh:0");
    }else{
        echo 'ERROR Data Not Updated';
        header("Refresh:0");
    }

}

?>