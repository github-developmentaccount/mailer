<center>

<form method='POST' action="<?=base_url();?>index.php/main/">
  <fieldset>
    <legend>Введите ваши авторизационные данные электронного ящика сервиса Gmail</legend>
    <label for="uname">Ваше имя</label>
    <input type="text" placeholder='name..' name='uname' id='uname' value="<?=set_value('uname');?>">
    <div class="active"></div>

    <label for='email'>Email</label>
    <input type="text" placeholder="email.." name='email' id='email' value="<?=set_value('email');?>">

    <label for="pass">Пароль</label>
    <input type="password" name="pass" placeholder='password...' id='pass'>
    <br>
    <button type="submit" class="btn">Отправить</button>
  </fieldset>
</form>

<div class="alert-wrapper" style='width: 500px; min-height: 100px;'>
	
</div>
</center>

<script>
	$(document).ready(function (){
		var email = "<?=form_error('email');?>";
		var pass = "<?=form_error('pass');?>";
		var uname = "<?=form_error('uname');?>";
		var issue = "<?php if(isset($issue)) echo $issue; ?>";
		var obj = { 'uname': uname, 'email' : email, 'pass' : pass};
			$.each(obj, function(key, value){
				if(value != ''){
					$('.alert-wrapper').append('<div class="alert alert-danger">'+value+'</div>');
					$('input[name='+key+']').css('border-color', 'red');
				}
			});

		if(issue != ''){
			$('.alert-wrapper').append('<div class="alert alert-danger">'+issue+'</div>');
		}

		

	});
</script>