<?php


class XPathParser {

	private $doc;

	public function __construct( DOMXPath $doc ) {
		$this->doc = $doc;
	}


	public function getNodeList( $query ) {
		return $this->doc->evaluate( $query );
	}


	public function getNodeInnerText( $query, $context = NULL ) {
		if( $context ) {
			$node = $this->doc->evaluate( $query, $context );
		} else {
			$node = $this->doc->evaluate( $query );
		}

		if( $node->length == 0 ) {
			return FALSE;
		} else {
			return $node->item( 0 )->wholeText;
		}
	}


	public function getAttributeValue( $query ) {
		$node = $this->doc->evaluate( $query );

		if( $node->length == 0 ) {
			return FALSE;
		} else {
			return $node->item( 0 )->value;
		}
	}
}

