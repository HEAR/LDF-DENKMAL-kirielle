<?php
	
	$nom = $_POST['titre'];
	$legende = $_POST['legende'];
	$photo = $_FILES['image']['tmp_name'];

	//coordonees
	mkdir("img/".$nom, 0777, true);
	$jsonFile = 'img/'.$nom.'/'.$nom.'_coord.json';
	$openJson = fopen($jsonFile, 'w') or die('Erreur: '.$jsonFile);
	chmod($jsonFile , 0777);
	$coord = array();
	file_put_contents($jsonFile, json_encode($coord));
	//legende
	$legendeFile = 'img/'.$nom.'/'.$nom.'_legende.txt';
	$openTxt= fopen($legendeFile, 'w') or die('Erreur: '.$legendeFile);
	chmod($legendeFile , 0777);
	file_put_contents($legendeFile, $legende."\n");
	//image
	$extension_upload = strtolower(substr(strrchr($_FILES['image']['name'], '.'),1));
	$imgDir = 'img/'.$nom.'/'.$nom.'.'.$extension_upload;
	include('SimpleImage.php');
	$image = new SimpleImage();
	$image->load($photo);
	$image->resizeToWidth(1024);
	$image->save($imgDir);

	//redirection
	header('Location: denk_cropImg.php?dir='.$imgDir);

?>	