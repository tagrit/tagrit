<?php

class switchclass
{
    function switch_html($status, $name, $id, $input_attr = '')
    {
        $checked = $status == 1 ? "checked" : "";
        return '
        <div class="fs-permisstion-switch">
        <div class="form-group">
        <div class="checkbox checbox-switch switch-primary">
        <label class="swith-label">
        <input data-id="'.$id.'" type="checkbox" name="'.$name.'"  '.$checked.' '.$input_attr.'/>
        <span></span>
        </label>
        </div>
        </div>
        </div>';
    }
}