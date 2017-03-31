<?php
namespace CPA005;

use \CPA005\TransactionCode;

class Transaction
{
    private $transaction_code = null;
    private $institution = null;
    private $transit = null;
    private $account = null;
    private $amount = null;
    private $customer_name = null;
    private $identifier = null;

    public function __construct()
    {
    }

    public function __get($var)
    {
        if (! property_exists($this, $var)) {
            throw new \RuntimeException("property \"{$var}\" does not exist");
        }

        return $this->$var;
    }

    public function __set($var, $value)
    {
        if (! property_exists($this, $var)) {
            throw new \RuntimeException("property \"{$var}\" does not exist");
        }

        if (method_exists($this, "validate_{$var}")) {
            call_user_func(array($this, "validate_{$var}"), $value);
        }

        $this->$var = $value;
    }

    /**
     * validate our transaction object - make sure all of our object parameters have been filled out
     */
    public function validate()
    {
        foreach (get_object_vars($this) as $key => $var) {
            if (is_null($var)) {
                throw new \RuntimeException("Request object is incomplete! Missing \"{$key}\"");
            }
        }
    }

    public function validate_transaction_code($value)
    {
        if (! \CPA005\TransactionCode::valid($value)) {
            throw new \InvalidArgumentException('Invalid Transaction Code');
        }
    }

    public function validate_institution($value)
    {
        if (! preg_match('/\d{5}/', $value)) {
            throw new \InvalidArgumentException("Institution must be 5 digits");
        }
    }

    public function validate_transit($value)
    {
        if (! preg_match('/\d{3}/', $value)) {
            throw new \InvalidArgumentException("Transit must be 3 digits");
        }
    }

    public function validate_account($value)
    {
        if (! preg_match('/\d{1,12}/', $value)) {
            throw new \InvalidArgumentException("Account must be no longer than 12 digits");
        }
    }

    public function validate_customer_name($value)
    {
        if (! preg_match('/[\w\s]{1,30}/', $value)) {
            throw new \InvalidArgumentException("Customer Name must be no longer than 30 characters");
        }
    }

    public function validate_identifier($value)
    {
        if (! preg_match('/\d{0,19}/', $value)) {
            throw new \InvalidArgumentException("Identifier must be less than 19 digits");
        }
    }

    public function validate_amount($value)
    {
        if (! preg_match('/\d{1,10}/', $value)) {
            throw new \InvalidArgumentException("Amount must be less than 10 digits");
        }
    }
}
