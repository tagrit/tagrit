<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

<form class="container mt-5">
    <div class="form-group">
        <label>To continue with this request, please select one of the following options:</label>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="uninstall_option" id="option1" value="uninstall_no_data" required>
            <label class="form-check-label" for="option1">
                Uninstall without removing any data
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="uninstall_option" id="option2" value="uninstall_with_data" required>
            <label class="form-check-label" for="option2">
                Uninstall with data
            </label>
        </div>
    </div>

    <div id="confirmation" style="display: none;">
        <div class="form-group">
            <label for="confirm_text">Confirm by typing the following text (case sensitive):</label>
            <h2><?= $confirm_text ?></h2>
            <input type="text" class="form-control" id="confirm_text" name="confirm_text">
        </div>
    </div>

    <button type="submit" class="btn btn-danger">Continue</button>
</form>

<script>
    "use strict";

    document.addEventListener("DOMContentLoaded", function() {
        let uninstallOptions = document.querySelectorAll('input[name="uninstall_option"]');
        let confirmationSection = document.getElementById("confirmation");

        for (let i = 0; i < uninstallOptions.length; i++) {
            uninstallOptions[i].addEventListener("change", function() {
                if (this.value === "uninstall_with_data") {
                    confirmationSection.style.display = "block";
                } else {
                    confirmationSection.style.display = "none";
                }
            });
        }

        let form = document.querySelector("form");
        form.addEventListener("submit", function(event) {
            let selectedOption = document.querySelector('input[name="uninstall_option"]:checked');
            if (selectedOption && selectedOption.value === "uninstall_with_data") {
                let confirmText = document.getElementById("confirm_text").value;
                if (confirmText !== "<?= $confirm_text ?>") {
                    alert("Text does not match. Kindly enter the text as it appears.");
                    event.preventDefault();
                }
            }
        });
    });
</script>