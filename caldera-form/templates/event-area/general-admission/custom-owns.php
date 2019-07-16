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
		</div>	
<?php 
if( isset($_SESSION["bacty_quantity"])):
	$ot_event_date_id = get_the_ID( );
	$caldera_form_id = get_post_meta( $ot_event_date_id, '_caldera_form_id', false );
    $bacty_quantity=$_SESSION["bacty_quantity"]; ?>
	<div class="jquery-modal blocker current" style="display: none;">
		<div id="ex1" class="modal" style="display: inline-block;"> 
		<div id="tabs">
		  <ul>
			<?php  
			for ($x = 1; $x <= $bacty_quantity; $x++) {
			 echo '<li><a href="#tabs-'.$x.'">  Child '.$x.' </a></li>';
			}
			?> 
		  </ul>
			<?php  for ($x = 1; $x <= $bacty_quantity; $x++) {
		      echo ' <div id="tabs-'.$x.'">';
			  echo do_shortcode( '[caldera_form id="' . $caldera_form_id[0] . '" type="button"]' );
			  echo $_REQUEST['quantity'];
			  echo '</div>'; }
		    ?>
		</div>
		 
	    <?php if($x-1== $bacty_quantity): ?>
		  <div class="actions" rel="actions"  style="text-align:right;">
				<a href="<?php echo esc_attr( $cart_url ) ?>" class="button" rel="cart-btn"><?php echo apply_filters( 'qsot-get-option-value', __( 'Proceed to Cart', 'opentickets-community-edition' ), 'qsot-proceed-button-text' ) ?></a>
			</div>
		<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
</div>
<?php endif; ?>
<script type="text/javascript">
	var $ = jQuery;
	jQuery( document ).ready( function() {
	  jQuery('.jquery-modal').show();
	    jQuery(".btn-default").on("click", function () {
	        $( document ).ajaxComplete(function( event, request, settings ) {
			$('.ui-state-active').next('li').find('a').trigger('click');
			});
	    });
	  jQuery(".button").on("click", function () {
		    location.reload(true);
		 	var ot_event_date_id = $('.ot_event_date_id').val();
		 	var quantity = $('.very-short').val();
	    });
	});
</script>