import { Page, Locator } from "@playwright/test";

export class DashboardPage {
    readonly page: Page;
    readonly userManagementMenu: Locator;
    readonly contactsMenu: Locator;
    readonly productsMenu: Locator;
    readonly purchasesMenu: Locator;
    readonly saleMenu: Locator;
    readonly salesOrderSubMenu: Locator;
    readonly salesInvoiceMenu: Locator;
    readonly addSalesInvoiceMenu: Locator;
    readonly listSellReturnMenu: Locator;
    readonly orderFulfillmentMenu: Locator;
    readonly usersSubMenu: Locator;
    readonly userProfileDropdown: Locator;
    readonly logoutButton: Locator;

    constructor(page: Page) {
        this.page = page;
        // Sidebar Locators based on AdminSidebarMenu.php labels
        this.userManagementMenu = page.locator('span:has-text("User Management")');
        this.contactsMenu = page.locator('span:has-text("Customer Care")');
        this.productsMenu = page.locator('span:has-text("Products")');
        this.purchasesMenu = page.locator('span:has-text("Vendor Care")');
        this.saleMenu = page.locator('#tour_step7');
        this.salesOrderSubMenu = page.locator('a:has-text("Sales Order (SO)")');
        this.salesInvoiceMenu = page.locator('a:has-text("Sales Invoice (SI)")');
        this.addSalesInvoiceMenu = page.locator('a:has-text("Add Sale Invoice (SI)")');
        this.listSellReturnMenu = page.locator('a:has-text("List Sell Return(CN)")');
        this.orderFulfillmentMenu = page.locator('a[href="/order-fulfillment"]');
        this.usersSubMenu = page.locator('a:has-text("Users")');

        // Header Locators
        this.userProfileDropdown = page.locator('li.user-menu a.dropdown-toggle');
        this.logoutButton = page.locator('a:has-text("Sign Out")'); // Common label or Sign Out based on controllers
    }

    async clickSale() {
        // Ensure the menu is visible and click it to expand
        await this.saleMenu.waitFor({ state: 'visible' });
        await this.saleMenu.click();
    }

    async clickSalesOrder() {
        // Ensure the sub-menu is visible, if not, click the parent again
        const isVisible = await this.salesOrderSubMenu.isVisible();
        if (!isVisible) {
            await this.saleMenu.click();
        }
        await this.salesOrderSubMenu.waitFor({ state: 'visible' });
        await this.salesOrderSubMenu.click();
    }

    async clickAddSalesInvoice() {
        const isVisible = await this.addSalesInvoiceMenu.isVisible();
        if (!isVisible) {
            await this.saleMenu.click();
        }
        await this.addSalesInvoiceMenu.waitFor({ state: 'visible' });
        await this.addSalesInvoiceMenu.click();
    }

    async clickListSellReturn() {
        const isVisible = await this.listSellReturnMenu.isVisible();
        if (!isVisible) {
            await this.saleMenu.click();
        }
        await this.listSellReturnMenu.waitFor({ state: 'visible' });
        await this.listSellReturnMenu.click();
    }

    async clickUserManagement() {
        await this.userManagementMenu.click();
    }

    async clickUsers() {
        await this.usersSubMenu.first().click();
    }


    async clickContacts() {
        await this.contactsMenu.click();
    }

    async clickProducts() {
        await this.productsMenu.click();
    }

    async clickOrderFulfillment() {
        await this.orderFulfillmentMenu.click();
    }

    async logout() {
        await this.userProfileDropdown.click();
        await this.logoutButton.click();
    }
}
