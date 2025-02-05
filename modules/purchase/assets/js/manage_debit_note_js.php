<script>
   $(function(){
       var Debit_Notes_ServerParams = {};
     $.each($('._hidden_inputs._filters input'),function(){
       Debit_Notes_ServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
     });
     initDataTable('.table-debit-notes', admin_url+'purchase/debit_notes_table', ['undefined'], ['undefined'], Debit_Notes_ServerParams, [[1,'desc'], [0,'desc']]);
     init_debit_note();
  });

   // Init single credit note
  function init_debit_note(id) {
      load_small_table_item(id, '#debit_note', 'debit_note_id', 'purchase/get_debit_note_data_ajax', '.table-debit-notes');
  }
</script>