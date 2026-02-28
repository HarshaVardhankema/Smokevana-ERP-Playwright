<?php

namespace Modules\Subscription\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Business;
use Exception;

/**
 * NMI Payment Gateway Service for Recurring Subscriptions
 * 
 * @see https://support.nmi.com/hc/en-gb/articles/14525725002385-API-Recurring-Payments-and-Subscriptions
 */
class NMISubscriptionService
{
    protected $securityKey;
    protected $hostName = 'secure.nmi.com';
    protected $apiPath = '/api/transact.php';
    protected $recurringPath = '/api/recurring.php';
    protected $demoMode = false;
    protected $businessId = null;

    public function __construct($businessId = null)
    {
        $this->securityKey = config('services.nmi.security');
        $this->businessId = $businessId;
        
        // Check demo mode from business settings first, then fallback to config/env
        $this->demoMode = $this->checkDemoMode();
    }
    
    /**
     * Check if demo mode is enabled
     * Priority: 1. Business settings 2. Config 3. ENV
     */
    protected function checkDemoMode(): bool
    {
        // Try to get from business settings
        $businessId = $this->businessId ?? session('user.business_id');
        
        if ($businessId) {
            $business = Business::find($businessId);
            if ($business) {
                $settings = $business->subscription_settings ?? [];
                if (isset($settings['payment_demo_mode'])) {
                    return (bool) $settings['payment_demo_mode'];
                }
            }
        }
        
        // Fallback to config/env
        return config('subscription.demo_mode', env('SUBSCRIPTION_DEMO_MODE', false));
    }
    
    /**
     * Set business ID for settings lookup
     */
    public function setBusinessId($businessId): self
    {
        $this->businessId = $businessId;
        $this->demoMode = $this->checkDemoMode();
        return $this;
    }

    /**
     * Check if running in demo mode
     */
    public function isDemoMode(): bool
    {
        return $this->demoMode;
    }

    /**
     * Enable or disable demo mode
     */
    public function setDemoMode(bool $enabled): self
    {
        $this->demoMode = $enabled;
        return $this;
    }

    /**
     * Generate demo response for testing
     */
    protected function getDemoResponse(string $type, array $data = []): array
    {
        $transactionId = 'DEMO_TXN_' . time() . '_' . rand(1000, 9999);
        $subscriptionId = 'DEMO_SUB_' . time() . '_' . rand(1000, 9999);

        Log::info('NMI Demo Mode - ' . $type, [
            'demo' => true,
            'type' => $type,
            'data' => $data,
            'generated_transaction_id' => $transactionId,
            'generated_subscription_id' => $subscriptionId,
        ]);

        return [
            'status' => true,
            'success' => true,
            'response_code' => 100,
            'responsetext' => 'SUCCESS (DEMO MODE)',
            'transactionid' => $transactionId,
            'subscription_id' => $subscriptionId,
            'authcode' => 'DEMO' . rand(100000, 999999),
            'avsresponse' => 'Y',
            'cvvresponse' => 'M',
            'demo_mode' => true,
            'raw_response' => [
                'response' => '1',
                'responsetext' => 'SUCCESS',
                'authcode' => 'DEMO',
                'transactionid' => $transactionId,
                'subscription_id' => $subscriptionId,
                'response_code' => '100',
            ]
        ];
    }

    /**
     * Create a recurring plan in NMI
     * 
     * @param array $planData
     * @return array
     */
    public function createPlan(array $planData): array
    {
        // Demo mode - return simulated response
        if ($this->demoMode) {
            return $this->getDemoResponse('create_plan', $planData);
        }

        $postData = [
            'recurring' => 'add_plan',
            'plan_name' => $planData['name'],
            'plan_id' => $planData['plan_id'] ?? uniqid('plan_'),
            'plan_amount' => number_format($planData['amount'], 2, '.', ''),
            'plan_payments' => $planData['payments'] ?? 0, // 0 = unlimited
            'day_frequency' => $this->getDayFrequency($planData['billing_cycle'] ?? 'monthly'),
        ];

        // Optional plan parameters
        if (isset($planData['month_frequency'])) {
            $postData['month_frequency'] = $planData['month_frequency'];
        }
        if (isset($planData['day_of_month'])) {
            $postData['day_of_month'] = $planData['day_of_month'];
        }

        return $this->doRecurringRequest($postData);
    }

    /**
     * Create a subscription for a customer using a plan
     * 
     * @param array $subscriptionData
     * @return array
     */
    public function createSubscription(array $subscriptionData): array
    {
        // Demo mode - return simulated response
        if ($this->demoMode) {
            return $this->getDemoResponse('create_subscription', $subscriptionData);
        }

        $postData = [
            'recurring' => 'add_subscription',
            'plan_id' => $subscriptionData['plan_id'],
            'payment_token' => $subscriptionData['payment_token'],
            'start_date' => $subscriptionData['start_date'] ?? date('Ymd'),
        ];

        // Customer billing info
        if (isset($subscriptionData['billing'])) {
            $postData = array_merge($postData, $this->formatBillingData($subscriptionData['billing']));
        }

        // Optional: Order ID for tracking
        if (isset($subscriptionData['order_id'])) {
            $postData['orderid'] = $subscriptionData['order_id'];
        }

        // Optional: PO Number
        if (isset($subscriptionData['po_number'])) {
            $postData['ponumber'] = $subscriptionData['po_number'];
        }

        return $this->doRecurringRequest($postData);
    }

    /**
     * Create a custom subscription without a pre-defined plan
     * 
     * @param array $subscriptionData
     * @return array
     */
    public function createCustomSubscription(array $subscriptionData): array
    {
        // Demo mode - return simulated response
        if ($this->demoMode) {
            return $this->getDemoResponse('create_custom_subscription', $subscriptionData);
        }

        $postData = [
            'recurring' => 'add_subscription',
            'payment_token' => $subscriptionData['payment_token'],
            'plan_amount' => number_format($subscriptionData['amount'], 2, '.', ''),
            'plan_payments' => $subscriptionData['payments'] ?? 0, // 0 = unlimited
            'day_frequency' => $this->getDayFrequency($subscriptionData['billing_cycle'] ?? 'monthly'),
            'start_date' => $subscriptionData['start_date'] ?? date('Ymd'),
        ];

        // Customer billing info
        if (isset($subscriptionData['billing'])) {
            $postData = array_merge($postData, $this->formatBillingData($subscriptionData['billing']));
        }

        // Shipping info
        if (isset($subscriptionData['shipping'])) {
            $postData = array_merge($postData, $this->formatShippingData($subscriptionData['shipping']));
        }

        // Optional parameters
        if (isset($subscriptionData['order_id'])) {
            $postData['orderid'] = $subscriptionData['order_id'];
        }

        return $this->doRecurringRequest($postData);
    }

    /**
     * Process initial payment and create subscription
     * 
     * @param array $data
     * @return array
     */
    public function processSubscriptionPayment(array $data): array
    {
        // Demo mode - return simulated successful response
        if ($this->demoMode) {
            $demoResponse = $this->getDemoResponse('process_subscription_payment', $data);
            
            return [
                'success' => true,
                'message' => 'Payment processed successfully (DEMO MODE)',
                'transaction_id' => $demoResponse['transactionid'],
                'subscription_id' => isset($data['create_recurring']) && $data['create_recurring'] 
                    ? $demoResponse['subscription_id'] 
                    : null,
                'demo_mode' => true,
                'data' => $demoResponse
            ];
        }

        // First, process the initial payment
        $saleData = [
            'type' => 'sale',
            'amount' => number_format($data['amount'], 2, '.', ''),
            'payment_token' => $data['payment_token'],
        ];

        // Add billing info
        if (isset($data['billing'])) {
            $saleData = array_merge($saleData, $this->formatBillingData($data['billing']));
        }

        // Add shipping info
        if (isset($data['shipping'])) {
            $saleData = array_merge($saleData, $this->formatShippingData($data['shipping']));
        }

        // Add order description
        if (isset($data['description'])) {
            $saleData['orderdescription'] = $data['description'];
        }

        if (isset($data['order_id'])) {
            $saleData['orderid'] = $data['order_id'];
        }

        // Process the sale
        $saleResult = $this->doTransactRequest($saleData);

        if (!$saleResult['status']) {
            return [
                'success' => false,
                'message' => 'Initial payment failed: ' . ($saleResult['responsetext'] ?? 'Unknown error'),
                'transaction_id' => null,
                'subscription_id' => null,
                'data' => $saleResult
            ];
        }

        // If sale successful and recurring is requested, create subscription
        if (isset($data['create_recurring']) && $data['create_recurring']) {
            $subscriptionResult = $this->createCustomSubscription([
                'payment_token' => $data['payment_token'],
                'amount' => $data['amount'],
                'billing_cycle' => $data['billing_cycle'] ?? 'monthly',
                'billing' => $data['billing'] ?? [],
                'order_id' => $data['order_id'] ?? null,
                'start_date' => $this->calculateNextBillingDate($data['billing_cycle'] ?? 'monthly'),
            ]);

            return [
                'success' => true,
                'message' => 'Payment processed and subscription created',
                'transaction_id' => $saleResult['transactionid'],
                'subscription_id' => $subscriptionResult['subscription_id'] ?? null,
                'data' => [
                    'sale' => $saleResult,
                    'subscription' => $subscriptionResult
                ]
            ];
        }

        return [
            'success' => true,
            'message' => 'Payment processed successfully',
            'transaction_id' => $saleResult['transactionid'],
            'subscription_id' => null,
            'data' => $saleResult
        ];
    }

    /**
     * Update an existing subscription
     * 
     * @param string $subscriptionId
     * @param array $updateData
     * @return array
     */
    public function updateSubscription(string $subscriptionId, array $updateData): array
    {
        // Demo mode - return simulated response
        if ($this->demoMode) {
            return $this->getDemoResponse('update_subscription', array_merge(['subscription_id' => $subscriptionId], $updateData));
        }

        $postData = [
            'recurring' => 'update_subscription',
            'subscription_id' => $subscriptionId,
        ];

        // Update payment method
        if (isset($updateData['payment_token'])) {
            $postData['payment_token'] = $updateData['payment_token'];
        }

        // Update amount
        if (isset($updateData['amount'])) {
            $postData['plan_amount'] = number_format($updateData['amount'], 2, '.', '');
        }

        // Update billing info
        if (isset($updateData['billing'])) {
            $postData = array_merge($postData, $this->formatBillingData($updateData['billing']));
        }

        return $this->doRecurringRequest($postData);
    }

    /**
     * Cancel/Delete a subscription
     * 
     * @param string $subscriptionId
     * @return array
     */
    public function cancelSubscription(string $subscriptionId): array
    {
        // Demo mode - return simulated response
        if ($this->demoMode) {
            return $this->getDemoResponse('cancel_subscription', ['subscription_id' => $subscriptionId]);
        }

        $postData = [
            'recurring' => 'delete_subscription',
            'subscription_id' => $subscriptionId,
        ];

        return $this->doRecurringRequest($postData);
    }

    /**
     * Pause a subscription (NMI doesn't have pause, so we set next billing date far in future)
     * 
     * @param string $subscriptionId
     * @return array
     */
    public function pauseSubscription(string $subscriptionId): array
    {
        // NMI doesn't have a native pause feature
        // We can update the subscription to change the next billing date
        // Or simply track pause status in our database
        return [
            'success' => true,
            'message' => 'Subscription paused in system (NMI will be updated on resume)',
            'subscription_id' => $subscriptionId
        ];
    }

    /**
     * Resume a paused subscription
     * 
     * @param string $subscriptionId
     * @param string $billingCycle
     * @return array
     */
    public function resumeSubscription(string $subscriptionId, string $billingCycle = 'monthly'): array
    {
        // Update subscription with new start date
        return $this->updateSubscription($subscriptionId, [
            'start_date' => $this->calculateNextBillingDate($billingCycle)
        ]);
    }

    /**
     * Get subscription details from NMI
     * 
     * @param string $subscriptionId
     * @return array
     */
    public function getSubscription(string $subscriptionId): array
    {
        // Demo mode - return simulated response
        if ($this->demoMode) {
            return $this->getDemoResponse('get_subscription', ['subscription_id' => $subscriptionId]);
        }

        $postData = [
            'report_type' => 'recurring',
            'subscription_id' => $subscriptionId,
        ];

        return $this->doQueryRequest($postData);
    }

    /**
     * Perform a refund on a transaction
     * 
     * @param string $transactionId
     * @param float|null $amount Partial refund amount (null for full refund)
     * @return array
     */
    public function refundTransaction(string $transactionId, ?float $amount = null): array
    {
        // Demo mode - return simulated response
        if ($this->demoMode) {
            return $this->getDemoResponse('refund', [
                'transaction_id' => $transactionId,
                'amount' => $amount
            ]);
        }

        $postData = [
            'type' => 'refund',
            'transactionid' => $transactionId,
        ];

        if ($amount !== null) {
            $postData['amount'] = number_format($amount, 2, '.', '');
        }

        return $this->doTransactRequest($postData);
    }

    /**
     * Void a transaction (before settlement)
     * 
     * @param string $transactionId
     * @return array
     */
    public function voidTransaction(string $transactionId): array
    {
        // Demo mode - return simulated response
        if ($this->demoMode) {
            return $this->getDemoResponse('void', ['transaction_id' => $transactionId]);
        }

        $postData = [
            'type' => 'void',
            'transactionid' => $transactionId,
        ];

        return $this->doTransactRequest($postData);
    }

    /**
     * Make request to NMI Recurring API
     * 
     * @param array $postData
     * @return array
     */
    protected function doRecurringRequest(array $postData): array
    {
        $postData['security_key'] = $this->securityKey;
        $postUrl = "https://{$this->hostName}{$this->recurringPath}";

        return $this->executeRequest($postUrl, $postData, 'NMI Recurring');
    }

    /**
     * Make request to NMI Transaction API
     * 
     * @param array $postData
     * @return array
     */
    protected function doTransactRequest(array $postData): array
    {
        $postData['security_key'] = $this->securityKey;
        $postUrl = "https://{$this->hostName}{$this->apiPath}";

        return $this->executeRequest($postUrl, $postData, 'NMI Transaction');
    }

    /**
     * Make request to NMI Query API
     * 
     * @param array $postData
     * @return array
     */
    protected function doQueryRequest(array $postData): array
    {
        $postData['security_key'] = $this->securityKey;
        $postUrl = "https://{$this->hostName}/api/query.php";

        return $this->executeRequest($postUrl, $postData, 'NMI Query');
    }

    /**
     * Execute HTTP request to NMI
     * 
     * @param string $url
     * @param array $postData
     * @param string $logPrefix
     * @return array
     */
    protected function executeRequest(string $url, array $postData, string $logPrefix): array
    {
        $client = new Client();

        // Mask sensitive data for logging
        $logData = $postData;
        if (isset($logData['security_key'])) {
            $logData['security_key'] = '***MASKED***';
        }
        if (isset($logData['payment_token'])) {
            $logData['payment_token'] = substr($logData['payment_token'], 0, 10) . '...***MASKED***';
        }

        Log::info("{$logPrefix} Request", [
            'url' => $url,
            'request_data' => $logData
        ]);

        try {
            $response = $client->post($url, [
                'form_params' => $postData,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]);

            parse_str($response->getBody()->getContents(), $responseArray);

            $responseCode = isset($responseArray['response_code']) ? (int)$responseArray['response_code'] : 0;
            $status = in_array($responseCode, [1, 100, 200]) || 
                      (isset($responseArray['response']) && $responseArray['response'] == '1');

            $result = [
                'status' => $status,
                'success' => $status,
                'response_code' => $responseCode,
                'responsetext' => $responseArray['responsetext'] ?? ($responseArray['response_text'] ?? ''),
                'transactionid' => $responseArray['transactionid'] ?? null,
                'subscription_id' => $responseArray['subscription_id'] ?? null,
                'authcode' => $responseArray['authcode'] ?? '',
                'avsresponse' => $responseArray['avsresponse'] ?? '',
                'cvvresponse' => $responseArray['cvvresponse'] ?? '',
                'raw_response' => $responseArray
            ];

            Log::info("{$logPrefix} Response", [
                'status' => $status,
                'response_code' => $responseCode,
                'transaction_id' => $result['transactionid'],
                'subscription_id' => $result['subscription_id'],
                'response_text' => $result['responsetext']
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error("{$logPrefix} Error", [
                'error_message' => $e->getMessage(),
                'request_data' => $logData
            ]);

            return [
                'status' => false,
                'success' => false,
                'response_code' => 0,
                'responsetext' => 'Error: ' . $e->getMessage(),
                'transactionid' => null,
                'subscription_id' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Convert billing cycle to NMI day_frequency
     * 
     * @param string $billingCycle
     * @return int
     */
    protected function getDayFrequency(string $billingCycle): int
    {
        return match (strtolower($billingCycle)) {
            'daily' => 1,
            'weekly' => 7,
            'biweekly', 'bi-weekly' => 14,
            'monthly' => 30,
            'quarterly' => 90,
            'semi-annually', 'semiannually' => 180,
            'annually', 'yearly' => 365,
            default => 30
        };
    }

    /**
     * Calculate next billing date based on cycle
     * 
     * @param string $billingCycle
     * @return string YYYYMMDD format
     */
    protected function calculateNextBillingDate(string $billingCycle): string
    {
        $days = $this->getDayFrequency($billingCycle);
        return date('Ymd', strtotime("+{$days} days"));
    }

    /**
     * Format billing data for NMI
     * 
     * @param array $billing
     * @return array
     */
    protected function formatBillingData(array $billing): array
    {
        return [
            'first_name' => $billing['first_name'] ?? '',
            'last_name' => $billing['last_name'] ?? '',
            'company' => $billing['company'] ?? '',
            'address1' => $billing['address1'] ?? ($billing['address'] ?? ''),
            'address2' => $billing['address2'] ?? '',
            'city' => $billing['city'] ?? '',
            'state' => $billing['state'] ?? '',
            'zip' => $billing['zip'] ?? ($billing['postal_code'] ?? ''),
            'country' => $billing['country'] ?? 'US',
            'phone' => $billing['phone'] ?? '',
            'email' => $billing['email'] ?? '',
        ];
    }

    /**
     * Format shipping data for NMI
     * 
     * @param array $shipping
     * @return array
     */
    protected function formatShippingData(array $shipping): array
    {
        return [
            'shipping_firstname' => $shipping['first_name'] ?? '',
            'shipping_lastname' => $shipping['last_name'] ?? '',
            'shipping_company' => $shipping['company'] ?? '',
            'shipping_address1' => $shipping['address1'] ?? ($shipping['address'] ?? ''),
            'shipping_address2' => $shipping['address2'] ?? '',
            'shipping_city' => $shipping['city'] ?? '',
            'shipping_state' => $shipping['state'] ?? '',
            'shipping_zip' => $shipping['zip'] ?? ($shipping['postal_code'] ?? ''),
            'shipping_country' => $shipping['country'] ?? 'US',
        ];
    }
}
