<script>
    $('body').addClass('no-calculate-total');
    init_currency(<?php echo $debit_note->currencyid; ?>);
    $(function(){
        appValidateForm('#apply_debits_form');

        $('body').on('change blur', '.apply-debits-to-invoice .apply-debits-field', function () {
            var $applyDebits = $('#apply_debits');
            var $amountInputs = $applyDebits.find('input.apply-debits-field');
            var total = 0;
            var debitsRemaining = $applyDebits.attr('data-debits-remaining');

            $.each($amountInputs, function () {
                if ($(this).valid() === true) {
                    var amount = $(this).val();
                    amount = parseFloat(amount);
                    if (!isNaN(amount)) {
                        total += amount;
                    } else {
                        $(this).val(0);
                    }
                }
            });

            $applyDebits.find('#debits-alert').remove();
            $applyDebits.find('.amount-to-debit').html(format_money(total));
            if (debitsRemaining < total) {
                $('.debits-table').before($('<div/>', {
                    id: 'debits-alert',
                    class: 'alert alert-danger',
                }).html("<?php echo _l('debit_amount_bigger_then_debit_note_remaining_debits'); ?>"));
                $applyDebits.find('[type="submit"]').prop('disabled', true);
            } else {
                $applyDebits.find('.debit-note-balance-due').html(format_money(debitsRemaining - total));
                $applyDebits.find('[type="submit"]').prop('disabled', false);
            }
        });
    });
</script>