<?php

include_once('../config.php'); 

if(isset($_POST['update'])){

  // créer un evenement on submit ??//

  //genere le fichier json avec coord et tag
  $data = array (
    'tag' => $_POST['tag'],
    'x1' => $_POST['x1'], 
    'y1' => $_POST['y1'],
    'x2' => $_POST['x2'],
    'y2' => $_POST['y2'],
    'w' => $_POST['w'],
    'h' => $_POST['h']
  );
  $jsonString = file_get_contents('coord.json');
  $dataCoord = json_decode($jsonString);
  array_push($dataCoord, $data);
  $newJsonString = json_encode($dataCoord);
  file_put_contents('coord.json', $newJsonString);

  //recuperer les coord et générer la vignette
  $targ_w = $_POST['w'] ;
  $targ_h = $_POST['h'] ;
  $jpeg_quality = 90;
  $src = 'img/coolCar.jpg';  
  $img_r = imagecreatefromjpeg($src);
  $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
  imagecopyresampled($dst_r,$img_r,0,0,$_POST['x1'],$_POST['y1'],$targ_w,$targ_h,$_POST['w'],$_POST['h']);

  //enregistrer la vignette avec le tag
  imagejpeg($dst_r,'img_crop/'.$_POST['tag'].'.jpg',$jpeg_quality);
  imagedestroy($dst_r);
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Kyrielle Image Tag Editor</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.Jcrop.js"></script>
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
        });

      });

      function showCoords(c){
        $('#x1').val(c.x);
        $('#y1').val(c.y);
        $('#x2').val(c.x2);
        $('#y2').val(c.y2);
        $('#w').val(c.w);
        $('#h').val(c.h);

        //input tag position
        var posX = c.x + 5;
        var posY = c.y2 + 10;
        var div = document.getElementById("tagPos");
        div.style.position="absolute";
        div.style.left= posX+'px' ;
        div.style.top= posY +'px';
        div.style.zIndex="900";
      };

      function clearCoords(){
        $('#coords input').val('');
      };
    //fin Jcrop
    </script>

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

    <link rel="stylesheet" href="css/jquery.Jcrop.css" type="text/css" />
  </head>
  <body>

    <p><?php echo LOCAL_PATH; ?></p>
    <p><?php echo URL; ?></p>

    <!-- Img a croper -->
    <div class="wrapper">
      <img src="img/coolCar.jpg" id="target" alt="" />
      <?php //placement des vignettes
      $jsonF = file_get_contents('coord.json');
      $coord = json_decode($jsonF,true);
      $compteur = count($coord);

      for($i=0; $i<$compteur; $i ++){
        if($coord[$i]['tag'] != null){
          $posX=$coord[$i]['x1'];
          $posY=$coord[$i]['y1'];
          $h=$coord[$i]['h'];
          $w=$coord[$i]['w'];
          $tag=$coord[$i]['tag'];
        ?>

      <div id="tagImg" style="top:<?php echo $posY ?>px; left:<?php echo $posX ?>px; width:<?php echo $w?>px; height:<?php echo $h?>px;"><?php echo $tag?></div>

      <?php
        }
      }
      ?>
    </div>

    <!-- Formulaire pour recuperer les coord et le tag -->
    <form id="coords" class="coords" method="post" action="">
      <input type="hidden" name="update">
      <label>X1 <input type="number" size="4" id="x1" name="x1" /></label>
      <label>Y1 <input type="number" size="4" id="y1" name="y1" /></label>
      <label>X2 <input type="number" size="4" id="x2" name="x2" /></label>
      <label>Y2 <input type="number" size="4" id="y2" name="y2" /></label>
      <label>W <input type="number" size="4" id="w" name="w" /></label>
      <label>H <input type="number" size="4" id="h" name="h" /></label>
      <div id="tagPos">
        <input type="text" id="tag" name="tag" value="ici le tag"/> 
        <input type= "submit" value="envoyer"/>
      </div>
    </form>
    
   
  </body>
</html>

