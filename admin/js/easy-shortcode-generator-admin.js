(function( $ ) {
	'use strict';
	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	
	$( document ).ready( function(){
		$( document ).on('click', "form.esg_edit_form .esg-copy-shortcode", function(){
			/* Get the text field */
			var copyText = document.getElementById( "esg-short-view" );
			/* Select the text field */
			copyText.select();
		  copyText.setSelectionRange(0, 99999);
		  /* Copy the text inside the text field */
		  document.execCommand("copy");
		  $( "form.esg_edit_form .esg-copy-shortcode" ).addClass( "copied" );
		  $( "form.esg_edit_form .esg-copy-shortcode" ).html( "Copied" );
		});
		$( document ).on( 'submit', 'form#esg_list_form', function(event) {
			var values = $(this).serializeArray();
			for( var i = 0; i < values.length; i++ ){
				var element = values[i];
				if( ( element.name == 'action' && element.value == 'delete' ) ||
					( element.name == 'action2' && element.value == 'delete' )
				){
					if( !$( 'form#esg_list_form #detete-checkbox' ).prop( 'checked' ) ){
						$( "form.esg_list_form a#esg-pop-delete" ).attr( "href", "javascript:void(0);" );
						$( 'form#esg_list_form #detete-checkbox' ).prop( 'checked', true );
						return false;
					}					
				  break;
				}
			};
			return true;			
		});
		$( document ).on('click', "form.esg_list_form a#esg-pop-delete", function(){
			if( $(this).attr('href' ) == 'javascript:void(0);' ){
				$( "form#esg_list_form" ).submit();
			}
		});
		$( document ).on( 'click', 'form.esg_list_form .esg-delete', function() {
			var delete_link = "?page=" + $(this).data('page') + "&action=" + $(this).data('action') + "&id=" + $(this).data('id');
			$("form.esg_list_form a#esg-pop-delete").attr("href", delete_link);
		});

		$( document ).on('change', 'form.esg_edit_form select.post', function() {
			var post = this.value;
			$( "form.esg_edit_form div.term-wrapper" ).empty();
			if( post != '' ){
				$( "form.esg_edit_form div.taxonomy-wrapper" ).html( '<div class="loader"></div>' );
				$.ajax({
			        url: ajax_objects.ajax_url,
			        type: 'POST',
			        data: {
			            'action' 	: 'esg_ajax_callback',
			            'perform'   : 'get_taxonomy',
			            'post' 		: post
			        },
			        success:function( response ) {
			        	let data = JSON.parse( response );
			        	$( "form.esg_edit_form div.taxonomy-wrapper" ).html( data.taxonomy_html );
			        },
			        error: function( errorThrown ){
			            console.log( errorThrown );
			        }
			    });
			}
			else{
				$( "form.esg_edit_form div.taxonomy-wrapper" ).empty();
			}
		});
		$( document ).on( 'change', 'form.esg_edit_form select.taxonomy', function() {
			var taxonomy = this.value;
			var post     = $( "form.esg_edit_form select.post option:selected" ).val();
			if( taxonomy != '' && post != '' ){
				$( "form.esg_edit_form div.term-wrapper" ).html( '<div class="loader"></div>' );
				$.ajax({
			        url: ajax_objects.ajax_url,
			        type: 'POST',
			        data: {
			            'action' 	: 'esg_ajax_callback',
			            'perform'   : 'get_terms',
			            'taxonomy' 	: taxonomy,
			            'post'	    : post
			        },
			        success:function( response ) {
			        	let data = JSON.parse( response );
			        	$( "form.esg_edit_form div.term-wrapper" ).html( data.terms_html );
			        },
			        error: function( errorThrown ){
			            console.log( errorThrown );
			        }
			    });
			}
			else{
				$( "form.esg_edit_form div.term-wrapper" ).empty();
			}
		});
    
    $( document ).on("click", 'form.esg_edit_form input[type="checkbox"].pagination', function(){
        $( 'form.esg_edit_form .pagination_count' ).toggle();
    });
    $( document ).on("click", 'form.esg_edit_form input[type="checkbox"].trim_content', function(){
        $( 'form.esg_edit_form .trim_count' ).toggle();
    });
  });
	

})( jQuery );
