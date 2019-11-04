<?php
/**
 * Hooks for LogEntry extension
 *
 * @file
 * @ingroup Extensions
 */

// LogEntry hooks
class LogEntryHooks {

	/* Functions */

	// Initialization
	public static function register( $parser ) {
		// Register the hook with the parser
		$parser->setHook( 'logentry', 'LogEntryHooks::render' );

		// Continue
		return true;
	}

	// Render the entry form
	public static function render( $input, $args, $parser ) {
		global $egLogEntryMultiLine, $egLogEntryMultiLineRows;

		// Don't cache since we are passing the token in the form
		$parser->getOutput()->updateCacheExpiry( 0 );

		// Build HTML
		$htmlResult = Xml::openElement( 'form',
			array(
				'id' => 'logentryform',
				'name' => 'logentryform',
				'method' => 'post',
				'action' => htmlspecialchars( SpecialPage::getTitleFor( 'LogEntry' )->getLocalURL() ),
				'enctype' => 'multipart/form-data'
			)
		);
		if ( $egLogEntryMultiLine ) {
			$htmlResult .= Xml::element( 'textarea',
				array(
					'rows' => $egLogEntryMultiLineRows,
					'name' => 'line',
					'style' => 'width:100%;'
				)
			);
			$htmlResult .= Xml::tags( 'div',
				array(
					'align' => 'right'
				),
				Xml::element( 'input',
					array(
						'type' => 'submit',
						'name' => 'append',
						'value' => wfMessage( 'logentry-append' )->text()
					)
				)
			);
		} else {
			$htmlResult .= Xml::element( 'input',
				array(
					'type' => 'text',
					'name' => 'line',
					'style' => 'width:80%;'
				)
			);
			$htmlResult .= Xml::element( 'input',
				array(
					'type' => 'submit',
					'name' => 'append',
					'value' => wfMessage( 'logentry-append' )->text()
				)
			);
		}
		$htmlResult .= Xml::element( 'input',
			array(
				'type' => 'hidden',
				'name' => 'page',
				'value' => $parser->getTitle()->getPrefixedText()
			)
		);
		$htmlResult .= Xml::element( 'input',
			array(
				'type' => 'hidden',
				'name' => 'token',
				'value' => $parser->getUser()->getEditToken()
			)
		);
		$htmlResult .= Xml::closeElement( 'form' );

		// Return HTML output
		return $htmlResult;
	}
}
