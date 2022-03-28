<html lang="fr">
<meta charset="UTF-8">
<html>

    <noscript>
        <p>Javascript est désactivé. Si vous avez la possibilité de l'activer, faites-le, vous y gagnerez en confort d'utilisation du site.</p>
    </noscript>

    <head>

        <link rel="stylesheet" href="css/bootstrap.css">
        
        <!-- Mettre l'image en fond -->
        <style>
            body {
                background-image:url('./img/fond.png');
                background-repeat: no-repeat;
                background-attachment: fixed;
                background-position: center;
            }
        </style>
        
        <script>
            
            // Changer la couleur du texte de chargement
            function changerCouleurTexte()
            {
                document.getElementById('chargement').style.color = '#52BE80';
            }

            // Afficher le gif et le texte de chargement + cacher le formulaire
            function chargementPage()
            {
                document.getElementById("formulaireNbVilles").style.display="none";
                document.getElementById("chargement").innerHTML = "Veuillez patientez durant le chargement, merci !";
                document.getElementById("imgChargement").src = "https://www.gif-maniac.com/gifs/51/50660.gif";

                setTimeout(changerCouleurTexte, 500);
            }

		</script>

	</head>
    <body>

    <center>
    
    <br><a href="http://lakartxela.iutbayonne.univ-pau.fr/~gpeyrelongue/WEBSERVICE/" style="color: black"><h1>Formulaire d'envoi vers l'API</h1></a><br>

    <form name="formulaireNbVilles" id="formulaireNbVilles" method="GET">

    <!-- Affichage du message d'information -->
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-2">
                </div>

                <div class="col-md-8">
                    <div class="alert alert-dismissible alert-primary">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <p class="card-text">Cette partie de notre webservice vous permet d'entrer les codes postaux des villes que vous souhaitez comparer !
                        </p>
                    </div>
                </div>

                <div class="col-md-2">
                </div>
                
            </div>
        </div>

        <?php

        $nbLignesFormulaire = $_POST["nbVilles"];

        // Céer le formulaire de saisie des codes postaux à partir du résultat du formulaire précédent
        for($i = 1; $i <= $nbLignesFormulaire; $i++)
        {
            print("
            
            <div class=container-fluid>

                <div class=row>

                    <div class=col-md-4>
                    </div>

                    <div class=col-md-4>
                        <label class=form-label>Code postal $i : </label><br>
                        <input pattern=[0-9]{5} type=number id=cp$i name=cp$i class=form-control><br><br>
                    </div>

                    <div class=col-md-4>
                    </div>

                </div>
            </div>
            
            ");
        }

        ?>

        <!-- Affichage des deux boutons qui ramènent soit vers le JSON brut soit vers notre page d'exploitation des résultats -->
        <input type="submit" class="btn btn-primary" formaction="API.php" onclick="chargementPage()" value="Afficher le JSON">
        <input type="submit" class="btn btn-primary" formaction="comparaisonVilles.php" onclick="chargementPage()" value="Comparer les villes !">

    </form>

    <!-- Formation de la balise contenant le petit gif de chargement -->
    <div class=container-fluid>

                <div class=row>

                    <div class=col-md-12>
                    <br><br><br><br><h1 id=chargement></h1>
                    <img src="" id="imgChargement"/>
                    </div>
                
                </div>
    </div>

    </center>
</html>