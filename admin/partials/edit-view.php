<div class="wrap">
	<h1 class="wp-heading-inline"><?=$page_heading?></h1>
	<a href="?page=easy-shortcode-edit" class="page-title-action">Add New</a>
	<hr class="wp-header-end">
	
	
	<div class="esg_edit_form_wrapper">
		<?php
		if(count($messages) > 0){
			foreach ($messages as $msg_type => $message) {
				if(count($message) > 0){
					echo sprintf('<div class="notice notice-%s is-dismissible">', $msg_type);
											foreach ($message as $key => $msg) {
												echo sprintf('<p><strong>%s</strong></p>', $msg);
											}
					echo "</div>";
				}
			}
		}
		?>
		
		<form action="" method="post" name="esg_edit_form" enctype="multipart/form-data" id="esg_edit_form" class="esg_edit_form" >
			<div class="left-wrapper">
				<div class="form-item main-title">
					<label for="name">Title <strong>*</strong></label>
					<input type="text" id="name" name="name" value="<?=$form_data['name'] ?? ''?>" />
				</div>
				<div class="tokens">
					<h4>Available tokens</h4>
            <ul class="flex-row">
                <li><label>Title : </label> {title}</li>
                <li><label>Content : </label>{content}</li>
                <li><label>Image : </label> {image}</li>
                <li><label>Permalink : </label> {permalink}</li>
                <li><label>Author : </label> {author}</li>
                <li><label>Date : </label> {date}</li>
            </ul>
				</div>
				<div class="shortcode-template">
					<textarea name="template" id="template" cols="80" rows="10"><?=$form_data['template']?></textarea>
				</div>
				<div class="form-item wrapper-class">
					<label for="wrapper_class">Wrapper class</label>
					<input type="text" id="wrapper_class" name="wrapper_class" value="<?=(isset($form_data['wrapper_class'])? $form_data['wrapper_class'] : '')?>" />
				</div>
			</div>
			<div class="right-wrapper">
				<div class="actions">
					<input type="hidden" name="id" value="<?=isset($form_data['id'])? $form_data['id'] : ''?>" />
					<input type="submit" name="doSubmit" value="save" class="button button-primary button-large" />
				</div>
				<?php
				if( isset($form_data['shortcode']) && $form_data['shortcode'] != '' ){
					?>
					<div class="form-item">
						<label for="esg-short-code">Shortcode </label>
						<div id="esg-short-code">
							<input type="text" readonly id="esg-short-view" value="<?=htmlentities($form_data['shortcode'])?>">
							<a href="javascript:void(0);" class="esg-copy-shortcode" title="Copy to clipboard">Copy to clipboard</a>
						</div>						
					</div>
					<?php
				}
				?>				
				<div class="select-wrapper">
					<div class="form-item select-item">
						<label for="post">Post Type <strong>*</strong></label>
						<select name="post" id="post" class="post">
							<option value=""> -- Select -- </option>
							<?php
							$filter_post = array("attachment");
							foreach ($post_types as $key => $post) {
								if(!in_array($post->name, $filter_post)){
									echo sprintf('<option value="%1$s" %3$s >%2$s</option>', $post->name, $post->label, ((isset($form_data['post']) && $form_data['post'] == $post->name) ? 'selected' : '') );
														}
							}
							?>
						</select>
					</div>
					<div class="taxonomy-wrapper form-item select-item">
						<?php
						if(count($taxonomy_result) > 0){
						?>
						<label for="taxonomy">Taxonomy</label>
						<select name="taxonomy" class="taxonomy" id="taxonomy">
							<option value= "" > --Select -- </option>
							<?php
							foreach ($taxonomy_result as $key => $taxonomy) {
								if($taxonomy->label != ''){
									echo sprintf('<option value="%1$s" %3$s >%2$s</option>', $taxonomy->name, $taxonomy->label, ((isset($form_data['taxonomy']) && $form_data['taxonomy'] == $taxonomy->name) ? 'selected' : '') );
								}
							}
							?>
						</select>
						<?php
						}
						?>
					</div>
					<div class="term-wrapper">
						<?php
						if(count($terms_result) > 0){
						?>
						<h4>Terms</h4>
						<?php
						foreach ($terms_result as $key => $term) {
							echo sprintf('<div class="terms"><label><input type="checkbox" name="terms[]" value="%s" %s/> %s<label></div>', $term->term_id, (( isset($form_data['terms']) && is_array($form_data['terms']) && in_array($term->term_id, $form_data['terms']) ) ? 'checked' : ''), $term->name);
						}
						}
						?>
					</div>
				</div>
				<div class="paginaton-wrap">
					<div class="pag-title">
						<label><input type="checkbox" id="pagination" <?=((isset($form_data['pagination']) && $form_data['pagination'] == 1 )? 'checked' : '')?> name="pagination" class="pagination" value="1" /> Pagination </label>
					</div>
					<div class="pagination_count" style="display:<?=((!isset($form_data['pagination']) || $form_data['pagination'] != 1 )? 'none' : '')?>;">
						<label for="pagination_count">Pagination count </label>
						<input type="text" id="pagination_count" name="pagination_count" value="<?=(isset($form_data['pagination_count'])? $form_data['pagination_count'] : '')?>" />
					</div>
				</div>
				<div class="trim-wrap">
					<div class="pag-title">
					<label><input type="checkbox" id="trim_content" <?=((isset($form_data['trim_content']) && $form_data['trim_content'] == 1 )? 'checked' : '')?> class="trim_content" name="trim_content" value="1" /> Trim Content</label>
					</div>
					<div class="trim_count" style="display:<?=((!isset($form_data['trim_content']) || $form_data['trim_content'] != 1 )? 'none' : '')?>;">
						<div>
							<label for="trim_count">Trim count </label>
							<input type="text" id="trim_count" name="trim_count" value="<?=(isset($form_data['trim_count'])? $form_data['trim_count'] : '')?>" />
						</div>
					</div>
				</div>
				<div class="form-item">
					<label for="maximum_post">Maximum no.of post </label>
					<input type="text" id="maximum_post" name="maximum_post" value="<?=(isset($form_data['maximum_post']) && !empty($form_data['maximum_post'])? $form_data['maximum_post'] : '')?>" />
				</div>
				<div class="form-item select-item">
					<label for="sort">Sort </label>
					<select name="sort" id="sort">
						<option value="asc">Ascending</option>
						<option value="desc" <?=((isset($form_data['sort']) && $form_data['sort'] == 'desc' )? 'selected' : '')?> >Descending</option>
					</select>
				</div>
				<div class="form-item select-item">
					<label for="order_by">Order By</label>
					<select name="order_by" id="order_by">
						<option value="title">Title</option>
						<option value="date" <?=((isset($form_data['order_by']) && $form_data['order_by'] == 'date' )? 'selected' : '')?>>Created Date</option>
						<option value="modified" <?=((isset($form_data['order_by']) && $form_data['order_by'] == 'modified' )? 'selected' : '')?> >Modified Date</option>
					</select>
				</div>
			</div>
		</form>
	</div>
</div>