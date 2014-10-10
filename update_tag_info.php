<?php

include_once('config.php');
include_once('fonctions.php');

header('Content-Type: text/html; charset=utf-8');

/**
 * FICHIER/FONCTION SERVANT A METTRE A JOUR
 * TOUS LES FICHIERS LIES AU MOTS CLEFS ET INVERSEMENT
 */

echo "<pre>";

foreach( glob( "{" . LOCAL_PATH ."/keywords/*.json}", GLOB_BRACE ) as $keyword )
{

	$info = json_decode( file_get_contents( $keyword ) );

	echo "$info->mot / $info->identifiant \n";


	foreach (glob( "{" . LOCAL_PATH ."/data/*/thumbs/*.jpg}", GLOB_BRACE) as $vignette )
	{

		$pos  = strrpos($vignette, '/')+1;
		$name = substr( $vignette, $pos, strlen($vignette) ) ;

		$folder = str_replace( '/thumbs/', '', substr($vignette, 0, $pos));

		$pos = strrpos($folder, '/')+1;
		$imageName = substr( $folder, $pos, strlen($folder) ) ;

		echo "	$name ";
		echo "	$imageName \n";

		$tag = getCoordFromName($name)->nom;

		if($tag == $info->identifiant )
		{
			echo "	ok ";

			if( in_array($imageName, $info->images) )
			{
				echo "	<span style='color:green'>ok</span>\n";
			}
			else
			{
				echo "	<span style='color:red'>no</span>\n";
				echo "		--> on ajoute « $imageName » au fichier $keyword\n";
			}
		}	
	}
} 

echo "</pre>";
