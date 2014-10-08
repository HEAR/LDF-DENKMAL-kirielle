<?php


/**
 * FICHIER DES FONCTIONS GÉNÉRIQUES UTILISÉES DANS LE PROJET 
 */



/**
 * Fonction servant à récupérer les coordonnées x, y stockée dans le nom des vignettes
 * @param  [type] $filename nom du fichier sur le principe nom_du_Fich-ier[125x45].jpg
 * @return [type]           un objet PHP avec les attributs nom, ext, x, y
 */
function getCoordFromName($filename)
{
	// regex pour isoler les coordonnées d'une vignette
	// nom_du_Fich-ier[12x45].png
	// ^([a-zA-Z_-]+)\[([\d]+)x([\d]+)\].(jpg|png|gif) 
	// $1 = nom_du_Fich-ier
	// $2 = 12
	// $3 = 45
	// $4 = png
	// $str = 'nom_du_Fich-ier[125x45].png';
	// echo $matches['nom'] . ' ' . $matches['x'] . ' ' . $matches['y'] . ' ' . $matches['ext'] ;

	preg_match('/(?<nom>[a-zA-Z_-]+)\[(?<x>\d+)x(?<y>\d+)\].(?<ext>jpg|png|gif)/', $filename, $matches);	

	$info = new stdClass();
	$info->nom = $matches['nom'] ;
	$info->ext = $matches['ext'] ;
	$info->x   = $matches['x'] ;
	$info->y   = $matches['y'] ;

	return $info;
}


/**
 * http://www.weirdog.com/blog/php/supprimer-les-accents-des-caracteres-accentues.html
 * @param  [type] $str     [description]
 * @param  string $charset [description]
 * @return [type]          [description]
 */
function wd_remove_accents($str, $charset='utf-8')
{
    $str = htmlentities($str, ENT_NOQUOTES, $charset);
    
    $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
    
    return $str;
}





/**
 * Fonction qui sert à nettoyer le nom des fichiers
 * @param  [type] $str [description]
 * @return [type]      [description]
 */
function removeSpaceAccents($str){

	$str = wd_remove_accents($str);
	$str = preg_replace('/([^.a-z0-9]+)/i', '_', $str);

	return $str;
}