<script>
    "use strict";
    (() => {
        let subtotal = 0;
        const subtotalElement = document.querySelector('<?= $subtotal_selector; ?>');
        const addButtons = document.querySelectorAll('#<?= $modal_id; ?> .add-item');
        const removeButtons = document.querySelectorAll('#<?= $modal_id; ?> .remove-item');
        const paidItems = document.querySelector("<?= $paid_id_selector; ?>");

        addButtons.forEach((button, index) => {
            button.addEventListener('click', (e) => {
                if (button.dataset.confirm && !e?.detail?.skipConfirm) {
                    if (!confirm(button.dataset.confirm))
                        return false;
                }

                const billingMode = button.dataset?.billingMode;
                const oneTimeTag =
                    '<span data-toggle="tooltip" data-title="<?= perfex_saas_ecape_js_attr(_l('perfex_saas_service_one_time_payment_hint')); ?>" class="badge bg-warning tw-px-1"><?= perfex_saas_ecape_js_attr(_l('perfex_saas_service_one_time_payment')); ?></span>';
                let invoiceData = button.dataset.invoiceLink;
                invoiceData = invoiceData?.length ? atob(invoiceData) : invoiceData;
                if (!invoiceData?.length && billingMode == 'lifetime') {
                    invoiceData = oneTimeTag;
                }

                const originalPrice = parseFloat(button.dataset.price);
                const price = billingMode == 'lifetime' ? 0 : getDiscountedUnitPrice(button.dataset.key,
                    originalPrice, 1);
                subtotal += price;
                subtotalElement.textContent = `${appFormatMoney.format(subtotal)}`;
                subtotalElement.dataset.marketGroupTotal = subtotal;

                setTotalAmount();

                button.style.display = 'none';
                removeButtons[index].style.display = 'inline-block';

                paidItems.insertAdjacentHTML('beforebegin', `
                <tr data-key="${button.dataset.key}">
                    <td class="!tw-max-w-xs">${button.dataset.name}</td>
                    <td><?= $na; ?>
                        <input value="${button.dataset.key}" name="<?= $items_input_name; ?>" type="hidden" data-unit-price="${button.dataset.price}" data-quantity="1" class="feature-limit" />
                    </td>
                    <td><?= $na; ?></td>
                    <td>${button.dataset.priceFormatted}</td>
                    <td><span class="price-addition" data-price="${button.dataset.price}">${button.dataset.priceFormatted} ${invoiceData}</span></td>
                </tr>
            `);
            });
        });

        removeButtons.forEach((button, index) => {
            button.addEventListener('click', () => {
                const originalPrice = parseFloat(button.dataset.price);
                const billingMode = button.dataset?.billingMode;
                const price = billingMode == 'lifetime' ? 0 : getDiscountedUnitPrice(button.dataset.key,
                    originalPrice, 1);

                subtotal -= price;
                subtotalElement.textContent = `${appFormatMoney.format(subtotal)}`;
                subtotalElement.dataset.marketGroupTotal = subtotal;

                setTotalAmount();

                button.style.display = 'none';
                addButtons[index].style.display = 'inline-block';
                document.querySelector(`tr[data-key="${button.dataset.key}"]`).remove();
            });
        });

        const purchasedItems = <?= json_encode((array)$purchased_items); ?>;
        // Trigger summation of services using js
        if (purchasedItems.length)
            document.querySelectorAll("button.add-<?= $modal_type; ?>-" + purchasedItems.join(
                ", button.add-<?= $modal_type; ?>-")).forEach((
                    button) =>
                button.dispatchEvent(
                    new CustomEvent('click', {
                        bubbles: true,
                        detail: {
                            skipConfirm: true,
                        },
                    })));
    })();
</script>