<?php
namespace CPA005;

class Request
{

    const DEBIT = 'D';
    const CREDIT = 'C';

    /**
     * Debit transactions
     *
     * @var array transaction objects to process into batch
     */
    private $transactions;

    /**
     * @var string string representition of payment records
     */
    private $payment_records = '';

    /* rbc client number */
    private $client_number;

    /* company name that will display on the customer's bank statement */
    private $short_company_name;
    private $long_company_name;

    /* */
    private $processing_centre;

    /* record count - one record per 6 transactions */
    private $record_count = 0;

    /**
     * file transmission routing record
     *
     * @var string $file_routing_record
     */
    private $file_routing_record = '$$AAPACPA1464[PROD[NL$$'. PHP_EOL;

    /* file creation number */
    private $file_creation_number;

    /* file creation date */
    private $file_creation_date;

    /* default currency code for all transactions */
    private $currency_code = 'CAD';

    /* total amount of all debits, in pennies */
    private $total_amount = 0;

    public function __construct()
    {
        $this->file_creation_number = rand(0, 9999);
        $this->file_creation_date = sprintf("%06d", strftime("%y%j"));

        return $this;
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
        # if the property doesn't exist, throw
        if (! property_exists($this, $var)) {
            throw new \RuntimeException("property \"{$var}\" does not exist");
        }

        # if there's a $this->validate_$var() function, run it before we set
        if (method_exists($this, "validate_{$var}")) {
            call_user_func(array($this, "validate_{$var}"), $value);
        }

        $this->$var = $value;
    }

    private function validate_file_creation_number($value)
    {
        if (! preg_match('/^\d{4}$/', $value)) {
            throw new \InvalidArgumentException("File Creation Number must be 4 digits");
        }
    }

    private function validate_client_number($value)
    {
        if (! preg_match('/^\d{10}$/', $value)) {
            throw new \InvalidArgumentException("Client Number must be 10 digits");
        }
    }

    private function validate_processing_centre($centre)
    {
        if (! ProcessingCentre::valid($centre)) {
            throw new \InvalidArgumentException('Invalid Processing Centre');
        }
    }

    private function validate_short_company_name($value)
    {
        if (! preg_match('/^[\w\s]{1,15}$/', $value)) {
            throw new \InvalidArgumentException("Short Company Name must be less than 15 characters");
        }
    }

    private function validate_long_company_name($value)
    {
        if (! preg_match('/^[\w\s]{1,30}$/', $value)) {
            throw new \InvalidArgumentException("Long Company Name must be less than 30 characters");
        }
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


    public function add_transaction(Transaction $t)
    {
        $t->validate();

        $this->transactions[] = $t;
        return $this;
    }

    public function dump()
    {
        $this->validate();

        $dump  = $this->file_routing_record;
        $dump .= $this->header_record();
        $dump .= $this->payment_records();
        $dump .= $this->footer_record();

        return $dump;
    }


    /**
     * Create a CPA005 file header record
     *
     * @return string header record
     */
    private function header_record()
    {
        return join(array(
            'A',                                            # record type - always char 'A'
            sprintf("%09d", (int)1),                        # initial record count - always '000000001'
            sprintf("%-10s", $this->client_number),             # client number
            sprintf("%-4s", $this->file_creation_number),   # file creation number
            sprintf("%06d", $this->file_creation_date),         # file creation date - year (017) + day of year (125)
            sprintf("%5s", $this->processing_centre),       # processing centre id
            str_repeat(' ', 20),                            # reserved - leave blank
            sprintf("%3s", $this->currency_code),           # currency code - USD/CAD or blank for default
            str_repeat(' ', 1406),                          # filler - leave blank
            PHP_EOL
        ));
    }

    /**
     * generate the payment record block from the transactions
     *
     * @return string payment record block of CPA005 file
     */
    private function payment_records()
    {
        $segment_count = 0;
        $this->total_amount = 0;

        foreach ($this->transactions as $t) {
            if ($segment_count == 0) {
                $this->record_count++;

                $record_header = join(array(
                    'D',                                            # record type - always char 'D'
                    sprintf("%09d", $this->record_count),           # record count - one record per 6 segments
                    sprintf("%-10s", $this->client_number),             # client number
                    sprintf("%-4s", $this->file_creation_number),   # file creation number
                ));

                $this->payment_records .= $record_header;
            }

            $segment = join(array(
                sprintf("%03d", $t->transaction_code),              # CPA transaction code
                sprintf("%010d", $t->amount),                       # amount of transaction, decimals removed
                sprintf("%06d", $this->file_creation_date),             # file creation date
                sprintf("%04d", $t->institution),                   # debitor's institution code
                sprintf("%05d", $t->transit),                       # debitor's transit number
                sprintf("%-12.12s", $t->account),                   # debitor's account number
                str_repeat('0', 22),                                # reserved 1
                str_repeat('0', 3),                                 # reserved 2
                sprintf("%-15.15s", $this->short_company_name),         # short company name
                sprintf("%-30.30s", $t->customer_name),                 # debitors's full name
                sprintf("%-30.30s", $this->long_company_name),      # long company name
                sprintf("%-10.10s", $this->client_number),          # client number
                sprintf("%019d", $t->identifier),                   # debitor's identifier
                str_repeat('0', 9),                                     # reserved 3
                str_repeat(' ', 12),                                # reserved 4
                str_repeat(' ', 15),                                # client sundry information
                str_repeat(' ', 22),                                # reserved 5
                str_repeat(' ', 2),                                     # reserved 6
                str_repeat(' ', 11)                                     # reserved 7
            ));

            $this->payment_records .= $segment;

            # keep track of the total amount of all transactions
            $this->total_amount += $t->amount;

            $segment_count++;

            # if we're at the 6th segment, end the line and reset the segment count
            if ($segment_count == 6) {
                $this->payment_records .= PHP_EOL;
                $segment_count = 0;
            }

            # and start the loop again
        }

        # once we've looped through all transactions, end the line if we need to
        if (($segment_count != 0) and ($segment_count < 6)) {
            $this->payment_records .= PHP_EOL;
        }

        return $this->payment_records;
    }

    private function footer_record()
    {
        return join(array(
            'Z',                                                    # record type - always char 'Z'
            sprintf("%09d", ceil(count($this->transactions)/6)+2),  # record count, including header+footer
            sprintf("%-10s", $this->client_number),                 # client number
            sprintf("%-4s", $this->file_creation_number),           # file creation number
            sprintf("%014d", $this->total_amount),                  # total amount to debit, decimals removed
            sprintf("%08d", count($this->transactions)),            # transaction count
            str_repeat(0, 14),                                      # reserved 1
            str_repeat(0, 8),                                       # reserved 2
            str_repeat(0, 1396),                                    # padding
            PHP_EOL
        ));
    }
}
