<html lang="en">
  <head>
    <title>Denkmal Crop Img</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
  </head>
  <body>
	<form method="post" action="denk_receptImg.php" enctype="multipart/form-data">
	     <label for="mon_fichier">Fichier</label><br />
	     <!-- variable communiquée au navigateur
	     <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />-->
	     <!-- upload du fichier-->
	     <input type="file" name="image" id="image" /><br />
	    
	     <label for="titre">Titre :</label><br />
    	 <input type="text" name="titre" value="titre" id="titre" /><br />	
    	 <label for="titre">Légende :</label><br />
    	 <input type="text" name="legende" value="legende" id="legende" /><br />
	     <input type="submit" name="submit" value="Envoyer" />
	</form>
  </body>
</htlm>