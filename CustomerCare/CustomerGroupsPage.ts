import { Page, Locator } from "@playwright/test";

export class CustomerGroupsPage {
    readonly page: Page;
    readonly addBtn: Locator;
    readonly table: Locator;

    constructor(page: Page) {
        this.page = page;
        this.addBtn = page.locator('#dynamic_button');
        this.table = page.locator('#customer_groups_table');
    }

    async clickAdd() {
        await this.addBtn.click();
    }

    async getGroupRow(groupName: string) {
        return this.table.locator(`tr:has-text("${groupName}")`);
    }
}
