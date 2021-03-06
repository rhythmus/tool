<?php

use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

class LoggingMiddleWare {
	private $logger;
	private $nextHandler;

	public static function forLogger( LoggerInterface $logger ) {
		return function ( callable $handler ) use ( $logger ) {
			return new LoggingMiddleWare( $logger, $handler );
		};
	}

	public function __construct( LoggerInterface $logger, callable $nextHandler ) {
		$this->logger = $logger;
		$this->nextHandler = $nextHandler;
	}

	public function __invoke( RequestInterface $request, array $options ) {
		$this->logger->addDebug( $request->getMethod() . ' ' . $request->getUri() );
		$fn = $this->nextHandler;
		return $fn( $request, $options )->then( function( $response ) {
			if ( $response->getStatusCode() < 200 || $response->getStatusCode() > 299 ) {
				$this->logger->addWarning( 'HTTP response ' . $response->getStatusCode() );
			}
			return $response;
		} );
	}
}
