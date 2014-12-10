<?php

header('Content-Type: text/html; charset=utf-8');

include_once('../config.php');

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

$foundresult = false;

if(!empty($_GET['q'])) :

?>


<ul id="search">
	<?php

	// on fait la liste des mots clefs
	foreach( glob( "{" . LOCAL_PATH . '/keywords/*.json}', GLOB_BRACE ) as $file )
	{

		$info = json_decode(file_get_contents($file));

		$keyword      = $info->mot;
		$identifiant  = $info->identifiant;

		//echo $keyword . ' ';

		if( strpos( strtolower( wd_remove_accents( $keyword ) ), strtolower( wd_remove_accents ( $_GET['q'] ) ) ) !== false )
		{
			echo "<li data-identifiant='$identifiant'>$keyword</li>";

			$foundresult = true;
		}

	}

	if(!$foundresult){
		echo '<li>Pas de résultat</li>';
	}

	?>
</ul>
<script>
	$(document).ready(function(){

		$('ul#search li').click(function(event){

			console.log( $(this).data('identifiant') );

			$('#tag').val( $(this).text() );
			$('#identifiant').val( $(this).data('identifiant') );

			$('#jquery-live-search').slideUp();

			event.stopPropagation();

		});

	});
</script>

<?php else : ?>

<ul>
	<li>Pas de résultat</li>
</ul>

<?php endif; ?>
