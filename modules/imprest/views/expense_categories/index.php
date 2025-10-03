<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    /* General Styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 900px;
        margin: auto;
        padding: 20px;
    }

    .page-heading {
        text-align: center;
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #333;
    }

    /* Category Card */
    .categories-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .category-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        width: calc(50% - 10px); /* Two cards per row */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .category-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .category-header h3 {
        font-size: 18px;
        font-weight: bold;
        margin: 0;
        color: #007bff;
    }

    .add-button {
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        font-size: 18px;
        line-height: 30px;
        text-align: center;
        transition: background-color 0.3s ease;
    }

    .add-button:hover {
        background-color: #0056b3;
    }

    /* Subcategory List */
    .subcategory-list {
        list-style: none;
        padding: 0;
        margin: 0;
        font-size: 14px;
        color: #555;
    }

    .subcategory-list li {
        margin-bottom: 8px;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .create-category-button {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .create-category-button:hover {
        background-color: #0056b3;
    }

    .subcategory-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
        padding: 5px;
        border-bottom: 1px solid #ddd;
    }

    .delete-button {
        background: none;
        border: none;
        color: #ff4d4d;
        font-size: 16px;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .delete-button:hover {
        color: #e60000;
    }

    .delete-button i {
        pointer-events: none; /* Ensures only the button triggers the action */
    }

    /* Modal Styling */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: white;
        margin: 15% auto;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 500px;
    }

    .close-button {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        position: absolute;
        right: 20px;
        top: 10px;
        cursor: pointer;
    }

    .close-button:hover,
    .close-button:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    h2 {
        text-align: center;
    }

    label {
        display: block;
        margin: 10px 0;
    }

    input[type="text"] {
        width: 100%;
        padding: 10px;
        margin: 5px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .add-subcategory-btn, .submit-category-btn {
        background-color: #4CAF50;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .add-subcategory-btn:hover, .submit-category-btn:hover {
        background-color: #45a049;
    }

    .subcategory-input {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .remove-subcategory-btn {
        background-color: #ff4d4d;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        border-radius: 5px;
    }

    .remove-subcategory-btn:hover {
        background-color: #e60000;
    }

    .modal {
        z-index: 1050; /* Bootstrap default */
    }

    .modal-backdrop {
        z-index: 1040; /* Ensure it's lower than the modal */
    }

</style>


<div id="wrapper">
    <div class="content">
        <div class="container">
            <div class="header">
                <button class="create-category-button" data-toggle="modal"
                        data-target="#add_category">Create Expense Category
                </button>
            </div>
            <div class="categories-container">
                <!-- Loop Through Categories -->
                <?php foreach ($groupedCategories as $categoryId => $category): ?>
                    <?php if ($category['name'] !== 'Additional Funds'): ?>
                        <div class="category-card">
                            <div class="category-header">
                                <h3><?php echo $category['name']; ?></h3>
                                <button id="<?php echo $categoryId; ?>"
                                        class="add-button"
                                        data-toggle="modal"
                                        data-target="#add_sub_category">+
                                </button>

                            </div>
                            <ul class="subcategory-list">
                                <?php if (!empty($category['subcategories'])): ?>
                                    <?php foreach ($category['subcategories'] as $subcategory): ?>
                                        <li>
                                            <?php echo $subcategory['name']; ?>

                                            <?php echo form_open('admin/imprest/expense_categories/delete_subcategory/' . $subcategory['id'], [
                                                'id' => 'delete-subcategory-form',
                                                'enctype' => 'multipart/form-data'
                                            ]); ?>
                                            <button type="submit" class="delete-button">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php echo form_close(); ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li>No subcategories available.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Add Category</h4>
            </div>
            <?php echo form_open('admin/imprest/expense_categories/store', [
                'id' => 'create-category-form',
                'enctype' => 'multipart/form-data'
            ]); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">

                        <label for="category_name">Category Name</label>
                        <input type="text" name="name" id="name" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('Add Category'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!--add subcategory-->
<div class="modal fade" id="add_sub_category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Add Sub Category</h4>
            </div>
            <?php echo form_open('admin/imprest/expense_categories/add_subcategory', [
                'id' => 'create-subcategory-form',
                'enctype' => 'multipart/form-data'
            ]); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">

                        <label for="subcategory_name" class="form-label">Sub Category Name</label>
                        <input type="text" id="subcategory_name" name="subcategory_name" class="form-control"
                               placeholder="Enter category name" required>
                        <input type="hidden" id="updated_category_id" name="updated_category_id" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('Add Sub Category'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('add_sub_category');
        const subCategoryNameInput = modal.querySelector('#subcategory_name');
        const categoryIdInput = modal.querySelector('#updated_category_id');

        document.querySelectorAll('.add-button').forEach(button => {
            button.addEventListener('click', function () {
                categoryIdInput.value = this.getAttribute('id');
                subCategoryNameInput.value = ''; // Clear the input field
            });
        });
    });
</script>
