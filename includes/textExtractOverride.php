<?php

class StripStateOverride extends StripState{
	function getRegex(){
		return $this->regex;
	}
}

function TextExtractOverrideParseByTitle( $title, $alterntiveText ) {
	$text = TextExtractOverrideGetDescriptionFromCargo( $title );
	$isAlternativeTextDefault = wfMessage( 'popups-preview-no-preview') == $alterntiveText;
	//die($isAlternativeTextDefault);
	return $text ? $text : ($alterntiveText && !$isAlternativeTextDefault ? $alterntiveText : wfMessage( 'text-extracts-default-description')->text());
}	

function TextExtractOverrideGetDataFromCargo( $name, $title ) {
	$sqlQuery = \CargoSQLQuery::newFromValues(
		'page_synopsis',
		$name,
		"page_synopsis._pageName='{$title->mTextform}'",
		'', '', '', '', '', ''
	);
	try {
		$queryResults = $sqlQuery->run();

	} catch ( Exception $e ) {
		$queryResults = [];
	}
	return $queryResults;
}
function TextExtractOverrideGetDescriptionFromCargo( $title ) {
		if ( class_exists( 'CargoSQLQuery' ) ){
			$queryResults = TextExtractOverrideGetDataFromCargo( 'excerpt', $title );

			$formattedData = null;
			foreach ( $queryResults as $row ) {
				if ( $row ){
					$textFromRow = $row['excerpt'];
					$myParse = new \Parser();
					$formattedData = $myParse->parse( $textFromRow, $title, new \ParserOptions() );
					$text = html_entity_decode( $formattedData->getText() );
					$stripper = new StripStateOverride;
					$regex = $stripper->getRegex();
					$regex = preg_replace( '/\x7f/', '\?', $regex );
					$formattedData = preg_replace( $regex, '', $text );
					break;
				}
			}
			$text_to_return = $formattedData ? $formattedData . '...' : '';

			return strip_tags( $text_to_return );
		}

		return null;
	}
