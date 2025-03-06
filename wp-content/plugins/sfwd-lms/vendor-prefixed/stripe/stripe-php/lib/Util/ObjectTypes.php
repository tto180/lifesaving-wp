<?php

namespace StellarWP\Learndash\Stripe\Util;

class ObjectTypes
{
    /**
     * @var array Mapping from object types to resource classes
     */
    const mapping =
        [
            \StellarWP\Learndash\Stripe\Collection::OBJECT_NAME => \StellarWP\Learndash\Stripe\Collection::class,
            \StellarWP\Learndash\Stripe\Issuing\CardDetails::OBJECT_NAME => \StellarWP\Learndash\Stripe\Issuing\CardDetails::class,
            \StellarWP\Learndash\Stripe\SearchResult::OBJECT_NAME => \StellarWP\Learndash\Stripe\SearchResult::class,
            \StellarWP\Learndash\Stripe\File::OBJECT_NAME_ALT => \StellarWP\Learndash\Stripe\File::class,
            // The beginning of the section generated from our OpenAPI spec
            \StellarWP\Learndash\Stripe\Account::OBJECT_NAME => \StellarWP\Learndash\Stripe\Account::class,
            \StellarWP\Learndash\Stripe\AccountLink::OBJECT_NAME => \StellarWP\Learndash\Stripe\AccountLink::class,
            \StellarWP\Learndash\Stripe\AccountSession::OBJECT_NAME => \StellarWP\Learndash\Stripe\AccountSession::class,
            \StellarWP\Learndash\Stripe\ApplePayDomain::OBJECT_NAME => \StellarWP\Learndash\Stripe\ApplePayDomain::class,
            \StellarWP\Learndash\Stripe\Application::OBJECT_NAME => \StellarWP\Learndash\Stripe\Application::class,
            \StellarWP\Learndash\Stripe\ApplicationFee::OBJECT_NAME => \StellarWP\Learndash\Stripe\ApplicationFee::class,
            \StellarWP\Learndash\Stripe\ApplicationFeeRefund::OBJECT_NAME => \StellarWP\Learndash\Stripe\ApplicationFeeRefund::class,
            \StellarWP\Learndash\Stripe\Apps\Secret::OBJECT_NAME => \StellarWP\Learndash\Stripe\Apps\Secret::class,
            \StellarWP\Learndash\Stripe\Balance::OBJECT_NAME => \StellarWP\Learndash\Stripe\Balance::class,
            \StellarWP\Learndash\Stripe\BalanceTransaction::OBJECT_NAME => \StellarWP\Learndash\Stripe\BalanceTransaction::class,
            \StellarWP\Learndash\Stripe\BankAccount::OBJECT_NAME => \StellarWP\Learndash\Stripe\BankAccount::class,
            \StellarWP\Learndash\Stripe\Billing\Meter::OBJECT_NAME => \StellarWP\Learndash\Stripe\Billing\Meter::class,
            \StellarWP\Learndash\Stripe\Billing\MeterEvent::OBJECT_NAME => \StellarWP\Learndash\Stripe\Billing\MeterEvent::class,
            \StellarWP\Learndash\Stripe\Billing\MeterEventAdjustment::OBJECT_NAME => \StellarWP\Learndash\Stripe\Billing\MeterEventAdjustment::class,
            \StellarWP\Learndash\Stripe\Billing\MeterEventSummary::OBJECT_NAME => \StellarWP\Learndash\Stripe\Billing\MeterEventSummary::class,
            \StellarWP\Learndash\Stripe\BillingPortal\Configuration::OBJECT_NAME => \StellarWP\Learndash\Stripe\BillingPortal\Configuration::class,
            \StellarWP\Learndash\Stripe\BillingPortal\Session::OBJECT_NAME => \StellarWP\Learndash\Stripe\BillingPortal\Session::class,
            \StellarWP\Learndash\Stripe\Capability::OBJECT_NAME => \StellarWP\Learndash\Stripe\Capability::class,
            \StellarWP\Learndash\Stripe\Card::OBJECT_NAME => \StellarWP\Learndash\Stripe\Card::class,
            \StellarWP\Learndash\Stripe\CashBalance::OBJECT_NAME => \StellarWP\Learndash\Stripe\CashBalance::class,
            \StellarWP\Learndash\Stripe\Charge::OBJECT_NAME => \StellarWP\Learndash\Stripe\Charge::class,
            \StellarWP\Learndash\Stripe\Checkout\Session::OBJECT_NAME => \StellarWP\Learndash\Stripe\Checkout\Session::class,
            \StellarWP\Learndash\Stripe\Climate\Order::OBJECT_NAME => \StellarWP\Learndash\Stripe\Climate\Order::class,
            \StellarWP\Learndash\Stripe\Climate\Product::OBJECT_NAME => \StellarWP\Learndash\Stripe\Climate\Product::class,
            \StellarWP\Learndash\Stripe\Climate\Supplier::OBJECT_NAME => \StellarWP\Learndash\Stripe\Climate\Supplier::class,
            \StellarWP\Learndash\Stripe\ConfirmationToken::OBJECT_NAME => \StellarWP\Learndash\Stripe\ConfirmationToken::class,
            \StellarWP\Learndash\Stripe\ConnectCollectionTransfer::OBJECT_NAME => \StellarWP\Learndash\Stripe\ConnectCollectionTransfer::class,
            \StellarWP\Learndash\Stripe\CountrySpec::OBJECT_NAME => \StellarWP\Learndash\Stripe\CountrySpec::class,
            \StellarWP\Learndash\Stripe\Coupon::OBJECT_NAME => \StellarWP\Learndash\Stripe\Coupon::class,
            \StellarWP\Learndash\Stripe\CreditNote::OBJECT_NAME => \StellarWP\Learndash\Stripe\CreditNote::class,
            \StellarWP\Learndash\Stripe\CreditNoteLineItem::OBJECT_NAME => \StellarWP\Learndash\Stripe\CreditNoteLineItem::class,
            \StellarWP\Learndash\Stripe\Customer::OBJECT_NAME => \StellarWP\Learndash\Stripe\Customer::class,
            \StellarWP\Learndash\Stripe\CustomerBalanceTransaction::OBJECT_NAME => \StellarWP\Learndash\Stripe\CustomerBalanceTransaction::class,
            \StellarWP\Learndash\Stripe\CustomerCashBalanceTransaction::OBJECT_NAME => \StellarWP\Learndash\Stripe\CustomerCashBalanceTransaction::class,
            \StellarWP\Learndash\Stripe\CustomerSession::OBJECT_NAME => \StellarWP\Learndash\Stripe\CustomerSession::class,
            \StellarWP\Learndash\Stripe\Discount::OBJECT_NAME => \StellarWP\Learndash\Stripe\Discount::class,
            \StellarWP\Learndash\Stripe\Dispute::OBJECT_NAME => \StellarWP\Learndash\Stripe\Dispute::class,
            \StellarWP\Learndash\Stripe\Entitlements\ActiveEntitlement::OBJECT_NAME => \StellarWP\Learndash\Stripe\Entitlements\ActiveEntitlement::class,
            \StellarWP\Learndash\Stripe\Entitlements\Feature::OBJECT_NAME => \StellarWP\Learndash\Stripe\Entitlements\Feature::class,
            \StellarWP\Learndash\Stripe\EphemeralKey::OBJECT_NAME => \StellarWP\Learndash\Stripe\EphemeralKey::class,
            \StellarWP\Learndash\Stripe\Event::OBJECT_NAME => \StellarWP\Learndash\Stripe\Event::class,
            \StellarWP\Learndash\Stripe\ExchangeRate::OBJECT_NAME => \StellarWP\Learndash\Stripe\ExchangeRate::class,
            \StellarWP\Learndash\Stripe\File::OBJECT_NAME => \StellarWP\Learndash\Stripe\File::class,
            \StellarWP\Learndash\Stripe\FileLink::OBJECT_NAME => \StellarWP\Learndash\Stripe\FileLink::class,
            \StellarWP\Learndash\Stripe\FinancialConnections\Account::OBJECT_NAME => \StellarWP\Learndash\Stripe\FinancialConnections\Account::class,
            \StellarWP\Learndash\Stripe\FinancialConnections\AccountOwner::OBJECT_NAME => \StellarWP\Learndash\Stripe\FinancialConnections\AccountOwner::class,
            \StellarWP\Learndash\Stripe\FinancialConnections\AccountOwnership::OBJECT_NAME => \StellarWP\Learndash\Stripe\FinancialConnections\AccountOwnership::class,
            \StellarWP\Learndash\Stripe\FinancialConnections\Session::OBJECT_NAME => \StellarWP\Learndash\Stripe\FinancialConnections\Session::class,
            \StellarWP\Learndash\Stripe\FinancialConnections\Transaction::OBJECT_NAME => \StellarWP\Learndash\Stripe\FinancialConnections\Transaction::class,
            \StellarWP\Learndash\Stripe\Forwarding\Request::OBJECT_NAME => \StellarWP\Learndash\Stripe\Forwarding\Request::class,
            \StellarWP\Learndash\Stripe\FundingInstructions::OBJECT_NAME => \StellarWP\Learndash\Stripe\FundingInstructions::class,
            \StellarWP\Learndash\Stripe\Identity\VerificationReport::OBJECT_NAME => \StellarWP\Learndash\Stripe\Identity\VerificationReport::class,
            \StellarWP\Learndash\Stripe\Identity\VerificationSession::OBJECT_NAME => \StellarWP\Learndash\Stripe\Identity\VerificationSession::class,
            \StellarWP\Learndash\Stripe\Invoice::OBJECT_NAME => \StellarWP\Learndash\Stripe\Invoice::class,
            \StellarWP\Learndash\Stripe\InvoiceItem::OBJECT_NAME => \StellarWP\Learndash\Stripe\InvoiceItem::class,
            \StellarWP\Learndash\Stripe\InvoiceLineItem::OBJECT_NAME => \StellarWP\Learndash\Stripe\InvoiceLineItem::class,
            \StellarWP\Learndash\Stripe\Issuing\Authorization::OBJECT_NAME => \StellarWP\Learndash\Stripe\Issuing\Authorization::class,
            \StellarWP\Learndash\Stripe\Issuing\Card::OBJECT_NAME => \StellarWP\Learndash\Stripe\Issuing\Card::class,
            \StellarWP\Learndash\Stripe\Issuing\Cardholder::OBJECT_NAME => \StellarWP\Learndash\Stripe\Issuing\Cardholder::class,
            \StellarWP\Learndash\Stripe\Issuing\Dispute::OBJECT_NAME => \StellarWP\Learndash\Stripe\Issuing\Dispute::class,
            \StellarWP\Learndash\Stripe\Issuing\PersonalizationDesign::OBJECT_NAME => \StellarWP\Learndash\Stripe\Issuing\PersonalizationDesign::class,
            \StellarWP\Learndash\Stripe\Issuing\PhysicalBundle::OBJECT_NAME => \StellarWP\Learndash\Stripe\Issuing\PhysicalBundle::class,
            \StellarWP\Learndash\Stripe\Issuing\Token::OBJECT_NAME => \StellarWP\Learndash\Stripe\Issuing\Token::class,
            \StellarWP\Learndash\Stripe\Issuing\Transaction::OBJECT_NAME => \StellarWP\Learndash\Stripe\Issuing\Transaction::class,
            \StellarWP\Learndash\Stripe\LineItem::OBJECT_NAME => \StellarWP\Learndash\Stripe\LineItem::class,
            \StellarWP\Learndash\Stripe\LoginLink::OBJECT_NAME => \StellarWP\Learndash\Stripe\LoginLink::class,
            \StellarWP\Learndash\Stripe\Mandate::OBJECT_NAME => \StellarWP\Learndash\Stripe\Mandate::class,
            \StellarWP\Learndash\Stripe\PaymentIntent::OBJECT_NAME => \StellarWP\Learndash\Stripe\PaymentIntent::class,
            \StellarWP\Learndash\Stripe\PaymentLink::OBJECT_NAME => \StellarWP\Learndash\Stripe\PaymentLink::class,
            \StellarWP\Learndash\Stripe\PaymentMethod::OBJECT_NAME => \StellarWP\Learndash\Stripe\PaymentMethod::class,
            \StellarWP\Learndash\Stripe\PaymentMethodConfiguration::OBJECT_NAME => \StellarWP\Learndash\Stripe\PaymentMethodConfiguration::class,
            \StellarWP\Learndash\Stripe\PaymentMethodDomain::OBJECT_NAME => \StellarWP\Learndash\Stripe\PaymentMethodDomain::class,
            \StellarWP\Learndash\Stripe\Payout::OBJECT_NAME => \StellarWP\Learndash\Stripe\Payout::class,
            \StellarWP\Learndash\Stripe\Person::OBJECT_NAME => \StellarWP\Learndash\Stripe\Person::class,
            \StellarWP\Learndash\Stripe\Plan::OBJECT_NAME => \StellarWP\Learndash\Stripe\Plan::class,
            \StellarWP\Learndash\Stripe\PlatformTaxFee::OBJECT_NAME => \StellarWP\Learndash\Stripe\PlatformTaxFee::class,
            \StellarWP\Learndash\Stripe\Price::OBJECT_NAME => \StellarWP\Learndash\Stripe\Price::class,
            \StellarWP\Learndash\Stripe\Product::OBJECT_NAME => \StellarWP\Learndash\Stripe\Product::class,
            \StellarWP\Learndash\Stripe\ProductFeature::OBJECT_NAME => \StellarWP\Learndash\Stripe\ProductFeature::class,
            \StellarWP\Learndash\Stripe\PromotionCode::OBJECT_NAME => \StellarWP\Learndash\Stripe\PromotionCode::class,
            \StellarWP\Learndash\Stripe\Quote::OBJECT_NAME => \StellarWP\Learndash\Stripe\Quote::class,
            \StellarWP\Learndash\Stripe\Radar\EarlyFraudWarning::OBJECT_NAME => \StellarWP\Learndash\Stripe\Radar\EarlyFraudWarning::class,
            \StellarWP\Learndash\Stripe\Radar\ValueList::OBJECT_NAME => \StellarWP\Learndash\Stripe\Radar\ValueList::class,
            \StellarWP\Learndash\Stripe\Radar\ValueListItem::OBJECT_NAME => \StellarWP\Learndash\Stripe\Radar\ValueListItem::class,
            \StellarWP\Learndash\Stripe\Refund::OBJECT_NAME => \StellarWP\Learndash\Stripe\Refund::class,
            \StellarWP\Learndash\Stripe\Reporting\ReportRun::OBJECT_NAME => \StellarWP\Learndash\Stripe\Reporting\ReportRun::class,
            \StellarWP\Learndash\Stripe\Reporting\ReportType::OBJECT_NAME => \StellarWP\Learndash\Stripe\Reporting\ReportType::class,
            \StellarWP\Learndash\Stripe\ReserveTransaction::OBJECT_NAME => \StellarWP\Learndash\Stripe\ReserveTransaction::class,
            \StellarWP\Learndash\Stripe\Review::OBJECT_NAME => \StellarWP\Learndash\Stripe\Review::class,
            \StellarWP\Learndash\Stripe\SetupAttempt::OBJECT_NAME => \StellarWP\Learndash\Stripe\SetupAttempt::class,
            \StellarWP\Learndash\Stripe\SetupIntent::OBJECT_NAME => \StellarWP\Learndash\Stripe\SetupIntent::class,
            \StellarWP\Learndash\Stripe\ShippingRate::OBJECT_NAME => \StellarWP\Learndash\Stripe\ShippingRate::class,
            \StellarWP\Learndash\Stripe\Sigma\ScheduledQueryRun::OBJECT_NAME => \StellarWP\Learndash\Stripe\Sigma\ScheduledQueryRun::class,
            \StellarWP\Learndash\Stripe\Source::OBJECT_NAME => \StellarWP\Learndash\Stripe\Source::class,
            \StellarWP\Learndash\Stripe\SourceMandateNotification::OBJECT_NAME => \StellarWP\Learndash\Stripe\SourceMandateNotification::class,
            \StellarWP\Learndash\Stripe\SourceTransaction::OBJECT_NAME => \StellarWP\Learndash\Stripe\SourceTransaction::class,
            \StellarWP\Learndash\Stripe\Subscription::OBJECT_NAME => \StellarWP\Learndash\Stripe\Subscription::class,
            \StellarWP\Learndash\Stripe\SubscriptionItem::OBJECT_NAME => \StellarWP\Learndash\Stripe\SubscriptionItem::class,
            \StellarWP\Learndash\Stripe\SubscriptionSchedule::OBJECT_NAME => \StellarWP\Learndash\Stripe\SubscriptionSchedule::class,
            \StellarWP\Learndash\Stripe\Tax\Calculation::OBJECT_NAME => \StellarWP\Learndash\Stripe\Tax\Calculation::class,
            \StellarWP\Learndash\Stripe\Tax\CalculationLineItem::OBJECT_NAME => \StellarWP\Learndash\Stripe\Tax\CalculationLineItem::class,
            \StellarWP\Learndash\Stripe\Tax\Registration::OBJECT_NAME => \StellarWP\Learndash\Stripe\Tax\Registration::class,
            \StellarWP\Learndash\Stripe\Tax\Settings::OBJECT_NAME => \StellarWP\Learndash\Stripe\Tax\Settings::class,
            \StellarWP\Learndash\Stripe\Tax\Transaction::OBJECT_NAME => \StellarWP\Learndash\Stripe\Tax\Transaction::class,
            \StellarWP\Learndash\Stripe\Tax\TransactionLineItem::OBJECT_NAME => \StellarWP\Learndash\Stripe\Tax\TransactionLineItem::class,
            \StellarWP\Learndash\Stripe\TaxCode::OBJECT_NAME => \StellarWP\Learndash\Stripe\TaxCode::class,
            \StellarWP\Learndash\Stripe\TaxDeductedAtSource::OBJECT_NAME => \StellarWP\Learndash\Stripe\TaxDeductedAtSource::class,
            \StellarWP\Learndash\Stripe\TaxId::OBJECT_NAME => \StellarWP\Learndash\Stripe\TaxId::class,
            \StellarWP\Learndash\Stripe\TaxRate::OBJECT_NAME => \StellarWP\Learndash\Stripe\TaxRate::class,
            \StellarWP\Learndash\Stripe\Terminal\Configuration::OBJECT_NAME => \StellarWP\Learndash\Stripe\Terminal\Configuration::class,
            \StellarWP\Learndash\Stripe\Terminal\ConnectionToken::OBJECT_NAME => \StellarWP\Learndash\Stripe\Terminal\ConnectionToken::class,
            \StellarWP\Learndash\Stripe\Terminal\Location::OBJECT_NAME => \StellarWP\Learndash\Stripe\Terminal\Location::class,
            \StellarWP\Learndash\Stripe\Terminal\Reader::OBJECT_NAME => \StellarWP\Learndash\Stripe\Terminal\Reader::class,
            \StellarWP\Learndash\Stripe\TestHelpers\TestClock::OBJECT_NAME => \StellarWP\Learndash\Stripe\TestHelpers\TestClock::class,
            \StellarWP\Learndash\Stripe\Token::OBJECT_NAME => \StellarWP\Learndash\Stripe\Token::class,
            \StellarWP\Learndash\Stripe\Topup::OBJECT_NAME => \StellarWP\Learndash\Stripe\Topup::class,
            \StellarWP\Learndash\Stripe\Transfer::OBJECT_NAME => \StellarWP\Learndash\Stripe\Transfer::class,
            \StellarWP\Learndash\Stripe\TransferReversal::OBJECT_NAME => \StellarWP\Learndash\Stripe\TransferReversal::class,
            \StellarWP\Learndash\Stripe\Treasury\CreditReversal::OBJECT_NAME => \StellarWP\Learndash\Stripe\Treasury\CreditReversal::class,
            \StellarWP\Learndash\Stripe\Treasury\DebitReversal::OBJECT_NAME => \StellarWP\Learndash\Stripe\Treasury\DebitReversal::class,
            \StellarWP\Learndash\Stripe\Treasury\FinancialAccount::OBJECT_NAME => \StellarWP\Learndash\Stripe\Treasury\FinancialAccount::class,
            \StellarWP\Learndash\Stripe\Treasury\FinancialAccountFeatures::OBJECT_NAME => \StellarWP\Learndash\Stripe\Treasury\FinancialAccountFeatures::class,
            \StellarWP\Learndash\Stripe\Treasury\InboundTransfer::OBJECT_NAME => \StellarWP\Learndash\Stripe\Treasury\InboundTransfer::class,
            \StellarWP\Learndash\Stripe\Treasury\OutboundPayment::OBJECT_NAME => \StellarWP\Learndash\Stripe\Treasury\OutboundPayment::class,
            \StellarWP\Learndash\Stripe\Treasury\OutboundTransfer::OBJECT_NAME => \StellarWP\Learndash\Stripe\Treasury\OutboundTransfer::class,
            \StellarWP\Learndash\Stripe\Treasury\ReceivedCredit::OBJECT_NAME => \StellarWP\Learndash\Stripe\Treasury\ReceivedCredit::class,
            \StellarWP\Learndash\Stripe\Treasury\ReceivedDebit::OBJECT_NAME => \StellarWP\Learndash\Stripe\Treasury\ReceivedDebit::class,
            \StellarWP\Learndash\Stripe\Treasury\Transaction::OBJECT_NAME => \StellarWP\Learndash\Stripe\Treasury\Transaction::class,
            \StellarWP\Learndash\Stripe\Treasury\TransactionEntry::OBJECT_NAME => \StellarWP\Learndash\Stripe\Treasury\TransactionEntry::class,
            \StellarWP\Learndash\Stripe\UsageRecord::OBJECT_NAME => \StellarWP\Learndash\Stripe\UsageRecord::class,
            \StellarWP\Learndash\Stripe\UsageRecordSummary::OBJECT_NAME => \StellarWP\Learndash\Stripe\UsageRecordSummary::class,
            \StellarWP\Learndash\Stripe\WebhookEndpoint::OBJECT_NAME => \StellarWP\Learndash\Stripe\WebhookEndpoint::class,
            // The end of the section generated from our OpenAPI spec
        ];
}
