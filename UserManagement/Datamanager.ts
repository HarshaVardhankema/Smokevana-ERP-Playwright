import { Page, BrowserContext } from "@playwright/test";
import { LoginPage } from "./Loginpage";
import { DashboardPage } from "./Dashboardpage";
import { UserManagementPage } from "./Users";

export class LoginDataManager {
    readonly page: Page;
    readonly context: BrowserContext;
    private loginPage: LoginPage;
    private dashboardPage: DashboardPage;
    private userManagementPage: UserManagementPage;

    constructor(page: Page, context: BrowserContext) {
        this.page = page;
        this.context = context;
        this.loginPage = new LoginPage(this.page);
        this.dashboardPage = new DashboardPage(this.page);
        this.userManagementPage = new UserManagementPage(this.page);
    }

    getLoginPage(): LoginPage {
        return this.loginPage;
    }

    getDashboardPage(): DashboardPage {
        return this.dashboardPage;
    }

    getUserManagementPage(): UserManagementPage {
        return this.userManagementPage;
    }
}
