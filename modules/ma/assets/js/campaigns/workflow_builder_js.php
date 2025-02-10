<script type="text/javascript">
    var timer = null;
	var id = document.getElementById("drawflow");
    const editor = new Drawflow(id);
    (function($) {
      "use strict";

    editor.reroute = true;
    editor.start();
    <?php if($campaign->workflow != ''){ ?> 
    const dataToImport = <?php echo json_decode($campaign->workflow); ?>;
    editor.import(dataToImport);
    <?php } ?>
    <?php if(!isset($is_edit)){ ?>
        editor.editor_mode='fixed';
    <?php } ?>

    var elements = document.getElementsByClassName('drag-drawflow');
    for (var i = 0; i < elements.length; i++) {
      elements[i].addEventListener('touchend', drop, false);
      elements[i].addEventListener('touchmove', positionMobile, false);
      elements[i].addEventListener('touchstart', drag, false );
    }

    var mobile_item_selec = '';
    var mobile_last_move = null;

    $(document).on("keyup", "input[type=text]", function() { 
        clearTimeout(timer); 
        timer = setTimeout(workflow_input_change, 1000, $(this));
    });

    $(document).on("keyup", "input[type=number]", function() { 
        clearTimeout(timer); 
        timer = setTimeout(workflow_input_change, 1000, $(this));
    });

    $(document).on("change", "input.datetimepicker", function() { 
        clearTimeout(timer); 
        timer = setTimeout(workflow_input_change, 1000, $(this));
    });

    $(document).on("change", "input[type=time]", function() { 
        clearTimeout(timer); 
        timer = setTimeout(workflow_input_change, 1000, $(this));
    });

    $(document).on("change", "input[type=radio][name^=complete_action]", function() { 
        var parent = $(this).parents('.box');
        if(this.checked === true){
            parent.find('.div_complete_action_after').addClass('hide');
            parent.find('.div_complete_action_exact_time').addClass('hide');
            parent.find('.div_complete_action_exact_time_and_date').addClass('hide');

            parent.find('.div_complete_action_'+this.value).removeClass('hide');
        }
    });

    $(document).on("change", "select[name^=action]", function() {
        var parent = $(this).parents('.box');
        parent.find('.div_action_change_segments').addClass('hide');
        parent.find('.div_action_change_stages').addClass('hide');
        parent.find('.div_action_change_points').addClass('hide');
        parent.find('.div_action_point_action').addClass('hide');
        parent.find('.div_action_lead_status').addClass('hide');
        parent.find('.div_action_add_tags').addClass('hide');

        if (this.value == 'change_segments') {
            parent.find('.div_action_change_segments').removeClass('hide');
        }else if (this.value == 'change_stages') {
            parent.find('.div_action_change_stages').removeClass('hide');
        }else if (this.value == 'change_points') {
            parent.find('.div_action_change_points').removeClass('hide');
        }else if (this.value == 'point_action') {
            parent.find('.div_action_point_action').removeClass('hide');
        }else if (this.value == 'change_lead_status') {
            parent.find('.div_action_lead_status').removeClass('hide');
        }else if (this.value == 'add_tags') {
            parent.find('.div_action_add_tags').removeClass('hide');
        }
    });

    $(document).on("change", "input[type=radio][name^=lead_data_from]", function() { 
        var parent = $(this).parents('.box');
        if(this.checked === true){
            parent.find('.div_lead_data_from_segment').addClass('hide');
            parent.find('.div_lead_data_from_form').addClass('hide');

            parent.find('.div_lead_data_from_'+this.value).removeClass('hide');
        }

    });

    $(document).on("change", "input[type=radio][name^=client_data_from]", function() { 
        var parent = $(this).parents('.box');
        if(this.checked === true){
            parent.find('.div_client_data_from_segment').addClass('hide');
            parent.find('.div_client_data_from_group').addClass('hide');

            parent.find('.div_client_data_from_'+this.value).removeClass('hide');
        }

    });

    $(document).on("change", "input[type=radio][name^=data_type]", function() { 
        var parent = $(this).parents('.box');

        if(this.checked === true){
            parent.find('.div_data_type_lead').addClass('hide');
            parent.find('.div_data_type_customer').addClass('hide');

            parent.find('.div_data_type_'+this.value).removeClass('hide');
        }

    });

    $(document).on("change", "input[type=radio][name^=filter_type]", function() { 
        var parent = $(this).parents('.box');

        if(this.checked === true){
            parent.find('.div_filter_type_lead').addClass('hide');
            parent.find('.div_filter_type_customer').addClass('hide');

            parent.find('.div_filter_type_'+this.value).removeClass('hide');
        }

    });

    $(document).on("change", "input[type=radio][name^=action_segment_type]", function() { 
        var parent = $(this).parents('.box');

        if(this.checked === true){
            parent.find('.div_action_segment_type_lead').addClass('hide');
            parent.find('.div_action_segment_type_customer').addClass('hide');

            parent.find('.div_action_segment_type_'+this.value).removeClass('hide');
        }

    });


    $(document).on("change", "select[name^=action]", function() {
        var parent = $(this).parents('.box');
        parent.find('.div_action_change_segments').addClass('hide');
        parent.find('.div_action_change_stages').addClass('hide');
        parent.find('.div_action_change_points').addClass('hide');
        parent.find('.div_action_point_action').addClass('hide');
        parent.find('.div_action_lead_status').addClass('hide');
        parent.find('.div_action_add_tags').addClass('hide');

        if (this.value == 'change_segments') {
            parent.find('.div_action_change_segments').removeClass('hide');
        }else if (this.value == 'change_stages') {
            parent.find('.div_action_change_stages').removeClass('hide');
        }else if (this.value == 'change_points') {
            parent.find('.div_action_change_points').removeClass('hide');
        }else if (this.value == 'point_action') {
            parent.find('.div_action_point_action').removeClass('hide');
        }else if (this.value == 'change_lead_status') {
            parent.find('.div_action_lead_status').removeClass('hide');
        }else if (this.value == 'add_tags') {
            parent.find('.div_action_add_tags').removeClass('hide');
        }
    });

    $(document).on("change", "select", function() {
        var parent = $(this).parents('.box');
        var nodeId = parent.attr('node-id');
        var node = editor.getNodeFromId(nodeId);
        var data_node = node.data;
        var select_name = this.name.split("[");
        data_node[select_name[0]] = this.value;
        editor.updateNodeDataFromId(nodeId, data_node);
    });

    $(document).on("change", "input[id=tags]", function() {
        var parent = $(this).parents('.box');
        var nodeId = parent.attr('node-id');
        var node = editor.getNodeFromId(nodeId);
        var data_node = node.data;
        var select_name = this.name.split("[");
        data_node[select_name[0]] = this.value;
        editor.updateNodeDataFromId(nodeId, data_node);
    });

    $('input[type="time"]').datetimepicker({
        datepicker: false,
        format: 'H:i'
    });

    $( document ).ready(function() {
        $('input[type=radio][name^=data_type]').change();
        $('input[type=radio][name^=filter_type]').change();
        $('input[type=radio][name^=action_segment_type]').change();
        $('input[type=radio][name^=lead_data_from]').change();
        $('input[type=radio][name^=client_data_from]').change();
        $('input[type=radio][name^=complete_action]').change();
        $('select[name^=action]').change();
    });

    appValidateForm($('#workflow-form'), {
    },workflow_form_handler);

    })(jQuery);


   function positionMobile(ev) {
      "use strict";
     mobile_last_move = ev;
   }

   function allowDrop(ev) {
      "use strict";
      ev.preventDefault();
    }

    function drag(ev) {
      "use strict";
      if (ev.type === "touchstart") {
        mobile_item_selec = ev.target.closest(".drag-drawflow").getAttribute('data-node');
      } else {
      ev.dataTransfer.setData("node", ev.target.getAttribute('data-node'));
      }
    }

    function drop(ev) {
      "use strict";
      if (ev.type === "touchend") {
        var parentdrawflow = document.elementFromPoint( mobile_last_move.touches[0].clientX, mobile_last_move.touches[0].clientY).closest("#drawflow");
        if(parentdrawflow != null) {
          addNodeToDrawFlow(mobile_item_selec, mobile_last_move.touches[0].clientX, mobile_last_move.touches[0].clientY);
        }
        mobile_item_selec = '';
      } else {
        ev.preventDefault();
        var data = ev.dataTransfer.getData("node");
        addNodeToDrawFlow(data, ev.clientX, ev.clientY);
      }

    }

    function addNodeToDrawFlow(name, pos_x, pos_y) {
      "use strict";
      if(editor.editor_mode === 'fixed') {
        return false;
      }
      pos_x = pos_x * ( editor.precanvas.clientWidth / (editor.precanvas.clientWidth * editor.zoom)) - (editor.precanvas.getBoundingClientRect().x * ( editor.precanvas.clientWidth / (editor.precanvas.clientWidth * editor.zoom)));
      pos_y = pos_y * ( editor.precanvas.clientHeight / (editor.precanvas.clientHeight * editor.zoom)) - (editor.precanvas.getBoundingClientRect().y * ( editor.precanvas.clientHeight / (editor.precanvas.clientHeight * editor.zoom)));


      switch (name) {
        case 'flow_start':
            $.post(admin_url + 'ma/get_workflow_node_html', {
                type: 'flow_start',
                nodeId: editor.nodeId,
                csrf_token_name: $('input[name=csrf_token_hash]').val()
            }).done(function (html) {
                editor.addNode('flow_start', 0,  1, pos_x, pos_y, 'flow_start', {}, html );
                
                init_selectpicker();
            });

          break;
        case 'condition':
            $.post(admin_url + 'ma/get_workflow_node_html', {
                type: 'condition',
                nodeId: editor.nodeId,
                csrf_token_name: $('input[name=csrf_token_hash]').val()
            }).done(function (html) {
                editor.addNode('condition', 1, 2, pos_x, pos_y, 'condition', {}, html );
                
                init_selectpicker();
            });

          break;
        case 'action':

            $.post(admin_url + 'ma/get_workflow_node_html', {
                type: 'action',
                nodeId: editor.nodeId,
                csrf_token_name: $('input[name=csrf_token_hash]').val()
            }).done(function (html) {

                editor.addNode('action', 1, 1, pos_x, pos_y, 'action', {}, html );
                
                init_selectpicker();
                init_tags_inputs();
            });

          break;
          case 'email':
            $.post(admin_url + 'ma/get_workflow_node_html', {
                type: 'email',
                nodeId: editor.nodeId,
                csrf_token_name: $('input[name=csrf_token_hash]').val()
            }).done(function (html) {
                editor.addNode('email', 1, 1, pos_x, pos_y, 'email', {}, html );
                
                init_selectpicker();
                init_datepicker();
                $('input[type="time"]').datetimepicker({
                    datepicker: false,
                    format: 'H:i'
                });
            });

            break;
            case 'sms':
            $.post(admin_url + 'ma/get_workflow_node_html', {
                type: 'sms',
                nodeId: editor.nodeId,
                csrf_token_name: $('input[name=csrf_token_hash]').val()
            }).done(function (html) {
                editor.addNode('sms', 1, 1, pos_x, pos_y, 'sms', {}, html );

                init_selectpicker();
                init_datepicker();
                $('input[type="time"]').datetimepicker({
                    datepicker: false,
                    format: 'H:i'
                });
            });



            break;

          case 'filter':
            $.post(admin_url + 'ma/get_workflow_node_html', {
                type: 'filter',
                nodeId: editor.nodeId,
                csrf_token_name: $('input[name=csrf_token_hash]').val()
            }).done(function (html) {
                editor.addNode('filter', 1, 2, pos_x, pos_y, 'filter', {}, html );
                
                init_selectpicker();
                init_datepicker();
            });
            break;
        case 'telegram':
          var telegrambot = `
          <div>
            <div class="title-box"><i class="fab fa-telegram-plane"></i> Telegram bot</div>
            <div class="box">
              <p>Send to telegram</p>
              <p>select channel</p>
              <select df-channel>
                <option value="channel_1">Channel 1</option>
                <option value="channel_2">Channel 2</option>
                <option value="channel_3">Channel 3</option>
                <option value="channel_4">Channel 4</option>
              </select>
            </div>
          </div>
          `;
          editor.addNode('telegram', 1, 0, pos_x, pos_y, 'telegram', {}, telegrambot );
          break;

        default:
      }

        

    }

  var transform = '';
  function showpopup(e) {
      "use strict";
    e.target.closest(".drawflow-node").style.zIndex = "9999";
    e.target.children[0].style.display = "block";

    transform = editor.precanvas.style.transform;
    editor.precanvas.style.transform = '';
    editor.precanvas.style.left = editor.canvas_x +'px';
    editor.precanvas.style.top = editor.canvas_y +'px';

    editor.editor_mode = "fixed";

  }

   function closemodal(e) {
      "use strict";
     e.target.closest(".drawflow-node").style.zIndex = "2";
     e.target.parentElement.parentElement.style.display  ="none";
     editor.precanvas.style.transform = transform;
       editor.precanvas.style.left = '0px';
       editor.precanvas.style.top = '0px';
      editor.editor_mode = "edit";
   }

    function changeModule(event) {
      "use strict";
      var all = document.querySelectorAll(".menu ul li");
        for (var i = 0; i < all.length; i++) {
          all[i].classList.remove('selected');
        }
      event.target.classList.add('selected');
    }

    function changeMode(option) {
      "use strict";

      if(option == 'lock') {
        lock.style.display = 'none';
        unlock.style.display = 'block';
      } else {
        lock.style.display = 'block';
        unlock.style.display = 'none';
      }

    }

    function save_workflow() {
      "use strict";
        $('input[name=workflow]').val('');
        $('#workflow-form').submit();
    }

    function builder() {
      "use strict";
        window.location.assign(admin_url + 'ma/workflow_builder/<?php echo html_entity_decode($campaign->id); ?>');
    }

    function workflow_input_change(input) {
      "use strict";
        var value = input.val();

        input.attr('value',value);

        var parent = input.parents('.box');
        var nodeId = parent.attr('node-id');
        var node = editor.getNodeFromId(nodeId);
        var data_node = node.data;
        var select_name = input.attr('name').split("[");
        if(select_name != ''){
            data_node[select_name[0]] = value;
            editor.updateNodeDataFromId(nodeId, data_node);
        }
    }


    function workflow_form_handler(form) {
        "use strict";

        var formURL = form.action;
        var formData = new FormData($(form)[0]);
        formData.append(csrfData.token_name, csrfData.hash);

        var workflow = new File([JSON.stringify(editor.export())], "workflow.txt", { type: "text/plain" });
        formData.append("workflow", workflow);

        $.ajax({
            type: $(form).attr('method'),
            data: formData,
            mimeType: $(form).attr('enctype'),
            contentType: false,
            cache: false,
            processData: false,
            url: formURL
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success === true || response.success == 'true' || $.isNumeric(response.success)) {
              alert_float('success', response.message);
              if(response.url){
                window.location.assign(response.url);
              }
            }else {
              alert_float('danger', response.message);
              $('#btn-submit').attr('disabled', false);
            }
        }).fail(function(error) {
            alert_float('danger', JSON.parse(error.mesage));
            $('#btn-submit').attr('disabled', false);
        });

        return false;
    }
</script>