<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\nsi;

/**
 * Elemento responsável por receber e gerenciar as requisições
 * REST definidas pelo protocolo NSI Document Distribution Service 1.0.
 *
 * A documentação de cada função é baseada no schema XML oficial do
 * protocolo (https://github.com/BandwidthOnDemand/nsi-dds). 
 *
 * @author Maurício Quatrin Guerreiro
 */
interface DocumentDistributionServer {
    
    /**
     * This root resource contains a collection of zero or more
     * subscriptions and documents held within the NSA.
     *
     * HTTP operations: GET
     * URI: /
     *
     * HTTP Parameters:
     *   Accept - Identifies the content type encoding requested for
     *   the returned results. Must be a content type supported by the
     *   protocol.
     *
     *   If-Modified-Since - Return only entries discovered or
     *     modified since this time.
     *
     * Query Parameters: None
     *
     * Returns (code, element):
     *     200 collection
     *         Return collection element containing all subscription
     *         and document resources matching the query.  If no
     *         subscriptions or documents match the query, then an empty
     *         documents collection is returned.
     *
     *     304 None
     *         Successful operation where there were no changes to any
     *         subscription or document resource given the If-Modified-Since
     *         criteria.  Returns no message body.
     *     
     *     400 error
     *         Returned if a client specifies an invalid request.  An
     *         error element will be included populated with appropriate
     *         error information.
     *
     *     500 error
     *         Returned if an internal server error occurred during the
     *         processing of this request. An error element will be
     *         included populated with appropriate error information.
     **/
    public function getCollection();
    
    /**
     * The subscriptions resource contains a collection of zero or
     * more subscriptions held within the provider NSA.
     *
     * HTTP operations: GET
     * URI: /subscriptions
     *
     * HTTP Parameters:
     *   Accept - Identifies the content type encoding requested for
     *   the returned results. Must be a content type supported by the
     *   protocol.
     *
     *   If-Modified-Since - Constrains the GET request to return only
     *   those subscriptions that have been created or updated since the
     *   time specified in this parameter.
     *
     * Query Parameters:
     *   requesterId - Return all subscription resources containing the
     *   specified requesterId.
     *
     * Returns (code, element):
     *
     *   200   subscriptions
     *         Return all subscription resources matching the query in a
     *         subscriptions element.  If no subscriptions match the query,
     *         then an empty subscriptions element is returned.
     *
     *   304   None
     *         Successful operation where there were no changes to any
     *         subscription resources matching the query filter given the
     *         If-Modified-Since criteria. Returns no message body.
     *
     *   400   error
     *         Returned if a client specifies an invalid request. An error
     *         element will be included populated with appropriate error
     *         information.
     *
     *   500   error
     *         Returned if an internal server error occurred during the
     *         processing of this request. An error element will be included
     *         populated with appropriate error information.
     **/
    public function getSubscriptions($requesterId = null);

    /**
     * The subscription resource contains a single subscription from
     * the provider NSA.
     *
     * HTTP operations: GET
     * URI: /subscriptions/{id}
     *         {id} is the unique subscription identifier.
     *
     * HTTP Parameters:
     * Accept - Identifies the content type encoding requested for
     * the returned results. Must be a content type supported by the
     * protocol.
     *
     * If-Modified-Since - Constrains the GET request to return only
     * the subscription if it has been updated since the time specified
     * in this parameter.
     *
     * Query Parameters: None
     *
     * Returns (code, element):
     *
     * 200 subscription
     *     Successful operation returns the subscription identified by
     *     id in a subscription element.  The Last-Modified header
     *     parameter will contain the time this subscription resource
     *     was last modified.
     *
     * 304 None
     *     Successful operation where there were no changes to the
     *     subscription resource identified by id given the
     *     If-Modified-Since criteria. Returns no message body.
     *
     * 400 error
     *     Returned if a client specifies an invalid request. An error
     *     element will be included populated with appropriate error
     *     information.
     *
     * 404 error
     *     Returned if the requested subscription was not found.  An
     *     error element will be included populated with appropriate
     *     error information.
     *
     * 500 error
     *     Returned if an internal server error occurred during the
     *     processing of this request. An error element will be included
     *     populated with appropriate error information.
     **/
    public function getSubscription($id);

    /**
     * The subscriptionRequest is a collection of parameters from the
     * subscription resource that is used to create a new subscription
     * resource or update an existing subscription resource.
     *
     * Once a subscription has been successfully created or updated on
     * the provider the server will immediately send notifications for
     * all documents matching the filter criteria independent of the
     * event filter.
     *
     * HTTP operations: POST (create), PUT (update)
     * URI: /subscriptions
     *
     * HTTP Parameters:
     * Content-Type - Identifies the content type encoding of the POST
     * body contents.  Must be a content type supported by the protocol.
     *
     * Accept - Identifies the content type encoding requested for
     * the returned results. Must be a content type supported by the
     * protocol.
     *
     * If-Modified-Since - Constrains the GET request to return only
     * the subscription if it has been updated since the time specified
     * in this parameter.
     *
     * Query Parameters: N/A
     *
     * Returns (code, element):
     *
     * 201 subscription
     *     Returns a copy of the new subscription resource created as
     *     the result of a successful operation.  The HTTP Location
     *     header field will contain the URI of the new subscription
     *     resource.
     *
     * 400 error
     *     Returned if a client specifies an invalid request. An error
     *     element will be included populated with appropriate error
     *     information.
     *
     * 403 error
     *     The server understood the request, but is refusing to fulfill
     *     it. Authorization will not help and the request SHOULD NOT be
     *     repeated.  An error element will be included populated with
     *     appropriate error information.
     *
     * 500 error
     *     Returned if an internal server error occurred during the
     *     processing of this request. An error element will be included
     *     populated with appropriate error information.
     **/
    public function setSubscription($data);

    /**
     * The documents element models a list of documents from the
     * document space.
     *
     * HTTP operations: GET
     * URI: /documents/{nsa}/{type}
     *
     * The documents element contains document resources discovered
     * within the document space, or a subset of documents based on
     * supplied query parameters.  Zero or more document instances will
     * be returned in a documents element.
     *
     * The URI template “/documents/{nsa}/{type}” can be used as an
     * alternative to, or in conjunction with, the use of query
     * parameters.  Performing a GET on “/documents/{nsa}/” will
     * return all documents associated with the specified NSA.
     * Performing a GET on “/documents/{nsa}/{type}” will return
     * all documents of {type} from the specified NSA.
     *
     * HTTP Parameters:
     *
     * Accept - Identifies the content type encoding requested for
     * the returned results. Must be a content type supported by the
     * protocol.
     *
     * If-Modified-Since - Constrains the GET request to return only
     * those documents that have been created or updated since the
     * time specified in this parameter.
     *
     * Query Parameters:
     *
     * id (string) - Return all document resources containing the specified Id.
     *
     * nsa (string) - Return all document resources containing the
     * specified nsa identifier.  Cannot be used if the {nsa} URI
     * component is provided.
     *
     * type (string) - Return all document resources containing the
     * specified type. Cannot be used if the {type} URI component is
     * provided.
     *
     * summary (none) - Will return summary results of any documents
     * matching the query criteria.  Summary results includes all
     * document meta-data but not the signature or document contents.
     *
     * Returns (code, element):
     *
     * 200 documents
     * Return all document resources matching the query in a
     * documents element.  If no documents match the query,
     * then an empty documents element is returned.
     *
     * 304   None
     * Successful operation where there were no changes to any
     * subscription resources matching the query filter given the
     * If-Modified-Since criteria. Returns no message body.
     *
     * 400 error
     * Returned if a client specifies an invalid request. An error
     * element will be included populated with appropriate error
     * information.
     *
     * 500 error
     * Returned if an internal server error occurred during the
     * processing of this request. An error element will be included
     * populated with appropriate error information.
     **/
    public function getDocuments($data);

    /**
     * The document element models the metadata for a single document
     * from the document space.
     *
     * HTTP operations: GET
     * URI: /documents/{nsa}/{type}/{id}
     *
     * This operation will return a specific document instance
     * discovered within the document space based on the URI template
     * “/documents/{nsa}/{type}/{id}”, where {nsa} is the NSA sourcing
     * the document, {type} is the type of document, and {id} is the
     * identifier of the specific document.  The matching document is
     * returned in a single document element.
     *
     * HTTP Parameters:
     *
     * Accept - Identifies the content type encoding requested for
     * the returned results. Must be a content type supported by the
     * protocol.
     *
     * If-Modified-Since - Constrains the GET request to return only
     * those documents that have been created or updated since the
     * time specified in this parameter.
     *
     * Query Parameters: None.
     *
     * Returns (code, element):
     *
     * 200 local
     *     Successful operation returns the document identified by
     *     {nsa}/{type}/{id} in a document element.  The Last-Modified
     *     header parameter will contain the time this document resource
     *     was last discovered.
     *
     * 304   None
     *     Successful operation returns the document identified by
     *     {nsa}/{type}/{id} in a document element.  The Last-Modified
     *     header parameter will contain the time this document resource
     *     was last discovered.
     *
     * 400 error
     *     Returned if a client specifies an invalid request. An error
     *     element will be included populated with appropriate error
     *     information.
     *
     * 404 error
     *     Returned if the requested document was not found.  An error
     *     element will be included populated with appropriate error
     *     information.
     *
     * 500 error
     *     Returned if an internal server error occurred during the
     *     processing of this request. An error element will be included
     *     populated with appropriate error information.
     **/
    public function getDocument($data);

    /**
     * HTTP operations: POST
     * URI: /documents
     *
     * The POST operation on the “/documents” resource will create a
     * new document using the information supplied in the document
     * element contained in the POST body.  A successful operation
     * will return the new document resource.  This operation has
     * restricted access for clients and is made available by the
     * server based on access control permissions.
     *
     * Once a document has been successfully created on the server,
     * the server will immediately send notifications to all
     * subscriptions with filter criteria matching the document.
     *
     * HTTP Parameters:
     *
     * Content-Type - Identifies the content type encoding of the POST
     * body contents.  Must be a content type supported by the protocol.
     *
     * Accept - Identifies the content type encoding requested for
     * the returned results. Must be a content type supported by the
     * protocol.
     *
     * If-Modified-Since - Constrains the GET request to return only
     * those documents that have been created or updated since the
     * time specified in this parameter.
     *
     * Body Parameters:
     *
     * document - The document to add to the document space of the
     * local provider.
     *
     * Returns (code, element):
     *
     * 201 document
     *     Returns a copy of the new document resource created as the
     *     result of a successful operation.  The HTTP Location header
     *     field will contain the direct URI reference of the new
     *     document resource.  It will be structured using the URI
     *     template $root/documents/{nsa}/{type}/{id}.
     *
     * 400 error
     *     Returned if a client specifies an invalid request.  An error
     *     element will be included populated with appropriate error
     *     information.
     *
     * 403 error
     *     The server understood the request, but is refusing to fulfill
     *     it. Authorization will not help and the request SHOULD NOT
     *     be repeated.  An error element will be included populated
     *     with appropriate error information.
     *
     * 409 error
     *     A document already exists with the same name (nsa/type/id).
     *     An update of an existing document should use the PUT
     *     operation.
     *
     * 500 error
     *     Returned if an internal server error occurred during the
     *     processing of this request. An error element will be
     *     included populated with appropriate error information.
     **/
    public function addDocument($data);

    /**
     * HTTP operations: PUT
     * URI: /documents/{nsa}/{type}/{id}
     *
     * The PUT operation on the “/documents/{nsa}/{type}/{id}” resource
     * will allow a client to edit the document corresponding to the
     * identifier {id}, using the information supplied in the document
     * element contained in the PUT body.  A successful operation will
     * return the modified document and trigger any associated
     * notifications within the NSA.
     *
     * A document is deleted from the document space by updating it’s
     * expire date to a reasonably short period in the future.  This
     * updated document will get propagated throughout the document
     * space and then expire, removing it from the space.
     *
     *  HTTP Parameters:
     *
     *  Content-Type - Identifies the content type encoding of the PUT
     *  body contents.  Must be a content type supported by the
     *  protocol.
     *
     *  Accept - Identifies the content type encoding requested for
     *  the returned results. Must be a content type supported by the
     *  protocol.
     *
     *  Body Parameters:
     *
     *  document - The document to update in the document space of the
     *  local provider. The PUT request must contain the document
     *  element containing the existing parameters of the document
     *  resource if they were not modified, as well as any new/edited
     *  values.
     *
     *  Returns (code, element):
     *
     *  200 document
     *     Returns a copy of the modified document resource as the
     *     result of a successful operation.
     *
     *  400 error
     *     Returned if a client specifies an invalid request.  An
     *     error element will be included populated with appropriate
     *     error information.
     *
     *  403 error
     *     The server understood the request, but is refusing to fulfill
     *     it. Authorization will not help and the request SHOULD NOT be
     *     repeated.  An error element will be included populated with
     *     appropriate error information.
     *
     *  404 error
     *     Returned if the requested document was not found.  An error
     *     element will be included populated with appropriate error
     *     information.
     *
     *  500 error
     *     Returned if an internal server error occurred during the
     *     processing of this request. An error element will be included
     *     populated with appropriate error information.
     **/
    public function setDocument($data);

    /**
     * The local element models a list of documents from the document
     * space published by the local provider NSA.
     *
     * HTTP operations: GET
     * URI: /local/{type}
     *
     * The local element contains document resources published by the
     * local provider, or a subset of documents based on supplied query
     * parameters.  Zero or more document instances will be returned in
     * a local element.
     *
     * A client can perform a GET operation on the special “/local” URI
     * when it would like to discover all documents associated with the
     * local provider NSA.  This operation is equivalent to performing a
     * GET operation on the URI “/documents/{nsa}”, however, for “/local”
     * the client is not required to have previous knowledge of the
     * provider NSA identifier.
     *
     * The URI template “/local/{type}” can be used as an alternative to,
     * or in conjunction with, the use of query parameters.  Performing
     * a GET on “/local/{type}/” will return all documents of {type}
     * associated with the local NSA.
     *
     * HTTP Parameters:
     *
     * Accept - Identifies the content type encoding requested for
     * the returned results. Must be a content type supported by the
     * protocol.
     *
     * If-Modified-Since - Constrains the GET request to return only
     * those documents that have been created or updated since the
     * time specified in this parameter.
     *
     * Query Parameters:
     *
     * id (string) - Return all document resources containing the
     * specified Id.
     *
     * type (string) - Return all document resources containing the
     * specified type. Cannot be used if the {type} URI component is
     * provided.
     *
     * summary (none) - Will return summary results of any documents
     * matching the query criteria.  Summary results includes all
     * document meta-data but not the signature or document contents.
     *
     * Returns (code, element):
     *
     * 200 local
     *     Return all document resources matching the query in a
     *     documents element.  If no documents match the query,
     *     then an empty documents element is returned.
     *
     * 304   None
     *     Successful operation where there were no changes to any
     *     document resources matching the query filter given the
     *     If-Modified-Since criteria. Returns no message body.
     *
     * 400 error
     *     Returned if a client specifies an invalid request. An error
     *     element will be included populated with appropriate error
     *     information.
     *
     * 500 error
     *     Returned if an internal server error occurred during the
     *     processing of this request. An error element will be included
     *     populated with appropriate error information.
     **/
    public function getLocal($data);
}
    
?>