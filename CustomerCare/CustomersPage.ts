import { Page, Locator } from "@playwright/test";

export class CustomersPage {
    readonly page: Page;
    readonly addBtn: Locator;
    readonly searchInput: Locator;
    readonly customerTable: Locator;
    readonly statusTabs: Locator;

    // Add Customer Form Locators
    readonly businessNameInput: Locator;
    readonly locationDropdown: Locator;
    readonly customerGroupIdSelect: Locator;
    readonly prefixInput: Locator;
    readonly firstNameInput: Locator;
    readonly lastNameInput: Locator;
    readonly mobileInput: Locator;
    readonly emailInput: Locator;
    readonly addressLine1: Locator;
    readonly cityInput: Locator;
    readonly stateInput: Locator;
    readonly countryInput: Locator;
    readonly zipCodeInput: Locator;
    readonly saveBtn: Locator;
    readonly postSaveNoBtn: Locator;

    constructor(page: Page) {
        this.page = page;
        this.addBtn = page.locator('#amazonAddBtn');
        this.searchInput = page.getByPlaceholder('Search...');
        this.customerTable = page.locator('#contact_table');
        this.statusTabs = page.locator('.amazon-tab');

        // Add Customer Form
        this.businessNameInput = page.getByPlaceholder('Business Name');
        this.locationDropdown = page.locator('.select2-selection__rendered').filter({ hasText: 'Please Select' });
        this.customerGroupIdSelect = page.locator('select[name="customer_group_id"]');
        this.prefixInput = page.getByPlaceholder('Mr / Mrs / Miss');
        this.firstNameInput = page.getByPlaceholder('First Name');
        this.lastNameInput = page.getByPlaceholder('Last Name');
        this.mobileInput = page.getByPlaceholder('Mobile');
        this.emailInput = page.getByPlaceholder('Email');
        this.addressLine1 = page.getByPlaceholder('Address line 1');
        this.cityInput = page.getByPlaceholder('City');
        this.stateInput = page.getByPlaceholder('State');
        this.countryInput = page.getByPlaceholder('Country');
        this.zipCodeInput = page.getByPlaceholder('Zip/Postal Code');
        this.saveBtn = page.getByRole('button', { name: 'Save' });
        this.postSaveNoBtn = page.locator('button:has-text("No"), .swal2-cancel').last();
    }

    async clickAdd() {
        await this.addBtn.click();
    }

    async search(text: string) {
        await this.searchInput.fill(text);
        await this.page.keyboard.press('Enter');
    }

    async filterByStatus(status: 'Active' | 'Inactive' | 'Pending' | 'Rejected' | 'Guest') {
        await this.statusTabs.filter({ hasText: status }).click();
    }

    async getCustomerRow(text: string | RegExp) {
        // Use a more flexible row locator
        return this.page.getByRole('row').filter({ hasText: text });
    }

    async fillCustomerForm(data: any) {
        if (data.businessName) await this.businessNameInput.fill(data.businessName);
        
        if (data.location) {
            await this.locationDropdown.click();
            // Use a more relaxed locator for location selection
            await this.page.locator('li.select2-results__option', { hasText: data.location }).click();
        }

        if (data.customerGroupId) {
            await this.customerGroupIdSelect.selectOption(data.customerGroupId);
        }

        if (data.prefix) await this.prefixInput.fill(data.prefix);
        if (data.firstName) await this.firstNameInput.fill(data.firstName);
        if (data.lastName) await this.lastNameInput.fill(data.lastName);
        if (data.mobile) await this.mobileInput.fill(data.mobile);
        if (data.email) await this.emailInput.fill(data.email);

        if (data.address) {
            await this.addressLine1.fill(data.address);
            if (data.addressSuggestion) {
                // Wait for the suggestion list and click the first matching one
                const suggestionLocator = this.page.locator('.pac-item, .select2-results__option').filter({ hasText: data.addressSuggestion });
                try {
                    await suggestionLocator.first().waitFor({ state: 'visible', timeout: 5000 });
                    await suggestionLocator.first().click();
                } catch (e) {
                    console.log("Specific suggestion not found, skipping click step.");
                }
            }
        }

        if (data.city) await this.cityInput.fill(data.city);
        if (data.state) await this.stateInput.fill(data.state);
        if (data.country) await this.countryInput.fill(data.country);
        if (data.zipCode) await this.zipCodeInput.fill(data.zipCode);
    }

    async clickSave() {
        await this.saveBtn.click();
    }

    async clickNoOnPostSave() {
        // Wait for the 'No' button to be visible (likely in a confirmation modal)
        await this.postSaveNoBtn.waitFor({ state: 'visible' });
        await this.postSaveNoBtn.click();
    }
}
