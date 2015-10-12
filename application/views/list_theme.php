<style>
	.span8 {
		height: 340px;
		overflow: scroll;
		width: 800px;
	}
</style>
<div class="span8">
	<table class="table table-hover">
		<thead>
			<tr>
				<th>id</th>
				<th>Отправитель</th>
				<th>Тема письма</th>
				<th>Дата получения</th>
			</tr>
		</thead>

		<tbody>
		<?php foreach($output as $message) :?>
		
			<tr class="success">
		    <td style='line-height: 1px;'><input type='checkbox' value="<?=$message['mid'];?>"></td>
		    <td><?=$message['sender'];?><input type='hidden' name='to' value="<?=$message['to'];?>"></td>
		    <td><?=anchor("main/{$path}/view/{$message['mid']}",$message['subject']); ?></td>
		    <td><?=$message['date'];?></td>
		    </tr>
		
		<?php endforeach; ?>
		</tbody>
	</table>

	</div>
	</div>