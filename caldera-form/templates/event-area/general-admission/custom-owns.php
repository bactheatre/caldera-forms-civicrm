<?php if ( __FILE__ == $_SERVER['SCRIPT_FILENAME'] ) die( header( 'Location: /') ); ?>
<?php if ( 'yes' == apply_filters( 'qsot-get-option-value', 'no', 'qsot-locked-reservations' ) ): ?>
	<div class="ticket-form ticket-selection-section">
		<div class="form-inner update">
			<div class="title-wrap">
				<h3><?php _e( 'Step 2: Review', 'opentickets-community-edition' ) ?></h3>
			</div>
			<div class="field">
				<label class="section-heading"><?php _e( 'You currently have:', 'opentickets-community-edition' ) ?></label>
				<div class="availability-message helper"></div>
				<a href="#" class="remove-link" rel="remove-btn">X</a>
				<span rel="tt"></span>
				<?php _e( 'x', 'opentickets-community-edition' ) ?><span rel="qty"></span>
			</div>
		</div>
		<div class="actions" rel="actions">
			<a href="<?php echo esc_attr( $cart_url ) ?>" class="button" rel="cart-btn"><?php echo apply_filters( 'qsot-get-option-value', __( 'Proceed to Cart', 'opentickets-community-edition' ), 'qsot-proceed-button-text' ) ?></a>
		</div>
	</div>
<?php else: ?>
	<div class="ticket-form ticket-selection-section">
		<div class="form-inner update">
			<div class="title-wrap">
				<h3><?php _e( 'Step 2: Review', 'opentickets-community-edition' ) ?></h3>
			</div>
			<div class="field">
				<label class="section-heading"><?php _e( 'You currently have:', 'opentickets-community-edition' ) ?></label>
				<div class="availability-message helper"></div>
				<a href="#" class="remove-link" rel="remove-btn">X</a>
				<span rel="tt"></span>
				<?php if ( 1 !== intval( $limit ) ): ?>
					<input type="number" step="1" min="0" max="<?php echo $max ?>" rel="qty" name="quantity" value="1" class="very-short" />
					<input type="button" value="<?php echo esc_attr( apply_filters( 'qsot-get-option-value', __( 'Update', 'opentickets-community-edition' ), 'qsot-update-button-text' ) ) ?>" rel="update-btn" class="button" />
				<?php else: ?>
					<input type="hidden" rel="qty" name="quantity" value="1" /> <?php echo __( 'x', 'opentickets-community-edition' ) . ' 1' ?>
				<?php endif; ?>
			</div>

			<div class="field">


			<?php 

				$ot_event_date_id = get_the_ID();

				$caldera_form_id = get_post_meta( $ot_event_date_id, '_caldera_form_id', false );
				// checking Caldera form is assigned or not.
				if ( !empty( $caldera_form_id ) ) {
					echo do_shortcode( '[caldera_form_modal id="' . $caldera_form_id[0] . '" type="button"]Add Child[/caldera_form_modal]' );
					}
							

				foreach ( WC()->cart->get_cart() as $cart_item ) {  
					$quantity =  $cart_item['quantity'];
					 $quantity;
				}
			?>

			</div>



		</div>
		<div class="actions" rel="actions">
			<a href="<?php echo esc_attr( $cart_url ) ?>" class="button" rel="cart-btn"><?php echo apply_filters( 'qsot-get-option-value', __( 'Proceed to Cart', 'opentickets-community-edition' ), 'qsot-proceed-button-text' ) ?></a>
		</div>
	</div>
<?php endif; ?>
<button class="caldera-forms-modal" data-form="CF5cf4e6641d45a" data-remodal-target="cf-modal-CF5cf4e6641d45a5d27591bbaacc" title="Click to open the form Additional Participant in a modal">Add Child</button>

<div class="remodal-wrapper remodal-is-closed" style="display: none;"><div data-remodal-id="cf-modal-CF5cf4e6641d45a5d27591bbaacc" id="cf-modal-CF5cf4e6641d45a5d27591bbaacc" class="remodal caldera-front-modal-container remodal-is-initialized remodal-is-closed" data-form-id="CF5cf4e6641d45a" data-remodal-options="hashTracking: true, closeOnOutsideClick: false" tabindex="-1">
			<button data-remodal-action="close" class="remodal-close"></button>
			<div class="caldera-modal-body caldera-front-modal-body" id="cf-modal-CF5cf4e6641d45a5d27591bbaacc_modal_body">
				<div class="caldera-grid" id="caldera_form_2" data-cf-ver="1.8.5" data-cf-form-id="CF5cf4e6641d45a"><div id="caldera_notices_2" data-spinner="http://localhost/ADC/bact/wp-admin/images/spinner.gif"></div><form data-instance="2" class="CF5cf4e6641d45a caldera_forms_form cfajax-trigger _tisBound" method="POST" enctype="multipart/form-data" id="CF5cf4e6641d45a_2" data-form-id="CF5cf4e6641d45a" aria-label="Additional Participant" data-target="#caldera_notices_2" data-template="#cfajax_CF5cf4e6641d45a-tmpl" data-cfajax="CF5cf4e6641d45a" data-load-element="_parent" data-load-class="cf_processing" data-post-disable="1" data-action="cf_process_ajax_submit" data-request="http://localhost/ADC/bact/cf-api/CF5cf4e6641d45a">
<input type="hidden" id="_cf_verify_CF5cf4e6641d45a" name="_cf_verify" value="c2c38fce0a" data-nonce-time="1562859803"><input type="hidden" name="_wp_http_referer" value="/ADC/bact/qsot-event/rain-season-camp/06-03_2019-845am/"><div id="cf2-CF5cf4e6641d45a_2"></div><input type="hidden" name="_cf_frm_id" value="CF5cf4e6641d45a">
<input type="hidden" name="_cf_frm_ct" value="2">
<input type="hidden" name="cfajax" value="CF5cf4e6641d45a">
<input type="hidden" name="_cf_cr_pst" value="25366">
<div class="hide" style="display:none; overflow:hidden;height:0;width:0;">
<label>Web Site</label><input type="text" name="web_site" value="" autocomplete="off">
</div><div id="CF5cf4e6641d45a_2-row-1" class="row  first_row"><div class="col-sm-12  single"><div class=""><div class="cfc-notices-CF5cf4e6641d45a">
	</div>
</div></div></div><div id="CF5cf4e6641d45a_2-row-2" class="row  last_row"><div class="col-sm-12  single"><div data-field-wrapper="fld_2494557" class="form-group" id="fld_2494557_2-wrap">
	<label id="fld_2494557Label" for="fld_2494557_2" class="control-label">Name</label>
	<div class="">
		<input type="text" data-field="fld_2494557" class=" form-control" id="fld_2494557_2" name="fld_2494557" value="" data-type="text" aria-labelledby="fld_2494557Label">			</div>
</div>
<div data-field-wrapper="fld_8902336" class="form-group" id="fld_8902336_2-wrap">
	<label id="fld_8902336Label" for="fld_8902336_2" class="control-label">Age</label>
	<div class="">
		<input type="text" data-field="fld_8902336" class=" form-control" id="fld_8902336_2" name="fld_8902336" value="" data-type="text" aria-labelledby="fld_8902336Label">			</div>
</div>
<div data-field-wrapper="fld_4606617" class="form-group" id="fld_4606617_2-wrap">
	<label id="fld_4606617Label" for="fld_4606617_2" class="control-label">T-Shirt Size</label>
	<div class="">
		<input type="text" data-field="fld_4606617" class=" form-control" id="fld_4606617_2" name="fld_4606617" value="" data-type="text" aria-labelledby="fld_4606617Label">			</div>
</div>
<div data-field-wrapper="fld_1506555" class="form-group" id="fld_1506555_2-wrap">
	<label id="fld_1506555Label" for="fld_1506555_2" class="control-label">test</label>
	<div class="">
		<input type="text" data-field="fld_1506555" class=" form-control" id="fld_1506555_2" name="fld_1506555" value="" data-type="text" aria-labelledby="fld_1506555Label">			</div>
</div>
<div data-field-wrapper="fld_1244651" class="form-group" id="fld_1244651_2-wrap">
<div class="">
	<input class="btn btn-default" type="submit" name="fld_1244651" id="fld_1244651_2" value="Submit" data-field="fld_1244651">
</div>
</div>
	<input class="button_trigger_2" type="hidden" name="fld_1244651" id="fld_1244651_2_btn" value="" data-field="fld_1244651">
</div></div></form>
</div>
			</div>
		</div></div>