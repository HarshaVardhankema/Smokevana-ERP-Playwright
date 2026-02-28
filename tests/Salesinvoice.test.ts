import { test, expect } from "@playwright/test";
import { LoginPage } from "../UserManagement/Loginpage";
import { DashboardPage } from "../UserManagement/Dashboardpage";
import { SalesInvoicePage } from "../Sales/Salesinvoicepage";
import * as testData from "../Utlilies/testData.json";

test.describe("Smokevana ERP Sales Invoice Automation", () => {
    test("Create Sales Invoice with Matrix, Discount, and Submit", async ({ page }) => {
        const loginPage = new LoginPage(page);
        const dashboardPage = new DashboardPage(page);
        const salesInvoicePage = new SalesInvoicePage(page);

        // 1. Launch and Login
        console.log("Launching browser and logging in...");
        await page.goto("https://smokevanaerp.phantasm-agents.ai/login");
        await loginPage.login(testData.admin.username, testData.admin.password);

        // 2. Wait for login to complete
        await page.waitForTimeout(5000);
        await page.screenshot({ path: 'test_logs/screenshots/sales_invoice_login.png' });

        // Dismiss tour modal if present
        try {
            const tourClose = page.locator('button:has-text("End tour")').first();
            if (await tourClose.isVisible({ timeout: 5000 })) {
                await tourClose.click({ force: true });
                console.log("Closed Application Tour modal.");
            }
        } catch (e) { }

        // 3. Navigation to Sales Invoice via Sidebar
        console.log("Navigating to Add Sale Invoice (SI) via sidebar...");
        await dashboardPage.clickSale();
        await dashboardPage.clickAddSalesInvoice();
        // Wait for URL change or page load
        await page.waitForTimeout(3000); // Allow time for navigation

        // 4. Verify successful page load
        await page.waitForSelector('h1.so-header-title:has-text("Add Sale")', { timeout: 15000 });
        console.log("Sales Invoice creation page loaded.");

        // 5. Customer Selection
        console.log("Selecting Customer...");
        await salesInvoicePage.selectCustomer("HarshaVC");

        // 6. Enable Matrix
        console.log("Enabling Matrix...");
        await salesInvoicePage.clickEnableMatrix();

        // 7. Product Search
        console.log("Searching Product...");
        await salesInvoicePage.searchProduct("THCA ");

        // 8. Matrix Quantities
        console.log("Filling Matrix Quantities...");
        await salesInvoicePage.fillMatrixQuantity(0, "2");
        await salesInvoicePage.fillMatrixQuantity(1, "1");

        // 9. Click Add
        console.log("Clicking Add Matrix...");
        await salesInvoicePage.clickAddMatrix();

        // 10. Apply Discount
        console.log("Applying fixed discount of $5 to the first row...");
        // After matrix is added, the product table is populated. 
        // We apply a discount to the first added row (index 0).
        await salesInvoicePage.addDiscount(0, 'fixed', '5');

        await page.screenshot({ path: 'test_logs/screenshots/sales_invoice_discount_filled.png' });

        // 11. Submit Invoice
        console.log("Submitting Invoice...");
        await salesInvoicePage.submitInvoice();

        // Wait to verify successful submission (e.g. redirected to sales list or success message)
        await page.waitForTimeout(5000);
        await page.screenshot({ path: 'test_logs/screenshots/sales_invoice_success.png' });

        console.log("Sales Invoice creation completed successfully.");
    });
});
