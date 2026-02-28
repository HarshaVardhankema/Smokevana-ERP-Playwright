import { Page, Locator } from "@playwright/test";

export class SalesOrderPage {
    readonly page: Page;
    readonly customerDropdown: Locator;
    readonly searchProductInput: Locator;
    readonly orderTypeDropdown: Locator;
    readonly submitButton: Locator;
    readonly confirmSubmitModalButton: Locator;
    readonly posTable: Locator;

    constructor(page: Page) {
        this.page = page;

        // Locators directly extracted from resources/views/sell/create.blade.php
        this.customerDropdown = page.locator('select#customer_id');
        this.searchProductInput = page.locator('input#search_product');
        this.orderTypeDropdown = page.locator('select#order_type');
        this.submitButton = page.locator('button#submit-sell');

        // Modal Preview Confirm Button
        this.confirmSubmitModalButton = page.locator('button#confirm_invoice_submit');

        // POS Table
        this.posTable = page.locator('table#pos_table tbody');
    }

    async navigate() {
        // Assuming /sells/create?type=sales_order is the correct endpoint based on Controller logic
        await this.page.goto('/sells/create?status=ordered&status=quotation');
        // We'll refine this URL based on the actual routing, usually it's /sells/create with a query param or a dedicated /sales-order/create
    }

    async selectCustomer(customerName: string) {
        // Depending on whether it's a Select2 dropdown or standard select
        // For Select2, standard approach:
        await this.page.locator('span.select2-selection').first().click();
        await this.page.locator('input.select2-search__field').fill(customerName);
        await this.page.waitForTimeout(1000); // Wait for API debounce
        await this.page.locator('.select2-results__option--highlighted').click();
    }

    async addProduct(productName: string) {
        await this.searchProductInput.fill(productName);
        await this.page.waitForTimeout(1000); // Wait for API autocomplete
        // Assuming typical jQuery UI autocomplete used in the project
        await this.page.locator('ul.ui-autocomplete li.ui-menu-item').first().click();
    }

    async setOrderType(type: 'shipping' | 'pickup') {
        await this.orderTypeDropdown.selectOption(type);
    }

    async submitOrder() {
        await this.submitButton.click();

        // Wait for preview modal to appear and click confirm
        try {
            await this.confirmSubmitModalButton.waitFor({ state: 'visible', timeout: 3000 });
            await this.confirmSubmitModalButton.click();
        } catch (e) {
            console.log("No invoice preview modal appeared, or it was bypassed.");
        }
    }
}
