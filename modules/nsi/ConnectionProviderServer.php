<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\nsi;

/**
 * Elemento responsável por receber e gerenciar as mensagens
 * SOAP enviadas por Requesters, como definido pelo protocolo
 * NSI Connection Service 2.0.
 *
 * A documentação de cada função é baseada no WSDL oficial do
 * protocolo (https://github.com/BandwidthOnDemand/bod-nsi). 
 *
 * @author Maurício Quatrin Guerreiro
 */
interface ConnectionProviderServer {
    
    /**
     * The reserve message is sent from a Requester NSA to a Provider
     * NSA when a new reservation is being requested, or a modification
     * to an existing reservation is required. The reserveResponse
     * indicates that the Provider NSA has accepted the reservation
     * request for processing and has assigned it the returned
     * connectionId. A reserveConfirmed or reserveFailed message will
     * be sent asynchronously to the Requester NSA when reserve 
     * operation has completed processing.
     **/
    public function reserve($response);
    
    /**
     * The reserveCommit message is sent from a Requester NSA to a
     * Provider NSA when a reservation or modification to an existing
     * reservation is being committed. The reserveCommitACK indicates
     * that the Provider NSA has accepted the modify request for
     * processing. A reserveCommitConfirmed or reserveCommitFailed message
     * will be sent asynchronously to the Requester NSA when reserve
     * or modify processing has completed.
     **/
    public function reserveCommit($response);

    /**
     *  The reserveAbort message is sent from a Requester NSA to a
     * Provider NSA when a cancellation to an existing reserve or
     * modify operation is being requested. The reserveAbortACK
     * indicates that the Provider NSA has accepted the reserveAbort
     * request for processing. A reserveAbortConfirmed or
     * reserveAbortFailed message will be sent asynchronously to the
     * Requester NSA when reserveAbort processing has completed.
     **/
    public function reserveAbort($response);

    /**
     *  The provision message is sent from a Requester NSA to a Provider
     * NSA when an existing reservation is to be transitioned into a
     * provisioned state. The provisionACK indicates that the Provider
     * NSA has accepted the provision request for processing. A
     * provisionConfirmed message will be sent asynchronously to the
     * Requester NSA when provision processing has completed.  There is
     * no associated Failed message for this operation.
     **/
    public function provision($response);

    /**
     * The release message is sent from a Requester NSA to a Provider
     * NSA when an existing reservation is to be transitioned into a
     * released state. The releaseACK indicates that the Provider NSA
     * has accepted the release request for processing. A
     * releaseConfirmed message will be sent asynchronously to the
     * Requester NSA when release processing has completed.  There is
     * no associated Failed message for this operation.
     **/
    public function release($response);

    /**
     * The terminate message is sent from a Requester NSA to a Provider
     * NSA when an existing reservation is to be terminated. The
     * terminateACK indicates that the Provider NSA has accepted the
     * terminate request for processing. A terminateConfirmed or
     * terminateFailed message will be sent asynchronously to the Requester
     * NSA when terminate processing has completed.
     **/
    public function terminate($response);

    /**
     * The querySummary message is sent from a Requester NSA to a
     * Provider NSA to determine the status of existing reservations.
     * The querySummaryACK indicates that the target NSA has
     * accepted the querySummary request for processing. A
     * querySummaryConfirmed or querySummaryFailed message will be
     * sent asynchronously to the requesting NSA when querySummary
     * processing has completed.
     **/
    public function querySummary($response);

    /**
     * The querySummarySync message can be sent from a Requester NSA
     * to determine the status of existing reservations on the Provider
     * NSA. The querySummarySync is a synchronous operation that will
     * block until the results of the query operation have been
     * collected.  These results will be returned in the SOAP
     * response.
     **/
    public function querySummarySync($response);

    /**
     * The queryRecursive message can be sent from either a Provider or
     * Requester NSA to determine the status of existing reservations.
     * The queryRecursiveACK indicates that the target NSA has accepted
     * the queryRecursive request for processing. A queryRecursiveConfirmed
     * or queryRecursiveFailed message will be sent asynchronously to the
     * requesting NSA when query processing has completed.
     **/
    public function queryRecursive($response);

    /**
     * The queryNotification message is sent from a Requester NSA
     * to a Provider NSA to retrieve notifications against an existing
     * reservation residing on the Provider NSA. QueryNotification is an
     * asynchronous operation that will return results of the operation
     * to the Requester NSA's SOAP endpoint specified in the NSI header
     * replyTo field.
     **/
    public function queryNotification($response);

    /**
     * The queryNotificationSync message can be sent from a Requester NSA
     * to notifications against an existing reservations on the Provider
     * NSA. The queryNotificationSync is a synchronous operation that
     * will block until the results of the query operation have been
     * collected.  These results will be returned in the SOAP response.
     **/
    public function queryNotificationSync($response);

    /**
     * The queryResult message is sent from a Requester NSA to a Provider
     * NSA to retrieve operation results against an existing reservation
     * residing on the Provider NSA. QueryResult is an asynchronous
     * operation that will return results of the operation to the Requester
     * NSA's SOAP endpoint specified in the NSI header replyTo field.
     **/
    public function queryResult($response);

    /**
     * The queryResultSync message can be sent from a Requester NSA
     * to a Provider NASA to retrieve operation results against an
     * existing reservation on the Provider NSA. The queryResultSync
     * is a synchronous operation that will block until the results
     * of the query operation have been collected.  These results
     * will be returned in the SOAP response.
     **/
    public function queryResultSync($response);
}
    
?>