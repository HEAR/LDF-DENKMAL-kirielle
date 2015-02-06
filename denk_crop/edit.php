<?php

include_once('../config.php');
include_once(LOCAL_PATH.'/fonctions.php');

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

	if(!empty($_GET['remove']) && is_file($_GET['remove']))
	{
		unlink($_GET['remove']);

		header('Location:'.URL.'/denk_crop/edit.php?image='.$_GET['image']);
	}


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

	    /*$jsonString = file_get_contents($folderPATH.'/data.json');
	    $dataCoord  = json_decode($jsonString);
	    array_push($dataCoord->thumbs, $data);
	    $newJsonString = json_encode($dataCoord);
	    file_put_contents($folderPATH.'/data.json', $newJsonString);*/

	    //recuperer les coord et générer la vignette
	    $targ_w = $_POST['w'] ;
	    $targ_h = $_POST['h'] ;
	    $jpeg_quality = 90;

	    $src    = $imagePATH;  
	    $img_r  = imagecreatefromjpeg($src);
	    $dst_r  = ImageCreateTrueColor( $targ_w, $targ_h );
	    imagecopyresampled($dst_r,$img_r,0,0,$_POST['x1'],$_POST['y1'],$targ_w,$targ_h,$_POST['w'],$_POST['h']);

	    if( !is_dir($folderPATH.'/thumbs') )
	    {
	    	mkdir($folderPATH.'/thumbs');
	    }

	    $jsonKeyword = json_decode( file_get_contents( LOCAL_PATH."/keywords/$_POST[identifiant].json" ) );
	    $jsonKeyword->images[] = $_GET['image'];
	    $jsonKeyword->images   = array_unique($jsonKeyword->images);
	    file_put_contents(  LOCAL_PATH."/keywords/$_POST[identifiant].json" ,json_encode( $jsonKeyword ) );


	    //enregistrer la vignette avec le tag
	    imagejpeg($dst_r,$folderPATH.'/thumbs/'.$_POST['identifiant'].'['.$_POST['x1'].'x'.$_POST['y1'].'].jpg',$jpeg_quality);
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
	<link rel="stylesheet" href="../js/live-search/jquery.liveSearch.css" type="text/css" />

    <style type="text/css">
	   	.tagImg{
	        position:absolute;
	        background-color: pink;
	        opacity: 0.4;
	    }
	
		.tagImg:hover{
			opacity: 0.7;
		}

	    .wrapper{
	        position: relative;
	    }
    </style>

    
</head>
<body>

<div id="admin_page">

<?php if($isImage) : ?>

	<p><a href="./">Revenir à l'accueil</a></p>

    <h3><?php

	$json = json_decode( file_get_contents( $folderPATH.'/data.json' ) );
    echo $json->credit;

    ?></h3>

    <!-- Img a croper -->
    <div class="wrapper">
		<img src="<?php echo $imageURL; ?>" id="target" alt="" />
		<?php

			$thumbFolder  = $folderPATH . '/thumbs/';
			$zindex = 800;

			foreach( glob( "{" . $thumbFolder . '*.jpg}', GLOB_BRACE ) as $file )
			{
				//echo $file;

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

					echo "<div class='tagImg' style='top:{$y}px; left:{$x}px; width:{$w}px; height:{$h}px; z-index:{$zindex};' data-path='$file'>$nom</div>";

					$zindex ++ ;
				}
			}

		?>
	</div>

	<!-- Formulaire pour recuperer les coord et le tag -->
	<form id="coords" class="coords" method="post" action="">
		<input type="hidden" name="update">
		<input type="hidden" id="x1" name="x1" />
		<input type="hidden" id="y1" name="y1" />
		<input type="hidden" id="x2" name="x2" />
		<input type="hidden" id="y2" name="y2" />		
		<input type="hidden" id="w"  name="w"  />
		<input type="hidden" id="h"  name="h"  />
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

	</div>

	<script src="../js/jquery-1.11.1.min.js"></script>
    <script src="../js/jquery.Jcrop.js"></script>
    <script src="../js/live-search/jquery.liveSearch.js"></script>
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

		$('.tagImg').dblclick(function(event){
			//alert($(this).data('path'));

			location.href='<?php echo $_SERVER['REQUEST_URI']; ?>&remove='+$(this).data('path');
		})

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

