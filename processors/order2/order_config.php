<?php

$financial_types = civicrm_api3( 'FinancialType', 'get', [
	'sequential' => 1,
	'is_active' => 1,
] );

$contribution_status = civicrm_api3( 'Contribution', 'getoptions', [
  'sequential' => 1,
  'field' => 'contribution_status_id',
] );

$payment_instruments = civicrm_api3( 'Contribution', 'getoptions', [
	'field' => 'payment_instrument_id',
] );

$price_sets = caldera_forms_civicrm()->helper->get_price_sets();

?>

<p><strong><?php _e( 'Note:', 'caldera-forms-civicrm' ); ?></strong> <?php _e( 'This processor does not process payment transactions on it\'s own, it just creates a Contribution in CiviCRM with single or multiple line items. In order to process live payment transaction a Caldera Forms add-on is needed.', 'caldera-forms-civicrm' ); ?></p>

<!-- Contact ID -->
<h2><?php _e( 'Contact Link', 'caldera-forms-civicrm' ); ?></h2>
<div id="contact_link" class="caldera-config-group">
	<label><?php _e( 'Link to', 'caldera-forms-civicrm' ); ?></label>
	<div class="caldera-config-field">
		<?php caldera_forms_civicrm()->helper->contact_link_field(); ?>
		<p><?php _e( 'Select which contact you want to link this processor to.', 'caldera-forms-civicrm' ); ?></p>
	</div>
</div>
<hr style="clear: both;" />

<!-- Order Fields -->
<h2><?php _e( 'Order Fields', 'caldera-forms-civicrm' ); ?></h2>
<!-- Financial Type -->
<div id="financial_type_id" class="caldera-config-group">
	<label><?php _e( 'Financial Type', 'caldera-forms-civicrm' ); ?></label>
	<div class="caldera-config-field">
		<select class="block-input field-config" name="{{_name}}[financial_type_id]">
		<?php foreach ( $financial_types['values'] as $key => $financial_type ) { ?>
			<option value="<?php echo esc_attr( $financial_type['id'] ); ?>" {{#is financial_type_id value=<?php echo $financial_type['id']; ?>}}selected="selected"{{/is}}><?php echo esc_html( $financial_type['name'] ); ?></option>
		<?php } ?>
		</select>
	</div>
</div>

<!-- Contribution Status -->
<div id="contribution_status_id" class="caldera-config-group">
	<label><?php _e( 'Contribution Status', 'caldera-forms-civicrm' ); ?></label>
	<div class="caldera-config-field">
		<select class="block-input field-config" name="{{_name}}[contribution_status_id]">
		<?php foreach ( $contribution_status['values'] as $key => $status ) { ?>
			<option value="<?php echo esc_attr( $status['key'] ); ?>" {{#is contribution_status_id value=<?php echo $status['key']; ?>}}selected="selected"{{/is}}><?php echo esc_html( $status['value'] ); ?></option>
		<?php } ?>
		</select>
	</div>
</div>

<!-- Payment Method -->
<div id="payment_instrument_id" class="caldera-config-group">
	<label><?php _e( 'Payment Method', 'caldera-forms-civicrm' ); ?></label>
	<div class="caldera-config-field">
		<select class="block-input field-config" name="{{_name}}[payment_instrument_id]">
		<?php foreach ( $payment_instruments['values'] as $id => $method ) { ?>
			<option value="<?php echo esc_attr( $id ); ?>" {{#is payment_instrument_id value=<?php echo $id; ?>}}selected="selected"{{/is}}><?php echo esc_html( $method ); ?></option>
		<?php } ?>
		</select>
	</div>
</div>

<!-- Recieve Date -->
<div id="receive_date" class="caldera-config-group">
	<label><?php _e( 'Receive Date', 'caldera-forms-civicrm' ); ?></label>
	<div class="caldera-config-field">
		{{{_field slug="receive_date"}}}
	</div>
</div>

<!-- Transaction ID -->
<div id="trxn_id" class="caldera-config-group">
	<label><?php _e( 'Transaction ID', 'caldera-forms-civicrm' ); ?></label>
	<div class="caldera-config-field">
		{{{_field slug="trxn_id"}}}
	</div>
</div>

<!-- Total Amount -->
<div id="total_amount" class="caldera-config-group">
	<label><?php _e( 'Total Amount', 'caldera-forms-civicrm' ); ?></label>
	<div class="caldera-config-field">
		{{{_field slug="total_amount"}}}
	</div>
</div>
<hr style="clear: both;" />

<!-- Line Items -->
<h1><?php _e( 'Line Items', 'caldera-forms-civicrm' ); ?></h1>

<div class="caldera-config-group">
	<div class="caldera-config-field">
		<button 
			id="{{_id}}_line_item_add" 
			type="button" 
			data-id="{{_id}}" 
			data-complete="line_item_built" 
			class="pull-right button ajax-trigger" 
			data-template="#line-item-tmpl" 
			data-target-insert="append" 
			data-request="cfc_add_line_item" 
			data-target="#{{_id}}_line_items_wrapper"><?php esc_html_e( 'Add Line Item', 'caldera-forms-civicrm' ); ?></button>
	</div>
</div>

<div class="{{_id}}_line_items" id="{{_id}}_line_items_wrapper">
    {{#each this.line_items as |value item|}}
	    <div id="{{item}}" data-id="{{_id}}" class="line-item caldera-config-group">
			<label><?php _e( 'Line Item', 'caldera-forms-civicrm' ); ?></label>
			<div class="caldera-config-field">
				<input type="text" class="block-input field-config magic-tag-enabled caldera-field-bind" name="{{../_name}}[line_items][{{item}}]" value="{{value}}">
				<button type="button" class="button remove-line-item pull-right"><i class="icon-join"></i></button>
			</div>
		</div>
	{{/each}}
	{{#unless this.line_items}}
		<div id="line_item_1" data-id="{{_id}}" class="line-item caldera-config-group">
			<label><?php _e( 'Line Item', 'caldera-forms-civicrm' ); ?></label>
			<div class="caldera-config-field">
				<input type="text" class="block-input field-config magic-tag-enabled caldera-field-bind" name="{{../_name}}[line_items][line_item_1]" value="">
				<button type="button" class="button remove-line-item pull-right"><i class="icon-join"></i></button>
			</div>
		</div>
	{{/unless}}
</div>

<script type="text/html" id="line-item-tmpl">
	<div id="line_item_{{item_id}}" data-id="{{_id}}" class="line-item caldera-config-group">
		<label><?php _e( 'Line Item', 'caldera-forms-civicrm' ); ?></label>
		<div class="caldera-config-field">
			<input type="text" exclude="system" class="block-input field-config magic-tag-enabled caldera-field-bind" name="{{_name}}[line_items][line_item_{{item_id}}]" value="">
			<button type="button" class="button remove-line-item pull-right"><i class="icon-join"></i></button>
		</div>
	</div>
</script>

<script>

	var cfc_add_line_item = function( obj ) {
		
		var id = obj.trigger.data('id'),
		config = JSON.parse( $( '#' + id + ' .processor_config_string' ).val() ),
		item_id = 'line_item_' + ( $( '#' + id + ' .line-item' ).length + 1 ),
		_name = 'config[processors][' + id + '][config]';

		if ( config.line_items ) {
			config.line_items[item_id] = '';
		} else {
			config['line_items'] = {}
			config.line_items['line_item_1'] = '';
		}
		
		var processor = { id: id, config: config };

		var line_item = { item_id: item_id, _name: _name };
		return line_item;
	}

	// FIXME
	// use this to modify appended element,
	// can't figure out how to change the 'context' in the handlebars template
	function line_item_built( obj ) {

		var id = $( '#line_item_' ).data( 'id' ),
		item_id = 'line_item_' + ( $( '#' + id + '_line_items_wrapper .line-item' ).length ),
		name = 'config[processors][' + id + '][config][line_items][' + item_id + ']';

		// update name
		$( '.' + id + '_line_items #line_item_ .field-config' ).attr( 'name', name );
		// update id
		$( '.' + id + '_line_items #line_item_' ).attr( 'id', item_id );
	}

	jQuery( document ).ready( function( $ ) {
		
		setTimeout( function(){
			$( '.line-item .field-config' ).closest( 'span' ).css( 'width', '80%' );
		}, 3000 )
		
		$( '.caldera-editor-body' ).on( 'click', '.remove-line-item', function( e ) {
			e.preventDefault();
			$(this).closest( '.line-item' ).remove();
		} );
	} )

</script>