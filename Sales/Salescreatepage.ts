import { Page, Locator } from "@playwright/test";

export class SalesCreatePage {
    readonly page: Page;
    readonly customerDropdown: Locator;
    readonly enableMatrixToggle: Locator;
    readonly productSearchPlaceholder: Locator;
    readonly matrixAddButton: Locator;
    readonly saveButton: Locator;
    readonly confirmButton: Locator;
    readonly invoiceLink: Locator;

    constructor(page: Page) {
        this.page = page;
        // Step 7: Click on Customer Dropdown
        this.customerDropdown = page.locator('span.select2-selection--single').first();
        // Step 10: Toggle Button (Enable Matrix) - Using user's specific element
        this.enableMatrixToggle = page.locator('#matrix_toggle_label');
        // Step 11: placeholder="Enter Product name / SKU / Scan bar code"
        this.productSearchPlaceholder = page.locator('input[placeholder="Enter Product name / SKU / Scan bar code"]');
        // Step 17: id="save_button_metrix"
        this.matrixAddButton = page.locator('#save_button_metrix');
        // Step 19: Click on Save
        this.saveButton = page.locator('#submit-sell');
        // Step 20: Click on Confirm
        this.confirmButton = page.locator('#confirm_invoice_submit');
        // Step 21: class="invoice-link"
        this.invoiceLink = page.locator('a.invoice-link');
    }

    async selectCustomer(customerName: string) {
        console.log(`Step 7: Clicking Customer Dropdown...`);
        await this.customerDropdown.waitFor({ state: 'visible', timeout: 10000 });
        await this.customerDropdown.click();

        console.log(`Step 8: Entering customer name: ${customerName}...`);
        // Use user-provided locator for Step 8
        const searchField = this.page.locator('input.select2-search__field:visible');
        await searchField.waitFor({ state: 'visible', timeout: 5000 });
        await searchField.fill(customerName);
        await this.page.waitForTimeout(1000); // Wait for results

        console.log(`Step 9: Selecting customer matching: ${customerName}...`);
        const option = this.page.locator('li.select2-results__option:visible').first();
        await option.waitFor({ state: 'visible', timeout: 10000 });
        await option.click();
    }

    async clickEnableMatrix() {
        console.log(`Step 10: Clicking Enable Matrix toggle...`);
        // Step 10: Click on Toggle Button as Enable Matrix
        await this.enableMatrixToggle.waitFor({ state: 'visible' });
        await this.enableMatrixToggle.click({ force: true });
        await this.page.waitForTimeout(1000);
    }

    async searchProduct(productName: string) {
        console.log(`Step 11-13: Searching and selecting product: ${productName}...`);
        // Step 11: Click on placeholder
        await this.productSearchPlaceholder.waitFor({ state: 'visible' });
        await this.productSearchPlaceholder.click();

        // Step 12: Enter the Product name
        await this.page.keyboard.type(productName, { delay: 100 });

        // Step 13: Select the First Product
        const firstProduct = this.page.locator('ul.ui-autocomplete li.ui-menu-item').first();
        await firstProduct.waitFor({ state: 'visible', timeout: 10000 });
        await firstProduct.click();
    }

    async fillMatrixQuantity(index: number, value: string) {
        console.log(`Step 14-16: Filling matrix quantity for index ${index}: ${value}...`);
        // Steps 14, 15, 16: Using user-provided class structure
        const qtyInputs = this.page.locator('input.quantity-input');
        await qtyInputs.nth(index).waitFor({ state: 'visible', timeout: 15000 });
        await qtyInputs.nth(index).click();
        await qtyInputs.nth(index).clear();
        await this.page.keyboard.type(value, { delay: 100 });
    }

    async clickAdd() {
        console.log(`Step 17: Clicking Add button...`);
        await this.matrixAddButton.waitFor({ state: 'visible' });
        await this.matrixAddButton.click();
        await this.page.waitForTimeout(1000);
    }

    async clickSave() {
        console.log(`Step 19: Clicking Save...`);
        await this.saveButton.waitFor({ state: 'visible' });
        await this.saveButton.click();
    }

    async clickConfirm() {
        console.log(`Step 20: Clicking Confirm...`);
        const confirmBtn = this.page.locator('#confirm_invoice_submit');
        await confirmBtn.waitFor({ state: 'visible', timeout: 15000 });
        await confirmBtn.click();
    }
}
