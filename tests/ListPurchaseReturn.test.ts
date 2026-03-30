import { test, expect } from '@playwright/test';
import { VendorDataManager } from '../Vendor Care/dataManager';

test.describe('Smokevana ERP List Purchase Return Automation', () => {
    let prManager: VendorDataManager;

    test.beforeEach(async ({ page, context }) => {
        prManager = new VendorDataManager(page, context);
        
        await page.goto('https://smokevanaerp.phantasm-agents.ai/login');
        await expect(page.getByRole('img', { name: 'Smokevana' })).toBeVisible();

        const loginPage = prManager.getLoginPage();
        await loginPage.login('admin', '123456');

        // Gracefully dismiss the Application Tour modal if it pops up
        await page.getByRole('button', { name: 'End tour' }).click({ timeout: 3000 }).catch(() => {});
    });

    test('Scenario: Add List Purchase Return', async ({ page }) => {
        const prPage = prManager.getListPurchaseReturnPage();

        await prPage.navigateToListPurchaseReturn();
        await prPage.clickAddReturn();

        // 🧠 Dynamically pass the values from the codegen trace:
        // Supplier Search: 'auto'
        // Branch: 'Smokevana Prime B2B (BL0001)'
        // Product/SKU search: '9596'
        await prPage.fillReturnFormAndSubmit('auto', 'Smokevana Prime B2B (BL0001)', '9596');
    });
});
