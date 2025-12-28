<?php
/**
 * Hooks for LogEntry extension
 *
 * @file
 * @ingroup Extensions
 */

use MediaWiki\Html\Html;

class LogEntryHooks {

	/**
	 * @param Parser $parser
	 */
	public static function register( $parser ) {
		$parser->setHook( 'logentry', [ __CLASS__, 'render' ] );
	}

	/**
	 * @param string $input
	 * @param array $args
	 * @param Parser $parser
	 * @return string
	 */
	public static function render( $input, $args, $parser ) {
		global $egLogEntryMultiLine, $egLogEntryMultiLineRows;

		// Don't cache since we are passing the token in the form
		$parser->getOutput()->updateCacheExpiry( 0 );
		if ( method_exists( $parser, 'getUserIdentity' ) ) {
			// MW 1.36+
			$user = MediaWiki\MediaWikiServices::getInstance()
				->getUserFactory()->newFromUserIdentity( $parser->getUserIdentity() );
		} else {
			$user = $parser->getUser();
		}

		$htmlResult = Html::openElement( 'form',
			[
				'id' => 'logentryform',
				'name' => 'logentryform',
				'method' => 'post',
				'action' => htmlspecialchars( SpecialPage::getTitleFor( 'LogEntry' )->getLocalURL() ),
				'enctype' => 'multipart/form-data'
			]
		);
		if ( $egLogEntryMultiLine ) {
			$htmlResult .= Html::element( 'textarea',
				[
					'rows' => $egLogEntryMultiLineRows,
					'name' => 'line',
					'style' => 'width:100%;'
				]
			);
			$htmlResult .= Html::rawElement( 'div',
				[
					'align' => 'right'
				],
				Html::element( 'input',
					[
						'type' => 'submit',
						'name' => 'append',
						'value' => wfMessage( 'logentry-append' )->text()
					]
				)
			);
		} else {
			$htmlResult .= Html::element( 'input',
				[
					'type' => 'text',
					'name' => 'line',
					'style' => 'width:80%;'
				]
			);
			$htmlResult .= Html::element( 'input',
				[
					'type' => 'submit',
					'name' => 'append',
					'value' => wfMessage( 'logentry-append' )->text()
				]
			);
		}
		$htmlResult .= Html::element( 'input',
			[
				'type' => 'hidden',
				'name' => 'page',
				'value' => $parser->getTitle()->getPrefixedText()
			]
		);
		$htmlResult .= Html::element( 'input',
			[
				'type' => 'hidden',
				'name' => 'token',
				'value' => $user->getEditToken()
			]
		);
		$htmlResult .= Html::closeElement( 'form' );

		return $htmlResult;
	}
}
