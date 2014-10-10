
	</div>
	<div id="credit">
		<p>Conception, réalisation : Lucas Descroix, Arman Mohtadji, Léna Robin</br>
		Remerciements : Thomas Deyriès, Loïc Horellou, Alain Willaume, Philippe Delangle</p>
	</div>
	<div id="apropos">
		<a href="<?php echo URL;?>">Accueil</a><a href="" id="credit_btn">Crédits</a>
	</div>	

	<!-- fin #page -->
	
	<script src="<?php echo URL;?>/js/jquery-1.11.1.min.js"></script>
	<script>

		var start = 0;

		$(document).ready(function(){

			$("#credit_btn").click(function(event){
				$("#apropos").slideUp();
				$("#credit").slideDown();

				event.preventDefault();
			});

			$("#credit").click(function(event){
				$("#apropos").slideDown();
				$("#credit").slideUp();

				event.preventDefault();
			});

			$("#go").click(function(event){
				$("#accueil").hide();

				updateVignettes();

				setInterval(updateVignettes, 2000);

				
				event.preventDefault();


			})

			$('.tagImg').mouseover(function(){
				$("ul#listetags").find('.'+$(this).data('tag')).addClass('activeTag');
			});

			$('.tagImg').mouseout(function(){
				$("ul#listetags li").removeClass('activeTag');
			});


			$('.tagImg').click(function(event){

				console.log( $(this).data('tag') );
				//location.href='<?php echo URL;?>'+'/tag/'+$(this).data('tag')+'/';

			});		

		});

		function updateVignettes(){
			$tags = $("#tags").data("tags").split(',');
			//var rand = Math.floor( Math.random() * $tags.length );

			
			
			$.ajax({
					url: "<?php echo URL;?>/get_tag_info.php?tag="+$tags[ start ],
					dataType: 'json',
					success:function(data){
						$("#tags img").remove();

						console.log(data);

						$("#tags h1").text(data.keyword);

						console.log(data.listeThumbs);

						$.each(data.listeThumbs, function(key,value){

							var image = $("<a>").attr("href",value.target).append(
								$("<img>").css({left:value.x+"px",top:value.y+"px",position:"absolute"}).attr("src",value.url)
							);

							$("#tags").append(image);

						});

						$("#tags").show();

						start ++ ;

						if(start >= $tags.length){
							start = 0;
						}
					}
			});
		}

	</script>

</body>
</html>