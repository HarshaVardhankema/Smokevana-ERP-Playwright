import { test, expect } from "@playwright/test";
import { LoginPage } from "../UserManagement/Loginpage";
import { DashboardPage } from "../UserManagement/Dashboardpage";
import { SalesDataManager } from "../Sales/Datamanager";
import * as testData from "../Utlilies/testData.json";

test.describe("Smokevana ERP - Sales Order Automation", () => {
    test("Scenario One: Create a new Sales Order", async ({ page, context }) => {
        const loginPage = new LoginPage(page);
        const dashboardPage = new DashboardPage(page);
        const dataManager = new SalesDataManager(page, context);
        const salesOrderPage = dataManager.getNewSalesorderPage();

        // Point 1-3: Launch browser and login
        console.log("Point 1-3: Launching browser and logging in...");
        await page.goto("https://smokevanaerp.phantasm-agents.ai/login");
        await loginPage.login(testData.admin.username, testData.admin.password);
        await page.waitForTimeout(5000);

        // Handle End Tour modal if present
        try {
            const tourClose = page.locator('button:has-text("End tour")').first();
            if (await tourClose.isVisible({ timeout: 5000 })) {
                await tourClose.click({ force: true });
                console.log("Closed Application Tour modal.");
            }
        } catch (e) { }

        // Point 4-5: Navigate via Sidebar to Sales Order
        console.log("Point 4-5: Navigating to Sales Order via Sidebar...");
        await dashboardPage.clickSale();
        await dashboardPage.clickSalesOrder();
        await page.waitForURL('**/sales-order**', { timeout: 15000 });
        console.log("Navigated to Sales Order list page.");

        // Point 5.5: Click Add Sales Order button on index page
        console.log("Point 5.5: Clicking Add Sales Order button...");
        await salesOrderPage.clickAddSalesOrder();

        // Point 6: Wait for the Sales Order creation form to load
        console.log("Point 6: Waiting for Sales Order creation form to load...");
        await page.waitForSelector('span.select2-selection--single', { timeout: 15000 });
        console.log("Sales Order creation form loaded.");

        // Point 7: Click the Customer dropdown
        console.log("Point 7: Opening Customer dropdown...");
        await salesOrderPage.openCustomerDropdown();

        // Point 8: Enter customer name and select result
        console.log("Point 8: Searching for customer 'HarshaVC'...");
        await salesOrderPage.searchAndSelectCustomer("HarshaVC");

        // Point 9: Click Enable Matrix toggle
        console.log("Point 9: Clicking Enable Matrix toggle...");
        await salesOrderPage.clickEnableMatrix();

        // Point 10: Search for product
        console.log("Point 10: Searching for product 'THCA'...");
        await salesOrderPage.searchProduct("THCA");

        // Point 11: Select first result from autocomplete
        console.log("Point 11: Selecting first product result...");
        await salesOrderPage.selectFirstSearchResult();

        // Point 12: Fill quantity - nth(0) with "1"
        console.log("Point 12: Filling quantity[0] = 1...");
        await salesOrderPage.fillQuantity(0, "1");

        // Point 13: Fill quantity - nth(1) with "1"
        console.log("Point 13: Filling quantity[1] = 1...");
        await salesOrderPage.fillQuantity(1, "1");

        // Point 14: Fill quantity - nth(2) with "1"
        console.log("Point 14: Filling quantity[2] = 1...");
        await salesOrderPage.fillQuantity(2, "1");

        // Point 15: Click Add (Matrix) button
        console.log("Point 15: Clicking Add Matrix button...");
        await salesOrderPage.clickAddMatrix();

        // Screenshot before saving
        await page.screenshot({ path: 'screenshots/new_sales_order_ready.png' });

        // Point 16: Click Save button
        console.log("Point 16: Clicking Save button...");
        await salesOrderPage.clickSave();

        // Point 17: Click Confirm button
        console.log("Point 17: Clicking Confirm button...");
        await salesOrderPage.clickConfirm();

        // Verification: Check for success message
        console.log("Verifying success message...");
        await expect(page.locator('.toast-success')).toBeVisible({ timeout: 15000 });

        // Screenshot after saving
        await page.screenshot({ path: 'screenshots/new_sales_order_success.png' });

        console.log("Scenario One: Completed successfully.");
    });
});
