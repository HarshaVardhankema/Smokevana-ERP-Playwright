import { Page, Locator } from "@playwright/test";

export class LoginPage {
    readonly page: Page;
    readonly usernameField: Locator;
    readonly passwordField: Locator;
    readonly loginButton: Locator;

    constructor(page: Page) {
        this.page = page;
        this.usernameField = page.getByLabel('Username');
        this.passwordField = page.getByLabel('Password');
        this.loginButton = page.getByRole('button', { name: 'Sign in' });
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
