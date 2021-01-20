// @codekit-prepend "conditional-fields-legacy.js";
// @codekit-prepend "conditions.js";
/**
* Handles showing and hiding fields conditionally
*/
jQuery( document ).ready( function( $ ) {

	// Show/hide elements as necessary when a conditional field is changed
	$( '#anugu-gallery-settings input:not([type=hidden]), #anugu-gallery-settings select' ).conditions(
		[

			{	// Main Theme Elements
				conditions: {
					element: '[name="_anugu_gallery[lightbox_theme]"]',
					type: 'value',
					operator: 'array',
					condition: [ 'base', 'captioned', 'polaroid', 'showcase', 'sleek', 'subtle' ]
				},
				actions: {
					if: [
						{
							element: '#anugu-config-lightbox-title-display-box, #anugu-config-lightbox-arrows-box, #anugu-config-lightbox-toolbar-box, #anugu-config-supersize-box',
							action: 'show'
						}
					]
				}
			},
			{
				conditions: {
					element: '[name="_anugu_gallery[lightbox_theme]"]',
					type: 'value',
					operator: 'array',
					condition: [ 'base_dark' ]
				},
				actions: {
					if: [
						{
							element: '#anugu-config-lightbox-title-display-box, #anugu-config-lightbox-arrows-box, #anugu-config-lightbox-toolbar-box, #anugu-config-supersize-box',
							action: 'hide'
						}
					]
				}
			},
			{	// Gallery arrows Dependant on Theme
				conditions: [
					{
						element: '[name="_anugu_gallery[lightbox_theme]"]',
						type: 'value',
						operator: 'array',
						condition: [ 'base', 'captioned', 'polaroid', 'showcase', 'sleek', 'subtle' ]
					},
					{
						element: '[name="_anugu_gallery[arrows]"]',
						type: 'checked',
						operator: 'is'
					}
				],
				actions: {
					if: {
						element: '#anugu-config-lightbox-arrows-position-box',
						action: 'show'
					},
					else: {
						element: '#anugu-config-lightbox-arrows-position-box',
						action: 'hide'
					}
				}
			},
			{	// Gallery Toolbar
				conditions: [
					{
						element: '[name="_anugu_gallery[toolbar]"]',
						type: 'checked',
						operator: 'is'
					},
					{
						element: '[name="_anugu_gallery[lightbox_theme]"]',
						type: 'value',
						operator: 'array',
						condition: [ 'base', 'captioned', 'polaroid', 'showcase', 'sleek', 'subtle' ]
					}
				],
				actions: {
					if: [
						{
							element: '#anugu-config-lightbox-toolbar-title-box, #anugu-config-lightbox-toolbar-position-box',
							action: 'show'
						}
					],
					else: [
						{
							element: '#anugu-config-lightbox-toolbar-title-box, #anugu-config-lightbox-toolbar-position-box',
							action: 'hide'
						}
					]
				}
			},
			{	// Mobile Elements Dependant on Theme
				conditions: [
					{
						element: '[name="_anugu_gallery[lightbox_theme]"]',
						type: 'value',
						operator: 'array',
						condition: [ 'base', 'captioned', 'polaroid', 'showcase', 'sleek', 'subtle' ]
					},
					{
						element: '[name="_anugu_gallery[mobile_lightbox]"]',
						type: 'checked',
						operator: 'is'
					}
				],
				actions: {
					if: {
						element: '#anugu-config-mobile-arrows-box, #anugu-config-mobile-toolbar-box',
						action: 'show'
					},
					else: {
						element: '#anugu-config-mobile-arrows-box, #anugu-config-mobile-toolbar-box',
						action: 'hide'
					}
				}
			},
			{	// Thumbnail Elements Dependant on Theme
				conditions: [
					{
						element: '[name="_anugu_gallery[lightbox_theme]"]',
						type: 'value',
						operator: 'array',
						condition: [ 'base', 'captioned', 'polaroid', 'showcase', 'sleek', 'subtle' ]
					},
					{
						element: '[name="_anugu_gallery[thumbnails]"]',
						type: 'checked',
						operator: 'is'
					}
				],
				actions: {
					if: {
						element: '#anugu-config-thumbnails-position-box',
						action: 'show'
					},
					else: {
						element: '#anugu-config-thumbnails-position-box',
						action: 'hide'
					}
				}
			},
			{	// Thumbnail Elements Independant of Theme
				conditions: [
					{
						element: '[name="_anugu_gallery[thumbnails]"]',
						type: 'checked',
						operator: 'is'
					}
				],
				actions: {
					if: {
						element: '#anugu-config-thumbnails-height-box, #anugu-config-thumbnails-width-box',
						action: 'show'
					},
					else: {
						element: '#anugu-config-thumbnails-height-box, #anugu-config-thumbnails-width-box',
						action: 'hide'
					}
				}
			},
			{	// Justified Gallery
				conditions: {
					element: '[name="_anugu_gallery[columns]"]',
					type: 'value',
					operator: 'array',
					condition: [ '0' ]
				},
				actions: {
					if: [
						{
							element: '#anugu-config-standard-settings-box',
							action: 'hide'
						},
						{
							element: '#anugu-config-justified-settings-box',
							action: 'show'
						}
					],
					else: [
						{
							element: '#anugu-config-standard-settings-box',
							action: 'show'
						},
						{
							element: '#anugu-config-justified-settings-box',
							action: 'hide'
						}
					]
				}
			},
			{	// Gallery Description
				conditions: {
					element: '[name="_anugu_gallery[description_position]"]',
					type: 'value',
					operator: 'array',
					condition: [ '0' ]
				},
				actions: {
					if: [
						{
							element: '#anugu-config-description-box',
							action: 'hide'
						}
					],
					else: [
						{
							element: '#anugu-config-description-box',
							action: 'show'
						}
					]
				}
			},
			{	// Gallery Sorting
				conditions: {
					element: '[name="_anugu_gallery[random]"]',
					type: 'value',
					operator: 'array',
					condition: [ '0' ]
				},
				actions: {
					if: [
						{
							element: '#anugu-config-sorting-direction-box',
							action: 'hide'
						}
					],
					else: [
						{
							element: '#anugu-config-sorting-direction-box',
							action: 'show'
						}
					]
				}
			},
			{	// Gallery CSS animations
				conditions: {
					element: '[name="_anugu_gallery[css_animations]"]',
					type: 'checked',
					operator: 'is'
				},
				actions: {
					if: [
						{
							element: '#anugu-config-css-opacity-box',
							action: 'show'
						}
					],
					else: [
						{
							element: '#anugu-config-css-opacity-box',
							action: 'hide'
						}
					]
				}
			},
			{	// Gallery image size
				conditions: {
					element: '[name="_anugu_gallery[image_size]"]',
					type: 'value',
					operator: 'array',
					condition: [ 'default' ]
				},
				actions: {
					if: [
						{
							element: '#anugu-config-crop-size-box, #anugu-config-crop-box',
							action: 'show'
						}
					],
					else: [
						{
							element: '#anugu-config-crop-size-box, #anugu-config-crop-box',
							action: 'hide'
						}
					]
				}
			},
			{	// Gallery Lightbox
				conditions: {
					element: '[name="_anugu_gallery[lightbox_enabled]"]',
					type: 'checked',
					operator: 'is'
				},
				actions: {
					if: [
						{
							element: '#anugu-lightbox-settings',
							action: 'show'
						},
						{
							element: '#anugu-config-lightbox-enabled-link',
							action: 'hide'
						},
					],
					else: [
						{
							element: '#anugu-lightbox-settings',
							action: 'hide'
						},
						{
							element: '#anugu-config-lightbox-enabled-link',
							action: 'show'
						},
					]
				}
			},
			{	// Album Mobile Touchwipe
				conditions: {
					element: '[name="_anugu_gallery[lazy_loading]"]',
					type: 'checked',
					operator: 'is'
				},
				actions: {
					if: [
						{
							element: '#anugu-config-lazy-loading-delay',
							action: 'show'
						}
					],
					else: [
						{
							element: '#anugu-config-lazy-loading-delay',
							action: 'hide'
						}
					]
				}
			},

		]
	);

} );