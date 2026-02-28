# 🗺️ Smokevana ERP - Automation Roadmap

Based on my analysis of the `smokevana-erp-main` documentation and source code, here is the proposed automation strategy for the ERP system.

## 🚀 Key Modules for Automation

| Priority | Module | Description | Key User Flow |
|----------|--------|-------------|---------------|
| **P0** | **Login & Auth** | Common entryway for Admin and Vendors | Multi-user role login (Admin vs Vendor) |
| **P0** | **Order Fulfillment** | The core of the ERP workflow | Preprocessing -> Order Splitting -> Fulfillment |
| **P1** | **Product Management** | Product lifecycle | Create Product -> Assign to Vendor -> Stock Update |
| **P1** | **Vendor Portal** | Third-party fulfillment | View Orders -> Accept -> Add Tracking -> Ship |
| **P2** | **Sales & POS** | Front-end revenue generation | POS Transaction -> Invoice Generation -> Payment |

---

## 🏗️ Proposed POM Structure

Following the **Fullstackmidoc** format we've established:

### 1. Page Objects (`/pages`)

- [ ] `DashboardPage.ts` - Stats, navigation, quick actions.
- [ ] `ProductListPage.ts` - Searching, filtering, and "Add Product" button.
- [ ] `AddProductPage.ts` - Form fields for brands, categories, and vendors.
- [ ] `FulfillmentPage.ts` - Tabs: Preprocessing, Pending, Processing, Picked, Shipped.
- [ ] `VendorOrderPage.ts` - For the Vendor Portal specific order list.

### 2. Management Class (`DataManager.ts`)

The `DataManager` will act as the orchestrator, initializing all page objects for a test session:

```typescript
export class DataManager {
    // ... pages
    fulfillmentPage: FulfillmentPage;
    productPage: ProductListPage;
    vendorPage: VendorOrderPage;

    constructor(page: Page, context: BrowserContext) {
        // ... init pages
    }
}
```

---

## 📝 Critical Test Scenarios

1. **The "Dropship" Flow (End-to-End)**:
    - **Step 1**: Admin creates an order with a dropship product.
    - **Step 2**: Admin goes to `Order Fulfillment > Preprocessing` and splits the order.
    - **Step 3**: Vendor logs into the Vendor Portal.
    - **Step 4**: Vendor accepts order and adds a tracking number.
    - **Step 5**: Admin verifies the status update in the ERP.

2. **Inventory Sync**:
    - Verify that fulfilling a sale correctly decrements stock in the specific `Business Location`.

3. **Multi-Location Testing**:
    - Ensure a sale at "Location A" doesn't affect stock at "Location B".

---

## 🛠️ System Credentials (found in Docs)

- **Vendor Portal**: `testvendor@gohunter.com` / `Vendor@123`
- **ERP URL**: `https://testerp.gohunterdistro.com/login`
- **Vendor URL**: `https://testerp.gohunterdistro.com/vendorlogin`

> [!TIP]
> I recommend we start by automating the **Login/Logout** for both Admin and Vendor roles, then move to the **Order Fulfillment** flow as it's the most complex and critical part of the system.
