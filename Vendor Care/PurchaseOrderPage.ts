import { Page, Locator, expect } from '@playwright/test';

export class PurchaseOrderPage {
    page: Page;
    vendorCareMenu: Locator;
    purchaseOrderMenu: Locator;
    addBtn: Locator;
    supplierDropdownContainer: Locator;
    supplierSearchInput: Locator;
    locationDropdown: Locator;
    productSearchInput: Locator;
    quantityInput: Locator;
    unitPriceInput: Locator;
    saveBtn: Locator;
    actionToggleFirst: Locator;
    createPRLink: Locator;

    constructor(page: Page) {
        this.page = page;
        this.vendorCareMenu = page.getByRole('link', { name: 'Vendor Care' });
        this.purchaseOrderMenu = page.getByRole('link', { name: 'Purchase Order' });
        this.addBtn = page.getByRole('link', { name: 'Add', exact: true });
        this.supplierDropdownContainer = page.locator('#select2-supplier_id-container');
        this.supplierSearchInput = page.locator('input[type="search"]').nth(2);
        this.locationDropdown = page.locator('#add_purchase_form select[name="location_id"]');
        this.productSearchInput = page.getByRole('textbox', { name: 'Enter Product name / SKU /' });
        this.quantityInput = page.locator('input[name="purchases[0][quantity]"]');
        this.unitPriceInput = page.locator('input[name="purchases[0][pp_without_discount]"]');
        this.saveBtn = page.getByRole('button', { name: 'Save', exact: true });
        this.actionToggleFirst = page.getByRole('button', { name: 'Actions Toggle Dropdown' }).first();
        this.createPRLink = page.getByRole('link', { name: ' Create PR' });
    }

    async navigateToPurchaseOrder() {
        if (!(await this.purchaseOrderMenu.isVisible())) {
            await this.vendorCareMenu.click();
        }
        await this.purchaseOrderMenu.click();
        await expect(this.page.getByRole('row', { name: 'Action Date: activate to sort' }).first()).toBeVisible();
    }

    async clickAddPurchaseOrder() {
        await this.addBtn.click();
        await expect(this.page.getByRole('link', { name: 'Home' })).toBeVisible();
    }

    async fillPurchaseOrderDetails(supplierName: string, productSku: string, quantity: string, unitPrice: string) {
        await this.supplierDropdownContainer.click();
        
        await this.supplierSearchInput.fill(supplierName);
        // Using filter and first() makes this resilient against exact generated names
        await this.page.getByRole('treeitem').filter({ hasText: supplierName }).first().click();

        await this.locationDropdown.selectOption('1');
        
        await this.productSearchInput.click();
        await this.productSearchInput.fill(productSku);
        
        // Let UI fetch product, we wait for product row to appear using regex that contains the Sku
        const productRegex = new RegExp(productSku);
        await expect(this.page.getByRole('row', { name: productRegex }).first()).toBeVisible();

        await this.quantityInput.click();
        await this.quantityInput.fill(quantity);
        await expect(this.page.getByRole('row', { name: 'Total Items:' })).toBeVisible();

        await this.unitPriceInput.click();
        await this.unitPriceInput.fill(unitPrice);
        await expect(this.page.getByRole('row', { name: productRegex }).first()).toBeVisible();
    }

    async savePurchaseOrder() {
        await this.saveBtn.click();
        await expect(this.page.getByRole('row', { name: 'Action Date: activate to sort' }).first()).toBeVisible();
    }

    async createPRForFirstOrder() {
        await this.actionToggleFirst.click();
        await expect(this.page.getByRole('link', { name: 'View' })).toBeVisible();
        await this.createPRLink.click();
    }

    async fillPRDetailsAndSave(productSku: string, quantity: string) {
        const productRegex = new RegExp(productSku);
        await expect(this.page.getByRole('row', { name: productRegex }).first()).toBeVisible();
        
        await this.quantityInput.click();
        await this.quantityInput.fill(quantity);
        await expect(this.page.getByRole('row', { name: productRegex }).first()).toBeVisible();

        await this.locationDropdown.selectOption('1');
        await this.page.getByRole('button', { name: 'Save' }).click();
    }
}
