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
	$sqlQuery = \CargoSQLQuery::newFromValues( 'meta_data', $name, "meta_data._pageName='{$title->mTextform}'",'','','','','');
	try {
		$queryResults = $sqlQuery->run();

	} catch ( Exception $e ) {
		$queryResults = [];
	}
	return $queryResults;
}
function TextExtractOverrideGetDescriptionFromCargo( $title ) {
		//die(print_r($title));
		if(class_exists('CargoSQLQuery')){
			$queryResults = TextExtractOverrideGetDataFromCargo('excerpt', $title);

			if(!count($queryResults) || !$queryResults[0]['excerpt']){
				$queryResults = TextExtractOverrideGetDataFromCargo('excerpt-text', $title);
			}
			//die(print_r(['mmm',$queryResults[0]]));
			$formattedData = null;
			foreach ( $queryResults as $row ) {
				if($row){
					$textFromRow = isset($row['excerpt']) ? $row['excerpt'] : $row['excerpt-text'];
					$myParse = new \Parser();
					$formattedData = $myParse->parse($textFromRow, $title, new \ParserOptions());
					$text = html_entity_decode($formattedData->getText());
					$stripper = new StripStateOverride;
					$regex = $stripper->getRegex();
					$regex = preg_replace('/\x7f/', '\?', $regex);
					//echo 'fdsdsfdf' . $regex;
					$formattedData = preg_replace($regex, '', $text);
					//die('bbb' . $formattedData);
					break;
				}
				
			}
			//die(print_r($formattedData));
			$text_to_return = $formattedData ? $formattedData . '...' : '';

			return strip_tags($text_to_return);
		}
	}