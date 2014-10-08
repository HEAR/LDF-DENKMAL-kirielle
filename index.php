<?php

include_once( 'config.php' );
include_once(LOCAL_PATH.'/fonctions.php');



// on utilise le fichier .htaccess pour récupérer une belle adresse au lieu d'avoir une adresse avec parametre du type ?url=nom_de_l_image
// ainsi le projet aura des url du type denkmal/kirielle/nom_de_l_image

if( !empty( $_GET['url'] ) )
{
	$param = explode( '/', $_GET['url'] );

	// pour supprimer les lignes vides
	$param = array_filter($param, 'strlen');


	/*echo "<ul>";
	foreach ($param as $key => $value) {
		//if( !empty( $value ) )
		echo "<li>$value</li>";
	}
	echo "</ul>";
	*/

	if( count($param) == 2)
	{

		switch($param[0])
		{

			case "image" :

				include_once('header.php');
				// DEBUT image 
				$imageName = $param[1];

				if( is_file( LOCAL_PATH."/data/$imageName/$imageName.jpg" ) )
				{
					$imageURL = URL."/data/$imageName/$imageName.jpg";

					echo "<div class='wrapper'>\n";
					echo "<img src='$imageURL' alt='image'/>\n";


					$thumbFolder  = LOCAL_PATH."/data/$imageName/thumbs/";

					foreach( glob( "{" . $thumbFolder . '*.jpg}', GLOB_BRACE ) as $file )
					{
						if( is_file( $file ) )
						{	
							$dim =  getimagesize($file);

							$fileName = str_replace($thumbFolder, '', $file);

							$info = getCoordFromName($fileName);

							$nom = $info->nom;
							$x = $info->x;
							$y = $info->y;
							$w = $dim[0];
							$h = $dim[1];

							echo "<div class='tagImg' style='top:{$y}px; left:{$x}px; width:{$w}px; height:{$h}px;' data-tag='$nom'></div>\n";
						}
					}

					echo "</div>\n";
					// FIN image
				}
				else
				{
					echo "<p>L'image n'existe pas</p>";
				}

				include_once('footer.php');

			break;

			case "tag" :

				$targetImages = json_decode( file_get_contents( LOCAL_PATH."/keywords/{$param[1]}.json" ) )->images;
				$imageName    = $targetImages[array_rand($targetImages, 1) ];

				header( 'Location:'.URL."/image/{$imageName}/" );

				// DEBUT tag
				// echo "<p>TAG : $param[1]</p>";
				// FIN tag

			break;

			default:
				echo "<p>???</p>";
			break;

		}

	}
	else if( count($param) == 1 )
	{
		include_once('header.php');
		echo "<p>On est sur une page<p>";

		switch ( $param[0] )
		{

			case 'a_propos' :

				echo "<p>Page à propos</p>";

			break;

			case 'autre' :

				echo "<p>Page autre</p>";

			break;

			case 'HD' :

				echo "<p>Page HD</p>";

			break;

			case 'image' :

				echo "<p>Il n'y a aucune image ici</p>";

			break;

			default :

				echo "<p>La page que vous cherchez n'existe pas. <a href='./'>Revenir à l'accueil</a></p>";
				// header('Location:./');

			break;

		}

		include_once('footer.php');

	}	

}
else
{
	include_once('header.php');
	echo "<p>Accueil</p>";
	echo "<ul>";
	foreach( glob( "{" . LOCAL_PATH . '/keywords/*.json}', GLOB_BRACE ) as $file )
	{

		$info = json_decode(file_get_contents($file));

		$keyword      = $info->mot;
		$identifiant  = $info->identifiant;

		if( count( $info->images ) > 0 )
		{
			echo "<li><a href='".URL."/tag/$identifiant/'>$keyword</a></li>";
		}
	}
	echo "</ul>";
	include_once('footer.php');

}
