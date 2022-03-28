<?php

header('content-type:application/json');

// Déclaration de l'ensemble de nos tableaux
$tabCPVilles = array();
$tabNomVille = array();
$tempVilles = array();
$indiceAirVilles = array();
$prixGazVilles = array();
$tabAEncoder = array();
$scoreVilles = array();
$prixGazoil = array();
$prixSP = array();

// Récupérer les codes postaux du formulaire
for($i=1;$i<=5;$i++)
{

    if (!(empty($_GET["cp$i"])))
    {
        array_push($tabCPVilles,$_GET["cp$i"]);

        $ville = enleverCasse(recupNomVille($_GET["cp$i"]));
        array_push($tabNomVille,$ville);
        
        ${"tabPrixGaz.$ville"} = array();

        if ($ville=="NAN"){
            array_push($tempVilles,"NAN");
            array_push($indiceAirVilles,"NAN");
            array_push(${"tabPrixGaz.$ville"},"NAN");
        }
        else 
        {
           
            array_push($tempVilles,recupTempVille($ville));
            array_push($indiceAirVilles,recupIndiceAirVille($ville));

            // pour le carburant

            $urlPrixEssence = "https://public.opendatasoft.com/api/records/1.0/search/?dataset=prix_des_carburants_j_7&q=" . $ville . "&rows=1&sort=update&facet=city&facet=fuel";

            $json = json_decode(file_get_contents($urlPrixEssence));
 
            if ($json->nhits == 0)
            {
                array_push(${"tabPrixGaz.$ville"},"NAN");
            }
            else
            {
                $gazDispo=$json->records[0]->{"fields"}->{"fuel"};
    
                $tabGaz=explode("/", $gazDispo);
    
                for ($j=0;$j<count($tabGaz);$j++)
                {
                    $chaine="price_".strtolower($tabGaz[$j]);
            
                    $prixGazActuel = $json->records[0]->{"fields"}->{"$chaine"};
    
                    array_push(${"tabPrixGaz.$ville"},array("$tabGaz[$j]" => $prixGazActuel));

                    if($chaine == "price_gazole")
                    {
                        array_push($prixGazoil, $prixGazActuel);
                    }
                    else if($chaine == "price_sp98")
                    {
                        array_push($prixSP, $prixGazActuel);
                    }
                }               
            }           
        }
    }   
}

// Déclaration de l'ensemble des fonctions

function enleverCasse($uneChaine) // Nous avions un problème de casse avec l'api de météo car celui-ci ne gère pas les accents, donc nous avons créer notre méthode qui remplace les lettres accentués par leurs lettres sans accent
{
    $search  = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
    $replace = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');

    $uneChaine = str_replace($search, $replace, $uneChaine);

    return $uneChaine;
}

function verfiTableau($tab)
{

    if(in_array("NAN", $tab))
    {
        $tab = array_diff($tab, array("NAN"));
    }

    return $tab;
}

// Récupérer uniquement le nom de la ville (exp : Paris 01 Louvre -> Paris)
function recupVille($chaine){

    $tabChaine=explode(" ", $chaine);

    $ville = $tabChaine[0];

    return $ville;
}

function testerURL($url){
    if (!(@file_get_contents($url))) // un @ pour ne pas avoir le message d'erreur
    {
            return false;
    }
    else{
            return true;
    }
}

// Parcourir l'ensemble du formulaire et intérroger Zippopotam pour obtenir le nom de la ville
function recupNomVille ($cpVille)
{

    $urlVille = "http://api.zippopotam.us/fr/" . $cpVille;


    if (testerURL($urlVille)){

        $json = json_decode(file_get_contents($urlVille));

        $villeRecup = $json->places[0]->{"place name"};

        $laVille = recupVille($villeRecup);

        return $laVille;

    }
    else
    {
        return "NAN";
    }


}


// A partir des noms de villes trouvées précédemment, on trouve leur température
function recupTempVille ($nomVille)
{

    
        $urlMeteo = "http://api.weatherapi.com/v1/current.json?key=f0f93b5cb2914b0b9d1164738220702&q=" .  $nomVille . ",fr";

        $json = json_decode(file_get_contents($urlMeteo));

        $tempAffiche = $json->current->{"temp_c"};

        return $tempAffiche;
        
   
}



// A partir des noms de villes trouvées précédemment, on trouve leur indice de qualité d'air
function recupIndiceAirVille ($nomVille)
{

    $urlPolluAir = "https://api.waqi.info/feed/" . $nomVille . "/?token=84a747ac9cc777f08d9ffa12bcaa8cb3c55440f8";

    $json = json_decode(file_get_contents($urlPolluAir));
        
    if ($json->status=="error")
    {
        return "NAN";
    }
    else
    {
        $indiceAir = $json->data->{"aqi"};
        return $indiceAir;
    }        
}


// A partir des noms de villes trouvées précédemment, on obtient la liste des carburants disponibles dans cette ville, puis on obtient le prix de chaque carburant
function obtenirPrixEssenceDispo($nomVille){

        $urlPrixEssence = "https://public.opendatasoft.com/api/records/1.0/search/?dataset=prix_des_carburants_j_7&q=" . $nomVille . "&rows=1&sort=update&facet=city&facet=fuel";

        $json = json_decode(file_get_contents($urlPrixEssence));

        
        if ($json->nhits == 0)
        {
            return "NAN";
        }
        else
        {
            $gazDispo=$json->records[0]->{"fields"}->{"fuel"};

            $tabGaz=explode("/", $gazDispo);

            for ($j=0;$j<count($tabGaz);$j++)
            {
                $chaine="price_".strtolower($tabGaz[$j]);
        
                $prixGazActuel = $json->records[0]->{"fields"}->{"$chaine"};

                return array("$tabGaz[$j]" => $prixGazActuel);
            }
            
        }
}


// Formation du premier JSON avec les données récoltées, il sera exploitable
for($i=0; $i<count($tabNomVille); $i++)
{
    array_push($tabAEncoder,array(
                                        "nom_ville" => $tabNomVille[$i],
                                        "temp_ville" => $tempVilles[$i],
                                        "qualite_air_ville" => $indiceAirVilles[$i],
                                        "gaz" => ${"tabPrixGaz.$tabNomVille[$i]"}));
}

// Obtenir un tableau à partir de notre json généré
$json = json_encode($tabAEncoder);
$json = json_decode($json);

// Si l'on a pas de données sur la ville, on remplace les données vides par NAN
$tempVilles = verfiTableau($tempVilles);
$indiceAirVilles = verfiTableau($indiceAirVilles);
$prixGazoil = verfiTableau($prixGazoil);
$prixSP = verfiTableau($prixSP);

// Trier l'ensemble de nos tableaux pour pouvoir calculer nos points par la suite
sort($tempVilles);
sort($indiceAirVilles);
sort($prixGazoil);
sort($prixSP);

// Etablir les scores de chaque équipe et les affecter dans le tableau
for($i=0; $i<count($tabNomVille); $i++)
{
    ${"score.$tabNomVille[$i]"} = 0;

    if($json[$i]->{'temp_ville'} == "NAN")
    {

    }
    else if($tempVilles[count($tempVilles)-1] == $json[$i]->{'temp_ville'})
    {
       ${"score.$tabNomVille[$i]"} = ${"score.$tabNomVille[$i]"} + 1;
    }

    if($json[$i]->{'qualite_air_ville'} == "NAN")
    {

    }
    else if($indiceAirVilles[0] == $json[$i]->{'qualite_air_ville'})
    {
        ${"score.$tabNomVille[$i]"} = ${"score.$tabNomVille[$i]"} + 1;
    }


    if($json[$i]->{'gaz'}[0] == "NAN")
    {

    }
    else if(@array_key_exists('Gazole', $json[$i]->{'gaz'}[0])) 
    {
        if($prixGazoil[0] == $json[$i]->{'gaz'}[0]->{'Gazole'})
        ${"score.$tabNomVille[$i]"} = ${"score.$tabNomVille[$i]"} + 1;
    }

    if($json[$i]->{'gaz'}[0] == "NAN")
    {

    }
    else if(@array_key_exists('SP98', $json[$i]->{'gaz'}[count($json[$i]->{'gaz'})-1]))
    {
        if($prixSP[0] == $json[$i]->{'gaz'}[count($json[$i]->{'gaz'})-1]->{'SP98'})
        ${"score.$tabNomVille[$i]"} = ${"score.$tabNomVille[$i]"} + 1;
    }

    array_push($scoreVilles, array(${"score.$tabNomVille[$i]"}, $tabNomVille[$i]));
}

// Trier les scores pour placer la meilleure ville en dernier
sort($scoreVilles);
array_push($tabAEncoder, array("scoresDesVilles" => $scoreVilles));

// Encodage du tableau
$json = json_encode($tabAEncoder);

// Affichage du JSON
echo $json;

?>