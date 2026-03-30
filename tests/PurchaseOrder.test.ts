import { test, expect } from '@playwright/test';
import { VendorDataManager } from '../Vendor Care/dataManager';

test.describe('Smokevana ERP Purchase Order Automation', () => {
    let poManager: VendorDataManager;

    test.beforeEach(async ({ page, context }) => {
        poManager = new VendorDataManager(page, context);
        
        // Handle login manually to match codegen exactly
        await page.goto('https://smokevanaerp.phantasm-agents.ai/login');
        await expect(page.getByRole('img', { name: 'Smokevana' })).toBeVisible();

        const loginPage = poManager.getLoginPage();
        await loginPage.login('admin', '123456');

        // Gracefully dismiss the Application Tour modal if it pops up
        await page.getByRole('button', { name: 'End tour' }).click({ timeout: 3000 }).catch(() => {});
    });

    test('Scenario: Add Purchase Order and Create PR', async ({ page }) => {
        const poPage = poManager.getPurchaseOrderPage();

        await poPage.navigateToPurchaseOrder();
        await poPage.clickAddPurchaseOrder();

        // Fill vendor and product details
        // Supplier 'Auto' handles matching the dynamic test vendors reliably!
        // The original script used 'Auto Vendor-558184 - Test', but dynamic searching avoids failures exactly like before.
        await poPage.fillPurchaseOrderDetails('Auto', '9596', '10', '30');
        await poPage.savePurchaseOrder();

        // Trigger PR flow
        await poPage.createPRForFirstOrder();
        await poPage.fillPRDetailsAndSave('9596', '10');
    });
});
