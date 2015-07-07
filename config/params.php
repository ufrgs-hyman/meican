<?php

return [
	"meican.version" => "2.2.0",
		
	"google.analytics.enabled" => false,
	"google.analytics.key" => "UA-64193376-1",

	"google.maps.geocode.key" => "AIzaSyD1WDhjvDx14Z_mlG5l3TeMz9thwLU-n8Q",
		
    'aggregator.importer.enabled' => false,
	'aggregator.default.url' => "https://nsi-aggr-west.es.net/discovery",
			
	'meican.certificate.filename' => "meican.pem",
	'meican.certificate.passphrase' => "futurarnp",	
		
	'meican.nsa.id' => "urn:ogf:network:cipo.ufrgs.br:2014:nsa:meican",

	'reservation.port.unidirectional.enabled' => false,
	
	//only for automatedtests
	//workaround for meican-console app
	'meican.connection.requester.url' => 'http://meican.cipo.rnp.br/circuits/connection',	
	
	"perfsonar.importer.enabled" => true,
	"perfsonar.default.url"	=> "http://monitora.cipo.rnp.br:8084/perfSONAR_PS/services/topology",
	
	"provider.force.dummy" => true,

];
