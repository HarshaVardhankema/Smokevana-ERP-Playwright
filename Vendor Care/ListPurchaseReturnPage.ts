import { Page, Locator, expect } from '@playwright/test';

export class ListPurchaseReturnPage {
    page: Page;
    vendorCareMenu: Locator;
    listPurchaseReturnMenu: Locator;
    addBtn: Locator;
    
    // Form fields
    supplierDropdown: Locator;
    supplierSearchInput: Locator;
    branchDropdown: Locator;
    productSearchInput: Locator;
    submitBtn: Locator;

    constructor(page: Page) {
        this.page = page;
        this.vendorCareMenu = page.getByRole('link', { name: 'Vendor Care' });
        this.listPurchaseReturnMenu = page.getByRole('link', { name: 'List Purchase Return' }); 
        this.addBtn = page.getByRole('link', { name: 'Add', exact: true });
        
        this.supplierDropdown = page.locator('#select2-supplier_id-container');
        this.supplierSearchInput = page.locator('input[type="search"]');
        this.branchDropdown = page.getByTitle('Please Select'); // Or #select2-location_id-container
        this.productSearchInput = page.getByRole('textbox', { name: 'Search Products' });
        this.submitBtn = page.getByRole('button', { name: 'Submit' });
    }

    async navigateToListPurchaseReturn() {
        if (!(await this.listPurchaseReturnMenu.isVisible())) {
            await this.vendorCareMenu.click();
        }
        await this.listPurchaseReturnMenu.click();
        await expect(this.page.getByRole('row', { name: 'Date: activate to sort column' }).first()).toBeVisible();
    }

    async clickAddReturn() {
        await this.addBtn.click();
        await expect(this.page.getByRole('row', { name: 'Product Quantity Unit Price' }).first()).toBeVisible();
    }

    async fillReturnFormAndSubmit(supplierSearch: string, branchName: string, productSKU: string) {
        // Select Supplier dynamically
        await this.supplierDropdown.click();
        await this.supplierSearchInput.fill(supplierSearch);
        // Wait for autocomplete and pick the first matching treeitem (handles dynamic IDs like Auto Vendor-034739)
        const supplierOption = this.page.getByRole('treeitem', { name: new RegExp(supplierSearch, 'i') }).first();
        await expect(supplierOption).toBeVisible();
        await supplierOption.click();

        // Select Branch securely
        await this.branchDropdown.click();
        const branchOption = this.page.getByRole('treeitem', { name: branchName }).first();
        await expect(branchOption).toBeVisible();
        await branchOption.click();

        // Search Product string/SKU
        await this.productSearchInput.click();
        await this.productSearchInput.fill(productSKU);
        
        // Ensure the product was selected/loaded (the codegen waited for the row to appear)
        await expect(this.page.getByRole('row', { name: new RegExp(productSKU) }).first()).toBeVisible();

        // Setup the dialog handler directly before submitting to catch any alerts that pop during submit
        this.page.once('dialog', dialog => {
            console.log(`Auto-dismissed dialog: ${dialog.message()}`);
            dialog.dismiss().catch(() => {});
        });

        await this.submitBtn.click();
    }
}
