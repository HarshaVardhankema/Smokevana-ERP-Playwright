import { test, expect } from '@playwright/test';

test('test login and navigate', async ({ page }) => {
    await page.goto('https://smokevanaerp.phantasm-agents.ai/login');
    await page.waitForTimeout(2000);
    
    // Check what's on the page
    console.log("Current URL:", page.url());
    
    // Fill login
    const userField = page.locator('input[name="username"], #username, input[placeholder*="Username"]');
    await userField.first().fill('admin');
    
    const passField = page.locator('input[name="password"], #password, input[placeholder*="Password"]');
    await passField.first().fill('123456');
    
    await page.click('button:has-text("Sign in")');
    
    await expect(page).toHaveURL(/.*home/, { timeout: 20000 });
    console.log("Login SUCCESS");
    
    await page.goto('https://smokevanaerp.phantasm-agents.ai/contacts?type=customer');
    console.log("Navigated to Customers");
    
    await page.waitForTimeout(3000);
    await page.screenshot({ path: 'debug-customers.png' });
});
