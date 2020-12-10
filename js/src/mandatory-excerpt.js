
/*
mandatory_excerpt_opts is the array of values passed in via wp_localize_script in Mandatory_Excerpt class.
If this passes, we know that the excerpt is required.
 */
if ( typeof mandatory_excerpt_opts !== 'undefined' && mandatory_excerpt_opts.is_required ) {

	/*
	subscribe() is a method on the wp.data object that is usually referenced directly after setting it, but
	the step to set up the reference is usually missing from the tutorials.
	 */
	const { subscribe } = wp.data;

	// Same with dispatch.
	const { dispatch }  = wp.data;

	// This sets up the i18n support in conjunction with wp_set_script_translations() in the class.
	const __ = wp.i18n.__;

	// Save the starting state for the excerpt so we can compare it to detect changes.
	let wasExcerpt     = wp.data.select( 'core/editor' ).getEditedPostAttribute('excerpt');

	// Set the initial state for the update/publish button
	toggle_publishing_if_needed();

	// This is the event listener that fires whenever anything in GB changes.
	subscribe( function() {
		/*
		This bit manages the state of the "Update" button (for published posts) and the "Publish" button
		(for unpublished posts). Runs only if the excerpt has actually changed.
		 */
		let isExcerpt = wp.data.select( 'core/editor' ).getEditedPostAttribute('excerpt');
		if ( wasExcerpt !== isExcerpt ) {
			toggle_publishing_if_needed();
		}
		wasExcerpt = isExcerpt;
	});


	/*
	This adds a section to the pre-publish panel to make sure the user knows that the excerpt is required.
	The publish button is disabled until the excerpt is written.
	@src https://wordpress.stackexchange.com/questions/339138/add-pre-publish-conditions-to-the-block-editor
	@src https://developer.wordpress.org/block-editor/data/data-core-editor/#lockPostSaving
	 */
	const { registerPlugin }              = wp.plugins;
	const { PluginPrePublishPanel }       = wp.editPost;

	const MandatoryExcerptPrePublishPanel = function() {
		toggle_publishing_if_needed();
		if ( ! is_excerpt_written() ) {
			return wp.element.createElement(
				PluginPrePublishPanel,
				{
						title: __('Mandatory Excerpt', 'mandatory-excerpt'),
						initialOpen: true,
				},
				__( 'This post cannot be published because an excerpt is required. Please add an excerpt and try again.', 'mandatory-excerpt' )
			);
		}
		return null;
	};
	registerPlugin( 'mandatory-excerpt-prepublish', { render: MandatoryExcerptPrePublishPanel } );


	/**
	 * Determines if the excerpt is written.
	 *
	 * @returns {boolean}
	 */
	function is_excerpt_written() {
		return !!wp.data.select( 'core/editor' ).getEditedPostAttribute( 'excerpt' );
	}


	/**
	 * Determines if a post should be locked or not, based on the excerpt's presence.
	 */
	function toggle_publishing_if_needed() {
		const { dispatch }  = wp.data;
		if ( is_excerpt_written() ) {
			// Needed and present; saving is OK so unlock saving.
			if ( wp.data.select( 'core/editor' ).isPostSavingLocked() ) {
				dispatch( 'core/editor' ).unlockPostSaving( 'mandatory-excerpt' );
			}
		} else {
			// Needed not present; saving is not OK so lock saving.
			if ( ! wp.data.select( 'core/editor' ).isPostSavingLocked() ) {
				dispatch( 'core/editor' ).lockPostSaving( 'mandatory-excerpt' );
			}
		}
	}

}


