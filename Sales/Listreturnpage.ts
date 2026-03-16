import { Page, Locator } from "@playwright/test";

export class ListReturnPage {
    page: Page;

    // Locators
    addReturnButton: Locator;
    sellIdDropdown: Locator;
    returnFormBox: Locator;
    returnTableRows: Locator;
    discountTypeDropdown: Locator;
    discountAmountInput: Locator;
    saveButton: Locator;

    constructor(page: Page) {
        this.page = page;

        // Locators based on sell_return/index.blade.php and create.blade.php
        this.addReturnButton = page.locator('a.amazon-orange-add[href*="sell-return/create"]');
        this.sellIdDropdown = page.locator('select#sell_id');
        this.returnFormBox = page.locator('#return_form_box');
        this.returnTableRows = page.locator('table#sell_return_table tbody tr');
        this.discountTypeDropdown = page.locator('select#discount_type');
        this.discountAmountInput = page.locator('input#discount_amount');
        this.saveButton = page.locator('button[type="submit"].tw-dw-btn-primary');
    }

    async clickAddReturn() {
        await this.addReturnButton.waitFor({ state: 'visible' });
        await this.addReturnButton.click();
    }

    async selectInvoiceToReturn(index: number = 1) {
        await this.sellIdDropdown.waitFor({ state: 'visible' });
        // select option by index (0 is usually placeholder so we use 1)
        const options = await this.page.locator('select#sell_id option').all();
        if (options.length > index) {
            const value = await options[index].getAttribute('value');
            if (value) {
                await this.sellIdDropdown.selectOption(value);
            }
        }

        // Wait for the form to populate from the ajax request
        await this.returnFormBox.waitFor({ state: 'visible' });
        // Wait a small buffer for the table rows to render
        await this.page.waitForTimeout(1000);
    }

    async fillReturnQuantities(rowIndex: number = 0, quantity: string) {
        const row = this.returnTableRows.nth(rowIndex);
        const qtyInput = row.locator('input.return_qty');
        await qtyInput.waitFor({ state: 'visible' });
        await qtyInput.click();
        await qtyInput.clear();
        await qtyInput.fill(quantity);
    }

    async applyDiscount(discountType: 'fixed' | 'percentage', amount: string) {
        await this.discountTypeDropdown.waitFor({ state: 'visible' });
        await this.discountTypeDropdown.selectOption(discountType);

        await this.discountAmountInput.waitFor({ state: 'visible' });
        await this.discountAmountInput.click();
        await this.discountAmountInput.clear();
        await this.discountAmountInput.fill(amount);
    }

    async submitReturn() {
        await this.saveButton.waitFor({ state: 'visible' });
        await this.saveButton.click();
    }
}
