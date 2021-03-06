<?php

//////////////////////////////////////////////////
//  BUAN-Projekt                                //
//  Dateiname:   receipt.php                    //
//  Fachbereich Medien FH-Kiel - 5. Semester    //
//  Beschreibung : Abrechnungserstellung        //
//  Ersteller    : Jannik Sievert               //
//  Stand        : 27.11.2019                   //
//  Version      : 1.0                          //
//////////////////////////////////////////////////

// Orientiert an: 
// https://www.php-einfach.de/experte/php-codebeispiele/pdf-per-php-erstellen-pdf-rechnung/

//Sprachdatei einbinden
include($lang_receipt);

// Werte aus POST in Variablen schreiben
$retailer = $_POST['retailer'];
$year = $_POST['year'];
$month = $_POST['month'];
//Datenbankverbindung herstellen
$pdo;

//Daten beziehen
$sql =   $pdo->prepare("SELECT * 
            FROM products, retailer, orders 
            WHERE id_r  = $retailer
            AND r_id = id_r
            AND id_p = p_id
            AND order_date BETWEEN '$year-$month-01' AND '$year-$month-31'");
$sql->execute();
$row = $sql->fetch();

//Bestelldatum in Variable schreiben
$time = strtotime($row['order_date']);


// Keine Bestellungen im Monat getätigt?
if (empty($row)) {
    $sql = $pdo->prepare("SELECT *
          FROM retailer
          WHERE id_r = $retailer");
    $sql->execute();
    $row = $sql->fetch();

 }


$rechnungs_nummer = $_POST['billnumber'];
$rechnungs_datum = date("Y-m-t");
$pdfAuthor = "BUAN";

//Logo-Dummy
$rechnungs_header = '
<img src="logo.png">
BUAN
Jannik Sievert';
 
$rechnungs_empfaenger = ''.$row['r_prename'].' '.$row['r_surname'].'
'.$row['r_street'].'
'.$row['r_postal'].' '.$row['r_city'].'
'.$row['r_country'].'';
 
$pdfName = $lang_receipt[$_SESSION['language']][0].'-'.$rechnungs_nummer.".pdf";

 
 
//////////////////////////// Inhalt des PDFs als HTML-Code \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 
$html = '
<table cellpadding="5" cellspacing="0" style="width: 100%; ">
 <tr>
 <td>'.nl2br(trim($rechnungs_header)).'</td>
    <td style="text-align: right">'
    .$lang_receipt[$_SESSION['language']][1].$rechnungs_nummer.'<br>'
    .$lang_receipt[$_SESSION['language']][2].$rechnungs_datum.'<br>

 </td>
 </tr>
 
 <tr>
 <td style="font-size:1.3em; font-weight: bold;">
<br><br>'.$lang_receipt[$_SESSION['language']][0].'
<br>
 </td>
 </tr>
 
 
 <tr>
 <td colspan="2">'.nl2br(trim($rechnungs_empfaenger)).'</td>
 </tr>
</table>
<br><br><br>
 
<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0">
 <tr style="background-color: #cccccc; padding:5px;">
 <td style="padding:5px;"><b>'.$lang_receipt[$_SESSION['language']][3].'</b></td>
 <td style="text-align: center;"><b>'.$lang_receipt[$_SESSION['language']][4].'</b></td>
 <td style="text-align: center;"><b>'.$lang_receipt[$_SESSION['language']][5].'</b></td>
 <td style="text-align: center;"><b>'.$lang_receipt[$_SESSION['language']][6].'</b></td>
 </tr>';
 
// Positionen zählen
$sql =   ("SELECT * FROM products, retailer, orders 
WHERE id_r = $retailer
AND r_id = id_r
AND id_p = p_id
AND order_date BETWEEN '$year-$month-01' AND '$year-$month-31'");
$positions = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();

// Bestellungen eines Monats ausgeben
foreach ($pdo->query($sql) as $key => $row) {
    
$name = $row['p_name'];
$quantity = $row['qty'];
$pieces = $lang_receipt[$_SESSION['language']][10];
$singleprice = $row['p_price'];
$price = $row['qty']*$row['p_price'];
$key += 1;
$html .=
'<tr>
<th scope="row">'.$key.'</th>
<td style="text-align: center;">'.$name.'</td>
<td style="text-align: center;">'.$quantity.' '.$pieces.'</td>
<td style="text-align: center;">'.$singleprice.' &euro;</td>
<td style="text-align: center;">'.$price.' &euro;</td>
</tr>';


}
 
$html .="</table>";
 
$html .= '
<hr>
<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0">';
 
$html .='
            <tr>
                <td colspan="3"><b>'.$lang_receipt[$_SESSION['language']][7].'</b></td>
                <td style="text-align: center;"><b>'.$_POST['total'].' Euro</b></td>
            </tr>
            <tr>
                <td colspan="3"><b>'.$lang_receipt[$_SESSION['language']][8].' </b></td>
                <td style="text-align: center;"><b>'.$_POST['basicpay'].' Euro</b></td>
            </tr>
            <tr>
                <td colspan="3"><b>Bonus: </b></td>
                <td style="text-align: center;"><b>'.$_POST['bonus'].' Euro</b></td>
            </tr>
            <tr>
                <td colspan="3"><b>'.$lang_receipt[$_SESSION['language']][9].' </b></td>
                <td style="text-align: center;"><b>'.$_POST['pay'].' Euro</b></td>
            </tr> 
        </table>
<br><br><br>';
 

//Kreisdiagramm
// Viertes Semester
// Anteilige Prozentwerte je Eingabewert --
// alle Werte ergeben 100 Prozent ---------
$alle = $_POST['pay'];

// Prozentanteil für Regen ----------------
$prozent_wert1 = $_POST['bonus'] * 100 / $alle;
// Prozentanteil für Sonne ----------------
$prozent_wert2 = $_POST['basicpay'] * 100 / $alle;

// Grundsätzliche Grafikgröße deklarieren ---
$flaeche = imagecreate (980,350);
// Hintergrundfarbe -------------------------
$hintergrund = imagecolorallocate($flaeche,255,255,255);

// Textfarbe --------------------------------
$schriftfarbe = imagecolorallocate($flaeche,0,0,0);
// Textkonstante ----------------------------
$text = 'Legende';
// Text auf Grafik einbinden ----------------
imagestring($flaeche,15,580,40,$text,$schriftfarbe);

// Legende 1 *******************************
// Farbfestlegung --------------------------
$gruen = imagecolorallocate($flaeche,0,255,0);

// Platzierung und Farbintegration ---------
// hier Rechteckfunktion genutzt -----------
imagefilledrectangle($flaeche,580,140,600,120,$gruen);

// Legendentext ----------------------------
imagestring($flaeche,15,620,80,$lang_receipt[$_SESSION['language']][8].$_POST['basicpay']." Euro",$schriftfarbe);

// Legende 2 *******************************
$rot = imagecolorallocate($flaeche,255,0,0);
imagefilledrectangle($flaeche,580,80,600,100,$rot);
imagestring($flaeche,15,620,120,"Bonus: ".$_POST['bonus']." Euro",$schriftfarbe);

// Kreisdiagramm ***************************
// Gradanteil je Kreissegment --------------
$grad_je_prozent = 360 / 100;
// Kreissegment 1 **************************
// Ende des ersten Kreissegments (Gradanzahl)
$ende_kreissegment_1 = $grad_je_prozent * $prozent_wert1;

// Farbzuweisung 1. Kreissegment -----------
$gruen = imagecolorallocate($flaeche,0,255,0);

// Kreissegment 1 Mittelpunkt, Durchmesser,-
// Startposition, Endposition, ----
// Farbzuweisung ---------------------------
// Funktion imagefilledarc() ---------------
imagefilledarc($flaeche,250, 175,350, 350, 0,$ende_kreissegment_1,$gruen, IMG_ARC_PIE);

// Kreissegment 2 **************************
$anfang_kreissegment_2 = $grad_je_prozent * $prozent_wert1;
$ende_kreissegment_2 = $grad_je_prozent * ($prozent_wert1 + $prozent_wert2);
$rot = imagecolorallocate ($flaeche,255,0,0);
imagefilledarc($flaeche, 250, 175, 350, 350,
$anfang_kreissegment_2,
$ende_kreissegment_2,
$rot, IMG_ARC_PIE);

imagepng ($flaeche, "diagramm.png");
imagedestroy($flaeche);



 
 
 
//////////////////////////// Erzeugung des PDF Dokuments \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 
// TCPDF Library laden
require_once('../../../tcpdf/tcpdf.php');
 
// Erstellung des PDF Dokuments
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
 
// Dokumenteninformationen
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($pdfAuthor);
$pdf->SetTitle('Rechnung '.$rechnungs_nummer);
$pdf->SetSubject('Rechnung '.$rechnungs_nummer);
 
 
// Header und Footer Informationen
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
 
// Auswahl des Font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
 
// Auswahl der Margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
 
// Automatisches Autobreak der Seiten
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
 
// Image Scale 
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
 
// Schriftart
$pdf->SetFont('dejavusans', '', 10);
 
// Neue Seite
$pdf->AddPage();
 
// Fügt den HTML Code in das PDF Dokument ein
$pdf->writeHTML($html, true, false, true, false, '');


$img = "diagramm.png";
$pdf->setImageScale(2);

// Bildeinstellungen

$pdf->Image
( $img
, 0               // $x
, 200               // $y
, 0               // WIDTH
, 0               // HEIGHT
, 'PNG'           // TYPE
, '#'             // LINK URL
, 'T'             // SET POINTER TOP LEFT
, TRUE            // NO RESIZING
, 300             // DPI
, 'R'             // PALIGN
, FALSE           // ISMASK
, FALSE           // IMGMASK
, 0               // BORDER
, FALSE           // FIT TO BOX
, FALSE           // HIDDEN
, FALSE           // FIT ON PAGE
);
 
//Ausgabe der PDF
 
$pdf->Output(realpath('./../..').DIRECTORY_SEPARATOR."pdf".DIRECTORY_SEPARATOR.$pdfName, 'F');


//Daten beziehen
$billnumber =   !empty($_POST['billnumber']) ? trim($_SESSION['language']."-".$_POST['billnumber']) : null;
$retailer = !empty($_POST['retailer']) ? trim($_POST['retailer']) : null;
$billdate =  date("Y-m-t H:i:s");
$basicpay = !empty($_POST['basicpay']) ? trim($_POST['basicpay']) : null;
$revenue =  !empty($_POST['total']) ? trim($_POST['total']) : 0;
$bonus =  !empty($_POST['bonus']) ? trim($_POST['bonus']) : 0; 
$pay =  !empty($_POST['pay']) ? trim($_POST['pay']) : null;
$pdfName =  !empty($pdfName) ? trim($pdfName) : null;


//Generierte Invoice_Nummer mit der Datenbank prüfen
$sql = "SELECT COUNT(invoice_number) AS num FROM bills WHERE invoice_number = :invoice_number";
$stmt = $pdo->prepare($sql);

$stmt->bindValue(':invoice_number', $billnumber);

//Ausführen
$stmt->execute();
    
//Zeile fetchen
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Rechnung existiert schon? Beenden & anzeigen!
if($row['num'] > 0){
    
    die("
        <div class=\"btn btn-warning btn-lg text-center mt-2\">".$lang_receipt[$_SESSION['language']][12]."</div>

        <form action=\"billing.php\" method=\"post\">
            <input type=\"hidden\" name=\"retailer\" value=\"".$retailer."\">
            <input type=\"hidden\" name=\"month\" value=\"".$month."\">
            <input type=\"hidden\" name=\"year\" value=\"".$year."\">
            <br><br>
            <a type=\"button\" class=\"btn btn-primary\" href=\"../../pdf".DIRECTORY_SEPARATOR.$pdfName."\" target=\"_blank\">".$lang_receipt[$_SESSION['language']][13]."</a>
            <br>
            <button class=\"btn btn-outline-danger mt-4 mr-2\" type=\"submit\">".$lang_receipt[$_SESSION['language']][14]."</button>
            <a href=\"order_show.php\" class=\"btn btn-outline-danger mt-4\" >".$lang_receipt[$_SESSION['language']][15]."</a>
            
        </form>
        
    ");
    
} 
//Daten in Datenbank schreiben
$sql = "INSERT INTO bills (
    invoice_number,
    r_id,
    invoice_date,
    basic_pay,
    revenue,
    bonus,
    pay,
    pdf
)
VALUES (
        :invoice_number,
        :r_id, 
        :invoice_date,
        :basic_pay,
        :revenue,
        :bonus,
        :pay,
        :pdf
        )";

$stmt = $pdo->prepare($sql);

//Werte an Parameter binden
$stmt->bindValue(':invoice_number', $billnumber);
$stmt->bindValue(':r_id', $retailer);
$stmt->bindValue(':invoice_date', $billdate);
$stmt->bindValue(':basic_pay', $basicpay);
$stmt->bindValue(':revenue', $revenue);
$stmt->bindValue(':bonus', $bonus);
$stmt->bindValue(':pay', $pay);
$stmt->bindValue(':pdf', $pdfName);

$result = $stmt->execute();

// Wenn erfolgreich, Rechnungsstatus, PDF-Link und Zurückbutton einblenden
if($result){
    
    echo "<div class=\"btn btn-danger btn-lg text-center mt-2\">".$lang_receipt[$_SESSION['language']][11]."</div>

    <form action=\"billing.php\" method=\"post\">
        <input type=\"hidden\" name=\"retailer\" value=\"".$retailer."\">
        <input type=\"hidden\" name=\"month\" value=\"".$month."\">
        <input type=\"hidden\" name=\"year\" value=\"".$year."\">
        <br><br>
        <a type=\"button\" class=\"btn btn-primary\" href=\"../../pdf".DIRECTORY_SEPARATOR.$pdfName."\" target=\"_blank\">".$lang_receipt[$_SESSION['language']][13]."</a>
        <br>
        <button class=\"btn btn-outline-danger mrr-2 mt-4\" type=\"submit\">".$lang_receipt[$_SESSION['language']][14]."</button>
        <a href=\"order_show.php\" class=\"btn btn-outline-danger mt-4\" >".$lang_receipt[$_SESSION['language']][15]."</a>
        
    </form>";
    unset($_POST['bill']);
}
else {
    echo $lang_receipt[$_SESSION['language']][16];
}
?>
