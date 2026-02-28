import { Page, Locator } from "@playwright/test";

export class NewSalesorderPage {
    readonly page: Page;

    // From sales_order/index.blade.php
    readonly addSalesOrderButton: Locator;

    // Step 1: Customer dropdown - span.select2-selection--single
    readonly customerDropdown: Locator;

    // Step 3: Enable Matrix toggle - div#toggle_switch_display
    readonly matrixToggle: Locator;

    // Step 4: Product search - input#search_product
    readonly productSearchInput: Locator;

    // Step 9: Add Matrix button - button#save_button_metrix
    readonly addMatrixButton: Locator;

    // Step 10: Save button - button#submit-sell
    readonly saveButton: Locator;

    // Step 11: Confirm button - button#confirm_invoice_submit
    readonly confirmButton: Locator;

    constructor(page: Page) {
        this.page = page;

        this.addSalesOrderButton = page.locator('a.amazon-btn.amazon-btn-primary', { hasText: 'Add Sales Order' });
        this.customerDropdown = page.locator('span.select2-selection.select2-selection--single').first();
        this.matrixToggle = page.locator('div#toggle_switch_display');
        this.productSearchInput = page.locator('input#search_product');
        this.addMatrixButton = page.locator('button#save_button_metrix');
        this.saveButton = page.locator('button#submit-sell');
        this.confirmButton = page.locator('button#confirm_invoice_submit');
    }

    /** Step 1: Click Add Sales Order button on the index page */
    async clickAddSalesOrder() {
        await this.addSalesOrderButton.waitFor({ state: 'visible', timeout: 10000 });
        await this.addSalesOrderButton.click();
    }

    /** Step 1: Click the Customer dropdown to open it */
    async openCustomerDropdown() {
        await this.customerDropdown.waitFor({ state: 'visible', timeout: 10000 });
        await this.customerDropdown.click();
    }

    /** Step 2: Type in the Select2 search field and pick first result */
    async searchAndSelectCustomer(customerName: string) {
        // The customer search field is the 3rd select2-search__field on the page (nth index 2)
        const searchInput = this.page.locator('input.select2-search__field').nth(2);
        await searchInput.waitFor({ state: 'visible', timeout: 5000 });
        await searchInput.fill(customerName);
        await this.page.waitForTimeout(1000);

        const firstOption = this.page.locator('li.select2-results__option:visible').first();
        await firstOption.waitFor({ state: 'visible', timeout: 10000 });
        await firstOption.click();
    }

    /** Step 3: Click the Enable Matrix toggle */
    async clickEnableMatrix() {
        await this.matrixToggle.waitFor({ state: 'visible', timeout: 5000 });
        await this.matrixToggle.click({ force: true });
        await this.page.waitForTimeout(1000);
    }

    /** Step 4: Type in the product search field */
    async searchProduct(productName: string) {
        await this.productSearchInput.waitFor({ state: 'visible', timeout: 10000 });
        await this.productSearchInput.fill(productName);
        await this.page.waitForTimeout(1000);
    }

    /** Step 5: Select the first autocomplete result */
    async selectFirstSearchResult() {
        const firstResult = this.page.locator('li.ui-menu-item:visible').first();
        await firstResult.waitFor({ state: 'visible', timeout: 10000 });
        await firstResult.click();
        await this.page.waitForTimeout(1000);
    }

    /** Step 6-8: Fill quantity inputs using input.quantity-input by index */
    async fillQuantity(index: number, qty: string) {
        const qtyInput = this.page.locator('input.quantity-input').nth(index);
        await qtyInput.waitFor({ state: 'visible', timeout: 5000 });
        await qtyInput.click({ clickCount: 3 });
        await qtyInput.fill(qty);
    }

    /** Step 9: Click the Add (Matrix) button */
    async clickAddMatrix() {
        await this.addMatrixButton.waitFor({ state: 'visible', timeout: 5000 });
        await this.addMatrixButton.click();
        await this.page.waitForTimeout(1000);
    }

    /** Step 10: Click Save button */
    async clickSave() {
        await this.saveButton.waitFor({ state: 'visible', timeout: 10000 });
        await this.saveButton.click();
    }

    /** Step 11: Click Confirm button */
    async clickConfirm() {
        await this.confirmButton.waitFor({ state: 'visible', timeout: 10000 });
        await this.confirmButton.click();
    }
}
