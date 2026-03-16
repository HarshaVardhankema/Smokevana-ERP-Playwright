import { test, expect } from '@playwright/test';
import { SalesDataManager } from '../Sales/Datamanager';
import * as testData from '../Utlilies/testData.json';
import { LoginPage } from '../UserManagement/Loginpage';
import { DashboardPage } from '../UserManagement/Dashboardpage';

test.describe('Smokevana ERP List Return Automation', () => {
    test('Scenario Three: Create List Return for existing invoice', async ({ page, context }) => {
        const dataManager = new SalesDataManager(page, context);
        const loginPage = new LoginPage(page);
        const dashboardPage = new DashboardPage(page);
        const listReturnPage = dataManager.getListReturnPage();

        // Point 1-3. Launch and Login
        console.log("Point 1-3: Launching browser and logging in...");
        await page.goto("https://smokevanaerp.phantasm-agents.ai/login");
        await loginPage.login(testData.admin.username, testData.admin.password);

        // Wait for redirection
        await page.waitForTimeout(5000);
        await page.screenshot({ path: 'screenshots/list_return_login_attempt.png' });

        // Handle possible tour modal
        try {
            const tourClose = page.getByRole('button', { name: 'End tour' }).first();
            await tourClose.waitFor({ state: 'visible', timeout: 5000 });
            await tourClose.click({ force: true });
            console.log("Closed Application Tour modal.");
        } catch (e) {
            console.log("End tour button not found or already closed. Continuing...");
        }

        // Point 4-5. Navigate via Sidebar
        console.log("Point 4-5: Navigating to List Sell Return(CN) via Sidebar...");
        try {
            await dashboardPage.clickSale();
            await dashboardPage.clickListSellReturn();
        } catch (e) {
            console.warn("Sidebar navigation failed, trying to continue...");
        }

        // Point 6. Wait for page load
        console.log("Point 6: Wait for List Return index page to load...");
        await page.waitForTimeout(2000); // Allow time for navigation
        await expect(page.locator('.sr-header-title').filter({ hasText: 'Sell Return' })).toBeVisible({ timeout: 15000 });
        console.log("List Return index page loaded.");

        // Point 7. Initiate Add Return
        console.log("Point 7: Clicking Add button...");
        await listReturnPage.clickAddReturn();
        await page.waitForSelector('h1:has-text("Sell Return")', { timeout: 15000 });
        console.log("Return creation form loaded.");

        // Point 8. Select Invoice
        console.log("Point 8: Selecting the first available invoice from the dropdown...");
        await listReturnPage.selectInvoiceToReturn(1); // Selects first non-empty option
        console.log("Invoice selected and form populated.");

        // Point 9-11. Fill Quantities
        console.log("Point 9-11: Filling return quantity for the first item...");
        await listReturnPage.fillReturnQuantities(0, "1");

        // Point 12. Applying Return Discount
        console.log("Point 12: Applying return discount...");
        await listReturnPage.applyDiscount("fixed", "2");

        // Point 13. Take intermediate screenshot
        console.log("Point 13: Taking intermediate screenshot...");
        await page.screenshot({ path: 'screenshots/list_return_filled.png' });

        // Point 14. Submit Return
        console.log("Point 14: Submitting return...");
        await listReturnPage.submitReturn();

        // Point 15. Verification
        console.log("Point 15: Verifying success...");
        // Playwright will verify the toastr success message or the return to the index page.
        await expect(page.locator('.toast-success')).toBeVisible({ timeout: 15000 });

        console.log("Scenario 3: Completed successfully up to Step 15.");
    });
});
