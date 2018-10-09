<?php

function TextExtractOverrideParseByTitle( $title, $alterntiveText ) {
	$text = TextExtractOverrideGetDescriptionFromCargo( $title );
	$isAlternativeTextDefault = wfMessage( 'popups-preview-no-preview') == $alterntiveText;
	//die($isAlternativeTextDefault);
	return $text ? $text : ($alterntiveText && !$isAlternativeTextDefault ? $alterntiveText : wfMessage( 'text-extracts-default-description')->text());
}	

function TextExtractOverrideGetDescriptionFromCargo( $title ) {
		//die(print_r($title));
		if(class_exists('CargoSQLQuery')){
			$sqlQuery = \CargoSQLQuery::newFromValues( 'meta_data', 'excerpt', "meta_data._pageName='{$title->mTextform}'",'','','','','');
			try {
				$queryResults = $sqlQuery->run();

			} catch ( Exception $e ) {
				$queryResults = [];
			}

			// Format data as the API requires it.
			//$formattedData = array();
			$formattedData = null;
			foreach ( $queryResults as $row ) {
				if($row){
					$myParse = new \Parser();
					$formattedData = $myParse->parse($row['excerpt'], $title, new \ParserOptions());
					break;
				}
				
			}
			//die(print_r($formattedData));
			$text_to_return = $formattedData ? html_entity_decode($formattedData->mText) : '';
			return strip_tags($text_to_return);
		}
	}