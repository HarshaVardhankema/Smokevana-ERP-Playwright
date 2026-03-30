import { Page, Locator, expect } from '@playwright/test';

export class ListPurchaseRequestPage {
    page: Page;
    vendorCareMenu: Locator;
    listPurchaseRequestMenu: Locator;
    addBtn: Locator;

    constructor(page: Page) {
        this.page = page;
        this.vendorCareMenu = page.getByRole('link', { name: 'Vendor Care' });
        // The exact menu name might need to be adjusted based on the codegen trace
        this.listPurchaseRequestMenu = page.getByRole('link', { name: 'List Purchase Request' }); 
        this.addBtn = page.getByRole('link', { name: 'Add', exact: true });
    }

    async navigateToListPurchaseRequest() {
        if (!(await this.listPurchaseRequestMenu.isVisible())) {
            await this.vendorCareMenu.click();
        }
        await this.listPurchaseRequestMenu.click();
        
        // This assertion might need adjustment based on the actual table headers loaded
        await expect(this.page.getByRole('row', { name: 'Action Date: activate to sort' }).first()).toBeVisible();
    }

    async clickAddRequest() {
        await this.addBtn.click();
    }
}
