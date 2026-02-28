import { test, expect } from "@playwright/test";
import { LoginDataManager } from "../UserManagement/Datamanager";

test.describe("Smokevana ERP Dashboard Automation", () => {

    test("Verify Admin Login and Dashboard Navigation", async ({ page, context }) => {
        const dataManager = new LoginDataManager(page, context);
        const loginPage = dataManager.getLoginPage();
        const dashboardPage = dataManager.getDashboardPage();

        // 1. Login
        await loginPage.navigate();
        await loginPage.login("admin", "123456");

        // 2. Verify Dashboard elements are visible
        await expect(dashboardPage.productsMenu).toBeVisible();
        await expect(dashboardPage.saleMenu).toBeVisible();

        // 3. Navigate to Order Fulfillment
        await dashboardPage.clickOrderFulfillment();
        await expect(page).toHaveURL(/.*order-fulfillment/);

        // 4. Logout (Optional - can be in afterEach if needed)
        // Note: Logout implementation depends on exact UI state after login
        // await dashboardPage.logout();
    });
});
