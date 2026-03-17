import { test, expect } from '@playwright/test';
import { LoginPage } from '../UserManagement/Loginpage';

test('find more ids', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.navigate();
    await loginPage.login('admin', '123456');

    await page.goto('https://smokevanaerp.phantasm-agents.ai/products/create', { waitUntil: 'load' });
    
    const logDetails = async (labelText: string) => {
        const label = page.locator('label', { hasText: labelText }).first();
        if (await label.count() > 0) {
            const forAttr = await label.getAttribute('for');
            let input = page.locator(`[id="${forAttr}"]`);
            if (await input.count() === 0) {
                input = label.locator('..').locator('input, select').first();
            }
            console.log(labelText, '-> ID:', await input.getAttribute('id'), 'Name:', await input.getAttribute('name'));
        } else {
            console.log(labelText, 'not found');
        }
    };
    
    await logDetails('Alert quantity');
    await logDetails('ML Value');
    await logDetails('Max Sale Limit');
    await logDetails('Exc. tax:*');
    await logDetails('Barcode No:*');
});
