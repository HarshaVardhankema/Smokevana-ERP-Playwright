# Subscription Module - Test Cases for QA Team

## Overview
This document outlines all test cases for the Prime Customer Subscription module.

---

## 1. Subscription Plans Management

### TC-1.1: Create Subscription Plan
**Steps:**
1. Navigate to Prime Subscriptions → Subscription Plans
2. Click "Add Plan"
3. Fill in plan details (Name, Price, Billing Cycle, etc.)
4. Select Customer Group to assign
5. Enable/Disable prime features
6. Save

**Expected Result:** Plan is created and appears in the plans list

### TC-1.2: Edit Subscription Plan
**Steps:**
1. Navigate to Subscription Plans
2. Click Edit on an existing plan
3. Modify plan details
4. Save

**Expected Result:** Plan is updated successfully

### TC-1.3: Delete/Deactivate Plan
**Steps:**
1. Navigate to Subscription Plans
2. Click on a plan with no active subscribers
3. Deactivate or delete the plan

**Expected Result:** Plan is deactivated/deleted (only if no active subscribers)

---

## 2. Customer Subscription Lifecycle

### TC-2.1: Create New Subscription (ERP Manual)
**Steps:**
1. Navigate to Prime Subscriptions → Add Subscription
2. Select a Customer
3. Select a Plan (e.g., "Platinum")
4. Click "Create Subscription"

**Expected Result:** 
- Subscription is created with status "Pending"
- Customer group should NOT change yet (still waiting for payment)

### TC-2.2: Activate Subscription (Confirm Payment)
**Steps:**
1. After creating subscription, confirm payment
2. Or use Edit → Change Status to "Active"

**Expected Result:**
- Status changes to "Active"
- **Customer Group automatically updates** to plan's customer group (e.g., "Platinum Customers")
- Activity log shows "subscription_activated" and "customer_group_updated"

### TC-2.3: Pause Subscription
**Steps:**
1. Navigate to subscription detail page
2. Click "Pause"

**Expected Result:**
- Status changes to "Paused"
- **Customer Group reverts** to default from Settings (e.g., "Sliver Customers")
- Activity log shows "subscription_paused" and "customer_group_reverted"

### TC-2.4: Resume Subscription
**Steps:**
1. Navigate to a paused subscription
2. Click "Resume"

**Expected Result:**
- Status changes to "Active"
- **Customer Group restores** to plan's customer group
- Activity log shows "subscription_resumed" and "customer_group_updated"

### TC-2.5: Change Plan (Upgrade/Downgrade)
**Steps:**
1. Navigate to subscription detail page
2. Click "Edit"
3. Select different plan from dropdown
4. Save

**Expected Result:**
- Plan changes immediately
- **Customer Group updates** to new plan's customer group
- Activity log shows "plan_changed" and "customer_group_updated"

### TC-2.6: Cancel Subscription (End of Period)
**Steps:**
1. Navigate to subscription detail page
2. Click "Cancel"
3. Select "Cancel at end of period"

**Expected Result:**
- Status changes to "Cancelled"
- Customer Group remains until expiry date
- Activity log shows "subscription_cancelled"

### TC-2.7: Cancel Subscription (Immediate)
**Steps:**
1. Navigate to subscription detail page
2. Click "Cancel"
3. Select "Cancel immediately"

**Expected Result:**
- Status changes to "Cancelled"
- **Customer Group reverts** to default from Settings
- Activity log shows "subscription_cancelled" and "customer_group_reverted"

### TC-2.8: Expire Subscription (Automatic via Cron)
**Steps:**
1. Set subscription expiry date to past (for testing)
2. Run: `php artisan subscription:expire`

**Expected Result:**
- Status changes to "Expired"
- **Customer Group reverts** to default from Settings
- Activity log shows "subscription_expired"

### TC-2.9: Force Expire (Testing API)
**Steps:**
1. Call API: `POST /api/subscription/b2b/force-expire`
2. Body: `{ "subscription_id": 1 }`

**Expected Result:**
- Status changes to "Expired"
- Customer Group reverts to default

---

## 3. Settings Configuration

### TC-3.1: Configure Default Customer Group on Expiry
**Steps:**
1. Navigate to Prime Subscriptions → Settings
2. Select "Default Customer Group (On Expiry)" (e.g., "Sliver Customers")
3. Save

**Expected Result:**
- Setting is saved
- When subscriptions expire/pause, customers revert to this group

### TC-3.2: Configure Default Selling Price Group
**Steps:**
1. Navigate to Settings
2. Select "Default Selling Price Group (On Expiry)"
3. Save

**Expected Result:** Setting is saved

---

## 4. Customer Group Synchronization

### TC-4.1: Sync Customer Group (Manual)
**Steps:**
1. Navigate to subscription detail page
2. If "Customer group mismatch" warning appears
3. Click "Sync Customer Group"

**Expected Result:**
- Customer group updates to match plan's required group
- Warning message disappears

---

## 5. API Endpoints Testing (B2B E-commerce)

### TC-5.1: Get Available Plans
```
GET /api/subscription/v1/plans
Header: business-id: 1
```

**Expected Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Prime Month Plan",
      "price": 299.00,
      ...
    }
  ]
}
```

### TC-5.2: Subscribe to Plan
```
POST /api/subscription/v1/subscribe
Header: Authorization: Bearer {token}
Body: { "plan_id": 2 }
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "subscription_id": 2,
    "subscription_no": "SUB...",
    "invoice_id": 1,
    "amount_due": 399.00
  }
}
```

### TC-5.3: Confirm Payment
```
POST /api/subscription/v1/confirm-payment
Header: Authorization: Bearer {token}
Body: { 
  "subscription_id": 2,
  "payment_method": "stripe",
  "transaction_id": "TXN123456"
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Payment confirmed and subscription activated.",
  "data": {
    "subscription_id": 2,
    "status": "active",
    "customer_group": "Platinum Customers"
  }
}
```

### TC-5.4: Get Customer Subscription
```
GET /api/subscription/v1/my-subscription
Header: Authorization: Bearer {token}
```

### TC-5.5: Change Plan
```
POST /api/subscription/v1/change-plan
Header: Authorization: Bearer {token}
Body: { "new_plan_id": 1 }
```

### TC-5.6: Pause Subscription
```
POST /api/subscription/v1/pause
Header: Authorization: Bearer {token}
```

### TC-5.7: Resume Subscription
```
POST /api/subscription/v1/resume
Header: Authorization: Bearer {token}
```

### TC-5.8: Cancel Subscription
```
POST /api/subscription/v1/cancel
Header: Authorization: Bearer {token}
Body: { "reason": "Test cancellation", "cancel_immediately": true }
```

### TC-5.9: Get Subscription by Contact ID (B2B)
```
GET /api/subscription/b2b/subscription/{contactId}
Header: business-id: 1
```

---

## 6. Cron Jobs Testing

### TC-6.1: Test Subscription Expiry Cron
**Steps:**
1. Create a test subscription
2. Set expiry date to past: 
```sql
UPDATE customer_subscriptions SET expires_at = NOW() - INTERVAL 1 DAY WHERE id = ?;
```
3. Run: `php artisan subscription:expire --dry-run` (preview)
4. Run: `php artisan subscription:expire` (execute)

**Expected Result:**
- Subscription status changes to "Expired"
- Customer group reverts to default

### TC-6.2: Test Renewal Reminders Cron
**Steps:**
1. Create a subscription expiring in 5 days
2. Run: `php artisan subscription:send-reminders --days=7 --dry-run`

**Expected Result:**
- Subscription appears in reminder list
- Activity log shows "renewal_reminder_sent"

---

## 7. Scheduled Tasks (Verify in Production)

| Schedule | Command | Description |
|----------|---------|-------------|
| Daily 00:00 | `subscription:expire` | Auto-expire past-due subscriptions |
| Daily 09:00 | `subscription:send-reminders --days=7` | Send renewal reminders |

**Verify with:** `php artisan schedule:list`

---

## 8. Edge Cases

### TC-8.1: Double Subscription Prevention
**Steps:**
1. Try to subscribe same customer to same plan twice

**Expected Result:** Error - "You already have an active subscription"

### TC-8.2: Plan with No Customer Group
**Steps:**
1. Create a plan without assigning a customer group
2. Subscribe a customer to this plan

**Expected Result:** Subscription works but customer group not changed

### TC-8.3: Delete Customer with Active Subscription
**Steps:**
1. Try to delete a customer who has an active subscription

**Expected Result:** Warning/prevention or cascade handling

---

## 9. Test Data Setup Commands

### Quick Test: Force Expire a Subscription
```bash
# Via API
curl -X POST http://127.0.0.1:8000/api/subscription/b2b/force-expire \
  -H "Content-Type: application/json" \
  -H "business-id: 1" \
  -d '{"subscription_id": 1}'
```

### Set Subscription Expiry for Testing
```sql
-- Set to expire tomorrow
UPDATE customer_subscriptions 
SET expires_at = NOW() + INTERVAL 1 DAY,
    current_period_end = NOW() + INTERVAL 1 DAY
WHERE id = 1;

-- Set to expired (yesterday)
UPDATE customer_subscriptions 
SET expires_at = NOW() - INTERVAL 1 DAY,
    current_period_end = NOW() - INTERVAL 1 DAY
WHERE id = 1;
```

### Check Scheduled Tasks
```bash
php artisan schedule:list
php artisan subscription:expire --dry-run
php artisan subscription:send-reminders --dry-run
```

---

## 10. Verification Queries

### Check Customer Group After Subscription Changes
```sql
SELECT 
  cs.id as subscription_id,
  cs.status,
  c.name as customer_name,
  cg.name as current_customer_group,
  sp.name as plan_name,
  pcg.name as plan_required_group
FROM customer_subscriptions cs
JOIN contacts c ON cs.contact_id = c.id
LEFT JOIN customer_groups cg ON c.customer_group_id = cg.id
JOIN subscription_plans sp ON cs.plan_id = sp.id
LEFT JOIN customer_groups pcg ON sp.customer_group_id = pcg.id
WHERE cs.id = 1;
```

### Check Subscription Activity Log
```sql
SELECT * FROM subscription_logs 
WHERE subscription_id = 1 
ORDER BY created_at DESC 
LIMIT 20;
```

---

## Summary Checklist

| Feature | Test Status |
|---------|-------------|
| ✅ Create Subscription Plan | |
| ✅ Create Subscription (Manual) | |
| ✅ Activate Subscription | |
| ✅ Customer Group Auto-Update on Activate | |
| ✅ Pause Subscription | |
| ✅ Customer Group Revert on Pause | |
| ✅ Resume Subscription | |
| ✅ Customer Group Restore on Resume | |
| ✅ Change Plan (Upgrade/Downgrade) | |
| ✅ Customer Group Update on Plan Change | |
| ✅ Cancel Subscription | |
| ✅ Expire Subscription (Cron) | |
| ✅ Customer Group Revert on Expiry | |
| ✅ Settings: Default Customer Group | |
| ✅ Sync Customer Group (Manual) | |
| ✅ API: Get Plans | |
| ✅ API: Subscribe | |
| ✅ API: Confirm Payment | |
| ✅ API: Change Plan | |
| ✅ API: Pause/Resume | |
| ✅ API: Cancel | |
| ✅ API: Force Expire (Testing) | |
| ✅ Cron: Auto-Expire | |
| ✅ Cron: Renewal Reminders | |
