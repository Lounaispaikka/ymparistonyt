<?php
  if(is_numeric($_GET['id'])) {
    $file_id = $_GET['id'];
    $fileDir = "../../data/charts/".$file_id; // M��ritell��n polku, ei kauttaviivaa loppuun
    $fileName = "data.csv"; // Tiedostonimi, voidaan noutaa dynaamisesti my�s esim. SQL-kyselyll�


    $fileString=$fileDir.'/'.$fileName; // Polun ja tiedostonimen yhdistelm�
    // Muunnetaan tiedostonimet IE:lle kelvollisiksi
    if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")){
      $fileName = preg_replace('/\./', '%2e', $fileName, substr_count($fileName, '.') - 1);
    }  // Varmistetaan tiedoston olemassaolo ennen headerien l�hett�mist�
    if (!$fdl=@fopen($fileString,'r')){
      die ("Virhe: tiedostoa ei ole.");
    } else {
      header("Cache-Control: "); // J�tet��n tyhj�ksi IE-ongelmien v�ltt�miseksi
      header("Pragma: "); // J�tet��n tyhj�ksi IE-ongelmien v�ltt�miseksi
      header("Content-Type: application/octet-stream");
      header("Content-Disposition: attachment; filename=\"".$fileName."\"");
      header("Content-Length:".(string)(filesize($fileString)));
      sleep(1); // ilman t�t� jotkut >32kt tiedostot aiheuttavat ongelmia
      // vaihtoehtoinen fpassthru ja readfile-funkkareille, k�ytt�� v�hemm�n muistia:
      while(!feof($fdl)) {
        $buffer = fread($fdl, 4096);
        print $buffer;
      }
      fclose($fdl);
       // t�h�n kohtaan voi laittaa vaikka SQL-kyselyn p�ivitt�m��n latauskertojen laskuria niin halutessaan
    }
  } else { 
      header('Location: ../404.php');
      die();
  }
?>
