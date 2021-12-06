<table class="business-hours">
	<tbody>
		<?php foreach( $settings->rows as $row ) : ?>
			<tr>
				<th class="title"><span class="inner"><?php echo $row->title; ?></span></th>
				<td class="hours"><span class="inner"><?php echo $row->hours; ?></span></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>