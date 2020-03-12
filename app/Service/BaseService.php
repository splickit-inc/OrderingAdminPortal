<?php
/**
 * Created by PhpStorm.
 * User: diego.rodriguez
 * Date: 10/12/17
 * Time: 11:00 AM
 */

namespace App\Service;
use Illuminate\Support\Facades\Log;


abstract class BaseService {
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * Adds an error to the error array
     * @param $error
     */
    protected function addError($error) {
        array_push($this->errors, $error);
    }

    /**
     * Gets the error array
     * @return array
     */
    public function errors() {
        return $this->errors;
    }

    protected function clearErrors(){
        $this->errors = [];
    }

    protected function safeExec(callable $func, callable $onError = null) {
        $this->clearErrors();
        try {
            $func();
            return true;
        } catch (\Exception $e) {
            Log::error($e);
            $this->addError($e->getMessage());
            if ($onError != null) {
                $onError();
            }
        }
        return false;
    }
}