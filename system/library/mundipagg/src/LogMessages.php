<?php

namespace Mundipagg;

class LogMessages
{
    const LOG_HEADER = 'Mundipagg Opencart V0.1 |';
    
    /** Error messages */
    const UNKNOWN_ORDER_STATUS = 'Unknown order status received';
    const INVALID_CREDIT_CARD_REQUEST = 'Invalid credit card request';
    const MALFORMED_REQUEST = 'Malformed request';
    const ORDER_ID_NOT_FOUND = 'Order id not found';
    const API_REQUEST_FAIL = 'Mundipagg api request failed';
    const UNABLE_TO_CREATE_ORDER = 'Unable to create Order in Mundipagg';
    const UNABLE_TO_CREATE_MUNDI_ORDER = 'Unable to create order in mundipagg_order';
    const UNKNOWN_WEBHOOK_TYPE = 'Unknown webhook type received';

    /** Debug Messages */
    const ORDER_NOT_FOUND_IN_ORDER_TABLE = 'Mundipagg order id not found in mundipagg_order table';

    /** Info Messages */
    const ORDER_ALREADY_UPDATED = 'Order already updated';
    const ORDER_STATUS_CHANGED = 'Order status changed';
    const ERROR_DURING_STATUS_UPDATE = 'Error during order status update';
    const REQUEST_INFO = 'Request information';
    const CREATE_ORDER_MUNDIPAGG_REQUEST = 'Create a Mundipagg order';
    const CREATE_ORDER_MUNDIPAGG_RESPONSE = 'Response from Mundipagg';
    const ORDER_CREATED = 'Received an order created';

    /** Warning Messages */
    const UNABLE_TO_SAVE_TRANSACTION = 'Unable to save transaction into mundipagg_transaction table';

    /** Webhook */
    const INVALID_WEBHOOK_REQUEST = 'Module received an invalid webhook request from MundiPagg';
    const INVALID_WEBHOOK_ORDER_STATUS = 'Module received an unknown webhook order status from Mundipagg';
    const INVALID_WEBHOOK_CHARGE_STATUS = 'Module received an unknown webhook charge status from Mundipagg';
    const CANNOT_SET_ORDER_STATUS = 'Unable to set order status';
    const CREATE_WEBHOOK_RECEIVED = 'WebHook create received';
}
