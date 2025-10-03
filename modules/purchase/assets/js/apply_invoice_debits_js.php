<script>
    $('body').addClass('no-calculate-total');
    init_currency(<?php echo $base_currency->id; ?>);
    $(function(){
        appValidateForm('#apply_debits_form');
    });

    $('body').on('change blur', '.apply-debits-from-invoice .apply-debits-field', function () {

        var $applyCredits = $('#apply_debits');
        var $amountInputs = $applyCredits.find('input.apply-debits-field');
        var total = 0;
        var invoiceBalanceDue = $applyCredits.attr('data-balance-due');

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

        $applyCredits.find('#debits-alert').remove();
        $applyCredits.find('.amount-to-debit').html(format_money(total));
        if (total > invoiceBalanceDue) {
            $('.debits-table').before($('<div/>', {
                id: 'debits-alert',
                class: 'alert alert-danger',
            }).html(app.lang.credit_amount_bigger_then_invoice_balance));
            $applyCredits.find('[type="submit"]').prop('disabled', true);
        } else {
            $applyCredits.find('.invoice-balance-due').html(format_money(invoiceBalanceDue - total));
            $applyCredits.find('[type="submit"]').prop('disabled', false);
        }
    });
</script>