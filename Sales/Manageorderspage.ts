import { Locator, Page } from "@playwright/test";

export class ManageOrdersPage {
    page: Page;
    salesMenuLink: Locator;
    manageOrderLink: Locator;
    firstOrderCheckbox: Locator;
    bypassBtn: Locator;
    confirmBypassBtn: Locator;
    packingTab: Locator;
    makeShipmentBtn: Locator;
    continueBtn: Locator;
    noBtn: Locator;
    endTourBtn: Locator;

    constructor(page: Page) {
        this.page = page;
        this.salesMenuLink = page.getByRole('link', { name: 'Sale', exact: true });
        this.manageOrderLink = page.getByRole('link', { name: 'Manage Order' });

        // Picking the first checkbox inside the table body
        this.firstOrderCheckbox = page.locator('tbody input[type="checkbox"]').first();
        this.bypassBtn = page.getByRole('button', { name: 'BYPASS' });
        this.confirmBypassBtn = page.getByRole('button', { name: ' Confirm Bypass' });
        this.packingTab = page.getByRole('link', { name: 'Packing' });

        this.makeShipmentBtn = page.getByRole('button', { name: ' Make Shipment' });
        this.continueBtn = page.getByRole('button', { name: 'Continue' });
        this.noBtn = page.getByRole('button', { name: 'No' });
        this.endTourBtn = page.getByRole('button', { name: 'End tour' }).first();
    }

    async closeTourIfPresent() {
        try {
            // Wait for the button to appear and click it, with a timeout in case it doesn't appear.
            await this.endTourBtn.waitFor({ state: 'visible', timeout: 5000 });
            await this.endTourBtn.click();
        } catch (error) {
            console.log("End tour button not found or already closed. Continuing...");
        }
    }

    async navigateToManageOrders() {
        await this.salesMenuLink.click();
        await this.manageOrderLink.click();
    }

    async selectUnassignedOrder() {
        const row = this.page.getByRole('row').filter({ hasText: 'Not Assigned' }).first();
        await row.getByRole('checkbox').check();
    }

    async selectPickedOrder() {
        const row = this.page.getByRole('row').filter({ hasText: 'PICKED' }).nth(1);
        await row.getByRole('checkbox').check();
    }

    async clickBypass() {
        await this.bypassBtn.click();
    }

    async confirmBypass() {
        await this.confirmBypassBtn.click();
    }

    async openPackingTab() {
        await this.packingTab.click();
    }

    async selectUnassignedShipment() {
        const row = this.page.getByRole('gridcell', { name: 'PICKED' }).first();
        await row.getByRole('checkbox').check();
    }

    async clickMakeShipment() {
        await this.makeShipmentBtn.click();
    }

    async clickContinue() {
        await this.continueBtn.click();
    }

    async clickNoPopup() {
        await this.noBtn.click();
    }
}

//getByRole('gridcell', { name: 'PICKED' }).first()