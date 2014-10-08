	
	<script src="<?php echo URL;?>/js/jquery-1.11.1.min.js"></script>
	<script>

		$(document).ready(function(){

			$('.tagImg').mouseover(function(){
				$("ul#tags").find('.'+$(this).data('tag')).addClass('activeTag');
			});

			$('.tagImg').mouseout(function(){
				$("ul#tags li").removeClass('activeTag');
			});


			$('.tagImg').click(function(event){

				console.log( $(this).data('tag') );
				location.href='<?php echo URL;?>'+'/tag/'+$(this).data('tag')+'/';

			});		

		});

	</script>

</body>
</html>