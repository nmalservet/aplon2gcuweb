<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Aplon.csv > CGU-WEB.csv</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

        <!-- Latest compiled and minified JavaScript -->
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container theme-showcase" role="main">
            <div class="page-header">
                <h1>Aplon.csv > CGU-WEB.csv<small>Version Beta</small></h1>
            </div>
<div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Processus</h3>
                </div>
    <div class="panel-body">
        Lexture des informations du fichier CSV aplon, format attendu : Nom; Prénom;Classe;DateNaissance;Classe;<br>
        
    </div>
</div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Sélection du fichier source .csv depuis aplon</h3>
                </div>
                <div class="panel-body">
                    <form action="aplontocguweb.php"  method="post" enctype="multipart/form-data">
                        <div>Sélectionner votre fichier aplon ci-dessous: </div><br>
                        <label for="file">Fichier :</label>
                        <input type="file" name="file" id="file"><br>
                        <input type="radio" name="exporttype" value="eleve">eleve<br>
                        <input type="radio" name="exporttype" value="prof">prof
                        <br>
                        <input type="submit" value="Formatter au format CGU-WEB">
                    </form> 

                </div>
            </div>

            <br>
            <br>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Résultat:</h3>
                </div>
                <div class="panel-body">

                    <?php
                    if (isset($_FILES["file"])) {
                        if ($_FILES["file"]["error"] > 0) {
                            echo "Error: " . $_FILES["file"]["error"] . "<br>";
                        } else {
                            echo "<div class=\"alert alert-success\" role=\"alert\">

    <strong>Succès</strong>Le fichier a été téléchargé correctement</div>";
                            echo "Fichier : " . $_FILES["file"]["name"] . "<br>";
                            echo "Type: " . $_FILES["file"]["type"] . "<br>";
                            echo "Taille: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
                            //pour le dev/ echo "Stored in: " . $_FILES["file"]["tmp_name"];
                        }
                        $source = file_get_contents($_FILES['file']['tmp_name']); //$_POST["source"];
                        //efface le fichier source
                        unlink($_FILES['file']['tmp_name']);
//split des lignes pour chaque entrée
                        $lignes = explode("\n", $source);
//pour chaque ligne split des colonnes separees par ;
                        $i = 0;
                        $result = "";
                        foreach ($lignes as $ligne) {
                            $i++;
                            $colonnes = explode(";", $ligne);
                            //normalmeent 5 colonnes
                            if ($_POST['exporttype'] == "prof") {
                                if (count($colonnes) <> 2) {
                                    echo "<div class=\"alert alert-warning\" role=\"alert\"><strong>Attention</strong>
erreur nb de colonnes incorrect pour la ligne :" . $i . "</i><br>";
                                    echo "nb colonnes:" . count($colonnes) . "</div><br>";
                                } else {
                                    //ajout des ; et données manquantes
                                    //1=>Nom, 2=>Prenom
                                    //attendu : CRE;/NOM/;/PRENOM/;;;;;Enseignant;;;;/DATENAISS/;;;SCOLAIRE;/Filiere/;/Niveau/;/Classe/;Z:;\\0541309E-DATA\utilisateurs\%username%;winlogon.vbs;\\0541309E-DATA\Profils_Profs\%Profil%;;;;;;;;;;;;;;;
                                    $result.= "CRE;" . rtrim($colonnes[0]) . ";" . rtrim($colonnes[1]) . ";;;;;Enseignant;;;;01/01/1900;;;;;;;Z:;" . addslashes("\\") . "0541309E-DATA\utilisateurs\%username%;winlogon.vbs;" . addslashes("\\") . "0541309E-DATA\Profils_Profs\%Profil%;;;;;;;;;;;;;;;\n";
                                }
                            } else {
                                if (count($colonnes) <> 5) {
                                    echo "<div class=\"alert alert-warning\" role=\"alert\">

    <strong>Attention</strong>
erreur nb de colonnes ligne:" . $i . "</i><br>";
                                    echo "nb colonnes:" . count($colonnes) . "</div><br>";
                                } else {
                                    //ajout des ; et données manquantes
                                    //1=>Nom, 2=>Prenom, 3=>classe, 4=>Date naissance, 5= classe
                                    //attendu : CRE;/NOM/;/PRENOM/;;;;;Eleve;;;;/DATENAISS/;;;SCOLAIRE;/Filiere/;/Niveau/;/Classe/;Z:;\\0541309E-DATA\utilisateurs\%username%;winlogon.vbs;\\0541309E-DATA\Profils_Eleves\%Profil%;;;;;;;;;;;;;;;
                                    $result.= "CRE;" . $colonnes[0] . ";" . $colonnes[1] . ";;;;;Eleve;;;;" . $colonnes[3] . ";;;SCOLAIRE;;;" . rtrim($colonnes[2]) . ";Z:;" . addslashes("\\") . "0541309E-DATA\utilisateurs\%username%;winlogon.vbs;" . addslashes("\\") . "0541309E-DATA\Profils_Eleves\%Profil%;;;;;;;;;;;;;;;\n";
                                }
                            }
                        }
                        echo "<br>nb de lignes traitées: " . count($lignes) . "<br>";
                        ?>
                        <?php
                        //echo $result;
                        $filename = "./result" . time() . ".csv";
                        // if (is_writable($filename)) {
                        $handler = fopen($filename, 'w') or die("can't open file");
                        if (fwrite($handler, $result) === FALSE) {
                            echo "probleme d ecriture dans ke fichier";
                        }
                        fclose($handler);


                        echo "<a href=\"./" . $filename . "\" class=\"btn btn-primary btn-lg active\" role=\"button\">Fichier resultat à télécharger</a>";
                    } else {
                        echo "Aucun fichier sélectionné.";
                    }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>
