<?php
gp_title( __( 'Export Project &lt; GlotPress' ) );
gp_breadcrumb( array(
	__('Export Project'),
) );
gp_tmpl_header();
?>
<h2><?php _e( 'Export Project' ); ?></h2>
<form action="" method="post">
    <input type="hidden" name="project_id" value="<?php echo $project->id; ?>" />
    <p>
        <label>
            <?php _e('Available languages'); ?>
        </label>
        <select name="export_locale">
            <?php foreach($locales as $locale) { ?>
                <option value="<?php echo $locale['locale']; ?>"><?php echo $locale['name']." - (".$locale['percent']."%)"; ?></option>
            <?php }; ?>
        </select>
    </p>
	<p>
		<input type="submit" name="submit" value="<?php echo esc_attr( __('Export') ); ?>" id="submit" />
		<span class="or-cancel">or <a href="javascript:history.back();">Cancel</a></span>
	</p>
</form>
<?php gp_tmpl_footer();