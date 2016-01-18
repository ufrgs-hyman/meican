<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\nsi\connection;

/**
 * Interface a ser implementada por classes que representem 
 * o módulo servidor SOAP do protocolo NSI Connection Service Requester 2.0
 *
 * @author Maurício Quatrin Guerreiro
 */
interface RequesterSoapServer {
    
    /**
     * This reserveConfirmed message is sent from a Provider NSA to
     * Requester NSA as an indication of a successful reservation. This
     * is in response to an original reserve request from the
     * associated Requester NSA.
     **/
    public function reserveConfirmed($response);
    
    /**
     * This reserveFailed message is sent from a Provider NSA to
     * Requester NSA as an indication of a reserve failure. This
     * is in response to an original reserve request from the
     * associated Requester NSA.
     **/
    public function reserveFailed($response);

    /**
     * This reserveCommitConfirmed message is sent from a Provider NSA to
     * Requester NSA as an indication of a successful reserveCommit request.
     * This is in response to an original reserveCommit request from the
     * associated Requester NSA.
     **/
    public function reserveCommitConfirmed($response);

    /**
     * This reserveCommitFailed message is sent from a Provider NSA to
     * Requester NSA as an indication of a modify failure. This
     * is in response to an original modify request from the
     * associated Requester NSA.
     **/
    public function reserveCommitFailed($response);

    /**
     * This reserveAbortConfirmed message is sent from a Provider NSA to
     * Requester NSA as an indication of a successful reserveAbort.
     * This is in response to an original reserveAbort request from the
     * associated Requester NSA.
     **/
    public function reserveAbortConfirmed($response);

    /**
     * This provisionConfirmed message is sent from a Provider NSA to
     * Requester NSA as an indication of a successful provision operation.
     * This is in response to an original provision request from the
     * associated Requester NSA.
     **/
    public function provisionConfirmed($response);

    /**
     * This releaseConfirmed message is sent from a Provider NSA to
     * Requester NSA as an indication of a successful release operation.
     * This is in response to an original release request from the
     * associated Requester NSA.
     **/
    public function releaseConfirmed($response);

    /**
     * This terminateConfirmed message is sent from a Provider NSA to
     * Requester NSA as an indication of a successful terminate operation.
     * This is in response to an original terminate request from the
     * associated Requester NSA.
     **/
    public function terminateConfirmed($response);

    /**
     * This querySummaryConfirmed message is sent from the target NSA to
     * requesting NSA as an indication of a successful querySummary
     * operation. This is in response to an original querySummary request
     * from the associated Requester NSA.
     **/
    public function querySummaryConfirmed($response);

    /**
     * This queryRecursiveConfirmed message is sent from the Provider NSA to
     * Requester NSA as an indication of a successful queryRecursive
     * operation. This is in response to an original queryRecursive request
     * from the associated Requester NSA.
     **/
    public function queryRecursiveConfirmed($response);

    /**
     * This queryNotificationConfirmed message is sent from the Provider NSA to
     * Requester NSA as an indication of a successful queryNotification
     * operation. This is in response to an original queryNotification request
     * from the associated Requester NSA.
     **/
    public function queryNotificationConfirmed($response);

    /**
     * This queryResultConfirmed message is sent from the Provider NSA to
     * Requester NSA as an indication of a successful queryResult operation.
     * This is in response to an original queryResult request from the
     * associated Requester NSA.
     **/
    public function queryResultConfirmed($response);

    /**
     * The error message is sent from a Provider NSA to Requester
     * NSA as an indication of the occurence of an error condition.
     * This  is in response to an original request from the associated
     * Requester NSA.
     **/
    public function error($response);

    /**
     * An autonomous error message issued from a Provider NSA to Requester
     * NSA.  The acknowledgment indicates that the Requester NSA has
     * accepted the notification request for processing. There are no
     * associated confirmed or failed messages.
     **/
    public function errorEvent($response);

    /**
     * An autonomous message issued from a Provider NSA to Requester
     * NSA.  The acknowledgment indicates that the Requester NSA has
     * accepted the notification request for processing. There are no
     * associated confirmed or failed messages.
     **/
    public function reserveTimeout($response);

    /**
     * An autonomous message issued from a Provider NSA to Requester
     * NSA.  The acknowledgment indicates that the Requester NSA has
     * accepted the notification request for processing. There are no
     * associated confirmed or failed messages.
     **/
    public function dataPlaneStateChange($response);
    
    /**
     * An autonomous message issued from a Provider NSA to Requester
     * NSA.  The acknowledgment indicates that the Requester NSA has
     * accepted the notification request for processing. There are no
     * associated confirmed or failed messages.
     **/
    public function messageDeliveryTimeout($response);
}
    
?>