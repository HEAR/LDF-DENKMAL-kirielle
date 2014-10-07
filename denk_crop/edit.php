<?php

include_once('../config.php');

$isImage = false;

/**
 * ON VERIFIE QUE L'IMAGE EXISTE BIEN
 */
if(!empty($_GET['image']) && is_file(LOCAL_PATH.'/data/'.$_GET['image'].'/'.$_GET['image'].'.jpg'))
{
    $isImage = true;
}

/**
 * SI L'IMAGE EXISTE
 */
if($isImage)
{

	$folderPATH = LOCAL_PATH.'/data/'.$_GET['image'];
	$folderURL  = URL.'/data/'.$_GET['image'];

	$imagePATH  = $folderPATH.'/'.$_GET['image'].'.jpg';
	$imageURL   = $folderURL.'/'.$_GET['image'].'.jpg';

	if(isset($_POST['update']))
	{

	    //genere le fichier json avec coord et tag
	    $data = array (
	        'tag' => $_POST['identifiant'],
	        'x1'  => $_POST['x1'], 
	        'y1'  => $_POST['y1'],
	        'x2'  => $_POST['x2'],
	        'y2'  => $_POST['y2'],
	        'w'   => $_POST['w'],
	        'h'   => $_POST['h']
	    );

	    $jsonString = file_get_contents($folderPATH.'/data.json');
	    $dataCoord  = json_decode($jsonString);
	    array_push($dataCoord->thumbs, $data);
	    $newJsonString = json_encode($dataCoord);
	    file_put_contents($folderPATH.'/data.json', $newJsonString);

	    //recuperer les coord et générer la vignette
	    $targ_w = $_POST['w'] ;
	    $targ_h = $_POST['h'] ;
	    $jpeg_quality = 90;

	    $src    = $imagePATH;  
	    $img_r  = imagecreatefromjpeg($src);
	    $dst_r  = ImageCreateTrueColor( $targ_w, $targ_h );
	    imagecopyresampled($dst_r,$img_r,0,0,$_POST['x1'],$_POST['y1'],$targ_w,$targ_h,$_POST['w'],$_POST['h']);

	    //enregistrer la vignette avec le tag
	    imagejpeg($dst_r,$folderPATH.'/thumbs/'.$_POST['identifiant'].'.jpg',$jpeg_quality);
	    imagedestroy($dst_r);
	
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Kirielle Image Tag Editor</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

	<link rel="stylesheet" href="css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="js/live-search/jquery.liveSearch.css" type="text/css" />

    <style type="text/css">
	    #tagImg{
	        position:absolute;
	        background-color: pink;
	        opacity: 0.4;
	    }
	    .wrapper{
	        position: relative;
	    }
    </style>

    
</head>
<body>

<?php if($isImage) : ?>

	<p><a href="./">Revenir à l'accueil</a></p>
	
    <p><?php echo $imagePATH; ?></p>
    <p><?php echo $imageURL; ?></p>

    <!-- Img a croper -->
    <div class="wrapper">
		<img src="<?php echo $imageURL; ?>" id="target" alt="" />
		<?php //placement des vignettes
			$jsonF = file_get_contents($folderPATH.'/data.json');
			$coord = json_decode($jsonF,true);
			$compteur = count($coord);

			if($compteur > 0)
			{
				for($i=0; $i<$compteur; $i ++)
				{
					if( !empty($coord[$i]['tag'] ) && $coord[$i]['tag'] != null)
					{
						$posX = $coord[$i]['x1'];
						$posY = $coord[$i]['y1'];
						$h 	  = $coord[$i]['h'];
						$w 	  = $coord[$i]['w'];
						$tag  = $coord[$i]['tag'];
			?>

		<div id="tagImg" style="top:<?php echo $posY ?>px; left:<?php echo $posX ?>px; width:<?php echo $w?>px; height:<?php echo $h?>px;"><?php echo $tag?></div>

		<?php
					}
				}
			}
		?>
	</div>

	<!-- Formulaire pour recuperer les coord et le tag -->
	<form id="coords" class="coords" method="post" action="">
		<input type="hidden" name="update">
		<label>x1 <input type="number" size="4" id="x1" name="x1"/></label>
		<label>y1 <input type="number" size="4" id="y1" name="y1"/></label>
		<label>x2 <input type="number" size="4" id="x2" name="x2"/></label>
		<label>y2 <input type="number" size="4" id="y2" name="y2"/></label>		
		<label>w  <input type="number" size="4" id="w"  name="w" /></label>
		<label>h  <input type="number" size="4" id="h"  name="h" /></label>
		<div id="tagPos">
			<input type="text" id="tag" name="tag" value="" placeholder="mot clef" />
			<input type="hidden" id="identifiant" name="identifiant" value="" />
			<input type="submit" value="Sauver" />
		</div>
	</form>

	<?php else: ?>

		<h2>Aucune image n'a été sélectionnée, ou alors elle n'existe pas.</h2>
		<p><a href="./">Revenir à l'accueil</a></p>

	<?php endif;?>

	<script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.Jcrop.js"></script>
    <script src="js/live-search/jquery.liveSearch.js"></script>
    <script type="text/javascript">

    //Jcrop
    jQuery(function($){

		var jcrop_api;

		$('#target').Jcrop({
			onChange:   showCoords,
			onSelect:   showCoords,
			onRelease:  clearCoords
		},function(){
			jcrop_api = this;
		});

		$('#coords').on('change','input',function(e){
			var x1 = $('#x1').val(),
			x2 = $('#x2').val(),
			y1 = $('#y1').val(),
			y2 = $('#y2').val();
			jcrop_api.setSelect([x1,y1,x2,y2]);

			$('#jquery-live-search').hide();
		});

		$('#tagPos').hide();

		$('#tagPos input[name="tag"]').liveSearch({
			url: 'search.php?q=',
		});

    });

    /**
     * [showCoords description]
     * @param  {[type]} c [description]
     * @return {[type]}   [description]
     */
    function showCoords(c){
        $('#x1').val(c.x);
        $('#y1').val(c.y);
        $('#x2').val(c.x2);
        $('#y2').val(c.y2);
        $('#w').val(c.w);
        $('#h').val(c.h);

        //input tag position
        var posX = c.x + 15;
        var posY = c.y + 90;
        var div  = document.getElementById("tagPos");
        div.style.position = "absolute";
        div.style.left	   = posX+'px' ;
        div.style.top	   = posY +'px';
        div.style.zIndex   ="900";

        $('#tagPos').show();
    };

    /**
     * [clearCoords description]
     * @return {[type]} [description]
     */
    function clearCoords(){
        $('#coords input[type=number]').val('');
        $('#tagPos #tag').val('');
        $('#tagPos #identifiant').val('');
        $('#tagPos').hide();
    };
    //fin Jcrop
    </script>
</body>
</html>

