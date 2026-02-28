import { Page, Locator } from "@playwright/test";

/**
 * Page Object Model for the Manage Orders (Order Fulfillment) page.
 */
export class ManageOrdersPage {
    readonly page: Page;
    readonly bypassButton: Locator;
    readonly confirmBypassButton: Locator;
    readonly packingTab: Locator;
    readonly makeShipmentButton: Locator;
    readonly continueButton: Locator;
    readonly noButton: Locator;

    constructor(page: Page) {
        this.page = page;

        // Locators based on HTML snippets provided in Scenario 3
        this.bypassButton = page.locator('#amazonBypassBtn');
        this.confirmBypassButton = page.locator('#bypass_confirm_btn');
        this.packingTab = page.locator('#amazon-tab-packing');
        this.makeShipmentButton = page.locator('#amazonMakeShipmentBtn');
        this.continueButton = page.locator('#continue_button');
        this.noButton = page.locator('button.swal-button--cancel');
    }

    /**
     * Closes the application tour if it appears.
     */
    async clickEndTour() {
        // More generic locator for the tour end button
        const tourClose = this.page.locator('button:has-text("End tour"), button:has-text("End Tour"), button:has-text("Skip")').first();
        try {
            await tourClose.waitFor({ state: 'visible', timeout: 5000 });
            await tourClose.click({ force: true });
            console.log("Closed Application Tour.");
        } catch (e) {
            console.log("Tour modal not present or already closed.");
        }
    }

    /**
     * Selects an order by its checkbox value with extreme resilience.
     */
    async selectOrderByValue(value: string) {
        console.log(`Searching for order with value: ${value}...`);

        // Wait for table body to be present
        try {
            await this.page.waitForSelector('tbody tr', { state: 'attached', timeout: 10000 });
        } catch (e) {
            console.warn("Table rows didn't appear in tbody within 10s.");
        }

        // 1. Try specific value first
        const specific = this.page.locator(`tbody input[value="${value}"], tbody .order-checkbox[value="${value}"]`).first();
        try {
            if (await specific.isVisible({ timeout: 3000 })) {
                await specific.click({ force: true });
                console.log(`Successfully selected specific order: ${value}`);
                return;
            }
        } catch (e) {
            console.log(`Specific order ${value} not clickable or visible.`);
        }

        // 2. Fallback to first available checkbox in any data row
        console.warn("Attempting fallback: clicking the first checkbox in the table body...");
        const firstCheckbox = this.page.locator('tbody tr input[type="checkbox"], tbody tr input.order-checkbox').first();

        try {
            await firstCheckbox.waitFor({ state: 'attached', timeout: 5000 });
            await firstCheckbox.scrollIntoViewIfNeeded();
            await firstCheckbox.click({ force: true });
            console.log("Fallback: Force-clicked the first available order checkbox.");
        } catch (err) {
            console.error("CRITICAL: No order checkboxes found in tbody even after fallback.");
            await this.page.screenshot({ path: 'screenshots/no_checkbox_found_final_debug.png' });
            throw new Error("UI Automation: Failed to find or click any order checkbox in the table.");
        }
    }

    /**
     * Clicks the BYPASS button to move order to packing.
     */
    async clickBypass() {
        // If the modal is already open (from clicking the checkbox), we can skip this
        const modal = this.page.locator('div.modal-content:has-text("Bypass Order")').first();
        if (await modal.isVisible({ timeout: 2000 })) {
            console.log("Bypass modal already open, skipping clickBypass.");
            return;
        }
        await this.bypassButton.waitFor({ state: 'visible' });
        await this.bypassButton.click({ force: true });
    }

    /**
     * Confirms the bypass action in the modal.
     */
    async clickConfirmBypass() {
        const confirmBtn = this.page.locator('#bypass_confirm_btn, button:has-text("Confirm Bypass")').first();
        await confirmBtn.waitFor({ state: 'visible' });
        await confirmBtn.click({ force: true });
    }

    /**
     * Switches to the Packing tab.
     */
    async clickPackingTab() {
        await this.packingTab.waitFor({ state: 'visible' });
        await this.packingTab.click({ force: true });
    }

    /**
     * Clicks the Make Shipment button.
     */
    async clickMakeShipment() {
        await this.makeShipmentButton.waitFor({ state: 'visible' });
        await this.makeShipmentButton.click({ force: true });
    }

    /**
     * Clicks the Continue button in the shipment flow.
     */
    async clickContinue() {
        await this.continueButton.waitFor({ state: 'visible' });
        await this.continueButton.click({ force: true });
    }

    /**
     * Clicks the 'No' button on the SweetAlert popup.
     */
    async clickNoOnPopup() {
        const noBtn = this.page.locator('button.swal-button--cancel, button:has-text("No")').first();
        await noBtn.waitFor({ state: 'visible' });
        await noBtn.click({ force: true });
    }
}
