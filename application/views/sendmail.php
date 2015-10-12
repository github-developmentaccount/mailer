
<center>
<form action="<?=base_url();?>index.php/main/send/" method='post'>
<fieldset>
    <legend>Отправить письмо</legend>
    <label for="to"><b>Получатель</b></label>
    <input type="text" placeholder="email получателя…" name='to' value="<?=set_value('to');?>" id="to">

    <label for='theme'><b>Тема</b></label>
    <input type="text" placeholder="Тема письма…" name='theme' value="<?=set_value('theme');?>" id="theme">

    <label for="text"><b>Текст сообщения</b></label>
    <textarea name="text" id="text" cols="30" rows="10" value="<?=set_value('text');?>"></textarea>
	<br>
    <button type="submit" class="btn">Submit</button>
  </fieldset>
</form>
<?=anchor('main/inbox/', "Назад");?>
<div class="alert-wrapper" style='width: 400px;'>
    
</div>
</center>

<script>
    $(document).ready(function (){
        var toError = "<?=form_error('to');?>";
        var themeError = "<?=form_error('theme');?>";
        var textError = "<?=form_error('text');?>";

        var obj = { 'to': toError, 'theme' : themeError, 'text' : textError};
            $.each(obj, function(key, value){
                if(value != ''){
                    $('.alert-wrapper').append('<div class="alert alert-danger">'+value+'</div>');
                    $('#'+key).css('border-color', 'red');

                }
            });
    });

</script>