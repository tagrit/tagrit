document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('addPackage').addEventListener('click', function () {
        var packageTable = document.getElementById('packageTable').getElementsByTagName('tbody')[0];
        var newRow = packageTable.insertRow();

        newRow.innerHTML = `
            <td><?php echo form_input(['name' => 'amount[]', 'class' => 'form-control']); ?></td>
            <td><?php echo form_input(['name' => 'package_description[]', 'class' => 'form-control']); ?></td>
            <td><?php echo form_input(['name' => 'weight[]', 'class' => 'form-control']); ?></td>
            <td><?php echo form_input(['name' => 'length[]', 'class' => 'form-control']); ?></td>
            <td><?php echo form_input(['name' => 'width[]', 'class' => 'form-control']); ?></td>
            <td><?php echo form_input(['name' => 'height[]', 'class' => 'form-control']); ?></td>
            <td><?php echo form_input(['name' => 'weight_vol[]', 'class' => 'form-control']); ?></td>
            <td><?php echo form_input(['name' => 'declared_value[]', 'class' => 'form-control']); ?></td>
            <td><button type="button" class="btn btn-danger remove-package">Remove</button></td>
        `;

        attachRemoveEvent(newRow.getElementsByClassName('remove-package')[0]);
    });

    function attachRemoveEvent(button) {
        button.addEventListener('click', function () {
            this.closest('tr').remove();
        });
    }

    // Attach event to initial remove buttons
    var removeButtons = document.getElementsByClassName('remove-package');
    for (var i = 0; i < removeButtons.length; i++) {
        attachRemoveEvent(removeButtons[i]);
    }
});

