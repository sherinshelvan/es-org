<div class="wrap">
	<h1 class="wp-heading-inline"><?=$page_heading?></h1>
	<hr class="wp-header-end">	
	<div class="">
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
		<form action="" method="post" name="esg_config_form" enctype="multipart/form-data" id="esg_config_form" class="" >
			<h4>Aceess Permissions</h4>
			<?php 
			foreach($role as $key => $row){
				echo sprintf("<div>");
				echo sprintf('<label><input type="checkbox" name="access_permission[]" %s %s value="%s">%s</label>', ($row['access']?'checked' : ''), ( ($row['key'] == 'administrator') ? 'disabled' : ''), $row['key'], $row['name']);
				echo sprintf("</div>");
			}				
			?>
			<p class="submit">
				<input type="submit" name="doSubmit" value="Update Permissions" class="button button-primary button-large" />
			</p>				
		</form>
	</div>
</div>