import { test, expect } from "@playwright/test";
import { LoginPage } from "../UserManagement/Loginpage";
import { SalesDataManager } from "../Sales/Datamanager";
import * as testData from "../Utlilies/testData.json";

test.describe("Smokevana ERP - Manage Orders", () => {

    test("Scenario 3 - Manage Order Fulfillment", async ({ page, context }) => {

        const loginPage = new LoginPage(page);
        const salesData = new SalesDataManager(page, context);
        const manageOrdersPage = salesData.getManageOrdersPage();

        // Launch Application
        await page.goto("https://smokevanaerp.phantasm-agents.ai/login");

        // Login
        await loginPage.login(testData.admin.username, testData.admin.password);
        await expect(page.getByRole('link', { name: 'Home' }).first()).toBeVisible();

        // End Tour
        await manageOrdersPage.closeTourIfPresent();
        await expect(page.getByRole('link', { name: 'Home' }).first()).toBeVisible();

        // Navigate to Manage Orders
        await manageOrdersPage.navigateToManageOrders();
        await expect(page.getByRole('row', { name: 'Picking Status Sales Order:' })).toBeVisible();

        // Select Order
        await manageOrdersPage.selectUnassignedOrder();

        // Click Bypass
        await manageOrdersPage.clickBypass();

        // Confirm Bypass
        await manageOrdersPage.confirmBypass();

        // Open Packing Tab
        await manageOrdersPage.openPackingTab();
        await expect(page.getByRole('row', { name: 'Picking Status: activate to' })).toBeVisible({ timeout: 10000 });

        // Select Order in Packing
        await manageOrdersPage.selectPickedOrder();

        // Make Shipment
        await manageOrdersPage.clickMakeShipment();
        await expect(page.getByRole('heading', { name: 'Confirm Packing Completion' })).toBeVisible();

        // Continue Shipment
        await manageOrdersPage.clickContinue();
        await expect(page.getByRole('link', { name: 'Packing' })).toBeVisible();

        // Popup - Click No
        await manageOrdersPage.clickNoPopup();

        await page.screenshot({ path: "screenshots/manage-orders.png" });

    });
});