<html lang="fr">
<meta charset="UTF-8">
<html>

    <noscript>
        <p>Javascript est désactivé. Si vous avez la possibilité de l'activer, faites-le, vous y gagnerez en confort d'utilisation du site.</p>
    </noscript>
    
    <head>

    <!-- Inclusion du css et du js de bootstrap + js de chartJS -->
        <link rel="stylesheet" href="css/bootstrap.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="js/bootstrap.js"></script>
    
    <style>
            [class="row-fluid-1"]
            {
                background-color: rgb(5, 3, 51) ;
                   
                text-align: center;
            }
    </style>
    

    <?php

            // Initialisation des variables
            $url;
            $nbVilles = 0;
            $tabTemp = array();
            $tabAir = array();
            $tabPrixEssence = array();

            // Récupérer les codes postaux du formulaire et former l'url de requête de notre API
            for($i=1;$i<=5;$i++)
            {
                if (!(empty($_GET["cp$i"])))
                {
                    if($i==1)
                    {
                        $url = "http://lakartxela.iutbayonne.univ-pau.fr/~jroyet/AJAX/WEBSERVICE/API.php?cp1=" . $_GET["cp$i"];
                        $nbVilles++;
                    }
                    else
                    {
                        $url = $url . "&cp" . $i . "=" . $_GET["cp$i"];
                        $nbVilles++;
                    }
                    
                }
            }
            
            // Récupérer le json formé par l'API
            
            $json = json_decode(file_get_contents($url));

            // Récupérer le nom de la ville gagnante
            $nomVilleGagante =$json[count($json)-1]->{'scoresDesVilles'}[count($json[count($json)-1]->{'scoresDesVilles'})-1][1];
    ?>


    <div class="container-fluid">
	    <div class="row-fluid-1">
            <br>
            <div class="col-md-12">
                <center>
                    <a href="http://lakartxela.iutbayonne.univ-pau.fr/~gpeyrelongue/WEBSERVICE/"><img src="img/apiVille.gif" class="rounded-circle" height = "250" width="250"></a>
                </center>
            </div><br>
	</div><br><br>

    <!-- Affichage de la bulle d'information affichant la ville gagante et son score -->
    <div class="container-fluid">
            <div class="row"><br>
            <center>
                <div class="col-md-12">
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header">
                            <strong class="me-auto">Le grand gagnant</strong>
                        </div>
                        <div class="toast-body">
                            La ville qui a obtenu le plus de points est : 
                            <?php

                                print($nomVilleGagante);
                                print("<br>");
                                print("Elle a obtenu : " . $json[count($json)-1]->{'scoresDesVilles'}[count($json[count($json)-1]->{'scoresDesVilles'})-1][0] . " points !");

                            ?>
                        </div>
                    </div>
                </div>
            </center>
            </div>
    </div><br><br>

    </head>

    <!-- Affichage du bouton permettant d'afficher le tableau de comparaison de l'AQI -->
    <body>
	<div class="row">
		<div class="col-md-6">
        <center>
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">Afficher tableau d'indice de pollution</button>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel" weight="500">
                <div class="offcanvas-header">
                    <h5 id="offcanvasRightLabel">Tableau d'indice de pollution AQI</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" weight="500">
                    <img src="img/indicePollAir.png">
                </div>
            </div>
            </center>
            <br>
        <center>

        <?php print("       <!-- Afficher le graphique en radar -->
                            <canvas id=\"myChart\"></canvas>
                            <script>

                                    const data = {
                                    labels: [
                                        'Température en °C',
                                        'Polution de l\'air',
                                        'Nombre de carburant disponible',
                                    ],
                                    datasets: [
                                        ");
                                        $tabCouleur=['255, 99, 132','0, 99, 132','0, 0, 255','0, 255, 0','100, 50, 220'];
                                        for($i=0; $i<count($json[count($json)-1]->{'scoresDesVilles'}); $i++)
                                        {

                                            $ville= $json[$i]->{'nom_ville'};
                                            $aire=$json[$i]->{'qualite_air_ville'};
                                            $temps=$json[$i]->{'temp_ville'};
                                            $nbGazDispo =count($json[$i]->{'gaz'});
                                                
                                            if ($i ==count($json[count($json)-1]->{'scoresDesVilles'})-1 && $ville != 'NAN' && $aire!= 'NAN' && $temps!= 'NAN'){
                                                    print("   {
                                                        label: '$ville',
                                                        data: [$temps, $aire, $nbGazDispo],
                                                        fill: true,
                                                        backgroundColor: 'rgba($tabCouleur[$i], 0.2)',
                                                        borderColor: 'rgb($tabCouleur[$i])',
                                                        pointBackgroundColor: 'rgb($tabCouleur[$i])',
                                                        pointBorderColor: '#fff',
                                                        pointHoverBackgroundColor: '#fff',
                                                        pointHoverBorderColor: 'rgb($tabCouleur[$i])'
                                                    }");}
                                                    else if ( $ville != 'NAN' && $aire!= 'NAN' && $temps!= 'NAN'){
                                                        print("   {
                                                            label: '$ville',
                                                            data: [$temps, $aire, $nbGazDispo],
                                                            fill: true,
                                                            backgroundColor: 'rgba($tabCouleur[$i], 0.2)',
                                                            borderColor: 'rgb($tabCouleur[$i])',
                                                            pointBackgroundColor: 'rgb($tabCouleur[$i])',
                                                            pointBorderColor: '#fff',
                                                            pointHoverBackgroundColor: '#fff',
                                                            pointHoverBorderColor: 'rgb($tabCouleur[$i])'
                                                        },");
                                                    }
                                            
                                        }    
                                        
 
                                    print("]
                                    };
                            
                                        const config = {
                                            type: 'radar',
                                            data: data,
                                            options: {
                                                elements: {
                                                line: {
                                                    borderWidth: 3
                                                }
                                                }
                                            },
                                            };
                                            
                                            Chart.defaults.font.size = 15;

                                    </script>

                                    <script>
                                        const myChart = new Chart(
                                        document.getElementById('myChart'),
                                        config
                                        );
                                    </script>");
                            
        ?>

        </center> 
           
        </div>
        
        <!-- Affichage du tableau de comparatif des gaz par ville -->
		<div class="col-md-6">
        <table class="table">
				<thead>
                <tr>
                    <th>
						Carburant
					</th>

                    <?php $premier = true ;
                    for($i=0; $i<count($json[count($json)-1]->{'scoresDesVilles'}); $i++)
                    { 

                            for ($j = 0 ;$j<count($json[$i]->{'gaz'});$j++)
                            {
                                if ($json[$i]->{'gaz'}[0] != "NAN"){
                                    foreach($json[$i]->{'gaz'}[$j] as $key => $element){
                                        if ($premier == true){
                                            $tab[$j]= $key;
                                            print("<th>
                                                $key
                                            </th>");
                                            $premier = false;
                                        }
                                        else{
                                            $existe = false ;
                                            for ($elem =0; $elem<count($tab); $elem++){
                                                if ($key == $tab[$elem]){ $existe = true ; break;}
                                            }
                                            if ($existe != true){
                                                $tab[]=$key;
                                                print("<th>
                                                $key
                                                </th>");
                                            }
                                            else{$existe = false;}
                                        }
                                        
                                    }
                                }
                          
                            }
                        
                    }
                    
                    ?>
					
				</tr>
				</thead>

				<tbody>

                    <?php 
                    
                    for($i=0; $i<count($json[count($json)-1]->{'scoresDesVilles'}); $i++)
                    { 
                        
                        $ville =$json[$i]->{'nom_ville'};
                        if ($ville != "NAN"){
                        print("<tr>
                                <td>
                                $ville
                                </td>");
                        for ($cpt = 0 ;$cpt <count($tab); $cpt++){
                                    $tabDeAffichage[$cpt] = "NAN";
                        }

                        for ($j = 0 ;$j<count($json[$i]->{'gaz'});$j++){
                            if ($json[$i]->{'gaz'}[0] != "NAN"){
                                foreach($json[$i]->{'gaz'}[$j] as $key => $element){
                
                                    for ($elem =0; $elem<count($tab); $elem++){
                                        if($key == "$tab[$elem]"){
                                            $tabDeAffichage[$elem]= $element;              
                                            break;

                                        }  
                                    }    
                                }
                            }
                            
                        }
                        for ($cpt2 = 0 ;$cpt2 <count($tabDeAffichage); $cpt2++){
                            $valeur = $tabDeAffichage[$cpt2];
                            print("
                            <td>
                            $valeur
                            </td>");
                            }
                        
                        print("
                        <tr>");
                    }
                    }

                    ?>
					
				</tbody>
			</table>

            <!-- Affichage de la carte pointant sur la ville gagnante -->
            <iframe width="1000" height="500" id="gmap_canvas"    <?php print("src=\"https://maps.google.com/maps?q=$nomVilleGagante,France&t=&z=13&ie=UTF8&iwloc=&output=embed\"") ?> frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>       
            
	</div>
</div>
                
</body>

</html>