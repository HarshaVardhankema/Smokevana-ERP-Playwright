import { Page, Locator } from "@playwright/test";

export class UserManagementPage {
    readonly page: Page;
    readonly addUserButton: Locator;
    readonly firstNameField: Locator;
    readonly emailField: Locator;
    readonly usernameField: Locator;
    readonly passwordField: Locator;
    readonly confirmPasswordField: Locator;
    readonly roleDropdown: Locator;
    readonly saveButton: Locator;

    constructor(page: Page) {
        this.page = page;
        this.addUserButton = page.locator('a:has-text("Add User")');
        this.firstNameField = page.locator('#first_name');
        this.emailField = page.locator('#email');
        this.usernameField = page.locator('#username');
        this.passwordField = page.locator('#password');
        this.confirmPasswordField = page.locator('#confirm_password');
        this.roleDropdown = page.locator('#role');
        this.saveButton = page.locator('#submit_user_button');
    }

    async clickAddUser() {
        await this.addUserButton.first().click();
    }

    async fillUserForm(data: {
        firstName: string;
        email: string;
        username: string;
        password: string;
        confirmPassword?: string;
        role: string;
    }) {
        await this.firstNameField.fill(data.firstName);
        await this.emailField.fill(data.email);
        await this.usernameField.fill(data.username);
        await this.passwordField.fill(data.password);
        await this.confirmPasswordField.fill(data.confirmPassword || data.password);

        // Handling Select2 Role Dropdown
        // Note: Direct select might work if it's not a complex select2, 
        // but often we need to interact with the container.
        await this.roleDropdown.selectOption({ label: data.role });
    }

    async clickSave() {
        await this.saveButton.click();
    }
}
