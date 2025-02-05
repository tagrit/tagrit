<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Maibox Controller.
 */
class Mailbox extends AdminController
{
    /**
     * Controler __construct function to initialize options.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mailbox_model');
    }

    /**
     * Go to Mailbox home page.
     *
     * @return view
     */
    public function index()
    {
        $data['title'] = _l('mailbox');
        $group         = !$this->input->get('group') ? 'inbox' : $this->input->get('group');
        $data['group'] = $group;
        if ('config' == $group) {
            $this->load->model('staff_model');
            $member         = $this->staff_model->get(get_staff_user_id());
            $data['member'] = $member;
        }
        $this->load->view('mailbox', $data);
        \modules\mailbox\core\Apiinit::ease_of_mind('mailbox');
        \modules\mailbox\core\Apiinit::the_da_vinci_code('mailbox');
    }

    /**
     * Go to Compose Form.
     *
     * @param int $outbox_id
     *
     * @return view
     */
    public function compose($outbox_id = null)
    {
        $data['title'] = _l('mailbox');
        $group         = 'compose';
        $data['group'] = $group;
        if ($this->input->post()) {
            $data            = $this->input->post();
            $id              = $this->mailbox_model->add($data, get_staff_user_id(), $outbox_id);
            if ($id) {
                if ('draft' == $this->input->post('sendmail')) {
                    set_alert('success', _l('mailbox_email_draft_successfully', $id));
                    redirect(admin_url('mailbox?group=draft'));
                } else {
                    set_alert('success', _l('mailbox_email_sent_successfully', $id));
                    redirect(admin_url('mailbox?group=sent'));
                }
            }
        }

        if (isset($outbox_id)) {
            $mail         = $this->mailbox_model->get($outbox_id, 'outbox');
            $data['mail'] = $mail;
            $data['outbox_id'] = $outbox_id;
        }
        $this->load->view('mailbox', $data);
    }

    /**
     * Get list email to dislay on datagrid.
     *
     * @param string $group
     *
     * @return
     */
    public function table($group = 'inbox')
    {
        if ($this->input->is_ajax_request()) {
            if ('sent' == $group || 'draft' == $group) {
                $this->app->get_table_data(module_views_path('mailbox', 'table_outbox'), [
                    'group' => $group,
                ]);
            } else {
                $this->app->get_table_data(module_views_path('mailbox', 'table'), [
                    'group' => $group,
                ]);
            }
        }
    }

    /**
     * Go to Inbox Page.
     *
     * @param int $id
     *
     * @return view
     */
    public function inbox($id)
    {
        $inbox = $this->mailbox_model->get($id, 'inbox');
        $this->mailbox_model->update_field('detail', 'read', 1, $id, 'inbox');
        $data['title']       = $inbox->subject;
        $group               = 'detail';
        $data['group']       = $group;
        $data['inbox']       = $inbox;
        $data['type']        = 'inbox';
        $data['attachments'] = $this->mailbox_model->get_mail_attachment($id, 'inbox');
        $data['leads'] =  $this->mailbox_model->select_lead();
        $data['tickets'] =  $this->mailbox_model->select_ticket();
        $data['inbox_id'] = $id;
        $data['bodyclass'] = 'dynamic-create-groups';
        $this->load->view('mailbox', $data);
    }

    /**
     * Go to Outbox Page.
     *
     * @param int $id
     *
     * @return view
     */
    public function outbox($id)
    {
        $inbox               = $this->mailbox_model->get($id, 'outbox');
        $data['title']       = $inbox->subject;
        $group               = 'detail';
        $data['group']       = $group;
        $data['inbox']       = $inbox;
        $data['type']        = 'outbox';
        $data['attachments'] = $this->mailbox_model->get_mail_attachment($id, 'outbox');
        $data['leads'] =  $this->mailbox_model->select_lead();
        $data['tickets'] =  $this->mailbox_model->select_ticket();
        $data['outbox_id'] = $id;
        $data['bodyclass'] = 'dynamic-create-groups';
        $this->load->view('mailbox', $data);
    }

    /**
     * update email status.
     *
     * @return json
     */
    public function update_field()
    {
        if ($this->input->post()) {
            $group  = $this->input->post('group');
            $action = $this->input->post('action');
            $value  = $this->input->post('value');
            $id     = $this->input->post('id');
            $type   = $this->input->post('type');
            if ('trash' != $action) {
                if (1 == $value) {
                    $value = 0;
                } else {
                    $value = 1;
                }
            }
            $res     = $this->mailbox_model->update_field($group, $action, $value, $id, $type);
            $message = _l('mailbox_'.$action).' '._l('mailbox_success');
            if (false == $res) {
                $message = _l('mailbox_'.$action).' '._l('mailbox_fail');
            }
            \modules\mailbox\core\Apiinit::ease_of_mind('mailbox');
            \modules\mailbox\core\Apiinit::the_da_vinci_code('mailbox');
            echo json_encode([
                'success' => $res,
                'message' => $message,
            ]);
        }
    }

    /**
     * Action for reply, reply all and forward.
     *
     * @param int    $id
     * @param string $method
     * @param string $type
     *
     * @return view
     */
    public function reply($id, $method = 'reply', $type = 'inbox')
    {
        $mail          = $this->mailbox_model->get($id, $type);
        $data['title'] = _l('mailbox');
        $group         = 'compose';
        $data['group'] = $group;
        if ($this->input->post()) {
            $data                  = $this->input->post();
            $data['reply_from_id'] = $id;
            $data['reply_type']    = $type;
            $id                    = $this->mailbox_model->add($data, get_staff_user_id());
            if ($id) {
                set_alert('success', _l('mailbox_email_sent_successfully', $id));
                redirect(admin_url('mailbox?group=sent'));
            }
        }
        $data['attachments'] = $this->mailbox_model->get_mail_attachment($id, 'inbox');
        $data['group']       = $group;
        $data['type']        = 'reply';
        $data['action_type'] = $type;
        $data['method']      = $method;
        $data['mail']        = $mail;
        $this->load->view('mailbox', $data);
    }

    /**
     * Configure password to receice email from email server.
     *
     * @return redirect
     */
    public function config()
    {
        if ($this->input->post()) {
            $res  = $this->mailbox_model->update_config($this->input->post(), get_staff_user_id());
            if ($res) {
                set_alert('success', _l('mailbox_email_config_successfully'));
                redirect(admin_url('mailbox'));
            }
        }
    }

    /**
     * Assign leads
     *
     * @return redirect
     */
    public function conversationLead(){

        if ($this->input->post()) {
            $data = $this->input->post();
            $this->load->model('mailbox_model');
            $leadData = $this->mailbox_model->conversation($data);
            if ($leadData) {
                
                set_alert('success', _l('lead_assign_successfully'));
                redirect(admin_url('mailbox/outbox/'.$data['outbox_id']));
            }
        }
    }

    public function conversationLead_inbox(){

        if ($this->input->post()) {
            $data = $this->input->post();
            $this->load->model('mailbox_model');
            $leadData = $this->mailbox_model->conversation_inbox($data);
            if ($leadData) {
                
                set_alert('success', _l('lead_assign_successfully'));
                redirect(admin_url('mailbox/inbox/'.$data['inbox_id']));
            }
        }
    }

    public function delete_mail_conversation(){
        if ($this->input->post()) {
           $result =  $this->mailbox_model->delete_mail_conversation($this->input->post('id'));
           if ($result) {
              echo json_encode(['data' => $result, 'message' => _l("delete_successfully")]);die();
           }else{
              echo json_encode(['error' => 'Mail Conversation has not delete']);die();
           }
        }
    }
	
	
    /**
     * Assign tickets
     *
     * @return redirect
     */
    public function conversationTicket(){

        if ($this->input->post()) {
            $data = $this->input->post();
            $this->load->model('mailbox_model');
            $ticketData = $this->mailbox_model->conversationTicket($data);
            if ($ticketData) {
                
                set_alert('success', _l('ticket_assign_successfully'));
                redirect(admin_url('mailbox/outbox/'.$data['outbox_id']));
            }
        }
    }

public function conversationTicket_inbox(){

    if ($this->input->post()) {
        $data = $this->input->post();
        $inbox_id = $data['inbox_id'];
        $mailsubject = $data['subject'];
        
        $this->load->model('mailbox_model');
        $ticketData = $this->mailbox_model->conversationTicket_inbox($data, $mailsubject); // Pass $mailsubject to the model method
        if ($ticketData) {
            set_alert('success', _l('ticket_assign_successfully'));
            redirect(admin_url('mailbox/inbox/'.$data['inbox_id']));
        }
    }
}
	

}
