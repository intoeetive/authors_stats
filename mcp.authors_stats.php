<?php

/*
=====================================================
 Authors Stats
-----------------------------------------------------
 http://www.intoeetive.com/
-----------------------------------------------------
 Copyright (c) 2012 Yuri Salimovskiy
=====================================================
 This software is intended for usage with
 ExpressionEngine CMS, version 2.0 or higher
=====================================================
*/

if ( ! defined('BASEPATH'))
{
	exit('Invalid file request');
}

class Authors_stats_mcp {

    var $version = '0.1';
    
    var $settings = array();
    
    var $perpage = 25;
    
    var $multiselect_fetch_limit = 50;
    
    function __construct() { 
        // Make a local reference to the ExpressionEngine super object 
        $this->EE =& get_instance(); 
        
        $this->EE->cp->set_variable('cp_page_title', lang('authors_stats_module_name'));
    } 

	function index()
	{
		
		$this->EE->load->helper('form');
    	$this->EE->load->library('table');  
        $this->EE->load->library('javascript');
        
        $date_fmt = ($this->EE->session->userdata('time_format') != '') ? $this->EE->session->userdata('time_format') : $this->EE->config->item('time_format');
       	$date_format = ($date_fmt == 'us')?'%m/%d/%y %h:%i %a':'%Y-%m-%d %H:%i';
       	$date_format_picker = ($date_fmt == 'us')?'mm/dd/y':'yy-mm-dd';

    	$vars = array();
    	$i = 0;
        
        $vars['selected']['date_from']=($this->EE->input->get_post('date_from')!='')?$this->EE->input->get_post('date_from'):'';
        
        $vars['selected']['date_to']=($this->EE->input->get_post('date_to')!='')?$this->EE->input->get_post('date_to'):'';

        $this->EE->cp->add_js_script('ui', 'datepicker'); 
        $this->EE->javascript->output(' $("#date_from").datepicker({ dateFormat: "'.$date_format_picker.'" }); '); 
        $this->EE->javascript->output(' $("#date_to").datepicker({ dateFormat: "'.$date_format_picker.'" }); '); 
        
        $fields = array();
        $q = $this->EE->db->select('field_id')
        		->from('channel_fields')
        		->where('site_id', $this->EE->config->item('site_id'))
        		->where_in('field_type', array('textarea', 'dm_eeck'))
        		->get();
		foreach ($q->result_array() as $row)
		{
			$fields[] = 'field_id_'.$row['field_id'];
		}
		
		$date_from = ($vars['selected']['date_from']!='')?$this->EE->localize->convert_human_date_to_gmt($vars['selected']['date_from']. ' 00:00'):$this->EE->localize->now-30*24*60*60;
		$date_to = ($vars['selected']['date_to']!='')?$this->EE->localize->convert_human_date_to_gmt($vars['selected']['date_to']. ' 23:59'):$this->EE->localize->now;
		
		$vars['date_from'] = $this->EE->localize->decode_date('%d %F %Y', $date_from);
		$vars['date_to'] = $this->EE->localize->decode_date('%d %F %Y', $date_to);	
		$vars['table_headings'] = array();
		$vars['table_headings'][] = lang('member');
		
		$channels_q = $this->EE->db->select('channel_id, channel_title')
        		->from('channels')
        		//->where_in('channel_id', array('5,6'))
        		->where('site_id', $this->EE->config->item('site_id'))
        		->get();	
		foreach ($channels_q->result_array() as $row)
		{
			$channels[$row['channel_id']] = $row['channel_title'];
		}
        
        $q = $this->EE->db->select('member_id, screen_name')
        		->distinct()
        		->from('channel_titles')
        		->join('members', 'members.member_id=channel_titles.author_id', 'left')
        		//->where_in('channel_id', array('5,6'))
        		->where('entry_date >= ', $date_from)
        		->where('entry_date <= ', $date_to)
        		->where('site_id', $this->EE->config->item('site_id'))
        		->get();
		foreach ($q->result_array() as $row)
		{
			$vars['data'][$i]['member'] = "<a href=\"".BASE.AMP.'C=myaccount'.AMP.'id='.$row['member_id']."\">".$row['screen_name']."</a>";  
			$q2 = $this->EE->db->select('channel_data.channel_id, '.implode(", ", $fields))
        		->from('channel_data')
        		->join('channel_titles', 'channel_data.entry_id=channel_titles.entry_id', 'left')
        		//->where_in('channel_titles.channel_id', array('5,6'))
        		->where('entry_date >= ', $date_from)
        		->where('entry_date <= ', $date_to)
        		->where('author_id', $row['member_id'])
        		->where('channel_data.site_id', $this->EE->config->item('site_id'))
        		->get(); 
 			$channel_entries = array();
 			$channel_chars = array();
 			foreach ($q2->result_array() as $row2)
			{
				echo $row2['channel_id']. ' ';
				if (!isset($channel_entries[$row2['channel_id']]))
				{
					$channel_entries[$row2['channel_id']] = 0;
					$channel_chars[$row2['channel_id']] = 0;
				}
				$channel_entries[$row2['channel_id']]++;
				foreach ($fields as $field)
				{
					$channel_chars[$row2['channel_id']] += strlen(strip_tags($row2[$field]));
				}
				
			}
			//var_dump($channel_entries);
			foreach ($channel_entries as $channel_id => $entries)
			{
				if ($channel_entries[$channel_id] > 0)
				{
					$stat_vars = array('data' => array(
						'entries'	=> $channel_entries[$channel_id],
						'chars_total'	=> $channel_chars[$channel_id],
						'chars_avg'	=> round($channel_chars[$channel_id]/$channel_entries[$channel_id]),
					));
					$stat_vars['table_headings'] = array(
						lang('entries'),
						lang('chars_total'),
						lang('chars_avg')
					);
					//var_dump($stat_vars);
					$vars['data'][$i]['channels'][$channel_id]['channel_name'] = $channels[$channel_id];
					$vars['data'][$i]['channels'][$channel_id]['channel_table'] = $this->EE->load->view('channel_stats', $stat_vars, TRUE);
					$vars['table_headings'][] = $channels[$channel_id];
				}
			}
			$i++;
		}
	//var_dump($vars);
        
    	return $this->EE->load->view('stats', $vars, TRUE);
	
    }
  

}
/* END */
?>