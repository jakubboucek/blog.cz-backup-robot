<?php


class XPathParser {

	private $doc;

	public function __construct( DOMXPath $doc ) {
		$this->doc = $doc;
	}


	public function getNodeList( $query, $context = NULL ) {
		if( $context ) {
			return $this->doc->evaluate( $query, $context );
		} else {
			return $this->doc->evaluate( $query );
		}
	}

	public function getElementNode( $query, $context = NULL ) {
		if( $context ) {
			$node = $this->doc->evaluate( $query, $context );
		} else {
			$node = $this->doc->evaluate( $query );
		}

		if( $node->length == 0 ) {
			return FALSE;
		} else {
			return $node->item( 0 );
		}
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


	public function getTextContent( $query ) {
		$node = $this->doc->evaluate( $query );

		if( $node->length == 0 ) {
			return FALSE;
		} else {
			return $node->item( 0 )->textContent;
		}
	}

	public function getAttributeValue( $query, $context = NULL ) {
		if( $context ) {
			$node = $this->doc->evaluate( $query, $context );
		} else {
			$node = $this->doc->evaluate( $query );
		}

		if( $node->length == 0 ) {
			return FALSE;
		} else {
			return $node->item( 0 )->value;
		}
	}
}

