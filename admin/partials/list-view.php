<div class="wrap">
	<h1 class="wp-heading-inline"><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<a href="?page=easy-shortcode-edit" class="page-title-action">Add New</a>
	<hr class="wp-header-end">
	<form action="" method="post" enctype="multipart/form-data" name="esg_list_form" id="esg_list_form" class="esg_list_form">
		<?php
			$table->prepare_items();
			$table->search_box( 'search', 'search_id' );
		?>
		<input type="checkbox" id="detete-checkbox" />
		<div class="delete confirmation">
			<div class="inner">
				<span class="close-icon dashicons dashicons-no-alt"></span>
				<h3>Are you sure?</h3>
				<p>Do you really want to delete these records? This process cannot be undone.</p>
				<div class="button-wrapper">
					<a href="?page=easy-shortcode" id="esg-pop-delete" class="esg-pop-delete" title="Delete">Delete</a>
					<a href="javascript:void(0);" id="esg-pop-cancel" class="esg-pop-cancel"><label for="detete-checkbox" title="Cancel">Cancel</label></a>
				</div>
			</div>
		</div>
		<?php
			$table->display();
		?>
	</form>
</div>