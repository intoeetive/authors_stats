<?php

$this->table->set_template($cp_pad_table_template);
$this->table->set_heading($table_headings);

	$this->table->add_row($data['entries'], $data['chars_total'], $data['chars_avg']);

echo $this->table->generate();


$this->table->clear();
?>