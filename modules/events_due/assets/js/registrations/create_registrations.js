document.addEventListener("DOMContentLoaded", function () {
    let delegateIndex = 1;

    let addDelegateButton = document.getElementById("add-delegate");
    let delegateContainer = document.getElementById("delegates-container");

    if (addDelegateButton && delegateContainer) {
        addDelegateButton.addEventListener("click", function () {
            let delegateEntry = document.createElement("div");
            delegateEntry.classList.add("row", "align-items-center", "delegate-entry");
            delegateEntry.innerHTML = `
                <div class="mtop4 col-md-3">
                    <input type="text" name="delegates[${delegateIndex}][first_name]" class="form-control" placeholder="First Name">
                </div>
                <div style="margin-left:-20px;" class="mtop4 col-md-3">
                    <input type="text" name="delegates[${delegateIndex}][last_name]" class="form-control" placeholder="Last Name">
                </div>
                <div style="margin-left:-20px;" class="mtop4 col-md-3">
                    <input type="email" name="delegates[${delegateIndex}][email]" class="form-control" placeholder="Email">
                </div>
                <div style="margin-left:-20px;" class="mtop4 col-md-3">
                    <input type="text" name="delegates[${delegateIndex}][phone]" class="form-control" placeholder="Phone">
                </div>
                <div class="mtop4 text-center">
                  <button style="margin-left: -20px; border: 0px; color: red; background-color: transparent;" type="button" class="remove-delegate">
                   <i class="fas fa-trash-alt" style="font-size: 1.5rem;"></i>
                 </button>
               </div>
            `;

            delegateContainer.appendChild(delegateEntry);
            delegateIndex++;

            delegateEntry.querySelector(".remove-delegate").addEventListener("click", function () {
                delegateEntry.remove();
            });
        });

        document.querySelectorAll(".remove-delegate").forEach(button => {
            button.addEventListener("click", function () {
                this.closest(".delegate-entry").remove();
            });
        });
    }

});
