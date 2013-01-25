

<div id="filterMenu">
	<fieldset>
		<legend><?=lang('refine_results')?></legend>

	<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=authors_stats');?>

		<div class="group">
            <?php
			
			$field = array(
              'name'        => 'date_from',
              'value'       => $selected['date_from'],
              'size'        => '25',
              'id'          => 'date_from',
              'style'       => 'width:120px'
            );
			
			echo lang('from').NBS.NBS.form_input($field);
			
			$field = array(
              'name'        => 'date_to',
              'value'       => $selected['date_to'],
              'size'        => '25',
              'id'          => 'date_to',
              'style'       => 'width:120px'
            );
			
			echo NBS.lang('to').NBS.NBS.form_input($field);
	
            echo NBS.NBS.form_submit('submit', lang('show'), 'class="submit" id="search_button"');
            
            ?>
		</div>

	<?=form_close()?>
	</fieldset>
</div>

<h3><?=lang('from').' '.$date_from.' '.lang('to').' '.$date_to?></h3>


<?php

foreach ($data as $item)
{
	echo '<h3>'.$item['member'].'</h3>';
	foreach ($item['channels'] as $channel)
	{
		echo '<h4>'.$channel['channel_name'].'</h2>';
		echo '<h4>'.$channel['channel_table'].'</h2>';
	}
}

?>