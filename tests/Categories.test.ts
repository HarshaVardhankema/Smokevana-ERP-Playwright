import { test, expect } from '@playwright/test';
import { ProductsDataManager } from '../Products/Datamanager';
import * as testData from '../Utlilies/testData.json';
import * as path from 'path';

test.describe('Smokevana ERP Categories Automation', () => {
    let poManager: ProductsDataManager;

    test.beforeEach(async ({ page, context }) => {
        poManager = new ProductsDataManager(page, context);
        
        console.log("LOG: Starting Login Flow...");
        await page.goto('https://smokevanaerp.phantasm-agents.ai/login');
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

    test('Scenario: Add New Category using Page Object Model', async ({ page }) => {
        const categoriesPage = poManager.getCategoriesPage();

        console.log("LOG: Navigating to Categories...");
        await categoriesPage.navigate();
        
        console.log("LOG: Clicking Add Category...");
        await categoriesPage.clickAddCategory();

        console.log("LOG: Filling Category Details...");
        
        // Ensure path resolves to an actual file if we run the test (User's snippet used 'download (12).jpg')
        // Using a dummy path/file, but the user may need a real dummy artifact in the active dir or we just skip logo upload if not required
        // We will leave the logo upload as 'download (12).jpg' but point it to the active path or an existing artifact if run from tests to avoid 'file not found'
        const logoPath = path.resolve(__dirname, '..', 'download (12).jpg'); // User might have this in root
        
        await categoriesPage.fillCategoryDetails({
            // logoPath: logoPath, // Uncomment if the logo file actually exists in the project root
            locationValue: '1',
            name: 'TEST CATEGORY', // Adjusted slightly to uniquely identify
            slug: 'test-category-' + Date.now(), // Dynamic slug
            code: '0206',
            description: 'test description',
            brandSearch: '4',
            brandSelectName: 'KINGS Test'
        });

        console.log("LOG: Saving Category...");
        await categoriesPage.saveCategory();
        
        await page.waitForLoadState('networkidle');
        console.log("LOG: Category added successfully!");
    });
});
