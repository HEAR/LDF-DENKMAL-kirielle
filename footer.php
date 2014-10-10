	
	</div>
	<div id="apropos"><a>Crédits</a><a href="<?php echo URL;?>">Accueil</a></div>	

	<!-- fin #page -->
	
	<script src="<?php echo URL;?>/js/jquery-1.11.1.min.js"></script>
	<script>

		$(document).ready(function(){

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
			var rand = Math.floor( Math.random() * $tags.length );

			$("#tags img").remove();
			
			$.ajax({
					url: "<?php echo URL;?>/get_tag_info.php?tag="+$tags[rand],
					dataType: 'json',
					success:function(data){

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
					}
			});
		}

	</script>

</body>
</html>