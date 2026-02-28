import { Page, Locator } from "@playwright/test";

export class LoginPage {
    readonly page: Page;
    readonly usernameField: Locator;
    readonly passwordField: Locator;
    readonly loginButton: Locator;

    constructor(page: Page) {
        this.page = page;
        this.usernameField = page.locator('#username');
        this.passwordField = page.locator('#password');
        this.loginButton = page.locator('button.amazon-btn-primary, button:has-text("Sign in")');
    }

    async navigate() {
        await this.page.goto("/login");
    }

    async login(username: string, password: string) {
        console.log(`Filling username: ${username}...`);
        await this.usernameField.fill(username);
        console.log(`Filling password...`);
        await this.passwordField.fill(password);
        console.log(`Clicking Sign in...`);
        await this.loginButton.click();
    }
}
