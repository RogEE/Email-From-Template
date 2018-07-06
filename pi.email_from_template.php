<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
=====================================================

RogEE Email-from-Template
a plug-in for ExpressionEngine 2
by Michael Rog

Please e-mail me with questions, feedback, suggestions, bugs, etc.
>> michael@michaelrog.com
>> http://michaelrog.com/ee

This plugin is compatible with NSM Addon Updater:
>> http://github.com/newism/nsm.addon_updater.ee_addon

=====================================================

*/

/** ---------------------------------------
/**  Email_from_template class
/** ---------------------------------------*/

class Email_from_template {

	var $return_data = "";

	function Email_from_template($str = '')
	{

		// defaults	    
	    $this->to = ee()->config->item('webmaster_email');
	    $this->cc = "";
	    $this->bcc = "";
		$this->from = ee()->config->item('webmaster_email');
		$this->subject = "Email-from-Template: ".ee()->uri->uri_string();
		$this->echo_tagdata = TRUE;
		$this->append_debug = FALSE;

		// params: fetch / sanitize / validate
		
		$mailtype = (($mailtype = ee()->TMPL->fetch_param('mailtype')) == "html") ? "html" : "text";
		
		$from = (($from = ee()->TMPL->fetch_param('from')) === FALSE) ? $this->from : ee()->security->xss_clean($from);
		$to = (($to = ee()->TMPL->fetch_param('to')) === FALSE) ? $this->to : ee()->security->xss_clean($to);
		$cc = (($cc = ee()->TMPL->fetch_param('cc')) === FALSE) ? FALSE : ee()->security->xss_clean($cc);
		$bcc = (($bcc = ee()->TMPL->fetch_param('bcc')) === FALSE) ? FALSE : ee()->security->xss_clean($bcc);
		
		$subject = (($subject = ee()->TMPL->fetch_param('subject')) === FALSE) ? $this->subject : $subject;
		$alt_message = (($alt_message = ee()->TMPL->fetch_param('alt_message')) === FALSE) ? FALSE : ee()->security->xss_clean($alt_message);
		
		$decode_subject_entities = (strtolower(ee()->TMPL->fetch_param('decode_subject_entities')) == "no") ? FALSE : TRUE ;
		$decode_message_entities = (strtolower(ee()->TMPL->fetch_param('decode_message_entities')) == "no") ? FALSE : TRUE ;
		
		$attachments = (($attachments = ee()->TMPL->fetch_param('attachments')) === FALSE) ? FALSE : ee()->security->xss_clean($attachments);
		
		$echo_tagdata = (strtolower(ee()->TMPL->fetch_param('echo')) == "no" || strtolower(ee()->TMPL->fetch_param('echo')) == "off") ? FALSE : TRUE ;
		
		// fetch tag data
    
		if ($str == '')
		{
			$str = ee()->TMPL->tagdata ;
		}

		$tagdata = $str;
		
		// assemble and parse template variables
		
		$variables = array();
		
		$single_variables = array(
			'from' => $from,
			'to' => $to,
			'cc' => $cc,
			'bcc' => $bcc,
			'subject' => $subject,
			'ip' => ee()->input->ip_address(),
			'httpagent' => ee()->input->user_agent(),
			'uri_string' => ee()->uri->uri_string()
		);

		$variables[] = $single_variables;

		$message = ee()->TMPL->parse_variables($tagdata, $variables) ;
		
		// parse global variables
		
		$subject = ee()->TMPL->parse_globals($subject);
		$message = ee()->TMPL->parse_globals($message);
		
		// decode HTML entities
		
		if ($decode_subject_entities)
		{
			ee()->TMPL->log_item('Decoding HTML entities in subject...');
			$subject = $decode_subject_entities ? html_entity_decode($subject) : $subject;
		}
		
		if ($decode_message_entities)
		{
			ee()->TMPL->log_item('Decoding HTML entities in message...');
			$message = $decode_message_entities ? html_entity_decode($message) : $message;
		}

		// mail the message
				
		ee()->TMPL->log_item('Sending email from template...');
			
		ee()->load->library('email');
		ee()->email->initialize() ;

		ee()->TMPL->log_item('MAILTYPE: ' . $mailtype);
		ee()->email->mailtype = $mailtype;

		ee()->TMPL->log_item('FROM: ' . $from);
		ee()->email->from($from);

		ee()->TMPL->log_item('TO: ' . $to);
		ee()->email->to($to); 

		ee()->TMPL->log_item('CC: ' . ($cc ? $cc : '(none)'));
		ee()->email->cc($cc);
		
		ee()->TMPL->log_item('BCC: ' . ($bcc ? $bcc : '(none)'));
		ee()->email->bcc($bcc);

		ee()->TMPL->log_item('SUBJECT: ' . $subject);
		ee()->email->subject($subject);
		
		ee()->email->message($message);
		
		if ($alt_message !== FALSE)
		{
			ee()->email->set_alt_message($alt_message);	
		}
		
		if ($attachments !== FALSE)
		{
			ee()->TMPL->log_item('Adding attachemnts...');
			
			$attachments_array = explode(",", $attachments);
			foreach($attachments_array as $attachment_path)
			{
				ee()->TMPL->log_item('Attachment: '.$attachment_path);
				ee()->email->attach($attachment_path);
			}
		}
		
		ee()->email->Send();

		// more template debugging

		ee()->TMPL->log_item('Email sent!');
		
		if (! $echo_tagdata) { ee()->TMPL->log_item('Echo is off. Outputting nothing to template.'); }
		else { ee()->TMPL->log_item('Echo is on. Repeating message to template.'); }
		
		// return data to template
		
		$this->return_data = ($echo_tagdata) ? $message : "";
		
		if ($this->append_debug)
		{
			$this->return_data .= "<br><hr><br>".ee()->email->print_debugger();
		}

	}

}

/* End of file pi.email-from-template.php */ 
/* Location: ./system/expressionengine/third_party/email-from-template/pi.email-from-template.php */
