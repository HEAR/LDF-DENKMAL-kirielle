<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Denkmal Crop Img</title>
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
      function showCoords(c)
      {
        $('#x1').val(c.x);
        $('#y1').val(c.y);
        $('#x2').val(c.x2);
        $('#y2').val(c.y2);
        $('#w').val(c.w);
        $('#h').val(c.h);
      };

      function clearCoords()
      {
        $('#coords input').val('');
      };
    //fin Jcrop
    </script>
  <link rel="stylesheet" href="css/jquery.Jcrop.css" type="text/css" />
  </head>
  <body>

    <?php
      if ($_SERVER['REQUEST_METHOD'] == 'POST')
      {
          //calculer le nombre d'images dans le dossier
          $nbImg = count(glob('img_crop/*.jpg'));
          $n = $nbImg + 1;
        
          //recuperer les coord et générer la vignette
          $targ_w = $_POST['w'] ;
          $targ_h = $_POST['h'] ;
          $jpeg_quality = 90;
          $src = 'img/coolCar.jpg';  
          $img_r = imagecreatefromjpeg($src);
          $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
          imagecopyresampled($dst_r,$img_r,0,0,$_POST['x1'],$_POST['y1'],$targ_w,$targ_h,$_POST['w'],$_POST['h']);
         
          //enregistrer la vignette
          imagejpeg($dst_r,'img_crop/image'.$n.'.jpg',$jpeg_quality);
          imagedestroy($dst_r);

          //afficher toute les images
          for ($x = 1; $x < $n+1; $x ++){
            echo "<img src='img_crop/image".$x.".jpg'/>";
          }
       }  
      ?>
    <!-- Img a croper -->
    <img src="img/coolCar.jpg" id="target" alt="" />

    <!-- Formulaire pour recuperer les coord et le tag -->
    <form id="coords" class="coords" method="post" action="">

      <label>X1 <input type="number" size="4" id="x1" name="x1" /></label>
      <label>Y1 <input type="number" size="4" id="y1" name="y1" /></label>
      <label>X2 <input type="number" size="4" id="x2" name="x2" /></label>
      <label>Y2 <input type="number" size="4" id="y2" name="y2" /></label>
      <label>W <input type="number" size="4" id="w" name="w" /></label>
      <label>H <input type="number" size="4" id="h" name="h" /></label>
      <label>tag <input type="text" id="tag" name="tag" /></label>
      <input type= "submit" value="envoyer"/>
    </form>

    <?php
      //genere le fichier json avec coord et tag
      $json_file = fopen('coord.json', 'a');
      $coord = array(
        'tag' => $_POST['tag'],
        'x1' => $_POST['x1'], 
        'y1' => $_POST['y1'],
        'x2' => $_POST['x2'],
        'y2' => $_POST['y2'],
        'w' => $_POST['w'],
        'h' => $_POST['h']
      );

      $json_coord = json_encode($coord);
      fwrite($json_file, $json_coord."\n" );
      fclose($json_file);
    ?>

  </body>
</html>

