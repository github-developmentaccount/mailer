<style>
	.content{
		width: 84%;
		margin: 0px auto;
		min-height: 340px;
		background-color: #ECECEC;
		margin-top: 2em;
	
	}
	.button-topline {
		position: relative;
		height: 60px;
	}
	#writemail, #deletemail {
		position: relative;
		float: right;
		right: 4em;
		outline: none;
		margin-right: 25px;
		margin-top: 20px;
	}
	.span8 {
		height: 340px;
		overflow: scroll;
		/*width: 400px;*/
	}
	.span3 {
		margin-top:  20px;
		margin-right: 20px;
	}
	

</style>
	<script>
	$(document).ready(function (){
		jQuery('ul#route li').each(function(){
		    if(window.location.href.indexOf(jQuery(this).find('a:first').attr('href'))>-1)
		    {
		        jQuery(this).addClass('active').siblings().removeClass('active');
		    }
		});


		$('#delete').click(function () {
				var $checkboxes = $('input[type=checkbox]:checked');
				var result = '';
				var path = window.location.toString();
				if($checkboxes)  {
				$checkboxes.each(function(){
				    var x = this.value;
				    result += x + ',';
				});
				
			
				path = path.match(new RegExp('\/main\/([a-z]+[^#\s\d\W])'));
				
				$.ajax({
					type: 'post',
					url: "<?=base_url();?>index.php/main/delete",
					data: {'mid': result, 'path': path[1]},
					success: function (data){
						if(data == '1'){
							location.reload();
						}
						else {
							alert('Failed! '+data);
						}
					}
				});
			}
			
		});

	});
	</script>
<div class="content">
	
<div class="span3">
<ul class="nav nav-tabs nav-stacked" id='route'>
	<li><a href="<?=base_url();?>index.php/main/send">Написать</a></li>
	<li><a href="<?=base_url();?>index.php/main/inbox">Входящие</a></li>
	<li><a href="<?=base_url();?>index.php/main/outgoing">Исходящие</a></li>
	<li><a href="#" id='delete'>Удалить выбранные</a></li>
	<li><a href="<?=base_url();?>index.php/main/logout">Выход</a></li>

</ul>
</div>




