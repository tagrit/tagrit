<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?= form_open($this->uri->uri_string(), ['id' => 'article-form']); ?>
        <div class="tw-max-w-4xl tw-mx-auto">
            <div class="tw-flex tw-justify-between tw-mb-2">
                <div>
                    <h4 class="tw-my-0 tw-text-lg tw-font-bold tw-text-neutral-700">
                        <?= e($title); ?>
                    </h4>
                    <?php if (isset($article)) { ?>
                    <small>
                        <?php if ($article->staff_article == 1) { ?>
                        <a href="<?= admin_url('knowledge_base/view/' . $article->slug); ?>"
                            target="_blank"><?= admin_url('knowledge_base/view/' . $article->slug); ?></a>
                        <?php } else { ?>
                        <a href="<?= site_url('knowledge-base/article/' . $article->slug); ?>"
                            target="_blank"><?= site_url('knowledge-base/article/' . $article->slug); ?></a>
                        <?php } ?>
                    </small>
                    <br />
                    <small>
                        <span
                            class="tw-font-medium"><?= _l('article_total_views'); ?>:</span>
                        <?= total_rows(db_prefix() . 'views_tracking', ['rel_type' => 'kb_article', 'rel_id' => $article->articleid]);
                        ?>
                    </small>
                    <?php } ?>
                </div>
                <?php if (isset($article)) { ?>
                <div class="tw-self-start tw-space-x-1">
                    <?php if (staff_can('create', 'knowledge_base')) { ?>
                    <a href="<?= admin_url('knowledge_base/article'); ?>"
                        class="btn btn-primary"><?= _l('kb_article_new_article'); ?></a>
                    <?php } ?>
                    <?php if (staff_can('delete', 'knowledge_base')) { ?>
                    <a href="<?= admin_url('knowledge_base/delete_article/' . $article->articleid); ?>"
                        class="btn btn-default _delete">
                        <i class="fa-regular fa-trash-can"></i>
                    </a>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>

            <div class="panel_s">
                <div class="panel-body">
                    <?php $value = (isset($article) ? $article->subject : ''); ?>
                    <?php $attrs = (isset($article) ? [] : ['autofocus' => true]); ?>
                    <?= render_input('subject', 'kb_article_add_edit_subject', $value, 'text', $attrs); ?>
                    <?php if (isset($article)) {
                        echo render_input('slug', 'kb_article_slug', $article->slug, 'text');
                    } ?>
                    <?php $value = (isset($article) ? $article->articlegroup : ''); ?>
                    <?php if (staff_can('create', 'knowledge_base')) {
                        echo render_select_with_input_group('articlegroup', get_kb_groups(), ['groupid', 'name'], 'kb_article_add_edit_group', $value, '<div class="input-group-btn"><a href="#" class="btn btn-default" onclick="new_kb_group();return false;"><i class="fa fa-plus"></i></a></div>');
                    } else {
                        echo render_select('articlegroup', get_kb_groups(), ['groupid', 'name'], 'kb_article_add_edit_group', $value);
                    }
?>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" id="staff_article" name="staff_article" <?php if (isset($article) && $article->staff_article == 1) {
                            echo 'checked';
                        } ?>>
                        <label
                            for="staff_article"><?= _l('internal_article'); ?></label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" id="disabled" name="disabled" <?php if (isset($article) && $article->active_article == 0) {
                            echo 'checked';
                        } ?>>
                        <label
                            for="disabled"><?= _l('kb_article_disabled'); ?></label>
                    </div>
                    <p class="bold">
                        <?= _l('kb_article_description'); ?>
                    </p>
                    <?php $contents = '';
if (isset($article)) {
    $contents = $article->description;
} ?>
                    <?= render_textarea('description', '', $contents, [], [], '', 'tinymce tinymce-manual'); ?>
                </div>
                <?php if ((staff_can('create', 'knowledge_base') && ! isset($article)) || staff_can('edit', 'knowledge_base') && isset($article)) { ?>
                <div class="panel-footer text-right">
                    <button type="submit" class="btn btn-primary">
                        <?= _l('submit'); ?>
                    </button>
                </div>
                <?php } ?>
            </div>
        </div>

    </div>
    <?= form_close(); ?>
</div>
<?php $this->load->view('admin/knowledge_base/group'); ?>
<?php init_tail(); ?>
<script>
    $(function() {
        init_editor('#description', {
            toolbar_sticky: true,
        });

        appValidateForm($('#article-form'), {
            subject: 'required',
            articlegroup: 'required'
        });
    });
</script>
</body>

</html>