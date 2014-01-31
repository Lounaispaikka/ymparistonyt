<?php
  if(is_numeric($_GET['id'])) {
    $file_id = $_GET['id'];
    $fileDir = "../../data/charts/".$file_id; // Määritellään polku, ei kauttaviivaa loppuun
    $fileName = "data.csv"; // Tiedostonimi, voidaan noutaa dynaamisesti myös esim. SQL-kyselyllä


    $fileString=$fileDir.'/'.$fileName; // Polun ja tiedostonimen yhdistelmä
    // Muunnetaan tiedostonimet IE:lle kelvollisiksi
    if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")){
      $fileName = preg_replace('/\./', '%2e', $fileName, substr_count($fileName, '.') - 1);
    }  // Varmistetaan tiedoston olemassaolo ennen headerien lähettämistä
    if (!$fdl=@fopen($fileString,'r')){
      die ("Virhe: tiedostoa ei ole.");
    } else {
      header("Cache-Control: "); // Jätetään tyhjäksi IE-ongelmien välttämiseksi
      header("Pragma: "); // Jätetään tyhjäksi IE-ongelmien välttämiseksi
      header("Content-Type: application/octet-stream");
      header("Content-Disposition: attachment; filename=\"".$fileName."\"");
      header("Content-Length:".(string)(filesize($fileString)));
      sleep(1); // ilman tätä jotkut >32kt tiedostot aiheuttavat ongelmia
      // vaihtoehtoinen fpassthru ja readfile-funkkareille, käyttää vähemmän muistia:
      while(!feof($fdl)) {
        $buffer = fread($fdl, 4096);
        print $buffer;
      }
      fclose($fdl);
       // tähän kohtaan voi laittaa vaikka SQL-kyselyn päivittämään latauskertojen laskuria niin halutessaan
    }
  } else { 
      header('Location: ../404.php');
      die();
  }
?>
