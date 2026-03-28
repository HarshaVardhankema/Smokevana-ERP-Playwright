import { test, expect } from '@playwright/test';
import { ProductsDataManager } from '../Products/Datamanager';
import * as testData from '../Utlilies/testData.json';

test.describe('Smokevana ERP Brands Automation', () => {
    let poManager: ProductsDataManager;

    test.beforeEach(async ({ page, context }) => {
        poManager = new ProductsDataManager(page, context);
        
        console.log("LOG: Starting Login Flow...");
        // Directly to products or fallback to login? The snippet shows opening taxonomies then doing login flow.
        await page.goto('https://smokevanaerp.phantasm-agents.ai/taxonomies?type=product');
        const loginPage = poManager.getLoginPage();
        await loginPage.login(testData.admin.username, testData.admin.password);
        
        // Handle Tour if visible using forceful approach
        try {
            const endTourBtn = page.getByRole('button', { name: 'End tour' });
            await endTourBtn.click({ force: true, timeout: 5000 });
            console.log("LOG: End Tour button forcefully clicked.");
        } catch (e) {
            console.log("LOG: End Tour button not present, skipping...");
        }
    });

    test('Scenario: Add New Brand using Page Object Model', async ({ page }) => {
        const brandsPage = poManager.getBrandsPage();

        console.log("LOG: Navigating to Brands...");
        await brandsPage.navigate();
        
        console.log("LOG: Clicking Add Brand...");
        await brandsPage.clickAddBrand();

        console.log("LOG: Filling Brand Details...");
        await brandsPage.fillBrandDetails({
            locationValue: '1',
            name: 'TEST BRAND ' + Date.now().toString().slice(-4), // Unique
            description: 'TEST VAPE & SMOKES'
        });

        console.log("LOG: Saving Brand...");
        await brandsPage.saveBrand();
        
        await page.waitForLoadState('networkidle');
        console.log("LOG: Brand added successfully!");
    });
});
