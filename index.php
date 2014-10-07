<?php

	include_once( 'config.php' );

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Kirielle</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<h1>Kirielle</h1>

	<?php

		/*
		
		$templateListe = array();
		foreach(glob("{".LOCAL_PATH.SLIDE_TEMPLATE_FOLDER."*}",GLOB_BRACE) as $folder){
		    
		        if(is_dir($folder)){
		        	$dossier = str_replace(LOCAL_PATH.SLIDE_TEMPLATE_FOLDER,'',$folder);
		        	if($dossier != 'default' && $dossier != 'meteo'){
		      			$templateListe[$dossier] = $dossier ;
		      		}
				}
		}

		 */

		/*foreach(glob("{".LOCAL_PATH.SLIDE_TEMPLATE_FOLDER."*}",GLOB_BRACE) as $folder){

		}*/

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

					echo "<img src='$imageURL'>";
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

</body>
</html>