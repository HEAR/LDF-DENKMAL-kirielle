<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Kyrielle</title>
	<link rel="stylesheet" href="css/style.css">
</head>
	<body>
		<div id="page">
				<a href="" onclick="bascule('tag'); return false;">
					<div style='display:block;'id="tag">Chateau</div>
				</a>
				<a href="navigation.php"><div id="vignette"></div></a>
			<?php include("footer.php"); ?>
		</div>

		<script language="javascript" type="text/javascript">
			function bascule(elem){
			   etat=document.getElementById(elem).style.display;
			   if(etat=="block"){
			   document.getElementById(elem).style.display="none";
			   }else{
			   document.getElementById(elem).style.display="block";
			   }
			}
		</script>
	</body>
</html>