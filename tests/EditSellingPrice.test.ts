import { test, expect } from '@playwright/test';
import { ProductsDataManager } from '../Products/Datamanager';
import * as testData from '../Utlilies/testData.json';

test.describe('Smokevana ERP Edit Selling Price', () => {
    let poManager: ProductsDataManager;

    test.beforeEach(async ({ page, context }) => {
        poManager = new ProductsDataManager(page, context);
        
        console.log("LOG: Starting Login Flow...");
        await page.goto('https://smokevanaerp.phantasm-agents.ai/login');
        const loginPage = poManager.getLoginPage();
        await loginPage.login(testData.admin.username, testData.admin.password);
        
        // Handle Tour if visible
        try {
            const endTourBtn = page.getByRole('button', { name: 'End tour' });
            await endTourBtn.click({ force: true, timeout: 5000 });
            console.log("LOG: End Tour button forcefully clicked.");
        } catch (e) {
            console.log("LOG: End Tour button not present, skipping...");
        }
    });

    test('Scenario: Editing Selling Prices using Page Object Model', async ({ page }) => {
        const editSellingPricePage = poManager.getEditSellingPricePage();

        console.log("LOG: Navigating to Edit Selling Price...");
        await editSellingPricePage.navigate();
        
        console.log("LOG: Applying Filters...");
        // Applying the specific filters from your raw script
        await editSellingPricePage.applyFilters('Smokevana Prime B2B', 'All', 'KINGS Test');

        console.log("LOG: Filling Prices...");
        await editSellingPricePage.fillPrices({
            cost: '200',
            sellingPrice: '300',
            prime: '280',
            diamond: '260',
            lowest: '250',
            platinum: '240',
            gold: '220',
            silver: '275'
        });

        console.log("LOG: Applying Prices to All Rows...");
        await editSellingPricePage.applyToAllRows();

        console.log("LOG: Saving Changes...");
        await editSellingPricePage.saveChanges();
        
        // The script clicked Save then the test ended smoothly. We can wait for it to process network if necessary:
        await page.waitForLoadState('networkidle');
        console.log("LOG: Selling prices edited successfully!");
    });
});
