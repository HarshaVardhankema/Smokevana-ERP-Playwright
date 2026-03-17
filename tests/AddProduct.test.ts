import { test, expect } from '@playwright/test';
import { LoginPage } from '../UserManagement/Loginpage';
import { ProductsPage } from '../Products/ProductsPage';
import * as testData from '../Utlilies/testData.json';
import * as path from 'path';

test.describe('Smokevana ERP Product Automation', () => {
    let loginPage: LoginPage;
    let productsPage: ProductsPage;

    test.beforeEach(async ({ page }) => {
        loginPage = new LoginPage(page);
        productsPage = new ProductsPage(page);
        
        console.log("LOG: Login...");
        await page.goto('https://smokevanaerp.phantasm-agents.ai/login');
        await loginPage.login(testData.admin.username, testData.admin.password);
        
        // Handle Tour if visible
        try {
            const endTourBtn = page.getByRole('button', { name: 'End tour' });
            if (await endTourBtn.isVisible({ timeout: 5000 })) {
                await endTourBtn.click();
            }
        } catch (e) {
            console.log("LOG: Tour not visible, skipping...");
        }
    });

    test('Scenario: Add New Variable Product - Success Flow', async ({ page }) => {
        const unique = Date.now();
        const productName = `TEST-THCPA-${unique}`;
        const sku = `SKU-${unique}`;

        console.log("LOG: Navigating to Add Product page...");
        await page.goto('https://smokevanaerp.phantasm-agents.ai/products/create');
        await expect(page).toHaveURL(/.*create/);

        console.log(`LOG: Filling Basic Info for ${productName}...`);
        await productsPage.fillBasicInfo({
            name: productName,
            sku: sku,
            brand: "4 KINGS Test", 
            category: "THCA",
            alertQty: "5",
            visibility: "Public Product"
        });

        console.log("LOG: Setting Additional Info...");
        await productsPage.setAdditionalInfo({
            maxSaleLimit: "5",
            ct: "5"
        });

        console.log("LOG: Configuring Variable Product details...");
        // Following user's specific variation flow
        // 1. Set type to Variable
        await productsPage.productTypeDropdown.selectOption('variable'); 
        
        // 2. Select Variation Template (Flavor = 1)
        console.log("LOG: Selecting Variation Template: Flavor");
        await productsPage.variationTemplateDropdown.selectOption('1');
        
        // 3. Select Variation Values (Wild Mango and Chilli Berry)
        console.log("LOG: Selecting Variation Values...");
        await page.locator('input[name="product_variation[0][variations][0][value]"]').fill('MANGO');
        await page.getByRole('treeitem', { name: 'WILD MANGO', exact: true }).click();
        
        await page.getByRole('cell', { name: 'Flavor Select variation values' }).getByRole('textbox').fill('BERRY');
        await page.getByRole('treeitem', { name: 'CHILLI BERRY' }).click();

        // Wait for rows to finish re-rendering
        await page.waitForTimeout(3000);

        // 4. Fill Prices for all generated rows
        console.log("LOG: Filling Variation Pricing...");
        await page.locator('input[name="product_variation[0][variations][0][default_purchase_price]"]').fill('40');
        await page.locator('input[name="product_variation[0][variations][0][default_sell_price]"]').fill('100');

        await page.locator('input[name="product_variation[0][variations][1][default_purchase_price]"]').fill('50');
        await page.locator('input[name="product_variation[0][variations][1][default_sell_price]"]').fill('120');


        console.log("LOG: Saving product...");
        await productsPage.clickSave();

        // Verification
        console.log("LOG: Verifying product creation...");
        // Wait for redirect to products page, if it doesn't redirect there was an error saving
        await expect(page).toHaveURL(/.*\/products(\?.*)?$/, { timeout: 30000 });
        console.log("LOG: Reached products page. Searching...");
        await page.waitForLoadState('networkidle');
        
        const searchInput = page.getByPlaceholder('Search products...');
        await searchInput.fill(productName);
        await page.keyboard.press('Enter');
        await page.waitForTimeout(3000);

        const productRow = page.locator('table#product_table tbody tr', { hasText: productName });
        await expect(productRow).toBeVisible();
        
        console.log(`LOG: Success! Product ${productName} created with Master SKU: ${sku}`);
        
        // The SKUs of variations are dynamically generated as MasterSKU-1, MasterSKU-2 etc.
        const variation1SKU = `${sku}-1`;
        const variation2SKU = `${sku}-2`;
        
        console.log(`LOG: Variation 1 SKU generated: ${variation1SKU}`);
        console.log(`LOG: Variation 2 SKU generated: ${variation2SKU}`);

        // Save SKUs to a JSON file to help with further testing
        const fs = require('fs');
        const skuData = {
            masterSKU: sku,
            variation1SKU: variation1SKU,
            variation2SKU: variation2SKU,
            productName: productName
        };
        fs.writeFileSync('test-results/generated_skus.json', JSON.stringify(skuData, null, 2));
        console.log("LOG: Saved generated SKUs to test-results/generated_skus.json");
        
        await page.screenshot({ path: `test-results/VariableProductSuccess-${unique}.png` });
    });
});
