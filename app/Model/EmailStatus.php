<?php
/**
 * Created by PhpStorm.
 * User: diego.rodriguez
 * Date: 11/23/17
 * Time: 11:19 AM
 */

namespace App\Model;


class EmailStatus {
    const CREATED = 'created';
    const QUEUED = 'queued';
    const SENT = 'sent';
    const CANCELED = 'canceled';
    const ERROR = 'error';
}