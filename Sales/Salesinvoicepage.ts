import { Page, Locator } from "@playwright/test";

export class SalesInvoicePage {
    page: Page;
    customerDropdown: Locator;
    searchCustomerInput: Locator;
    productSearchPlaceholder: Locator;
    enableMatrixToggle: Locator;
    matrixAddButton: Locator;
    saveButton: Locator;
    confirmButton: Locator;

    constructor(page: Page) {
        this.page = page;
        // Customer Select
        this.customerDropdown = page.locator('span.select2-selection--single').first();
        this.searchCustomerInput = page.locator('input.select2-search__field');

        // Matrix & Product Search
        this.enableMatrixToggle = page.locator('#matrix_toggle_label');
        this.productSearchPlaceholder = page.locator('input[placeholder="Enter Product name / SKU / Scan bar code"]');
        this.matrixAddButton = page.locator('#save_button_metrix');

        // Finalize / Save / Confirm
        this.saveButton = page.locator('#submit-sell');
        this.confirmButton = page.locator('#confirm_invoice_submit');
    }

    async navigate() {
        console.log(`Navigating to Sales Invoice (sells/create)...`);
        await this.page.goto('/sells/create');
        await this.page.waitForLoadState('networkidle');
    }

    async selectCustomer(customerName: string) {
        console.log(`Selecting customer ${customerName}...`);
        await this.customerDropdown.waitFor({ state: 'visible' });
        await this.customerDropdown.click();

        // Search and pick
        const searchField = this.searchCustomerInput.locator('visible=true');
        await searchField.waitFor({ state: 'visible', timeout: 5000 });
        await searchField.fill(customerName);
        await this.page.waitForTimeout(1000);

        const option = this.page.locator('li.select2-results__option:visible').first();
        await option.waitFor({ state: 'visible', timeout: 5000 });
        await option.click();
    }

    async clickEnableMatrix() {
        console.log(`Enabling matrix...`);
        await this.enableMatrixToggle.waitFor({ state: 'visible' });
        await this.enableMatrixToggle.click({ force: true });
        await this.page.waitForTimeout(1000);
    }

    async searchProduct(productName: string) {
        console.log(`Searching for product ${productName}...`);
        await this.productSearchPlaceholder.waitFor({ state: 'visible' });
        await this.productSearchPlaceholder.click();
        await this.page.keyboard.type(productName, { delay: 100 });

        const firstProduct = this.page.locator('ul.ui-autocomplete li.ui-menu-item').first();
        await firstProduct.waitFor({ state: 'visible', timeout: 10000 });
        await firstProduct.click();
    }

    async fillMatrixQuantity(index: number, quantity: string) {
        console.log(`Filling matrix quantity for index ${index}: ${quantity}`);
        const qtyInputs = this.page.locator('input.quantity-input');
        await qtyInputs.nth(index).waitFor({ state: 'visible', timeout: 10000 });
        await qtyInputs.nth(index).click();
        await qtyInputs.nth(index).clear();
        await this.page.keyboard.type(quantity, { delay: 100 });
    }

    async clickAddMatrix() {
        console.log(`Clicking Matrix Add button...`);
        await this.matrixAddButton.waitFor({ state: 'visible' });
        await this.matrixAddButton.click();
        await this.page.waitForTimeout(1000); // give time for table to render
    }

    async addDiscount(rowIndex: number, discountType: 'fixed' | 'percentage', discountAmount: string) {
        console.log(`Adding ${discountType} discount of ${discountAmount} to row ${rowIndex}...`);
        // Find the specific row to apply the discount to
        const row = this.page.locator('table#pos_table tbody tr').nth(rowIndex);

        const discountTypeDropdown = row.locator('select.discount-type-sel');
        await discountTypeDropdown.waitFor({ state: 'visible' });
        await discountTypeDropdown.selectOption(discountType);

        const discountInput = row.locator('input.discount-amt');
        await discountInput.waitFor({ state: 'visible' });
        await discountInput.click();
        await discountInput.clear();
        await discountInput.fill(discountAmount);

        // Trigger blur/change to recalculate POS table totals
        await discountInput.press('Tab');
        await this.page.waitForTimeout(500); // Let JS recalculate
    }

    async submitInvoice() {
        console.log(`Submitting Invoice (Save)...`);
        await this.saveButton.waitFor({ state: 'visible' });
        await this.saveButton.click();

        // Handle invoice preview modal confirm click
        try {
            await this.confirmButton.waitFor({ state: 'visible', timeout: 5000 });
            console.log(`Confirming preview modal...`);
            await this.confirmButton.click();
        } catch (e) {
            console.log(`Invoice preview modal did not appear or was intercepted.`);
        }
    }
}
