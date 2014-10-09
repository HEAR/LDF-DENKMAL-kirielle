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

					// POUR GENERER LES CASES SUR LA PHOTO
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

							$keywords[] = $nom;

							$link = URL.'/tag/'.$nom.'/';

							echo "<a href='{$link}'><div class='tagImg' style='top:{$y}px; left:{$x}px; width:{$w}px; height:{$h}px;' data-tag='$nom'></div></a>\n";
						}
					}

					echo "<h3>© ";
					echo json_decode( file_get_contents( LOCAL_PATH."/data/$imageName/data.json" ) )->credit;
					echo "</h3>";

					// POUR GENERER LA LISTE DES MOTS CLEFS
					$keywords = array_unique($keywords);

					echo "<ul id='listetags'>\n";
					foreach ($keywords as $key => $value) {
						
						echo "<li class='$value'><span>";
						echo json_decode( file_get_contents( LOCAL_PATH."/keywords/{$value}.json" ) )->mot;
						echo "</span></li>\n";

					}
					echo "</ul>\n";

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

				//include  page à propos
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
	$nbrKeyword = count( glob( LOCAL_PATH . '/keywords/*.json' ) );
	$nbrImages = count( glob( LOCAL_PATH . '/data/*/' ) );

	include_once('header.php');
	echo "<div id='accueil'>";
	//kirielle
	echo "<p>Cérémonies du 11 novembre. Plusieurs lieux de commémoration. Des dizaines de jeunes photographes témoignent de ces instants.</p>
	<p>Juxtaposées, toutes ces images sont des points de vue qui se croisent, se répondent et se complètent. Elles créent des 11 novembre.</p>
	<p>Parcourez les images, dénichez les zones de liens et <a href='#' id='go'>empruntez vos propres pistes</a>.</p>";
	echo "<p>Actuellement 2 années, 5 cérémonies, 23 photographes, {$nbrImages} images et {$nbrKeyword} mots-clés</p>";
	//echo "<ul>";

	$listeTags = array();

	foreach( glob( "{" . LOCAL_PATH . '/keywords/*.json}', GLOB_BRACE ) as $file )
	{

		$info = json_decode(file_get_contents($file));

		$keyword      = $info->mot;
		$identifiant  = $info->identifiant;

		if( count( $info->images ) > 0 )
		{
			//echo "<li><a href='".URL."/tag/$identifiant/'>$keyword</a></li>";

			$listeTags[] = $identifiant;
		}
	}

	$listeTags = implode(',',$listeTags);

	//echo "</ul>";
	echo "</div>";
	echo "<div id='tags' data-tags='{$listeTags}'><h1>ok</h1></div>";
	include_once('footer.php');

}
