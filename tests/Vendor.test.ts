import { test, expect } from '@playwright/test';
import { VendorDataManager } from '../Vendor Care/dataManager';

test.describe('Smokevana ERP Vendor Automation', () => {
    let poManager: VendorDataManager;

    test.beforeEach(async ({ page, context }) => {
        poManager = new VendorDataManager(page, context);
        
        // Handle login manually to match codegen exactly
        await page.goto('https://smokevanaerp.phantasm-agents.ai/login');
        await expect(page.getByRole('img', { name: 'Smokevana' })).toBeVisible();

        const loginPage = poManager.getLoginPage();
        await loginPage.login('admin', '123456');

        await page.getByRole('button', { name: 'End tour' }).click({ timeout: 3000 }).catch(() => {});
    });

    test('Scenario: Add Vendor using Page Object Model', async ({ page }) => {
        const vendorPage = poManager.getVendorPage();

        await vendorPage.navigateToVendor();
        await vendorPage.clickAddVendor();

        // Fill Vendor Details with dynamically generated names
        const timestamp = Date.now().toString().slice(-6);
        await vendorPage.fillVendorDetails({
            businessName: `Test Vendor ${timestamp}`,
            firstName: `Auto`,
            lastName: `Vendor-${timestamp}`,
            mobile: `99${timestamp}00`,
            email: `vendor${timestamp}@automation.test`,
            address1: 'Ohio',
            zipCode: '03060'
        });

        await vendorPage.saveVendor();
        await vendorPage.clickNo();
    });
});
