import { test, expect } from "@playwright/test";
import { LoginDataManager } from "../UserManagement/Datamanager";
import * as testData from "../Utlilies/testData.json";

test.describe("Smokevana ERP User Management Automation", () => {

    test("Scenario One: Add New Admin User", async ({ page, context }) => {
        const dataManager = new LoginDataManager(page, context);
        const loginPage = dataManager.getLoginPage();
        const dashboardPage = dataManager.getDashboardPage();
        const userManagementPage = dataManager.getUserManagementPage();

        // 1. Click on login URL & Login
        await loginPage.navigate(); // Navigates to the URL defined in config/page
        await loginPage.login(testData.admin.username, testData.admin.password);

        // 5. Click on End Tour (if visible)
        // Using waitFor to handle potential modal loading delay
        const endTour = page.locator('button:has-text("End tour")');
        try {
            await endTour.waitFor({ state: 'visible', timeout: 5000 });
            await endTour.click();
        } catch (e) {
            console.log("End tour modal not found or already closed.");
        }

        // 6. Take the screenshot of Home Page
        await page.screenshot({ path: 'screenshots/home_page.png' });

        // 7. Click on user Management
        await dashboardPage.clickUserManagement();

        // 8. Click on users
        await dashboardPage.clickUsers();

        // 9. Click on Add Users
        await userManagementPage.clickAddUser();

        // 10-16. Fill user form and save using JSON data
        await userManagementPage.fillUserForm({
            firstName: testData.newUser.firstName,
            email: testData.newUser.email,
            username: testData.newUser.username,
            password: testData.newUser.password,
            role: testData.newUser.role
        });

        // 16. Click on Save
        await userManagementPage.clickSave();

        // Verification: Check if redirect or success message appears
        // Typical Laravel behavior: redirect back to index
        await expect(page).toHaveURL(/.*users/);
    });
});
