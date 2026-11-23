/**
 * Editor UI for the Read Next Card block. Plain JS, no build step.
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { useSelect } = wp.data;
	const { useBlockProps, InspectorControls } = wp.blockEditor;
	const { PanelBody, SelectControl, Placeholder } = wp.components;
	const ServerSideRender = wp.serverSideRender;
	const { __ } = wp.i18n;

	registerBlockType( 'read-next-card/card', {
		edit( { attributes, setAttributes, context } ) {
			const blockProps = useBlockProps();

			// Recent published posts to choose from, excluding the one being edited.
			const posts = useSelect(
				( select ) =>
					select( 'core' ).getEntityRecords( 'postType', 'post', {
						per_page: 50,
						status: 'publish',
						exclude: context.postId ? [ context.postId ] : [],
						_fields: 'id,title',
					} ),
				[ context.postId ]
			);

			const options = [
				{ label: __( 'Automatic (most related)', 'read-next-card' ), value: 0 },
				...( posts || [] ).map( ( post ) => ( {
					label: post.title.rendered || __( '(no title)', 'read-next-card' ),
					value: post.id,
				} ) ),
			];

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Linked post', 'read-next-card' ) },
						el( SelectControl, {
							value: attributes.postId,
							options,
							onChange: ( value ) => setAttributes( { postId: parseInt( value, 10 ) } ),
							help: __( 'Automatic picks the post sharing the most categories and tags.', 'read-next-card' ),
						} )
					)
				),
				el(
					'div',
					blockProps,
					// Live preview from PHP; shows a placeholder if nothing matches.
					el( ServerSideRender, {
						block: 'read-next-card/card',
						attributes,
						urlQueryArgs: { post_id: context.postId },
						EmptyResponsePlaceholder: () =>
							el( Placeholder, {
								icon: 'book-alt',
								label: __( 'Read Next Card', 'read-next-card' ),
								instructions: __( 'No related post found yet. Pick one in the sidebar, or add categories and tags.', 'read-next-card' ),
							} ),
					} )
				)
			);
		},

		// Dynamic block: rendered by PHP.
		save: () => null,
	} );
} )( window.wp );
