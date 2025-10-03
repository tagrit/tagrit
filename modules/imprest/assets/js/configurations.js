$(document).ready(function() {
  let editingItemIndex = null; // Stores the index of the item being edited

  // Submit form for adding or editing imprest item
  $("#addImprestItemForm").submit(function(event) {
    event.preventDefault(); // Prevent default form submission

    const imprestItemName = $("#imprestItemName").val();

    if (editingItemIndex !== null) {
      // Update existing item
      updateImprestItem(editingItemIndex, imprestItemName);
    } else {
      // Add new item
      addImprestItem(imprestItemName);
    }

    $("#addImprestItemModal").modal('hide'); // Close modal after submission
    editingItemIndex = null; // Reset editing index
  });

  // Function to add a new imprest item
  function addImprestItem(name) {
    const newRow = `<tr data-index="${getImprestItemList().length}">
      <td>${getImprestItemList().length + 1}</td>
      <td>${name}</td>
      <td>Active</td>
      <td>
        <button class="btn btn-sm btn-warning edit-btn" data-index="${getImprestItemList().length}">Edit</button>
        <button class="btn btn-sm btn-danger delete-btn" data-index="${getImprestItemList().length}">Delete</button>
      </td>
    </tr>`;

    $("#imprestItemList").append(newRow);

    // Make an AJAX request to save the item
    $.ajax({
      url: '<?= site_url("imprest_management/add_imprest_item"); ?>', // Replace with your actual endpoint
      method: 'POST',
      data: { item_name: name },
      success: function(response) {
        const result = JSON.parse(response);
        if (result.success) {
          // Handle success (e.g., update UI, refresh list, etc.)
          console.log('Item added successfully!');
        } else {
          console.error('Error adding item:', result.error);
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX request failed:', error);
      }
    });
  }

  // Function to update an existing imprest item
  function updateImprestItem(index, name) {
    const existingRow = $("#imprestItemList").find(`tr[data-index="${index}"]`);
    existingRow.find("td:nth-child(2)").text(name); // Update name cell
  }

  // Function to get the current list of imprest items (assuming it's an array)
  function getImprestItemList() {
    // Replace this with your actual logic to retrieve the imprest item list
    // You can use localStorage or a server-side API to store and retrieve data
    return [];
  }

  // Attach click events for edit and delete buttons
  $("#imprestItemList").on("click", ".edit-btn", function() {
    const index = $(this).data("index");
    editingItemIndex = index;

    const itemName = $(this).closest("tr").find("td:nth-child(2)").text();
    $("#imprestItemName").val(itemName); // Set input value for editing

    $("#addImprestItemModal").modal('show'); // Open modal for editing
  });

  $("#imprestItemList").on("click", ".delete-btn", function() {
    const index = $(this).data("index");
    // Implement logic to remove the item from the list (e.g., update the array)
    $("#imprestItemList").find(`tr[data-index="${index}"]`).remove();
  });
});


