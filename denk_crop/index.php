<?php

include_once('../config.php');
include_once('image.class.php'); 


/**
 * Fonction qui sert à nettoyer le nom des fichiers
 * @param  [type] $valeur [description]
 * @return [type]         [description]
 */
function makeFileName($valeur)
{
	$valeur = strtr($valeur,'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',				'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');

	$valeur = preg_replace('/([^.a-z0-9]+)/i', '_', $valeur);

	return $valeur;
}

/**
 * Sert à uploader une image dans un dossier à son nom
 * @param  [type] $file       [description]
 * @param  [type] $repository [description]
 * @return [type]             [description]
 */
function upload($file, $repository)
{	
	$name = $file["name"];
	$name = makeFileName($name);

	$pos 	   = strrpos($name, '.');
	$extension = substr($name, $pos, strlen($name) );
	$nom 	   = substr($name, 0, $pos);
	$cpt	   = 0;

	$repository = $repository.$nom.'/';
	mkdir($repository);

	while(file_exists($repository.$name))
	{
		$cpt++;
		$name = $nom.'('.$cpt.')'.$extension;
	}
	
	copy($file['tmp_name'], $repository.$name);


	$json 		  = new stdClass();
	$json->file   = $name;
	$json->thumbs = array();

	file_put_contents($repository.'data.json', json_encode($json));


	return $name;
}


/**
 * POUR SUPPRIMER UN MOT CLEF
 */
if( isset($_GET['suppr']) && !empty($_GET['suppr']) )
{

	if(is_file(LOCAL_PATH.'/keywords/'.$_GET['suppr'].'.json'))
	{
		unlink(LOCAL_PATH.'/keywords/'.$_GET['suppr'].'.json');
	}

	header('Location:./');
}

/**
 * POUR AJOUTER UN MOT CLEF
 */
if( isset( $_POST['add_keyword']) && !empty($_POST['keyword'] ) )
{

	$json              = new stdClass();
	$json->mot         = $_POST['keyword'];
	$json->identifiant = makeFileName($_POST['keyword']);
	$json->images      = array();

	$json              = json_encode($json);

	file_put_contents(LOCAL_PATH.'/keywords/'.makeFileName($_POST['keyword']).'.json', $json);

}

/**
 * POUR AJOUTER UNE IMAGE
 */
if( !empty( $_FILES['image_file']['name'] ) )
{
	echo $filepath = upload($_FILES['image_file'], LOCAL_PATH.'/data/');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Kyrielle Tag Image</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

	<script src="js/jquery-1.11.1.min.js"></script>

</head>
<body>

	<h1>Kirielle — administration</h1>
	<!--<p><?php echo LOCAL_PATH; ?></p>
	<p><?php echo URL; ?></p>-->

	

	<h2>Mots clefs :</h2>
	<form action="" method="post">

		<input type="text"    name="keyword" placeholder="Mot clef"/>
		<input type="hidden"  name="add_keyword" value="1"/>
		<input type="submit"  value="Ajouter"/>

	</form>
	<ul>
		<?php

		// on fait la liste des mots clefs
		foreach( glob( "{" . LOCAL_PATH . '/keywords/*.json}', GLOB_BRACE ) as $file )
		{

			$info = json_decode(file_get_contents($file));

			$keyword      = $info->mot;
			$identifiant  = $info->identifiant;

			echo "<li>$keyword / $identifiant / (<a href='?suppr=$identifiant' data-word='$keyword' class='suppr'>supprimer</a>)</li>";

		}

		?>
	</ul>


	<h2>Images :</h2>
	<form action="" enctype="multipart/form-data" method="post">
		<input type="file" value="" name="image_file" />
		<input type="submit" value="Ajouter l'image"/>
	</form>
	<ul>
		<?php

	  	// on fait la liste des images
		foreach( glob( "{" . LOCAL_PATH . '/data/*}', GLOB_BRACE ) as $folder )
		{

			$vignette = $folder.'/vignette.jpg';

			if(is_file($vignette))
			{

				$url     = str_replace(LOCAL_PATH, URL, $vignette);
				$folder  = explode('/', $folder);
				$editurl = URL.'/denk_crop/edit.php?image='.$folder[count($folder)-1];

				echo "<li class='vignette'><a href='$editurl'><img src='$url'/></a></li>";
			}
		}

		?>
	</ul>



	<script>
		$(document).ready(function(){
			$('.suppr').click(function(event){

				if(!confirm("Attention vous allez supprimer le mot clef « "+ $(this).data('word') +" » et toutes les vignettes associées, souhaitez vous continuer ?")){
					event.preventDefault();
					return false;
				}

			});
		});
	</script>
</body>
</html>

