<?php
defined('BASEPATH') or exit('No direct script access allowed');

trait Affiliate_management_common_trait
{
    public function _update_affiliate_slug($affiliate)
    {
        $slug = slug_it($this->input->post('affiliate_slug', true));

        $status = 'danger';
        $message = _l('affiliate_management_error_updating', _l('affiliate_management_slug'));

        $can_update_affiliate_id = $affiliate && AffiliateManagementHelper::can_update_affiliate_id($affiliate);
        if ($can_update_affiliate_id) {

            if ($this->affiliate_management_model->update_affiliate($affiliate->affiliate_id, ['affiliate_slug' => $slug])) {
                $status = 'success';
                $message =  _l('updated_successfully', _l('affiliate_management_slug'));
                $affiliate = $this->affiliate_management_model->get_affiliate($affiliate->affiliate_id);
            }
        }


        $slug = $affiliate->affiliate_slug;

        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => $status, 'message' => $message, 'slug' => $slug]);
            exit;
        }

        set_alert($status, $message);

        return redirect($this->redirect_url);
    }
}