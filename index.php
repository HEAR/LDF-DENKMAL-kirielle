<?php

	include_once( 'config.php' );
	include_once(LOCAL_PATH.'/fonctions.php');

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<title>Kirielle</title>
	<link rel="stylesheet" href="<?php echo URL;?>/css/style.css" />

	<style type="text/css">
	   .tagImg{
	        position:absolute;
	        border: solid pink 1px;
	        opacity: 0.4;
	    }
	    .wrapper{
	        position: relative;
	    }
    </style>
</head>



<body>
	<h1>Kirielle</h1>

		<?php

		// on utilise le fichier .htaccess pour récupérer une belle adresse au lieu d'avoir une adresse avec parametre du type ?url=nom_de_l_image
		// ainsi le projet aura des url du type denkmal/kirielle/nom_de_l_image

		if( !empty( $_GET['url'] ) )
		{
			$param = explode( '/', $_GET['url'] );

			// pour supprimer les lignes vides
			$param = array_filter($param, 'strlen');

		?>

		<h3>Chemin :</h3>

		<?php

			echo "<ul>";
			foreach ($param as $key => $value) {
				//if( !empty( $value ) )
				echo "<li>$value</li>";
			}
			echo "</ul>";


			if( count($param) >= 2 && $param[0] == 'image')
			{
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

				}
				else
				{
					echo "<p>L'image n'existe pas</p>";
				}

			}
			else if( count($param) == 1 )
			{
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

			}	

		}
		else
		{

			echo "<p>Accueil</p>";

		}
			

	?>
	
	<script src="<?php echo URL;?>/js/jquery-1.11.1.min.js"></script>
	<script>

		$(document).ready(function(){

			$('.tagImg').click(function(event){

				console.log( $(this).data('tag') );

			});		

		});

	</script>

</body>
</html>