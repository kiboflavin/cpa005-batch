<?php
namespace CPA005;

class TransactionCode {

	const PAYROLL_DEPOSIT                    = '200';
	const SPECIAL_PAYROLL                    = '201';
	const VACATION_PAYROLL                   = '202';
	const OVERTIME_PAYROLL                   = '203';
	const ADVANCE_PAYROLL                    = '204';
	const COMMISSION_PAYROLL                 = '205';
	const BONUS_PAYROLL                      = '206';
	const ADJUSTMENT_PAYROLL                 = '207';
	const PENSION                            = '230';
	const PRIVATE_PENSION                    = '233';
	const ANNUITY                            = '240';
	const DIVIDEND                           = '250';
	const COMMON_DIVIDEND                    = '251';
	const PREFERRED_DIVIDEND                 = '252';
	const INVESTMENT                         = '260';
	const MUTUAL_FUNDS                       = '261';
	const RSP_SPOUSAL_CONTRIBUTIONS          = '265';
	const RESP_CONTRIBUTIONS                 = '266';
	const RSP_CONTRIBUTION                   = '271';
	const RETIREMENT_INCOME_FUND             = '272';
	const TAX_FREE_SAVINGS_ACCOUNT           = '273';
	const REGISTERED_DISABILITY_SAVINGS_PLAN = '274';
	const INTEREST                           = '280';
	const LOTTERY_PRIZE_PAYMENT              = '281';
	const INSURANCE                          = '330';
	const LIFE_INSURANCE                     = '331';
	const AUTO_INSURANCE                     = '332';
	const PROPERTY_INSURANCE                 = '333';
	const CASUALTY_INSURANCE                 = '334';
	const MORTGAGE_INSURANCE                 = '335';
	const HEALTH_INSURANCE                   = '336';
	const LOANS                              = '350';
	const PERSONAL_LOANS                     = '351';
	const DEALER_PLAN_LOANS                  = '352';
	const FARM_IMPROVEMENT_LOANS             = '353';
	const HOME_IMPROVEMENT_LOANS             = '354';
	const TERM_LOANS                         = '355';
	const INSURANCE_LOANS                    = '356';
	const MORTGAGE                           = '370';
	const RESIDENTIAL_MORTGAGE               = '371';
	const COMMERCIAL_MORTGAGE                = '372';
	const FARM_MORTGAGE                      = '373';
	const TAXES                              = '380';
	const INCOME_TAXES                       = '381';
	const SALES_TAXES                        = '382';
	const CORPORATE_TAXES                    = '383';
	const SCHOOL_TAXES                       = '384';
	const PROPERTY_TAXES                     = '385';
	const WATER_TAXES                        = '386';
	const RENT_LEASES                        = '400';
	const RESIDENTIAL_RENT_LEASES            = '401';
	const COMMERCIAL_RENT_LEASES             = '402';
	const EQUIPMENT_RENT_LEASES              = '403';
	const AUTOMOBILE_RENT_LEASES             = '404';
	const APPLIANCE_RENT_LEASES              = '405';
	const BILL_PAYMENT                       = '430';
	const TELEPHONE_BILL_PAYMENT             = '431';
	const GASOLINE_BILL_PAYMENT              = '432';
	const HYDRO_BILL_PAYMENT                 = '433';
	const CABLE_BILL_PAYMENT                 = '434';
	const FUEL_BILL_PAYMENT                  = '435';
	const UTILITY_BILL_PAYMENT               = '436';
	const INTERNET_ACCESS_PAYMENT            = '437';
	const WATER_BILL_PAYMENT                 = '438';
	const AUTO_BILL_PAYMENT                  = '439';
	const MISCELLANEOUS_PAYMENTS             = '450';
	const CUSTOMER_CHEQUES                   = '451';
	const EXPENSE_PAYMENT                    = '452';
	const ACCOUNTS_PAYABLE                   = '460';
	const FEES                               = '470';
	const DONATIONS                          = '480';

	/**
	 * Check whether the provided string is a valid CPA transaction code
	 * 
	 * @param  string $centre
	 * @return bool
	 */
	public static function valid($centre): bool
	{
		$reflection = new \ReflectionObject(new self);

		return in_array($centre, $reflection->getConstants());
	}
}