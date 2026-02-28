import { test, expect } from "@playwright/test";
import { LoginDataManager } from "../UserManagement/Datamanager";

test("Smokevana ERP Login Test", async ({ page, context }) => {
    const dataManager = new LoginDataManager(page, context);
    const loginPage = dataManager.getLoginPage();

    await loginPage.navigate();
    await loginPage.login("admin", "123456");

    // Verification
    await expect(page).not.toHaveURL(/.*login/);
});

